/**
 * Floor Plan Interactive System
 * SVG-based factory floor mapping with real-time status updates
 */

// ════════════════════════════════════════════════════════════════
// SVG COORDINATE SYSTEM UTILITIES
// ════════════════════════════════════════════════════════════════

const FloorPlanCoordinates = {
    /**
     * Convert browser pixel coordinates to SVG viewBox coordinates
     * Uses the inverse transformation matrix to handle any SVG scaling/positioning
     * 
     * @param {SVGElement} svg - The SVG element
     * @param {MouseEvent} event - The click event
     * @returns {Object} Coordinates {cx, cy} in viewBox units
     */
    screenToViewBox(svg, event) {
        const pt = svg.createSVGPoint();
        pt.x = event.clientX;
        pt.y = event.clientY;

        // Get the CTM (Current Transformation Matrix) from screen to SVG
        const screenCTM = svg.getScreenCTM();
        if (!screenCTM) {
            console.error('Unable to get SVG screen CTM');
            return null;
        }

        // Invert the matrix to go from screen to viewBox
        const invertedCTM = screenCTM.inverse();

        // Transform the point using the inverted matrix
        const transformedPt = pt.matrixTransform(invertedCTM);

        return {
            cx: Math.round(transformedPt.x * 1000) / 1000,
            cy: Math.round(transformedPt.y * 1000) / 1000
        };
    },

    /**
     * Verify if coordinates are within reasonable bounds for the factory floor
     * @param {number} cx
     * @param {number} cy
     * @returns {boolean}
     */
    isValidCoordinate(cx, cy) {
        // Adjust these bounds based on your SVG dimensions
        return (
            !isNaN(cx) && !isNaN(cy) &&
            cx >= 0 && cy >= 0 &&
            cx <= 1000 && cy <= 1000 // Adjust to your viewBox
        );
    }
};

// ════════════════════════════════════════════════════════════════
// STATUS COLORS & STATUS HELPERS
// ════════════════════════════════════════════════════════════════

const StatusSystem = {
    colors: {
        'ok': '#22c55e',
        'safe': '#22c55e',
        'ada_masalah': '#ef4444',
        'problem': '#ef4444',
        'warning': '#eab308',
        'low-risk': '#eab308',
        'caution': '#f97316',
        'mid-risk': '#f97316',
    },

    names: {
        'ok': 'OK',
        'safe': 'Safe',
        'ada_masalah': 'Problem',
        'problem': 'Problem',
        'warning': 'Warning',
        'caution': 'Caution',
    },

    getClass(status) {
        if (!status) return 'unknown';
        if (status === 'ok' || status === 'safe') return 'safe';
        if (status === 'ada_masalah' || status === 'problem') return 'problem';
        if (status === 'warning') return 'low-risk';
        if (status === 'caution') return 'mid-risk';
        return 'unknown';
    },

    getColor(status) {
        return this.colors[status] || '#9ca3af';
    },

    getName(status) {
        return this.names[status] || status;
    }
};

// ════════════════════════════════════════════════════════════════
// FLOOR PLAN RENDERER
// ════════════════════════════════════════════════════════════════

const FloorPlanRenderer = {
    /**
     * Render machine status indicators on the SVG overlay
     * @param {Array} machines - Array of machine objects with coordinates and status
     * @param {SVGElement} overlay - The overlay SVG element
     */
    renderMachines(machines, overlay) {
        if (!overlay) return;

        // Clear existing indicators
        overlay.innerHTML = '';

        // Get base SVG for viewBox reference
        const baseSvg = overlay.parentElement?.querySelector('svg:first-child');
        if (!baseSvg) return;

        // Copy viewBox from base to overlay
        const viewBox = baseSvg.getAttribute('viewBox');
        if (viewBox) overlay.setAttribute('viewBox', viewBox);

        // Render each machine
        machines.forEach(machine => {
            if (!machine.floor_cx || !machine.floor_cy) return;

            const group = this.createMachineIndicator(machine);
            overlay.appendChild(group);
        });
    },

    /**
     * Create a machine indicator group element
     * @param {Object} machine - Machine with coordinates and status
     * @returns {SVGGroup}
     */
    createMachineIndicator(machine) {
        const statusClass = StatusSystem.getClass(machine.status);
        
        // Create group
        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.classList.add('machine-indicator', `status-${statusClass}`);
        g.setAttribute('data-machine-id', machine.id);
        g.setAttribute('data-machine-name', machine.name);
        g.setAttribute('data-status', machine.status);

        // Add pulse ring for problems
        if (statusClass === 'problem') {
            const pulse = this.createPulseRing(machine.floor_cx, machine.floor_cy, statusClass);
            g.appendChild(pulse);
        }

        // Add status circle
        const circle = this.createStatusCircle(machine.floor_cx, machine.floor_cy);
        g.appendChild(circle);

        // Add label
        const label = this.createLabel(machine.name, machine.floor_cx, machine.floor_cy);
        g.appendChild(label);

        // Add event listeners
        g.addEventListener('click', (e) => {
            e.stopPropagation();
            this.showMachineDetail(machine);
        });

        return g;
    },

    createStatusCircle(cx, cy) {
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', cx);
        circle.setAttribute('cy', cy);
        circle.setAttribute('r', '4');
        circle.setAttribute('stroke', 'white');
        circle.setAttribute('stroke-width', '1');
        return circle;
    },

    createPulseRing(cx, cy, statusClass) {
        const pulse = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        pulse.classList.add('pulse-ring', statusClass);
        pulse.setAttribute('cx', cx);
        pulse.setAttribute('cy', cy);
        pulse.setAttribute('r', '6');
        return pulse;
    },

    createLabel(name, cx, cy) {
        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('x', cx);
        text.setAttribute('y', parseFloat(cy) + 10);
        text.setAttribute('font-size', '7');
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('fill', 'white');
        text.textContent = name;
        return text;
    },

    showMachineDetail(machine) {
        const statusName = StatusSystem.getName(machine.status);
        const coords = `(${machine.floor_cx.toFixed(2)}, ${machine.floor_cy.toFixed(2)})`;
        
        alert(
            `Machine: ${machine.name}\n` +
            `Status: ${statusName}\n` +
            `Coordinates: ${coords}`
        );
    }
};

// ════════════════════════════════════════════════════════════════
// API CLIENT
// ════════════════════════════════════════════════════════════════

const FloorPlanAPI = {
    /**
     * Fetch machines for a factory's floor plan
     * @param {string} factory - Factory code (e.g., 'F2', 'F3')
     * @returns {Promise}
     */
    async getMachines(factory = 'F2') {
        try {
            const response = await fetch(`/api/machines/floor-plan?factory=${encodeURIComponent(factory)}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch machines:', error);
            return [];
        }
    },

    /**
     * Update machine floor coordinates
     * @param {number} machineId
     * @param {Object} coordinates - {floor_cx, floor_cy, floor_plan}
     * @returns {Promise}
     */
    async updateCoordinates(machineId, coordinates) {
        try {
            const response = await fetch(`/admin/machines/${machineId}/floor-coordinates`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify(coordinates)
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to update coordinates:', error);
            throw error;
        }
    },

    /**
     * Get CSRF token from meta tag
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
};

// ════════════════════════════════════════════════════════════════
// FLOOR PLAN DISPLAY MODULE
// ════════════════════════════════════════════════════════════════

const FloorPlanDisplay = {
    /**
     * Initialize the floor plan display with auto-refresh
     * @param {string} factory - Factory code
     * @param {number} refreshInterval - Refresh interval in ms (default: 10000)
     */
    async init(factory = 'F2', refreshInterval = 10000) {
        const overlay = document.getElementById('status-overlay');
        if (!overlay) {
            console.error('Status overlay element not found');
            return;
        }

        // Initial load
        await this.refresh(factory, overlay);

        // Auto-refresh every interval
        this.refreshIntervalId = setInterval(
            () => this.refresh(factory, overlay),
            refreshInterval
        );
    },

    /**
     * Refresh the display
     */
    async refresh(factory, overlay) {
        const machines = await FloorPlanAPI.getMachines(factory);
        FloorPlanRenderer.renderMachines(machines, overlay);
        this.updateStatusTable(machines);
    },

    /**
     * Update the machines table
     */
    updateStatusTable(machines) {
        const tbody = document.getElementById('machines-table');
        if (!tbody) return;

        tbody.innerHTML = machines.map(m => {
            const statusClass = StatusSystem.getClass(m.status);
            const statusName = StatusSystem.getName(m.status);
            const updatedAt = m.updated_at ? new Date(m.updated_at).toLocaleString() : '-';

            return `
                <tr>
                    <td class="coord-display">${m.name}</td>
                    <td>
                        <span class="status-badge ${statusClass}">
                            ${statusName}
                        </span>
                    </td>
                    <td class="coord-display">(${m.floor_cx || '-'}, ${m.floor_cy || '-'})</td>
                    <td>${updatedAt}</td>
                </tr>
            `;
        }).join('');
    },

    /**
     * Stop auto-refresh
     */
    destroy() {
        if (this.refreshIntervalId) {
            clearInterval(this.refreshIntervalId);
        }
    }
};

// ════════════════════════════════════════════════════════════════
// FLOOR PLAN EDITOR MODULE
// ════════════════════════════════════════════════════════════════

const FloorPlanEditor = {
    /**
     * Initialize the floor plan editor
     * @param {string} factory - Factory code
     */
    init(factory = 'F2') {
        const container = document.getElementById('svg-container');
        if (!container) return;

        const svg = container.querySelector('svg');
        if (!svg) {
            console.error('SVG element not found in svg-container');
            return;
        }

        // Handle SVG click
        svg.addEventListener('click', (e) => this.handleSvgClick(e, factory));

        // Setup form handlers
        this.setupFormHandlers(factory);

        // Load existing machines
        this.loadMachinesList(factory);
    },

    /**
     * Handle clicks on the SVG floor plan
     */
    handleSvgClick(event, factory) {
        const svg = event.currentTarget;
        const coords = FloorPlanCoordinates.screenToViewBox(svg, event);

        if (!coords || !FloorPlanCoordinates.isValidCoordinate(coords.cx, coords.cy)) {
            console.error('Invalid coordinates');
            return;
        }

        // Update input fields
        document.getElementById('coord-cx').value = coords.cx.toFixed(3);
        document.getElementById('coord-cy').value = coords.cy.toFixed(3);

        // Update status display
        const clickInfo = document.getElementById('click-info');
        if (clickInfo) {
            clickInfo.textContent = `cx="${coords.cx.toFixed(3)}" cy="${coords.cy.toFixed(3)}"`;
        }

        const posDisplay = document.getElementById('current-position');
        if (posDisplay) {
            posDisplay.innerHTML = `
                <div class="bg-blue-100 p-2 rounded font-mono text-blue-900">
                    ✓ Position: (${coords.cx.toFixed(2)}, ${coords.cy.toFixed(2)})
                </div>
            `;
        }
    },

    /**
     * Setup form handlers
     */
    setupFormHandlers(factory) {
        // Save button
        const saveBtn = document.getElementById('save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.handleSave(factory));
        }

        // Clear button
        const clearBtn = document.getElementById('clear-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.handleClear());
        }
    },

    /**
     * Handle save action
     */
    async handleSave(factory) {
        const machineId = document.getElementById('machine-select').value;
        const cx = document.getElementById('coord-cx').value;
        const cy = document.getElementById('coord-cy').value;

        if (!machineId) {
            alert('Please select a machine');
            return;
        }

        if (!cx || !cy) {
            alert('Please click on the floor plan to capture coordinates');
            return;
        }

        try {
            const result = await FloorPlanAPI.updateCoordinates(machineId, {
                floor_cx: parseFloat(cx),
                floor_cy: parseFloat(cy),
                floor_plan: factory
            });

            if (result.success) {
                alert(`✓ Coordinates saved for ${result.machine_name}`);
                this.handleClear();
                this.loadMachinesList(factory);
            } else {
                alert('Error: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error saving coordinates: ' + error.message);
        }
    },

    /**
     * Handle clear action
     */
    handleClear() {
        document.getElementById('machine-select').value = '';
        document.getElementById('coord-cx').value = '';
        document.getElementById('coord-cy').value = '';
        document.getElementById('click-info').textContent = 'Click on the floor plan...';
        document.getElementById('current-position').textContent = 'Position: -';
    },

    /**
     * Load and display machines with coordinates
     */
    async loadMachinesList(factory) {
        try {
            const machines = await FloorPlanAPI.getMachines(factory);
            const list = document.getElementById('machines-list');
            if (!list) return;

            if (machines.length === 0) {
                list.innerHTML = '<p class="text-gray-500 text-sm">No machines positioned yet</p>';
                return;
            }

            list.innerHTML = machines.map(m => `
                <div class="p-2 bg-gray-100 rounded flex justify-between items-center text-sm cursor-pointer hover:bg-gray-200" 
                     onclick="document.getElementById('coord-cx').value = ${m.floor_cx}; document.getElementById('coord-cy').value = ${m.floor_cy};">
                    <span class="font-mono font-semibold">${m.name}</span>
                    <span class="text-xs text-gray-600">(${m.floor_cx.toFixed(2)}, ${m.floor_cy.toFixed(2)})</span>
                </div>
            `).join('');
        } catch (error) {
            console.error('Failed to load machines list:', error);
        }
    }
};

// ════════════════════════════════════════════════════════════════
// AUTO-INITIALIZATION
// ════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    const hasEditor = document.getElementById('svg-container');
    const hasDisplay = document.getElementById('status-overlay');

    if (hasEditor) {
        // Initialize editor mode
        const factory = document.querySelector('meta[data-factory]')?.dataset.factory || 'f2';
        FloorPlanEditor.init(factory);
    }

    if (hasDisplay) {
        // Initialize display mode
        const factory = document.querySelector('meta[data-factory]')?.dataset.factory || 'F2';
        FloorPlanDisplay.init(factory, 10000);
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    FloorPlanDisplay.destroy();
});
