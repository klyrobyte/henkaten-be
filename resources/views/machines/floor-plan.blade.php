@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Factory Floor Plan - {{ $factory ?? 'F2' }}</h1>

        <div class="bg-white rounded-lg shadow-xl p-4">
            <!-- SVG Floor Plan Container -->
            <div id="floor-plan-container" class="relative mx-auto">
                {!! file_get_contents(public_path('svg/bitmap-f2.svg')) !!}

                <!-- Machine Status Indicators SVG Overlay -->
                <svg id="status-overlay" class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid meet"
                    pointer-events="all">
                    <!-- Dynamically populated by JavaScript -->
                </svg>
            </div>

            <!-- Status Legend -->
            <div class="mt-6 flex flex-wrap gap-8 justify-center text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-green-500"></div>
                    <span>Safe</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-yellow-400"></div>
                    <span>Low Risk</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-orange-400"></div>
                    <span>Mid Risk</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-red-500"></div>
                    <span>Problem</span>
                </div>
            </div>
        </div>

        <!-- Machines Status Table -->
        <div class="mt-8 bg-white rounded-lg shadow p-4">
            <h2 class="text-xl font-semibold mb-4">Machine Status Details</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Machine</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Position (X, Y)</th>
                            <th class="px-4 py-2 text-left">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody id="machines-table">
                        <!-- Dynamically populated -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        #floor-plan-container {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        #floor-plan-container svg:first-child {
            width: 100%;
            height: auto;
            display: block;
        }

        #status-overlay {
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Machine Status Indicators */
        .machine-indicator {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .machine-indicator:hover circle {
            stroke: #1f2937;
            stroke-width: 2;
        }

        .machine-indicator text {
            font-size: 8px;
            font-weight: bold;
            text-anchor: middle;
            dominant-baseline: middle;
            pointer-events: none;
            fill: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Status Colors */
        .status-safe circle {
            fill: #22c55e;
        }

        .status-low-risk circle {
            fill: #eab308;
        }

        .status-mid-risk circle {
            fill: #f97316;
        }

        .status-problem circle {
            fill: #ef4444;
        }

        /* Pulse Animation for Problems */
        @keyframes pulse {
            0% {
                stroke-width: 1;
                opacity: 0.8;
            }

            50% {
                stroke-width: 2;
                opacity: 0.4;
            }

            100% {
                stroke-width: 1;
                opacity: 0.8;
            }
        }

        .pulse-ring {
            fill: none;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .status-problem .pulse-ring {
            stroke: #ef4444;
        }

        /* Tooltip */
        .status-tooltip {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            position: absolute;
            z-index: 50;
            white-space: nowrap;
            pointer-events: none;
            display: none;
        }

        .machine-indicator:hover+.status-tooltip {
            display: block;
        }
    </style>

    <script>
        // ========================================
        // LIVE FLOOR PLAN RENDERER
        // ========================================

        const statusColors = {
            'ok': '#22c55e',
            'safe': '#22c55e',
            'ada_masalah': '#ef4444',
            'problem': '#ef4444',
            'warning': '#eab308',
            'low-risk': '#eab308',
            'caution': '#f97316',
            'mid-risk': '#f97316',
        };

        const statusNames = {
            'ok': 'OK',
            'safe': 'Safe',
            'ada_masalah': 'Problem',
            'problem': 'Problem',
            'warning': 'Warning',
            'caution': 'Caution',
        };

        function renderMachineIndicators(machines) {
            const overlay = document.getElementById('status-overlay');

            // Clear existing indicators completely
            overlay.innerHTML = '';

            // Get SVG dimensions for proper scaling
            const baselineSvg = document.querySelector('#floor-plan-container svg:first-child');
            if (!baselineSvg) {
                console.error('Baseline SVG not found');
                return;
            }

            const viewBoxAttr = baselineSvg.getAttribute('viewBox');
            if (!viewBoxAttr) {
                console.warn('SVG has no viewBox attribute, trying to get bounding box');
                // Fallback: try to get dimensions from SVG
                const width = baselineSvg.getAttribute('width') || baselineSvg.getBBox().width;
                const height = baselineSvg.getAttribute('height') || baselineSvg.getBBox().height;
                overlay.setAttribute('viewBox', `0 0 ${width} ${height}`);
            } else {
                // Set viewBox on overlay to match base SVG
                overlay.setAttribute('viewBox', viewBoxAttr);
            }

            overlay.setAttribute('preserveAspectRatio', 'xMidYMid meet');

            console.log('Floor plan viewBox:', viewBoxAttr, 'Rendering', machines.length, 'machines');

            machines.forEach(machine => {
                // Skip machines without coordinates
                if (!machine.floor_cx && machine.floor_cx !== 0 || !machine.floor_cy && machine.floor_cy !== 0) {
                    return;
                }

                const cx = parseFloat(machine.floor_cx);
                const cy = parseFloat(machine.floor_cy);

                // Validate coordinates
                if (isNaN(cx) || isNaN(cy)) {
                    console.warn(`Invalid coordinates for ${machine.name}: (${machine.floor_cx}, ${machine.floor_cy})`);
                    return;
                }

                console.log(`✓ Rendering ${machine.name} at (${cx}, ${cy}) status: ${machine.status}`);

                const statusClass = getStatusClass(machine.status);
                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                g.classList.add('machine-indicator', `status-${statusClass}`);
                g.setAttribute('data-machine-id', machine.id);
                g.setAttribute('data-machine-name', machine.name);
                g.setAttribute('data-status', machine.status);

                // Pulse Ring (for problems)  - must be first so it renders behind
                if (machine.status === 'ada_masalah' || machine.status === 'problem') {
                    const pulse = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    pulse.classList.add('pulse-ring');
                    pulse.setAttribute('cx', cx);
                    pulse.setAttribute('cy', cy);
                    pulse.setAttribute('r', '6');
                    g.appendChild(pulse);
                }

                // Status Dot
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', cx);
                circle.setAttribute('cy', cy);
                circle.setAttribute('r', '4');
                circle.setAttribute('stroke', 'white');
                circle.setAttribute('stroke-width', '1');
                g.appendChild(circle);

                // Machine Label
                const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                label.setAttribute('x', cx);
                label.setAttribute('y', cy + 10);
                label.setAttribute('font-size', '7');
                label.setAttribute('text-anchor', 'middle');
                label.setAttribute('fill', 'white');
                label.textContent = machine.name;
                g.appendChild(label);

                // Click Handler
                g.addEventListener('click', function () {
                    showMachineDetail(machine);
                });

                overlay.appendChild(g);
            });
        }

        function getStatusClass(status) {
            if (!status) return 'unknown';
            if (status === 'ok' || status === 'safe') return 'safe';
            if (status === 'ada_masalah' || status === 'problem') return 'problem';
            if (status === 'warning') return 'low-risk';
            if (status === 'caution') return 'mid-risk';
            return 'unknown';
        }

        function showMachineDetail(machine) {
            alert(`Machine: ${machine.name}\nStatus: ${statusNames[machine.status] || machine.status}\nCoordinates: (${machine.floor_cx}, ${machine.floor_cy})`);
        }

        function loadMachines() {
            const factory = '{{ $factory ?? "f2" }}';
            fetch(`/api/machines/floor-plan?factory=${factory}`)
                .then(r => r.json())
                .then(machines => {
                    renderMachineIndicators(machines);
                    renderMachinesTable(machines);
                })
                .catch(err => console.error('Failed to load machines:', err));
        }

        function renderMachinesTable(machines) {
            const tbody = document.getElementById('machines-table');
            tbody.innerHTML = machines.map(m => {
                const statusClass = getStatusClass(m.status);
                const statusBadge = `<span class="px-2 py-1 text-white rounded text-xs font-semibold bg-${getStatusBgColor(statusClass)}">${statusNames[m.status] || m.status}</span>`;

                return `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold">${m.name}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                        <td class="px-4 py-3 font-mono text-sm">(${m.floor_cx || '-'}, ${m.floor_cy || '-'})</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">${formatDate(m.updated_at)}</td>
                    </tr>
                `;
            }).join('');
        }

        function getStatusBgColor(statusClass) {
            const colorMap = {
                'safe': 'green-500',
                'low-risk': 'yellow-500',
                'mid-risk': 'orange-500',
                'problem': 'red-500',
            };
            return colorMap[statusClass] || 'gray-500';
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleString();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', loadMachines);

        // Refresh periodically (every 10 seconds)
        setInterval(loadMachines, 10000);
    </script>
@endsection