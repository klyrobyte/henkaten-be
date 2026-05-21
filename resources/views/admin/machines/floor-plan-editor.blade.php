<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Floor Plan Editor - HENKATEN</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; color: #333; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 { margin-bottom: 30px; font-size: 28px; color: #2e7d32; }
        .grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .card h2 { font-size: 18px; font-weight: 700; margin-bottom: 15px; color: #2e7d32; }
        #svg-container { border: 2px solid #ddd; border-radius: 6px; background: #fafafa; padding: 10px; overflow: auto; max-height: 600px; }
        #svg-container svg { width: 100%; height: auto; cursor: crosshair; display: block; }
        .info-box { background: #e3f2fd; border-left: 4px solid #2196F3; padding: 12px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px; }
        select, input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { padding: 10px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 14px; }
        .btn-primary { background: #2e7d32; color: white; }
        .btn-primary:hover { background: #1e5620; }
        .btn-secondary { background: #f5f5f5; color: #333; border: 1px solid #ddd; }
        .btn-secondary:hover { background: #efefef; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .mono { font-family: 'Courier New', monospace; font-size: 13px; }
        .text-sm { font-size: 12px; color: #666; }
        .success { color: #2e7d32; font-weight: 600; }
        .error { color: #e74c3c; font-weight: 600; }
        .warning { color: #f39c12; font-weight: 600; }
        .button-group { display: flex; gap: 8px; }
        button { flex: 1; }
        #machines-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
        .machine-card { background: #f9f9f9; border-left: 4px solid #2e7d32; padding: 12px; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏭 Floor Plan Coordinate Editor</h1>
        
        <div class="grid">
            <div class="card">
                <h2>Floor Plan (Factory <span id="factory-label">Loading...</span>)</h2>
                <p class="text-sm" style="margin-bottom: 10px;"><strong>Click</strong> on floor plan to capture coordinates</p>
                <div id="svg-container">
                    <p style="padding: 20px; text-align: center; color: #999;">Loading floor plan...</p>
                </div>
                <div class="info-box" style="margin-top: 15px;">
                    <strong>Last Click:</strong><br>
                    <span id="click-info" class="mono">Click on the floor plan to capture coordinates...</span>
                </div>
            </div>

            <div class="card">
                <h2>Assign Coordinates</h2>
                <div class="form-group">
                    <label>Select Machine</label>
                    <select id="machine-select">
                        <option value="">-- Select Machine --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>X Coordinate (ViewBox)</label>
                    <input type="number" id="coord-x" placeholder="0.0" step="0.1" readonly>
                </div>
                <div class="form-group">
                    <label>Y Coordinate (ViewBox)</label>
                    <input type="number" id="coord-y" placeholder="0.0" step="0.1" readonly>
                </div>
                <div class="button-group">
                    <button class="btn-primary" onclick="saveMachineCoordinates()">Save</button>
                    <button class="btn-secondary" onclick="clearSelection()">Clear Form</button>
                    <button class="btn-danger" onclick="deleteCoordinates()">Delete</button>
                </div>
                <div id="save-message" style="margin-top: 12px; font-weight: 600;"></div>
            </div>
        </div>

        <div class="card">
            <h2>Machines with Coordinates</h2>
            <div id="machines-list"></div>
        </div>
    </div>

    <script>
        // Get factory from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const FACTORY_CODE = urlParams.get('factory') || 'f2';
        const FACTORY_MAP = { 'f2': 'Factory 2', 'f34': 'Factory 3 & 4' };
        const FACTORY_NAME = FACTORY_MAP[FACTORY_CODE] || 'Factory 2';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let capturedX = null;
        let capturedY = null;
        let svgElement = null;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('factory-label').textContent = FACTORY_NAME;
            loadSVG();
            loadMachines();
            loadSavedMachines();
            
            // When machine is selected, load existing coordinates
            document.getElementById('machine-select').addEventListener('change', onMachineSelected);
        });

        // Handle machine selection - populate existing coordinates if available
        async function onMachineSelected(event) {
            const machineId = event.target.value;
            if (!machineId) {
                clearSelection();
                return;
            }
            
            try {
                // Fetch this specific machine's data
                const response = await fetch(`/api/machines/${machineId}/floor-plan`);
                
                if (response.ok) {
                    const machine = await response.json();
                    // If machine has coordinates, populate them
                    if (machine.floor_cx && machine.floor_cy) {
                        document.getElementById('coord-x').value = parseFloat(machine.floor_cx);
                        document.getElementById('coord-y').value = parseFloat(machine.floor_cy);
                        document.getElementById('click-info').textContent = 
                            `Existing: X: ${machine.floor_cx}, Y: ${machine.floor_cy} | Click to update`;
                        console.log(`Loaded existing coordinates for ${machine.name}`);
                    } else {
                        // No existing coordinates, prepare for new click
                        document.getElementById('coord-x').value = '';
                        document.getElementById('coord-y').value = '';
                        document.getElementById('click-info').textContent = 'Click on floor plan to set coordinates...';
                    }
                } else {
                    // Machine not found or no coordinates yet
                    document.getElementById('coord-x').value = '';
                    document.getElementById('coord-y').value = '';
                    document.getElementById('click-info').textContent = 'Click on floor plan to set coordinates...';
                }
            } catch (err) {
                console.error('Error loading machine coordinates:', err);
            }
        }

        // Load SVG floor plan
        async function loadSVG() {
            try {
                const response = await fetch(`/svg/bitmap-${FACTORY_CODE}.svg`);
                if (!response.ok) throw new Error('SVG not found');
                
                const svgText = await response.text();
                const container = document.getElementById('svg-container');
                container.innerHTML = svgText;
                
                svgElement = container.querySelector('svg');
                if (svgElement) {
                    svgElement.addEventListener('click', onSVGClick);
                    console.log('SVG loaded');
                } else {
                    throw new Error('SVG element not found');
                }
            } catch (err) {
                console.error('Failed to load SVG:', err);
                document.getElementById('svg-container').innerHTML = 
                    `<p style="padding:20px; color:#e74c3c; text-align:center;">Error: Could not load floor plan SVG</p>`;
            }
        }

        // Handle SVG click to capture viewBox coordinates
        function onSVGClick(event) {
            if (!svgElement) return;
            
            const machineId = document.getElementById('machine-select').value;
            if (!machineId) {
                alert('Please select a machine first!');
                return;
            }
            
            // Get the SVG element and its transformation matrix
            const ctm = svgElement.getScreenCTM();
            if (!ctm) {
                console.error('Cannot get screen CTM');
                return;
            }
            
            // Get click position on screen
            const screenX = event.clientX;
            const screenY = event.clientY;
            
            // Create SVG point from screen coordinates
            const point = svgElement.createSVGPoint();
            point.x = screenX;
            point.y = screenY;
            
            // Transform to viewBox coordinates
            const transformedPoint = point.matrixTransform(ctm.inverse());
            
            // Round to 1 decimal place
            capturedX = Math.round(transformedPoint.x * 10) / 10;
            capturedY = Math.round(transformedPoint.y * 10) / 10;
            
            // Update form fields
            document.getElementById('coord-x').value = capturedX;
            document.getElementById('coord-y').value = capturedY;
            document.getElementById('click-info').textContent = `X: ${capturedX}, Y: ${capturedY} (ready to save)`;
            
            console.log('Captured coords:', capturedX, capturedY);
        }

        // Load machines dropdown - get ALL machines, not just those with coordinates
        async function loadMachines() {
            try {
                // Load all machines for this factory (including those without coordinates yet)
                const response = await fetch(`/api/machines/all?factory=${encodeURIComponent(FACTORY_NAME)}`);
                
                if (!response.ok) throw new Error('Failed to load machines');
                
                const machines = await response.json();
                const select = document.getElementById('machine-select');
                
                // Sort machines by type and then name
                machines.sort((a, b) => {
                    const typeSort = a.type.localeCompare(b.type);
                    return typeSort !== 0 ? typeSort : (a.name || '').localeCompare(b.name || '');
                });
                
                machines.forEach(machine => {
                    const option = document.createElement('option');
                    option.value = machine.id;
                    const coordMark = machine.has_coordinates ? ' ✓' : '';
                    const typeLabel = machine.type_label || machine.type;
                    option.textContent = `${machine.name} [${typeLabel}]${coordMark}`;
                    select.appendChild(option);
                });
                
                console.log(`Loaded ${machines.length} machines from /api/machines/all`);
            } catch (err) {
                console.error('Load machines error:', err);
                // Try fallback endpoint
                loadMachinesFallback();
            }
        }

        // Fallback function if API fails
        function loadMachinesFallback() {
            console.warn('Using fallback: loading machines from floor-plan API');
            // Fallback to machines with coordinates at least
            fetch(`/api/machines/floor-plan?factory=${encodeURIComponent(FACTORY_NAME)}`)
                .then(r => r.json())
                .then(machines => {
                    const select = document.getElementById('machine-select');
                    machines.forEach(machine => {
                        const option = document.createElement('option');
                        option.value = machine.id;
                        option.textContent = `${machine.name} [${machine.type}] ✓`;
                        select.appendChild(option);
                    });
                    console.log(`Fallback loaded ${machines.length} machines with coordinates`);
                })
                .catch(err => console.error('Fallback also failed:', err));
        }

        // Save coordinates to database
        async function saveMachineCoordinates() {
            const machineId = document.getElementById('machine-select').value;
            const x = document.getElementById('coord-x').value;
            const y = document.getElementById('coord-y').value;
            const messageEl = document.getElementById('save-message');
            
            // Validate
            if (!machineId) {
                messageEl.innerHTML = '<span class="error">Please select a machine</span>';
                return;
            }
            if (!x || !y) {
                messageEl.innerHTML = '<span class="error">Please click on floor plan to capture coordinates</span>';
                return;
            }
            
            try {
                console.log(`Saving coordinates for machine ${machineId}: X=${x}, Y=${y}`);
                
                const response = await fetch(`/api/machines/${machineId}/floor-coordinates`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        floor_cx: parseFloat(x),
                        floor_cy: parseFloat(y),
                        floor_plan: FACTORY_CODE
                    })
                });
                
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server error:', text);
                    throw new Error(`HTTP ${response.status}: ${text}`);
                }
                
                const result = await response.json();
                console.log('Save successful:', result);
                
                const action = result.coordinates ? 'Updated' : 'Saved';
                messageEl.innerHTML = `<span class="success">✓ ${action} ${result.machine_name} at (${result.coordinates.cx}, ${result.coordinates.cy})</span>`;
                
                // Refresh the list after success
                setTimeout(() => {
                    loadSavedMachines();
                    // Don't clear selection - allow them to continue updating or select another machine
                }, 600);
                
            } catch (err) {
                console.error('Save error:', err);
                messageEl.innerHTML = `<span class="error">❌ Error: ${err.message}</span>`;
            }
        }

        // Clear form fields
        function clearSelection() {
            document.getElementById('machine-select').value = '';
            document.getElementById('coord-x').value = '';
            document.getElementById('coord-y').value = '';
            document.getElementById('save-message').innerHTML = '';
            document.getElementById('click-info').textContent = 'Click on floor plan to set coordinates...';
            capturedX = null;
            capturedY = null;
            console.log('Form cleared');
        }

        // Delete coordinates from database
        async function deleteCoordinates() {
            const machineId = document.getElementById('machine-select').value;
            const messageEl = document.getElementById('save-message');
            
            if (!machineId) {
                messageEl.innerHTML = '<span class="error">Please select a machine first</span>';
                return;
            }
            
            // Confirm deletion
            const machineName = document.getElementById('machine-select').options[document.getElementById('machine-select').selectedIndex].text;
            if (!confirm(`Delete coordinates for ${machineName}?`)) {
                return;
            }
            
            try {
                console.log(`Deleting coordinates for machine ${machineId}`);
                
                const response = await fetch(`/api/machines/${machineId}/floor-coordinates`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        floor_cx: null,
                        floor_cy: null,
                        floor_plan: null
                    })
                });
                
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server error:', text);
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Delete successful:', result);
                
                messageEl.innerHTML = `<span class="success">✓ Deleted coordinates for ${result.machine_name}</span>`;
                
                // Refresh the list and clear form after success
                setTimeout(() => {
                    loadSavedMachines();
                    clearSelection();
                }, 600);
                
            } catch (err) {
                console.error('Delete error:', err);
                messageEl.innerHTML = `<span class="error">❌ Error: ${err.message}</span>`;
            }
        }

        // Load and display saved machines
        async function loadSavedMachines() {
            try {
                const response = await fetch(`/api/machines/floor-plan?factory=${encodeURIComponent(FACTORY_NAME)}`);
                const machines = await response.json();
                
                const list = document.getElementById('machines-list');
                const saved = machines.filter(m => m.floor_cx && m.floor_cy);
                
                if (saved.length === 0) {
                    list.innerHTML = '<p style="color:#999; grid-column:1/-1;">No machines with coordinates yet. Click on the floor plan and save coordinates above.</p>';
                    return;
                }
                
                list.innerHTML = saved.map(m => `
                    <div class="machine-card">
                        <strong>${m.name}</strong><br>
                        X: ${parseFloat(m.floor_cx).toFixed(1)}<br>
                        Y: ${parseFloat(m.floor_cy).toFixed(1)}
                    </div>
                `).join('');
                
                console.log(`Displaying ${saved.length} machines with coordinates`);
            } catch (err) {
                console.error('Load saved machines error:', err);
            }
        }
    </script>
</body>
</html>
