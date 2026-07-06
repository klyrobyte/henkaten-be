<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HENKATEN TV — {{ strtoupper($factory) }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;600;700;900&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: transparent;
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 70px 16px 40px 16px;
            /* Space for tv.blade.php header and ticker */
        }

        .main-layout {
            display: flex;
            gap: 16px;
            flex: 1;
            min-height: 0;
        }

        /* ── LEFT: Floor Plan ── */
        .left-panel {
            flex: 0 0 38%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 16px;
        }

        .tv-img-wrap {
            position: relative;
            display: inline-block;
            line-height: 0;
            max-width: 100%;
            max-height: 100%;
        }

        .tv-img-wrap img {
            display: block;
            max-width: 100%;
            max-height: calc(100vh - 150px);
            object-fit: contain;
        }

        .tv-pin {
            position: absolute;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0;
            /* Hide text inside pin */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            border: 2px solid #fff;
        }

        .tv-pin-label {
            position: absolute;
            right: 100%;
            /* Position label to the left of the pin */
            margin-right: 8px;
            background: transparent;
            color: #000;
            font-weight: 900;
            font-size: 14px;
            white-space: nowrap;
            font-family: 'Roboto Condensed', sans-serif;
            text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
        }

        .pin-ok {
            background: #2E7D32;
        }

        .pin-warn {
            background: #F39C12;
        }

        .pin-problem {
            background: #E74C3C;
        }

        .pin-off {
            background: #9E9E9E;
        }

        .fp-empty {
            color: #777;
            text-align: center;
            font-size: 14px;
            line-height: 1.6;
        }


        /* ── RIGHT: Skill Matrix ── */
        .right-panel {
            flex: 1;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 16px;
        }

        .matrix-header {
            background: #185E35;
            /* Dark Green matching mockup */
            color: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        /* Summary Stats matching mockup */
        .summary-stats {
            display: flex;
            justify-content: space-around;
            padding: 10px 0 20px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 16px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-val {
            font-size: 24px;
            font-weight: 900;
            color: #333;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .stat-lbl {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        .matrix-content {
            flex: 1;
            overflow: auto;
        }

        .m-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Roboto', sans-serif;
            font-size: 11px;
        }

        .m-table th {
            background: #185E35;
            color: #fff;
            padding: 8px;
            font-weight: 600;
            text-align: center;
            border: 1px solid #114526;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .m-table thead tr:nth-child(2) th { top: 31px; z-index: 9; }

        .m-table th.col-name, .m-table td.col-name {
            width: 140px; min-width: 140px; max-width: 140px;
            left: 0; position: sticky; text-align: left;
        }

        .m-table th.col-shift, .m-table td.col-shift {
            width: 50px; min-width: 50px; max-width: 50px;
            left: 140px; position: sticky;
        }

        .m-table th.col-name, .m-table th.col-shift {
            z-index: 12 !important; background: #185E35;
        }

        .m-table td.col-name, .m-table td.col-shift {
            z-index: 11; background: #fff; box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1); font-weight: 600;
        }

        .m-table td {
            padding: 6px 8px;
            border: 1px solid #eee;
            text-align: center;
            color: #333;
            background: #fff;
        }

        .m-table tr:nth-child(even) td:not(.col-name):not(.col-shift) {
            background: #f9f9f9;
        }

        /* ILUO Style Circles for Skill */
        .skill-circle {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 1px solid #333;
            position: relative;
            background: #fff;
        }

        .skill-100 {
            background: #333;
        }

        .skill-75 {
            background: linear-gradient(90deg, #333 50%, #fff 50%);
            /* Actually 75% is typically 3 quarters filled, but this is a close approximation in pure CSS */
        }

        .skill-75::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: #333;
            border-radius: 0 0 14px 14px;
        }

        .skill-50 {
            background: linear-gradient(90deg, #333 50%, #fff 50%);
        }

        .skill-25 {
            background: linear-gradient(90deg, #333 50%, #fff 50%);
        }

        .skill-25::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: #fff;
            border-radius: 0 0 14px 14px;
        }

        .skill-0 {
            background: #fff;
            border-color: #ccc;
        }

        .chip-level {
            font-size: 10px;
            font-weight: 700;
            color: #2E7D32;
        }

        .chip-level.training {
            color: #E74C3C;
        }

        /* Hide scrollbars for cleaner TV look */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }
    </style>
</head>

<body>

    <div class="main-layout">

        <!-- LEFT: Floor Plan -->
        <div class="left-panel">
            @if($layout && $layout->image_url)
                <div class="tv-img-wrap">
                    <img src="{{ $layout->image_url }}" alt="Layout">
                    <div id="tvPinsLayer"></div>
                </div>
            @else
                <div class="fp-empty">
                    📷 Layout belum dikonfigurasi.<br>
                    Gunakan <strong>Floor Plan Manager</strong>.
                </div>
            @endif
        </div>

        <!-- RIGHT: Skill Matrix -->
        <div class="right-panel">
            <div class="matrix-header">
                <span>Man Power Skill Map — {{ $factory }}</span>
                <span id="headerClock">Loading...</span>
            </div>

            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-val" id="statTotal">0</div>
                    <div class="stat-lbl">Total operator</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" id="statMulti">0</div>
                    <div class="stat-lbl">Multi-skill ≥75%</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" id="statPengembangan">0</div>
                    <div class="stat-lbl">Pada pengembangan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-val" id="statBaru">0</div>
                    <div class="stat-lbl">Operator baru (<40%)< /div>
                    </div>
                </div>

                <div class="matrix-content" id="matrixBox">
                    <div style="display:flex;height:100%;align-items:center;justify-content:center;color:#777;">⏳ Memuat
                        data skill...</div>
                </div>
            </div>

        </div>

        <script>
            const FACTORY = @json($factory);
            const REFRESH_INT = 30000;

            /* Clock */
            setInterval(() => {
                const d = new Date();
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                document.getElementById('headerClock').textContent =
                    `Bulan ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} | PT. Sugity Creatives`;
            }, 1000);

            /* API Fetching */
            async function fetchData() {
                try {
                    const [resM, resS] = await Promise.all([
                        fetch(`/api/floor-plan-manager/machines?factory=${encodeURIComponent(FACTORY)}`),
                        fetch(`/api/skills?factory=${encodeURIComponent(FACTORY)}&shift=all`)
                    ]);
                    const machines = await resM.json();
                    const skillData = await resS.json();

                    renderPins(machines);
                    renderMatrix(skillData.members || [], skillData.skills || {});
                } catch (e) { console.error('Error fetching data', e); }
            }

            function renderPins(machines) {
                const layer = document.getElementById('tvPinsLayer');
                if (!layer) return;

                layer.innerHTML = machines
                    .filter(m => m.floor_cx !== null && m.floor_cy !== null)
                    .map(m => {
                        const s = (m.status || '').toLowerCase();
                        let cls = 'pin-ok';
                        if (s.includes('stop') || s.includes('rusak') || s.includes('problem')) cls = 'pin-problem';
                        else if (s.includes('slow') || s.includes('masalah') || s.includes('warn')) cls = 'pin-warn';
                        else if (s.includes('off') || s.includes('mati')) cls = 'pin-off';

                        return `<div class="tv-pin ${cls}" style="left:${m.floor_cx}%;top:${m.floor_cy}%;">
                        <div class="tv-pin-label">${m.name}</div>
                    </div>`;
                    }).join('');
            }

            function renderMatrix(members, skills) {
                const box = document.getElementById('matrixBox');

                if (!members.length) {
                    box.innerHTML = '<div style="text-align:center;padding:40px;color:#777;">Tidak ada data member aktif.</div>';
                    return;
                }

                const machineSet = new Set();
                Object.values(skills).forEach(ms => Object.keys(ms).forEach(k => machineSet.add(k)));
                const machineNames = [...machineSet].sort().slice(0, 12); // Fit more columns

                if (!machineNames.length) {
                    box.innerHTML = '<div style="text-align:center;padding:40px;color:#777;">Belum ada data skill diisi.</div>';
                    return;
                }

                // Calculate stats
                let totalOp = members.length;
                let multiSkill = 0;
                let pengembang = 0;
                let opBaru = 0;

                const displayMembers = members.slice(0, 20); // Fit more rows

                let html = `<table class="m-table">
        <thead>
            <tr>
                <th class="col-name" rowspan="2">Nama operator</th>
                <th class="col-shift" rowspan="2">Shift</th>
                <th colspan="${machineNames.length}">Mesin / Proses</th>
            </tr>
            <tr>
                ${machineNames.map(n => `<th>${esc(n)}</th>`).join('')}
            </tr>
        </thead>
        <tbody>`;

                displayMembers.forEach(m => {
                    let avgScore = 0;
                    let count = 0;

                    let rowHtml = `<tr>
            <td class="col-name">${esc(m.nama)}</td>
            <td class="col-shift" style="color:#2E7D32;font-weight:700;">${m.shift}</td>`;

                    machineNames.forEach(mn => {
                        const pct = skills[m.id]?.[mn]?.skill_pct ?? null;
                        let circleClass = 'skill-0';

                        if (pct !== null) {
                            avgScore += pct;
                            count++;
                            if (pct >= 100) circleClass = 'skill-100';
                            else if (pct >= 75) circleClass = 'skill-75';
                            else if (pct >= 50) circleClass = 'skill-50';
                            else if (pct > 0) circleClass = 'skill-25';
                        }
                        rowHtml += `<td><div class="skill-circle ${circleClass}"></div></td>`;
                    });

                    let finalAvg = count > 0 ? Math.round(avgScore / count) : 0;
                    if (finalAvg >= 75) { multiSkill++; }
                    else if (finalAvg >= 40) { pengembang++; }
                    else { opBaru++; }

                    rowHtml += `</tr>`;
                    html += rowHtml;
                });

                html += `</tbody></table>`;
                box.innerHTML = html;

                // Update summary stats
                document.getElementById('statTotal').textContent = totalOp;
                document.getElementById('statMulti').textContent = multiSkill;
                document.getElementById('statPengembangan').textContent = pengembang;
                document.getElementById('statBaru').textContent = opBaru;
            }

            function esc(s) { return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

            /* Required function for master slide integration */
            window.gotoSlide = function (n) {
                // We no longer have internal slides, we are just one big view
                // so we just ignore this or use it to refresh data
                if (n === 1) fetchData();
            };

            fetchData();
            setInterval(fetchData, REFRESH_INT);
        </script>
</body>

</html>