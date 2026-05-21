{{-- ════════════════════════════════════════════════════════════════
TV MODE FLOOR PLAN SECTION
Displays factory floor plan with real-time machine status indicators
════════════════════════════════════════════════════════════════ --}}

<style>
    /* TV Mode Floor Plan Styling - PORTRAIT LAYOUT */
    .tv-floorplan-section {
        display: flex;
        flex-direction: column;
        gap: 8px;
        height: 100%;
        width: 100%;
    }

    .tv-floorplan-header {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 0;
        border-bottom: 2px solid #2 e7d32;
    }

    .tv-floorplan-header .section-title {
        margin: 0;
        flex: 1;
        font-size: 12px;
        font-weight: 700;
    }

    .tv-floorplan-container {
        flex: 1 1 0;
        min-height: 0;
        position: relative;
        background: white;
        border: 2px solid #dde8c8;
        border-radius: 8px;
        overflow: auto;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 8px;
    }

    .tv-floorplan-container svg {
        width: 100%;
        height: auto;
        display: block;
        object-fit: contain;
    }

    #tv-floorplan-svg {
        width: 100%;
        height: 100%;
        position: relative;
        z-index: 1;
    }

    #tv-status-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: auto;
        z-index: 2;
    }

    /* Machine indicator styling for TV */
    .tv-machine-indicator {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tv-machine-indicator:hover circle {
        stroke: #1f2937;
        stroke-width: 2;
        filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.3));
    }

    .tv-machine-indicator text {
        pointer-events: none;
        /* Note: fill, font-size, text-anchor are set via SVG attributes in JS  - do NOT override here */
    }

    /* Status colors matching dashboard */
    .tv-status-safe circle {
        fill: var(--green, #2e7d32);
    }

    .tv-status-problem circle {
        fill: var(--red, #e74c3c);
    }

    .tv-status-warning circle {
        fill: var(--yellow, #f39c12);
    }

    /* Pulse animation for TV */
    @keyframes tv-pulse {
        0% {
            stroke-width: 1;
            opacity: 0.8;
        }

        100% {
            stroke-width: 0;
            opacity: 0;
        }
    }

    .tv-pulse-ring {
        fill: none;
        animation: tv-pulse 1.5s ease-out infinite;
    }

    .tv-pulse-ring.problem {
        stroke: var(--red, #e74c3c);
    }

    /* Legend for TV */
    .tv-floorplan-legend {
        flex-shrink: 0;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding: 6px 0;
        font-size: 11px;
        font-family: 'Roboto Condensed', sans-serif;
        font-weight: 600;
    }

    .tv-legend-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .tv-legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    /* Responsive for TV  - do NOT set font-size here; it is controlled by SVG viewBox scaling in JS */
    .tv-machine-indicator text {
        /* font-size is intentionally NOT overridden here  - JS sets it via SVG attributes */
    }
</style>

<div class="tv-floorplan-section" id="tvFloorplanSection">
    {{-- Header with legend --}}
    <div class="tv-floorplan-header">
        <div class="section-title">🏭 Layout Fact.{{ $factoryCode === 'f2' ? '2' : '3/4' }}</div>
    </div>

    {{-- Legend --}}
    <div class="tv-floorplan-legend">
        <div class="tv-legend-item">
            <div class="tv-legend-dot" style="background: var(--green, #2e7d32);"></div><span>Safe</span>
        </div>
        <div class="tv-legend-item">
            <div class="tv-legend-dot" style="background: var(--yellow, #f39c12);"></div><span>Warning</span>
        </div>
        <div class="tv-legend-item">
            <div class="tv-legend-dot" style="background: #f97316;"></div><span>Caution</span>
        </div>
        <div class="tv-legend-item">
            <div class="tv-legend-dot" style="background: var(--red, #e74c3c);"></div><span>Problem</span>
        </div>
    </div>

    {{-- Floor Plan Container --}}
    <div class="tv-floorplan-container">
        {{-- SVG Floor Plan (background) --}}
        <svg id="tv-floorplan-svg" preserveAspectRatio="xMidYMid meet">
            {{-- Floor plan content will be loaded here --}}
        </svg>

        {{-- SVG Overlay for Status Indicators (absolutely positioned) --}}
        <svg id="tv-status-overlay" preserveAspectRatio="xMidYMid meet"></svg>
    </div>
</div>

{{-- ════════════════════════════════════ --}}
{{-- Floor Plan Display JavaScript --}}
{{-- ════════════════════════════════════ --}}

<script>
    // TV Mode Floor Plan System
    const TVFloorPlan = {
        // Factory name for API calls (must match database values like "Factory 2")
        factory: '{{ $factory }}',
        factoryCode: '{{ $factoryCode }}',
        // Pass current TV date/shift context so problem-status dots are accurate
        tanggal: '{{ $tanggal }}',
        shift: '{{ $shift }}',
        statusColors: {
            'ok': 'var(--green,#2e7d32)',
            'safe': 'var(--green,#2e7d32)',
            'ada_masalah': 'var(--red,#e74c3c)',
            'problem': 'var(--red,#e74c3c)',
            'warning': 'var(--yellow,#f39c12)',
            'caution': '#f97316',
        },

        statusNames: {
            'ok': 'Safe',
            'safe': 'Safe',
            'ada_masalah': 'Problem',
            'problem': 'Problem',
            'warning': 'Warning',
            'caution': 'Caution',
        },

        // Initialize the floor plan for TV
        init() {
            console.log('TVFloorPlan init started for factory:', this.factory);
            const svgContainer = document.getElementById('tv-floorplan-svg');
            if (!svgContainer) {
                console.error('Floor plan SVG container not found');
                return;
            }

            console.log('SVG container found, loading SVG from /svg/bitmap-{{ $factoryCode }}.svg');

            // Load floor plan SVG
            this.loadFloorPlanSVG();

            // Start auto-refresh
            this.startAutoRefresh();
        },

        // Load the floor plan SVG file
        loadFloorPlanSVG() {
            const svgPath = `/svg/bitmap-{{ $factoryCode }}.svg`;
            const svgContainer = document.getElementById('tv-floorplan-svg');

            fetch(svgPath)
                .then(r => r.text())
                .then(html => {
                    // Parse SVG content
                    const parser = new DOMParser();
                    const svgDoc = parser.parseFromString(html, 'image/svg+xml');
                    const svgElement = svgDoc.documentElement;

                    // Copy viewBox and important attributes
                    const viewBox = svgElement.getAttribute('viewBox');
                    if (viewBox) {
                        svgContainer.setAttribute('viewBox', viewBox);
                    }

                    // Copy SVG content (paths, groups, etc) - exclude scripts
                    Array.from(svgElement.children).forEach(child => {
                        if (child.tagName.toLowerCase() !== 'script') {
                            svgContainer.appendChild(child.cloneNode(true));
                        }
                    });

                    // Now render machines
                    this.refresh();
                })
                .catch(err => {
                    console.error('Failed to load floor plan SVG:', err);
                    // Show error message
                    svgContainer.innerHTML = '<text x="50%" y="50%" text-anchor="middle" fill="#e74c3c" font-size="16">Failed to load floor plan</text>';
                });
        },

        // Refresh machine status indicators
        async refresh() {
            try {
                const machines = await this.fetchMachines();
                this.renderMachineIndicators(machines);
            } catch (err) {
                console.error('Failed to refresh floor plan:', err);
            }
        },

        // Fetch machines from API  - includes tanggal & shift so problem dots reflect current context
        async fetchMachines() {
            const factory = this.factory.trim();
            const encodedFactory = encodeURIComponent(factory);
            const url = `/api/machines/floor-plan?factory=${encodedFactory}&tanggal=${this.tanggal}&shift=${this.shift}`;
            console.log('TVFloorPlan.factory =', JSON.stringify(this.factory));
            console.log('Fetching URL:', url);
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    const text = await response.text();
                    console.error(`API returned ${response.status}:`, text);
                    throw new Error(`HTTP ${response.status}`);
                }
                const data = await response.json();
                console.log(`✓ API returned ${data.length} machines:`, data);
                return data;
            } catch (err) {
                console.error('Fetch error:', err);
                throw err;
            }
        },

        // Render machine status indicators
        renderMachineIndicators(machines) {
            const overlay = document.getElementById('tv-status-overlay');
            if (!overlay) {
                console.error('Status overlay not found');
                return;
            }

            // Clear existing
            overlay.innerHTML = '';

            // Get base SVG viewBox
            const baseSvg = document.getElementById('tv-floorplan-svg');
            if (baseSvg && baseSvg.hasAttribute('viewBox')) {
                overlay.setAttribute('viewBox', baseSvg.getAttribute('viewBox'));
            }

            console.log('Rendering', machines.length, 'machines');

            // Render each machine
            machines.forEach(machine => {
                console.log('Machine:', machine.name, 'cx:', machine.floor_cx, 'cy:', machine.floor_cy);
                if (!machine.floor_cx || !machine.floor_cy) {
                    console.warn('No coordinates for', machine.name);
                    return;
                }

                const group = this.createMachineIndicator(machine);
                overlay.appendChild(group);
            });

            console.log('Rendered indicators');
        },

        // Create a machine indicator element
        createMachineIndicator(machine) {
            const statusClass = this.getStatusClass(machine.status);
            const r = 22;   // dot radius in SVG viewBox units
            const cx = parseFloat(machine.floor_cx);
            const cy = parseFloat(machine.floor_cy);
            const machineName = machine.name;

            // Create group
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.classList.add('tv-machine-indicator', `tv-status-${statusClass}`);
            g.setAttribute('data-machine-id', machine.id);
            g.setAttribute('data-status', machine.status);

            // Determine fill color based on status
            let fillColor = '#2e7d32'; // default green
            if (statusClass === 'problem') {
                fillColor = '#e74c3c'; // red
            } else if (statusClass === 'warning') {
                fillColor = '#f39c12'; // yellow
            }

            // Pulse ring for problems
            if (statusClass === 'problem') {
                const pulse = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                pulse.classList.add('tv-pulse-ring', 'problem');
                pulse.setAttribute('cx', cx);
                pulse.setAttribute('cy', cy);
                pulse.setAttribute('r', r + 12);
                pulse.setAttribute('stroke', fillColor);
                pulse.setAttribute('fill', 'none');
                pulse.setAttribute('stroke-width', '3');
                g.appendChild(pulse);
            }

            // Status circle
            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', cx);
            circle.setAttribute('cy', cy);
            circle.setAttribute('r', r);
            circle.setAttribute('fill', fillColor);
            circle.setAttribute('stroke', 'white');
            circle.setAttribute('stroke-width', '3');
            g.appendChild(circle);

            // ── Machine name label  - transparent, LEFT of the dot ───────────────
            // text-anchor="end" so the text right-aligns flush against the dot edge.
            // No background rect  - fully transparent / clean look.
            const fontSize = 20;                    // SVG viewBox units (scales with SVG)
            const gap = 14;                    // gap between dot edge and text end
            const labelX = cx - r - gap;         // text right edge (end anchor)

            // Thin dark outline for legibility on light floor-plan backgrounds
            const outline = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            outline.setAttribute('x', labelX);
            outline.setAttribute('y', cy + fontSize * 0.36);
            outline.setAttribute('font-size', fontSize);
            outline.setAttribute('font-weight', '800');
            outline.setAttribute('text-anchor', 'end');
            outline.setAttribute('fill', 'none');
            outline.setAttribute('stroke', '#000000ff');
            outline.setAttribute('stroke-width', '6');
            outline.setAttribute('stroke-linejoin', 'round');
            outline.setAttribute('font-family', 'Arial, sans-serif');
            outline.setAttribute('pointer-events', 'none');
            outline.textContent = machineName;
            g.appendChild(outline);

            // White fill text on top of the outline
            const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            label.setAttribute('x', labelX);
            label.setAttribute('y', cy + fontSize * 0.36);
            label.setAttribute('font-size', fontSize);
            label.setAttribute('font-weight', '800');
            label.setAttribute('text-anchor', 'end');
            label.setAttribute('fill', '#e8f5e9e8');
            label.setAttribute('font-family', 'Arial, sans-serif');
            label.setAttribute('letter-spacing', '0.5');
            label.setAttribute('pointer-events', 'none');
            label.textContent = machineName;
            g.appendChild(label);

            return g;
        },

        // Get status class
        getStatusClass(status) {
            if (!status) return 'unknown';
            if (status === 'ok' || status === 'safe') return 'safe';
            if (status === 'ada_masalah' || status === 'problem') return 'problem';
            if (status === 'warning') return 'warning';
            if (status === 'caution') return 'warning';
            return 'safe';
        },

        // Start auto-refresh every 10 seconds
        startAutoRefresh() {
            this.refresh();
            this.refreshInterval = setInterval(() => this.refresh(), 10000);
        },

        // Stop auto-refresh
        stopAutoRefresh() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        },
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        TVFloorPlan.init();
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        TVFloorPlan.stopAutoRefresh();
    });
</script>