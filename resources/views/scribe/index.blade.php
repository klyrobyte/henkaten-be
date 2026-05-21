<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Henkaten API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://192.168.28.152:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.10.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.10.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-absence" class="tocify-header">
                <li class="tocify-item level-1" data-unique="absence">
                    <a href="#absence">Absence</a>
                </li>
                                    <ul id="tocify-subheader-absence" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="absence-POSTapi-absence-save">
                                <a href="#absence-POSTapi-absence-save">POST api/absence/save</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="absence-GETapi-absence-data">
                                <a href="#absence-GETapi-absence-data">GET api/absence/data</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="absence-GETapi-absence-candidates">
                                <a href="#absence-GETapi-absence-candidates">GET api/absence/candidates</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="absence-GETapi-absence-report">
                                <a href="#absence-GETapi-absence-report">GET api/absence/report</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="absence-POSTapi-absence-rebuild-summary">
                                <a href="#absence-POSTapi-absence-rebuild-summary">POST api/absence/rebuild-summary</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="absence-GETapi-absence-export">
                                <a href="#absence-GETapi-absence-export">GET api/absence/export</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="absence-GETapi-absence-export-excel">
                                <a href="#absence-GETapi-absence-export-excel">GET api/absence/export-excel</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-assignment" class="tocify-header">
                <li class="tocify-item level-1" data-unique="assignment">
                    <a href="#assignment">Assignment</a>
                </li>
                                    <ul id="tocify-subheader-assignment" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="assignment-POSTapi-assignment-save">
                                <a href="#assignment-POSTapi-assignment-save">POST api/assignment/save</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="assignment-GETapi-assignment-data">
                                <a href="#assignment-GETapi-assignment-data">GET api/assignment/data</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="assignment-GETapi-assignment-candidates">
                                <a href="#assignment-GETapi-assignment-candidates">GET api/assignment/candidates</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="assignment-POSTapi-assignment-sync-absen">
                                <a href="#assignment-POSTapi-assignment-sync-absen">POST api/assignment/sync-absen</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-attendance" class="tocify-header">
                <li class="tocify-item level-1" data-unique="attendance">
                    <a href="#attendance">Attendance</a>
                </li>
                                    <ul id="tocify-subheader-attendance" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="attendance-GETapi-attendance-data">
                                <a href="#attendance-GETapi-attendance-data">Ambil data absen + data chart untuk AJAX auto-refresh
GET /admin/attendance/data?tanggal=&factory=&shift=</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-dashboard" class="tocify-header">
                <li class="tocify-item level-1" data-unique="dashboard">
                    <a href="#dashboard">Dashboard</a>
                </li>
                                    <ul id="tocify-subheader-dashboard" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="dashboard-GETapi-status">
                                <a href="#dashboard-GETapi-status">GET api/status</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="dashboard-POSTapi-context">
                                <a href="#dashboard-POSTapi-context">POST api/context</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-machines-all">
                                <a href="#endpoints-GETapi-machines-all">Get ALL machines (for dropdown/selection, not floor plan display)
GET /api/machines/all</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-machines-floor-plan">
                                <a href="#endpoints-GETapi-machines-floor-plan">Get machines with floor plan coordinates and current status
GET /api/machines/floor-plan</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-machines--id--floor-plan">
                                <a href="#endpoints-GETapi-machines--id--floor-plan">Get a single machine's floor plan data
GET /api/machines/{id}/floor-plan</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-factory" class="tocify-header">
                <li class="tocify-item level-1" data-unique="factory">
                    <a href="#factory">Factory</a>
                </li>
                                    <ul id="tocify-subheader-factory" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="factory-GETapi-factories">
                                <a href="#factory-GETapi-factories">GET /admin/api/factories  - list for dropdowns</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="factory-POSTapi-factories">
                                <a href="#factory-POSTapi-factories">POST /admin/api/factories</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="factory-PUTapi-factories--factory_id-">
                                <a href="#factory-PUTapi-factories--factory_id-">PUT /admin/api/factories/{factory}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="factory-DELETEapi-factories--factory_id-">
                                <a href="#factory-DELETEapi-factories--factory_id-">DELETE /admin/api/factories/{factory}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="factory-PATCHapi-factories-reorder">
                                <a href="#factory-PATCHapi-factories-reorder">PATCH /admin/api/factories/reorder  - bulk update order_index</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-log" class="tocify-header">
                <li class="tocify-item level-1" data-unique="log">
                    <a href="#log">Log</a>
                </li>
                                    <ul id="tocify-subheader-log" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="log-GETapi-logs-list">
                                <a href="#log-GETapi-logs-list">GET api/logs/list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="log-GETapi-logs-combined">
                                <a href="#log-GETapi-logs-combined">Combined problem list for TV panel:
3M ProblemLog rows PLUS Man (absen) rows.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="log-POSTapi-logs">
                                <a href="#log-POSTapi-logs">POST api/logs</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="log-PATCHapi-logs--log_id--close">
                                <a href="#log-PATCHapi-logs--log_id--close">PATCH api/logs/{log_id}/close</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="log-PATCHapi-logs--log_id--reopen">
                                <a href="#log-PATCHapi-logs--log_id--reopen">PATCH api/logs/{log_id}/reopen</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="log-PATCHapi-logs--log_id-">
                                <a href="#log-PATCHapi-logs--log_id-">PATCH api/logs/{log_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="log-DELETEapi-logs--log_id-">
                                <a href="#log-DELETEapi-logs--log_id-">DELETE api/logs/{log_id}</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-machine" class="tocify-header">
                <li class="tocify-item level-1" data-unique="machine">
                    <a href="#machine">Machine</a>
                </li>
                                    <ul id="tocify-subheader-machine" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="machine-POSTapi-machines-photo">
                                <a href="#machine-POSTapi-machines-photo">Upload foto mesin
POST /admin/machines/photo</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="machine-POSTapi-machines-status">
                                <a href="#machine-POSTapi-machines-status">Update status mesin
POST /admin/machines/status</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="machine-GETapi-machines-lights">
                                <a href="#machine-GETapi-machines-lights">4M lights data  - otomatis berdasarkan:
  1. MachineStatus manual (override)
  2. ProblemLog open
  3. AbsenceRecord absen tanpa AssignmentReplacement → auto "man"</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="machine-GETapi-machines-statuses">
                                <a href="#machine-GETapi-machines-statuses">Ambil semua status mesin</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="machine-PATCHapi-machines--machine_id--floor-coordinates">
                                <a href="#machine-PATCHapi-machines--machine_id--floor-coordinates">Update floor plan coordinates for a machine
PATCH /admin/machines/{id}/floor-coordinates</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-member" class="tocify-header">
                <li class="tocify-item level-1" data-unique="member">
                    <a href="#member">Member</a>
                </li>
                                    <ul id="tocify-subheader-member" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="member-GETapi-members-list">
                                <a href="#member-GETapi-members-list">GET api/members/list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-GETapi-members--member_id-">
                                <a href="#member-GETapi-members--member_id-">GET api/members/{member_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-POSTapi-members">
                                <a href="#member-POSTapi-members">POST api/members</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-PUTapi-members--member_id-">
                                <a href="#member-PUTapi-members--member_id-">PUT api/members/{member_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-DELETEapi-members-clear-all">
                                <a href="#member-DELETEapi-members-clear-all">DELETE api/members/clear-all</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-DELETEapi-members--member_id-">
                                <a href="#member-DELETEapi-members--member_id-">DELETE api/members/{member_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-POSTapi-members-import">
                                <a href="#member-POSTapi-members-import">POST api/members/import</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-GETapi-members-export">
                                <a href="#member-GETapi-members-export">GET api/members/export</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="member-GETapi-members-template">
                                <a href="#member-GETapi-members-template">GET api/members/template</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-replacement" class="tocify-header">
                <li class="tocify-item level-1" data-unique="replacement">
                    <a href="#replacement">Replacement</a>
                </li>
                                    <ul id="tocify-subheader-replacement" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="replacement-GETapi-replacements">
                                <a href="#replacement-GETapi-replacements">Ambil pengganti aktif (hanya yang member aslinya masih absen)
GET /admin/replacements?tanggal=&factory=&shift=</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="replacement-POSTapi-replacements">
                                <a href="#replacement-POSTapi-replacements">Simpan pengganti yang dipilih
POST /admin/replacements
develop by rizky</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="replacement-DELETEapi-replacements--replacement_id-">
                                <a href="#replacement-DELETEapi-replacements--replacement_id-">Hapus pengganti manual (tombol batalkan)
DELETE /admin/replacements/{id}</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-section" class="tocify-header">
                <li class="tocify-item level-1" data-unique="section">
                    <a href="#section">Section</a>
                </li>
                                    <ul id="tocify-subheader-section" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="section-GETapi-sections">
                                <a href="#section-GETapi-sections">GET /admin/api/sections?factory_id=X  - list for dropdowns</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="section-POSTapi-sections">
                                <a href="#section-POSTapi-sections">POST /admin/api/sections</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="section-PUTapi-sections--section_id-">
                                <a href="#section-PUTapi-sections--section_id-">PUT /admin/api/sections/{section}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="section-DELETEapi-sections--section_id-">
                                <a href="#section-DELETEapi-sections--section_id-">DELETE /admin/api/sections/{section}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="section-PATCHapi-sections-reorder">
                                <a href="#section-PATCHapi-sections-reorder">PATCH /admin/api/sections/reorder  - bulk update order_index</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-status" class="tocify-header">
                <li class="tocify-item level-1" data-unique="status">
                    <a href="#status">Status</a>
                </li>
                                    <ul id="tocify-subheader-status" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="status-GETapi-statuses">
                                <a href="#status-GETapi-statuses">GET /admin/api/statuses</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="status-POSTapi-statuses">
                                <a href="#status-POSTapi-statuses">POST /admin/api/statuses</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="status-PUTapi-statuses--status_id-">
                                <a href="#status-PUTapi-statuses--status_id-">PUT /admin/api/statuses/{status}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="status-DELETEapi-statuses--status_id-">
                                <a href="#status-DELETEapi-statuses--status_id-">DELETE /admin/api/statuses/{status}</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-user" class="tocify-header">
                <li class="tocify-item level-1" data-unique="user">
                    <a href="#user">User</a>
                </li>
                                    <ul id="tocify-subheader-user" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="user-GETapi-users">
                                <a href="#user-GETapi-users">GET api/users</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-GETapi-users--user_id-">
                                <a href="#user-GETapi-users--user_id-">GET api/users/{user_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-POSTapi-users">
                                <a href="#user-POSTapi-users">POST api/users</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-PUTapi-users--user_id-">
                                <a href="#user-PUTapi-users--user_id-">PUT api/users/{user_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="user-DELETEapi-users--user_id-">
                                <a href="#user-DELETEapi-users--user_id-">DELETE api/users/{user_id}</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: May 20, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>## Henkaten Board Secure API

This API is protected via dual-layered HMAC and Session Nonce security.
- **Browser Clients**: Handled automatically via session nonce.
- **Service Clients**: Include the `X-App-Secret` header containing `hash_hmac('sha256', 'henkaten-api', APP_API_SECRET)`.</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include a <strong><code>X-App-Secret</code></strong> header with the value <strong><code>"{HMAC_SHA256_TOKEN}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>For service clients, provide the SHA-256 HMAC of "henkaten-api" using your APP_API_SECRET.</p>

        <h1 id="absence">Absence</h1>

    <p>APIs for managing Absence.</p>

                                <h2 id="absence-POSTapi-absence-save">POST api/absence/save</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-absence-save">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/absence/save" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"tanggal\": \"2026-05-20T05:58:41\",
    \"factory\": \"architecto\",
    \"shift\": \"B\",
    \"records\": []
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/save"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tanggal": "2026-05-20T05:58:41",
    "factory": "architecto",
    "shift": "B",
    "records": []
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-absence-save">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Imk5NnErSmJoQ2lNaTRuZVJEb3BpMUE9PSIsInZhbHVlIjoiYXdZaUlad3dSMzM5TGRiVVo5ZzVEdG5jQUZGNDUxZUczNDhjdG5SWkRuREU1bUZ2Z252TUdEVXN4UWNTSzc3RENEU0FEc2V5eEJBYlpiRnNCNGI1R3ViUlVlRmRkekszVmptcENsN0tpQVFXS1grb3QvMU5FLy91RTBRRUFhRVMiLCJtYWMiOiI4OWJjYjY5NzhmNmNiZGMzMTU2ZmQ3NDI4YjRkOTNkMmU1N2U2ZDZkNjU2NTk5OGZkNzA1ZjU3MjRlNTlhMWM4IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-absence-save" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-absence-save"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-absence-save"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-absence-save" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-absence-save">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-absence-save" data-method="POST"
      data-path="api/absence/save"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-absence-save', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-absence-save"
                    onclick="tryItOut('POSTapi-absence-save');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-absence-save"
                    onclick="cancelTryOut('POSTapi-absence-save');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-absence-save"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/absence/save</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-absence-save"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-absence-save"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-absence-save"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tanggal</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tanggal"                data-endpoint="POSTapi-absence-save"
               value="2026-05-20T05:58:41"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-05-20T05:58:41</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-absence-save"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-absence-save"
               value="B"
               data-component="body">
    <br>
<p>Example: <code>B</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>records</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="records"                data-endpoint="POSTapi-absence-save"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="absence-GETapi-absence-data">GET api/absence/data</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-absence-data">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/absence/data" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/data"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-absence-data">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkRma2d3M2pWQlJVZEI5L003bTlOelE9PSIsInZhbHVlIjoiOVlhTmt3dTc2TGFTZWhaa2QxZ0pLcVg2cnhQTXdmN3RNUmlYaEd5WUozbktzV3ZCUVFwZlhpZ2huTE50ak1FR1hDVXZFUzFYSnpkbTcxS2tySzFBKzBMRmZobFdGS2xSSmkwVkRMb21SM3ZVekhzV3VGQjE1bUc0VzFPVkFLa08iLCJtYWMiOiI5ZGM3YjY4YjdlN2EzOTE5Yzk2MzczZmY3MDAzNjY2ZmY4NjA0MzQ3ZmUxMTE3ZTFmNDE3YzBkNGEzYmQzNTgyIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-absence-data" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-absence-data"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-absence-data"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-absence-data" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-absence-data">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-absence-data" data-method="GET"
      data-path="api/absence/data"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-absence-data', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-absence-data"
                    onclick="tryItOut('GETapi-absence-data');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-absence-data"
                    onclick="cancelTryOut('GETapi-absence-data');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-absence-data"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/absence/data</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-absence-data"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-absence-data"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-absence-data"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="absence-GETapi-absence-candidates">GET api/absence/candidates</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-absence-candidates">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/absence/candidates" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/candidates"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-absence-candidates">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ikp3TE5iT0FCUGxHNlRLbFBTWkQ2eFE9PSIsInZhbHVlIjoiS0I4OVQ2ZmxKZTgwYU5FYWpFQUJnV1h0WXVib1pINlpCQzI1TkhmREk0dWRobjA1ZHRDR1ZDUE1nbHc0bWlvUlhMMlU4dzZkcnoyWXI2MDg2N0l2cVJSVlFDUWlMMzdmYVVmYzBHRVpia3dBTXg4QTZ4Y3ErR0FwTTNKTGdXWlgiLCJtYWMiOiI2NDQwNWEyN2Q4NzQxZDFkNTg4ZjA4NThjNDI0N2E5ZDNjMDMwMjU3YzQ2NjY1ZTYzZTNjNDMxNGQ3ZWQ3MzlmIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-absence-candidates" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-absence-candidates"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-absence-candidates"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-absence-candidates" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-absence-candidates">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-absence-candidates" data-method="GET"
      data-path="api/absence/candidates"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-absence-candidates', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-absence-candidates"
                    onclick="tryItOut('GETapi-absence-candidates');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-absence-candidates"
                    onclick="cancelTryOut('GETapi-absence-candidates');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-absence-candidates"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/absence/candidates</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-absence-candidates"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-absence-candidates"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-absence-candidates"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="absence-GETapi-absence-report">GET api/absence/report</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-absence-report">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/absence/report" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/report"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-absence-report">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ilp3bDRORlU3bXdZZ0JPR25zeWk0VXc9PSIsInZhbHVlIjoiMzh6N2ZvRWVrbUk4dmZkTlFqMVYzNE01KzJWSGVEUmxZT1A1dCt3TXhXZ0NWeDVCdWYwNGpMYUdDT1B4eGtrNFBmdHhkUGJ0bWplbDZhLzFsdEs3SmU1Tm1JdEVteEdaRTkySytYYTlUWTZpMURGNnVPZFhOT2xSVmRQSmJlRFMiLCJtYWMiOiI1MGM1MjUzOWFmMjIzYzJkYjFlYWRmOTYxNTk2MjIzYWNmYTJlZTZkYWI3YmViNDYyOWQ4MmE1ZDY5ZDI2NWQ1IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-absence-report" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-absence-report"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-absence-report"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-absence-report" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-absence-report">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-absence-report" data-method="GET"
      data-path="api/absence/report"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-absence-report', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-absence-report"
                    onclick="tryItOut('GETapi-absence-report');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-absence-report"
                    onclick="cancelTryOut('GETapi-absence-report');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-absence-report"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/absence/report</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-absence-report"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-absence-report"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-absence-report"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="absence-POSTapi-absence-rebuild-summary">POST api/absence/rebuild-summary</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-absence-rebuild-summary">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/absence/rebuild-summary" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"tanggal\": \"2026-05-20T05:58:41\",
    \"factory\": \"architecto\",
    \"shift\": \"A\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/rebuild-summary"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tanggal": "2026-05-20T05:58:41",
    "factory": "architecto",
    "shift": "A"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-absence-rebuild-summary">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlNjN2hGbGduQnptaHFaUWEwR3V6c3c9PSIsInZhbHVlIjoiT3h2QXZ5dG9MVnlvdVZnN1F3czdtaklWNXlwekJ1VzUzVVFyMnlRNUxRSjY3d2NSOW0yN2Q0TEFtTmsvSlIwTHQ1WUZzWmhnNms5SE5LdzNraUVaM2M5NUJkYnFKaWd4d2FiTUE3YXA1d3JoTGp0eTRUYjZKTC9ZTk5jbnF5M3ciLCJtYWMiOiI1MDhkMjFlMTYyZmJjMWVjMWZlY2VjMGEwZmMwNTQwOTBmM2U1ZTViMWFlYjY1MGY0NTA1NjRjMmZjZTkwMzAyIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-absence-rebuild-summary" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-absence-rebuild-summary"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-absence-rebuild-summary"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-absence-rebuild-summary" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-absence-rebuild-summary">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-absence-rebuild-summary" data-method="POST"
      data-path="api/absence/rebuild-summary"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-absence-rebuild-summary', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-absence-rebuild-summary"
                    onclick="tryItOut('POSTapi-absence-rebuild-summary');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-absence-rebuild-summary"
                    onclick="cancelTryOut('POSTapi-absence-rebuild-summary');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-absence-rebuild-summary"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/absence/rebuild-summary</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-absence-rebuild-summary"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-absence-rebuild-summary"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-absence-rebuild-summary"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tanggal</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tanggal"                data-endpoint="POSTapi-absence-rebuild-summary"
               value="2026-05-20T05:58:41"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-05-20T05:58:41</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-absence-rebuild-summary"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-absence-rebuild-summary"
               value="A"
               data-component="body">
    <br>
<p>Example: <code>A</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
        </div>
        </form>

                    <h2 id="absence-GETapi-absence-export">GET api/absence/export</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-absence-export">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/absence/export" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/export"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-absence-export">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkNvMDcrQU80aEhtUEtXVm4rOUI0cGc9PSIsInZhbHVlIjoidWwxS3ZIVjgrd2VsNW1ZOTdoMDUxK0QwN2EvVGRFckJXWWZhVzhOME0vUitzb2p0TWNMVlYzQTZCQ2hCU0t2dG9wektKekhJekRWemRzWCt1SW84SVhHRUNWVjJFcGlsWEMxRGJrcTZCZng3UE5WSERVK2xobkVpZjg4L0g0N1IiLCJtYWMiOiJiZmIwNjEwNGFkZTQyMTBmMmVhMjZlYmQ2NTM3Y2U2MTAwOGJmMWJhYjE0YThlNDYzZjhjMWE5YTg5ZGMyYWQ1IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-absence-export" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-absence-export"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-absence-export"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-absence-export" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-absence-export">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-absence-export" data-method="GET"
      data-path="api/absence/export"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-absence-export', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-absence-export"
                    onclick="tryItOut('GETapi-absence-export');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-absence-export"
                    onclick="cancelTryOut('GETapi-absence-export');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-absence-export"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/absence/export</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-absence-export"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-absence-export"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-absence-export"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="absence-GETapi-absence-export-excel">GET api/absence/export-excel</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-absence-export-excel">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/absence/export-excel" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/absence/export-excel"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-absence-export-excel">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkFWUm90M1R0NCtKSnFiWkNkZE1acmc9PSIsInZhbHVlIjoiRWRsWnY2c1p5cFhDSTdwcEJNQzNycUluM2RtSFF3MFFvS3MwVEhqeHpOT1BqblplTURKUHNkKzlVS1BZZUt6cjBMKzV1M042YzBhdGkyR2laTWZsVUlQVjRHZ0ZJZjcxVWxuVXNvck1SbzhoSFAxRzVrSXY2QTNXZFA1RWFIeHciLCJtYWMiOiIyYjVhZmNkMjZhNjM4NDMzNzM2NWY0YzA3ZWUxMTJmODI1YTNmZGRjMWZiZDgzNjRhMzg0NzJkMTVkM2Q5Y2U5IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-absence-export-excel" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-absence-export-excel"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-absence-export-excel"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-absence-export-excel" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-absence-export-excel">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-absence-export-excel" data-method="GET"
      data-path="api/absence/export-excel"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-absence-export-excel', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-absence-export-excel"
                    onclick="tryItOut('GETapi-absence-export-excel');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-absence-export-excel"
                    onclick="cancelTryOut('GETapi-absence-export-excel');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-absence-export-excel"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/absence/export-excel</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-absence-export-excel"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-absence-export-excel"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-absence-export-excel"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="assignment">Assignment</h1>

    <p>APIs for managing Assignment.</p>

                                <h2 id="assignment-POSTapi-assignment-save">POST api/assignment/save</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-assignment-save">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/assignment/save" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"tanggal\": \"2026-05-20T05:58:42\",
    \"factory\": \"architecto\",
    \"shift\": \"architecto\",
    \"assignments\": []
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/assignment/save"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tanggal": "2026-05-20T05:58:42",
    "factory": "architecto",
    "shift": "architecto",
    "assignments": []
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-assignment-save">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6ImdJcEdtVXF3b01iR21KdzVheUJjbEE9PSIsInZhbHVlIjoiYzZaKzE5UWh2Q0J1bEhLQ1NMbXdQTG82YlBxbEhHZjE2Umg5YnZub3NWNHJBZTV2Y0FLZWtycXlOQVRONkRzaFlzaGVIZ3FMaTFvMkVoTnN2TkRuQnFIZUE3MTM2WmF0UDVkb2NNNml5YmlzU2pPVWxJNS9BWWJkSktCZlNQeTciLCJtYWMiOiJkMTEyNGNhYTIxMjE3MmI0MDcwMGJiYmUzZDdhZjA5ODQzNjViZGYwMjI1MGI0YmI2OGRmODYzNjJlNTdhOWZkIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-assignment-save" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-assignment-save"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-assignment-save"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-assignment-save" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-assignment-save">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-assignment-save" data-method="POST"
      data-path="api/assignment/save"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-assignment-save', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-assignment-save"
                    onclick="tryItOut('POSTapi-assignment-save');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-assignment-save"
                    onclick="cancelTryOut('POSTapi-assignment-save');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-assignment-save"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/assignment/save</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-assignment-save"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-assignment-save"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-assignment-save"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tanggal</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tanggal"                data-endpoint="POSTapi-assignment-save"
               value="2026-05-20T05:58:42"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-05-20T05:58:42</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-assignment-save"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-assignment-save"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>assignments</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="assignments"                data-endpoint="POSTapi-assignment-save"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="assignment-GETapi-assignment-data">GET api/assignment/data</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-assignment-data">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/assignment/data" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/assignment/data"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-assignment-data">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InI1UXJHZ2dadlVQektrc0luVmtlZVE9PSIsInZhbHVlIjoiRTBIL3E5bXE2Z0tnTG51d0Vhcys3UHVySUVtV0RCNkxhUDNiRXpFUDIxZlpxTHI2YWhtTG4wMEJDQWV1TTB4bnBjamZlNG9qT1ZVOHQ4OUp3N1A3L2V1L2F6eEN3cTY0SHNjb1NucjVlRm5IT0QyK09QMDNGWUFHOE9kRFY0VisiLCJtYWMiOiJmODkwZGI1MzE2MTk4NTMxMGY4NGVmZTAxMjFiMWVhMWRlOTdkMTMwZTg2YTMxNjdmZDZkZDBmZTA2MjBkMDVhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-assignment-data" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-assignment-data"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-assignment-data"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-assignment-data" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-assignment-data">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-assignment-data" data-method="GET"
      data-path="api/assignment/data"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-assignment-data', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-assignment-data"
                    onclick="tryItOut('GETapi-assignment-data');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-assignment-data"
                    onclick="cancelTryOut('GETapi-assignment-data');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-assignment-data"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/assignment/data</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-assignment-data"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-assignment-data"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-assignment-data"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="assignment-GETapi-assignment-candidates">GET api/assignment/candidates</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-assignment-candidates">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/assignment/candidates" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/assignment/candidates"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-assignment-candidates">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IjFJeXFLVGY5QkhuRG1taTRrTnRqVFE9PSIsInZhbHVlIjoiWHlIaXY1SWgxSVNFUHZVVmdsNmxUaUF0bTFSdGZCUEtETGdhYnJDNzcveHhieHcwSHJxcldYWUtCQ2UvMnRNajRWaDhkVkJjYzg2OEYvNHhsLzAzdFVkQWpDRTFoVlh5WUg3QVpUbkM5a1RucXcyU092ZUhReXllYWtXcTUydEYiLCJtYWMiOiJmODEzZWIxNDk1ZTQzOWNiMWNmYWYxYzU5YThlZjIzMmM2MmQxN2NhZTc1ZGRjNmY5M2RkZjEwNDNjNzBhZTdhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-assignment-candidates" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-assignment-candidates"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-assignment-candidates"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-assignment-candidates" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-assignment-candidates">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-assignment-candidates" data-method="GET"
      data-path="api/assignment/candidates"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-assignment-candidates', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-assignment-candidates"
                    onclick="tryItOut('GETapi-assignment-candidates');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-assignment-candidates"
                    onclick="cancelTryOut('GETapi-assignment-candidates');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-assignment-candidates"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/assignment/candidates</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-assignment-candidates"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-assignment-candidates"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-assignment-candidates"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="assignment-POSTapi-assignment-sync-absen">POST api/assignment/sync-absen</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-assignment-sync-absen">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/assignment/sync-absen" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/assignment/sync-absen"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-assignment-sync-absen">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6ImpVbUJtVi9BQkF4OXVHMzYwRTZ0blE9PSIsInZhbHVlIjoiZ3VkZFl1WjdsSG1lbHVQY3BzWHdZaTUvUkRGRm00TlpGTDdKbjFPaGdXMG5aSHhPUEFGNkd6SnFSM00rRnhMaGJTUXFsKzRYNVdMZXB5OW85YlFRa0Vzbktxeko1MG5Sc0JDVjI2Z0VlOUxUS0lsdkFndlliR3E1VUc3UGZJakciLCJtYWMiOiIyZDIwZjA3NzdjNTBmZTAxNjRkODFjNDY0NTMyOTFjNDU0NWI3NTQ2OTQzYjliZjE0NTdmODdhMmE5OTk4ZmYwIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-assignment-sync-absen" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-assignment-sync-absen"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-assignment-sync-absen"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-assignment-sync-absen" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-assignment-sync-absen">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-assignment-sync-absen" data-method="POST"
      data-path="api/assignment/sync-absen"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-assignment-sync-absen', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-assignment-sync-absen"
                    onclick="tryItOut('POSTapi-assignment-sync-absen');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-assignment-sync-absen"
                    onclick="cancelTryOut('POSTapi-assignment-sync-absen');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-assignment-sync-absen"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/assignment/sync-absen</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-assignment-sync-absen"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-assignment-sync-absen"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-assignment-sync-absen"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="attendance">Attendance</h1>

    <p>APIs for managing Attendance.</p>

                                <h2 id="attendance-GETapi-attendance-data">Ambil data absen + data chart untuk AJAX auto-refresh
GET /admin/attendance/data?tanggal=&amp;factory=&amp;shift=</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-attendance-data">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/attendance/data" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/attendance/data"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-attendance-data">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlY0cUY1cXJyKytNa01wb3VlcURXNkE9PSIsInZhbHVlIjoiK09qaGw2R3psOGY4Ym80YzRFMFFTN3FaMTZsbEdLUlN5dlVlOXRuY080d0NkR1hkNTRhdUplMEZROEl4V3BOT1lJU2tDUkhiZUlvcUorKzc2Q3BoM0NkRU5ibFVJaGo4eXlia2Z3U0VrSUUxQWZxWHNPRVNFaUhZYkZNVjhSbXMiLCJtYWMiOiI1YmNhZDg1MzAyMDAxYmYzN2I4OTlkZmQ0NTBmMDg1MjZjNTc4OGE4MDllOGIzMGYyMjQxOTI1M2I4OTlmZWMwIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-attendance-data" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-attendance-data"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-attendance-data"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-attendance-data" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-attendance-data">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-attendance-data" data-method="GET"
      data-path="api/attendance/data"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-attendance-data', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-attendance-data"
                    onclick="tryItOut('GETapi-attendance-data');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-attendance-data"
                    onclick="cancelTryOut('GETapi-attendance-data');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-attendance-data"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/attendance/data</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-attendance-data"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-attendance-data"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-attendance-data"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="dashboard">Dashboard</h1>

    <p>APIs for managing Dashboard.</p>

                                <h2 id="dashboard-GETapi-status">GET api/status</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-status">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/status" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/status"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-status">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InhYWHNBYk5ieTB2NkxXVDltWk92Nnc9PSIsInZhbHVlIjoiMFhSOUZXV1BQU1ZTTll1N0pZOUpoMlFpSDE1OS9TUTd2MU5Oc1RWVHpoRlo4cmdEcXdHWWMrZTJoNkxNMzE5b2JoQUkyMHZDbGxnRnREY0FjdnNWMUpTNmt3N0NtVE0yTXlmN0tzV25JM2VyYWFRVW9qN1BJRDMxWk1SSXlSaWoiLCJtYWMiOiIzNzQ1ZTkzNmU3YWQyYjM3NGNlYzE3ZTM4ZDRkOTFiMDRlNjc4ZTczMzk3Yzk5ZTkxY2I5NmZhNzQzYmE2ZmQ1IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:37 GMT; Max-Age=7199; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-status" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-status"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-status"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-status" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-status">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-status" data-method="GET"
      data-path="api/status"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-status', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-status"
                    onclick="tryItOut('GETapi-status');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-status"
                    onclick="cancelTryOut('GETapi-status');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-status"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/status</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-status"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-status"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-status"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="dashboard-POSTapi-context">POST api/context</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-context">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/context" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"factory\": \"architecto\",
    \"shift\": \"A\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/context"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "factory": "architecto",
    "shift": "A"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-context">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlBuT003L1FZZk9nakZTN3JaekJjaWc9PSIsInZhbHVlIjoiNjdpSkV3L2syY09IQzZsMUJiRFljelIrZWcvTW1TWXZXTENmODd5UGd6TStNMzFnUFk4ZmtJSVFMZm5Ob0tCSDlUekVjdEJESlBpaHJMdUU2aURFbklxbkZDR05XcHhqRlExcTdDOThnSmIvU20veFhzQmFRak5mcU00enBXYVIiLCJtYWMiOiI4ZWE2ZTQyNGRlY2QxZjUyMzZiOTI1OTNjOTkxODgwZjJjNzgzOWMxODczNTNjMjM3NDBlNTliZjYyZGVmZWU0IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:38 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-context" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-context"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-context"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-context" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-context">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-context" data-method="POST"
      data-path="api/context"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-context', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-context"
                    onclick="tryItOut('POSTapi-context');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-context"
                    onclick="cancelTryOut('POSTapi-context');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-context"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/context</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-context"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-context"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-context"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-context"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-context"
               value="A"
               data-component="body">
    <br>
<p>Example: <code>A</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
        </div>
        </form>

                <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-machines-all">Get ALL machines (for dropdown/selection, not floor plan display)
GET /api/machines/all</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns all machines regardless of coordinates
Used for: floor plan editor dropdown, machine selection, etc</p>

<span id="example-requests-GETapi-machines-all">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/machines/all" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/all"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-machines-all">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: XSRF-TOKEN=eyJpdiI6IjZ4UTBndkhaSG55OTNubWpmM2hmd1E9PSIsInZhbHVlIjoiUVM4Zk1pZUFCOGprOHNRZGZqcWR1Z05RT2RVY0ZJY1NkcnlyWUd1S0pQOGwvbkpzMHZpT255ZVVtaHkyZ0pyT2pibW9aeWhXZGFJMUpacmg3ZWhIVGNuN2FLNWRGRzhTN0d5ODlzRFhzZmxzNnlqRkNacmZ6UUo1SXRGVmpkRzEiLCJtYWMiOiIyZWZiNjZjNzc4ZjNiMzc1MDYyOWUwY2I5MmQ5YzE4ZTA0ZTQxZTZjN2IxYTlhODBjOWI2MjdlNDk1MjQ4ZmE1IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; samesite=lax; henkaten-session=eyJpdiI6InZ1SGdEcEI4L3ZZMnBGeTA3QWJnN3c9PSIsInZhbHVlIjoiQUNwVUFnME83MFIvYnYrMXNFYmtpaEkvVnhhZ3p3d2IvUGpFVzJoNGFRK3c5TFF3WmdKQ1B0L051cURLR2JYdzdVS3ZybXIyREgvblE0aWtjR3JlRGM3Wm9iN1pUVTRES2dKdkRYL0Z1WW5KaEErbVZjSlpCL0Vmb05la3dZQjIiLCJtYWMiOiJmZjEwNjgxNWQ5ZWY1Y2NmMzVlMmQyZTU0N2FjMzhkZTc5NDg3NzcwNmE1YzM5NmU2N2E2ZWY4MmY4MmRkYTNhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-machines-all" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-machines-all"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-machines-all"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-machines-all" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-machines-all">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-machines-all" data-method="GET"
      data-path="api/machines/all"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-machines-all', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-machines-all"
                    onclick="tryItOut('GETapi-machines-all');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-machines-all"
                    onclick="cancelTryOut('GETapi-machines-all');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-machines-all"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/machines/all</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-machines-all"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-machines-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-machines-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-machines-floor-plan">Get machines with floor plan coordinates and current status
GET /api/machines/floor-plan</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Uses the same logic as dashboard:</p>
<ul>
<li>Status based on OPEN problem logs (Machine, Material, Method)</li>
<li>Not based on MachineStatus table (which is for shift tracking)</li>
<li>Only returns machines with floor plan coordinates</li>
<li>Machines with status "lainya" won't show unless they have coordinates</li>
</ul>

<span id="example-requests-GETapi-machines-floor-plan">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/machines/floor-plan" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/floor-plan"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-machines-floor-plan">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: XSRF-TOKEN=eyJpdiI6IlNNTmlCcEhVa25JYTZyb25XUVFHYWc9PSIsInZhbHVlIjoiSmNCVjg2YnZWRk4xcktWaWNrcVRTV1QzUnZUcmhrRlBydUszcjBrczllbTU5QXJkQVZGam9DV2thNFpsckc4RHplZ1FyOFdHVGRlMTNXZE5aVHVZUUdMRGxYMWlZYVZwV1VRNW5ZT0plcmw3cGJ5NlhISUx2bEd1emhXQjRlZ0giLCJtYWMiOiIzMTNjZGRiYmU3ODJkZTM0MmQzNzc2ZjY3ZjliNTkxMmY3NDg1OTU3Nzk1NDM5MDQ1MjU5MDFlNjlmMDVhYWVmIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; samesite=lax; henkaten-session=eyJpdiI6Ik9oVTcxRkFQZ1pjamlYelJMdDlCT1E9PSIsInZhbHVlIjoiS2FUZktuRUg3bnhqdnJCQ1loSHdZb0NPVlVtdkxHZHRrZkI3ZEwyMCtrY2VHdWRYV0l2Wi9IY01LWkZObTVHZVlkNVV5L0FqYVZydEthR29pNUYybVM2UC9OcDBoMXFaMy9wajVjSFhCbTlmQ0RGY0dVQjRNUGxrZFF6MEtRM2UiLCJtYWMiOiJjNjdlNDNkODE5ODU0Y2VlMDdiMjk0NDc2ZDg3MzU2ZmUxOTUyZTEzZDJjNTgxOTc1OTE4ZDJhNzFhMTY3MWZjIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-machines-floor-plan" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-machines-floor-plan"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-machines-floor-plan"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-machines-floor-plan" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-machines-floor-plan">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-machines-floor-plan" data-method="GET"
      data-path="api/machines/floor-plan"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-machines-floor-plan', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-machines-floor-plan"
                    onclick="tryItOut('GETapi-machines-floor-plan');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-machines-floor-plan"
                    onclick="cancelTryOut('GETapi-machines-floor-plan');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-machines-floor-plan"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/machines/floor-plan</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-machines-floor-plan"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-machines-floor-plan"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-machines-floor-plan"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-machines--id--floor-plan">Get a single machine&#039;s floor plan data
GET /api/machines/{id}/floor-plan</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-machines--id--floor-plan">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/machines/architecto/floor-plan" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/architecto/floor-plan"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-machines--id--floor-plan">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: XSRF-TOKEN=eyJpdiI6IlJSc0luWldmRzc3dCtJMk9HTTV3eUE9PSIsInZhbHVlIjoiVE5OamY3T3NkZDJwc05GajBnMlRsWW5KZ1B2VEVIcEhvZWw4UjJkenBRSUlGQ0FRQlR2MmZXQzdlYVkzZ20zZ25pVTBCK2crUXk1Tnl3V1IxRG11TFIzS3lzVm95ZWRuRVgwNTJNOEtBME9lWEJFeXZISFgwVWh1bTJVRGIzRk0iLCJtYWMiOiI5N2ExYWVlODg4ODM4ZmEwNGVjN2MzODc0OGFjZjRjMDhlMDQyNjUwZWZmZGJhYmY0N2IwOGFmMTgxOGI4YzI2IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; samesite=lax; henkaten-session=eyJpdiI6IjlqdFEyVU5nNm51TnlydlBPck96UHc9PSIsInZhbHVlIjoiMUt4WGJDcVBOZEoxQkxsaVU1R1dtblFxMHZWQVpkdkxPbWgwcnJ1Qk9rTUhPdnMxQ3FualMrTFk5MjNJTjMzWjM2TklSWC92U3RzSngwcWRselJ3a082OEFLSS9qTkJnd3VsaVFwNzY5VXg3WHkxb2VHdTJNTU1ZVFBWZzlTaDgiLCJtYWMiOiI0NGFiMDMwMWViZGQ1NmM5MjVmN2ViY2FmN2IxNjRmMmQ1Y2E0MWEwZDYwNDgxZjk2Y2E1OTBmNTAzYjFlNDU1IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-machines--id--floor-plan" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-machines--id--floor-plan"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-machines--id--floor-plan"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-machines--id--floor-plan" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-machines--id--floor-plan">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-machines--id--floor-plan" data-method="GET"
      data-path="api/machines/{id}/floor-plan"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-machines--id--floor-plan', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-machines--id--floor-plan"
                    onclick="tryItOut('GETapi-machines--id--floor-plan');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-machines--id--floor-plan"
                    onclick="cancelTryOut('GETapi-machines--id--floor-plan');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-machines--id--floor-plan"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/machines/{id}/floor-plan</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-machines--id--floor-plan"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-machines--id--floor-plan"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-machines--id--floor-plan"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-machines--id--floor-plan"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the machine. Example: <code>architecto</code></p>
            </div>
                    </form>

                <h1 id="factory">Factory</h1>

    <p>APIs for managing Factory.</p>

                                <h2 id="factory-GETapi-factories">GET /admin/api/factories  - list for dropdowns</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-factories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/factories" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/factories"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-factories">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ii8xV1hpeW91STFaajlMZHZTNTdaTUE9PSIsInZhbHVlIjoiald4WTd0MXNFODVqeHpkQTNqMEVkek1aSkxHcmV3dTBnT3NlU2dOZ1E5Z0FmOUxmNm5RblIzcG1wUEJmTkl5bGVPeE5tSnFjc1NHZ0Z5cVZXNTZxWTY0cjhkSlAxR25vbUFPamlMcmVtTDBIekJ6Y3RKZDV0OXR0Q1NBTW5KalAiLCJtYWMiOiIxOGQ4ZTAwOWFmODAwZDI2OWVkYWE0MWY1N2I2NTJmNWQwYTY4NjlkZGVlZDk4MTk4NjdmZmEyOTRmN2RlYjMxIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-factories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-factories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-factories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-factories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-factories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-factories" data-method="GET"
      data-path="api/factories"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-factories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-factories"
                    onclick="tryItOut('GETapi-factories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-factories"
                    onclick="cancelTryOut('GETapi-factories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-factories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/factories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-factories"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-factories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-factories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="factory-POSTapi-factories">POST /admin/api/factories</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-factories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/factories" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/factories"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-factories">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ink1dGNRRUd6L0lTVzFud0RUWUJ0UWc9PSIsInZhbHVlIjoiM3E0NjE0aG55TkJGNm5RQXlqMzNWQjNmRU92OEZ6TkpMdnkrQmwzTlZtb3JlVjZTbUtFQUhCNmNlN09SZk9rR0EzMk1BTS94Ym9TaDh3QWwrQVdTeUNSL0JCR05QZXhjdXBQY1FqOHJPR2tnb1BiWG9XUXB0ZHdzdUd6SW9BSGYiLCJtYWMiOiIyOTBmZjc0MzBiODgwNWQyMTBmYWQ2MGNjZGE2MWNlNTk0ZTIyYTgzODc2N2Y0ODBlM2JjMmY3OTI5NDMwYTBmIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-factories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-factories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-factories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-factories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-factories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-factories" data-method="POST"
      data-path="api/factories"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-factories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-factories"
                    onclick="tryItOut('POSTapi-factories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-factories"
                    onclick="cancelTryOut('POSTapi-factories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-factories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/factories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-factories"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-factories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-factories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-factories"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>b</code></p>
        </div>
        </form>

                    <h2 id="factory-PUTapi-factories--factory_id-">PUT /admin/api/factories/{factory}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-factories--factory_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://192.168.28.152:8000/api/factories/3" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/factories/3"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-factories--factory_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ik9PMmxWaGhxemh5dE41UlNYdE5zY1E9PSIsInZhbHVlIjoieHBaL1V0VVVZTE02STlPQWUzM2JCVFRYRTU2OWpMRjRjTzZPM1JLek11VWdaZEwzWWRwenpkZjVrRFBuNENlcVlhSjBUT3ZQSThaVXA4d1lTOEtURktIamt0QytoS1FxODErSlZSWG5razZaaFk4Y0RseTVKTGg0U1loUXFUT3oiLCJtYWMiOiJjYjJhYzE5YjhjNTdjNWM5MjI1NjcwYjQ3Y2EwYTkwNDQ1ZGZkM2RjYmVkYzYxZTU2ZGNiNWFkMzE0MDY3MjhlIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-factories--factory_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-factories--factory_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-factories--factory_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-factories--factory_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-factories--factory_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-factories--factory_id-" data-method="PUT"
      data-path="api/factories/{factory_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-factories--factory_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-factories--factory_id-"
                    onclick="tryItOut('PUTapi-factories--factory_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-factories--factory_id-"
                    onclick="cancelTryOut('PUTapi-factories--factory_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-factories--factory_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/factories/{factory_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PUTapi-factories--factory_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-factories--factory_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-factories--factory_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>factory_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="factory_id"                data-endpoint="PUTapi-factories--factory_id-"
               value="3"
               data-component="url">
    <br>
<p>The ID of the factory. Example: <code>3</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-factories--factory_id-"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="factory-DELETEapi-factories--factory_id-">DELETE /admin/api/factories/{factory}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-factories--factory_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/factories/3" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/factories/3"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-factories--factory_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkVQWjU2bzlrbUx2ckFJWVRxQUxlYnc9PSIsInZhbHVlIjoiS3E3ZWloZjg0eW8rTGVLdEkrYkNoZ1Bnb1YwMW0rOUxxV29DUklGSmRhYzNFVmVpczZZRCt6L1dNUTQyS0kxNDJlOUEvMzg0a3F4ZythYk0yV0p6NTRHMnFuOEx4VHcyOWoyMkh2NjlrMXhDbzlJK3QwR2liay9TZWRzeThIZzUiLCJtYWMiOiJiOGYxMWQ2NmIzNDMxZWY3ZGRiMWM3ZGI1MmRiZWM1YzVhMWVmZjBjNWFiMjY3YmQ4NTZkZmQwMzc1ODZlNjY5IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-factories--factory_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-factories--factory_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-factories--factory_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-factories--factory_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-factories--factory_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-factories--factory_id-" data-method="DELETE"
      data-path="api/factories/{factory_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-factories--factory_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-factories--factory_id-"
                    onclick="tryItOut('DELETEapi-factories--factory_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-factories--factory_id-"
                    onclick="cancelTryOut('DELETEapi-factories--factory_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-factories--factory_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/factories/{factory_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-factories--factory_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-factories--factory_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-factories--factory_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>factory_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="factory_id"                data-endpoint="DELETEapi-factories--factory_id-"
               value="3"
               data-component="url">
    <br>
<p>The ID of the factory. Example: <code>3</code></p>
            </div>
                    </form>

                    <h2 id="factory-PATCHapi-factories-reorder">PATCH /admin/api/factories/reorder  - bulk update order_index</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PATCHapi-factories-reorder">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://192.168.28.152:8000/api/factories/reorder" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"order\": [
        {
            \"id\": 16,
            \"order_index\": 16
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/factories/reorder"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "order": [
        {
            "id": 16,
            "order_index": 16
        }
    ]
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-factories-reorder">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ii9aSDhIdTR5RjRYOVFBckVMVDY3THc9PSIsInZhbHVlIjoiYjE2c2JpZ0psOHBqckVPRElaZE9LdFp3YUkyekN5eTRqeFAwaERXTVc0ZlR3eXkxb29mb0RUdFFrTzV6bDF6TElqOVA0QTdCbnoxSitGd1lRT3FpbW5SVzhPeksrS3NNdllPdW1zUDZlM1kyZGk2TmUzbVJCMU44UUgzY0pFZ3MiLCJtYWMiOiJkNDhhYjA5OWM2MDE2ZWU1MzFkYmMxNTJjYWQyMzIyZTE3ZTU4NTgwM2E3N2Y4YjJkYjExYTQwYzAyM2VhYzFlIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-factories-reorder" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-factories-reorder"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-factories-reorder"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-factories-reorder" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-factories-reorder">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-factories-reorder" data-method="PATCH"
      data-path="api/factories/reorder"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-factories-reorder', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-factories-reorder"
                    onclick="tryItOut('PATCHapi-factories-reorder');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-factories-reorder"
                    onclick="cancelTryOut('PATCHapi-factories-reorder');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-factories-reorder"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/factories/reorder</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PATCHapi-factories-reorder"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-factories-reorder"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-factories-reorder"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>order</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order.0.id"                data-endpoint="PATCHapi-factories-reorder"
               value="16"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the factories table. Example: <code>16</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>order_index</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order.0.order_index"                data-endpoint="PATCHapi-factories-reorder"
               value="16"
               data-component="body">
    <br>
<p>Example: <code>16</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                <h1 id="log">Log</h1>

    <p>APIs for managing Log.</p>

                                <h2 id="log-GETapi-logs-list">GET api/logs/list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-logs-list">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/logs/list" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs/list"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-logs-list">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkR4ZkVpV09XN3F6eC9oaG40b2ZROHc9PSIsInZhbHVlIjoiMDErUlNYSGp3b1ZNdXF1dmV6SEhuc0tpcUJJVnVOS2VFY2kyZS9BZkgxYkZEUFl5U2xpYk5vc1VsTjFyclluOVFjQThTa1JoUWZwOWM2V0h6S2VxcklrcGVuZXN4VjRHUmJxL2k3SWNGT09kbk9OcTdhOGY0bnFVaE81U2t2NkQiLCJtYWMiOiI0NDMwMTNiNTQ4YzgwYmI1YzRhMWM4MjRlYTRiNGRkOTQ3MGZlZGFlOTZlMWM2NTNhNmQwYTExMDFlMDEwOWZkIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:40 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-logs-list" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-logs-list"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-logs-list"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-logs-list" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-logs-list">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-logs-list" data-method="GET"
      data-path="api/logs/list"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-logs-list', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-logs-list"
                    onclick="tryItOut('GETapi-logs-list');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-logs-list"
                    onclick="cancelTryOut('GETapi-logs-list');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-logs-list"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/logs/list</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-logs-list"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-logs-list"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-logs-list"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="log-GETapi-logs-combined">Combined problem list for TV panel:
3M ProblemLog rows PLUS Man (absen) rows.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Logic mirrors the report CSV / ReportController:</p>
<ul>
<li>Absen WITHOUT replacement → status = open   (OPEN GOING)</li>
<li>Absen WITH    replacement → status = closed (DONE)</li>
<li>3M logs pass through as-is
Returned rows shape:
id, jenis, lokasi, waktu_mulai, waktu_selesai, durasi,
deskripsi, status, cause, countermeasure, pic, _source (log|absen)
Bug Fixed by rizky</li>
</ul>

<span id="example-requests-GETapi-logs-combined">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/logs/combined" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs/combined"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-logs-combined">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InIyalMzcTFsa1RZRUhnT1cwWnRKVUE9PSIsInZhbHVlIjoiWHo3R2h0eFlEa3RVR2JwVDNNZnhUa3Y2T1Ryckp5WjEyaC9ZZnNody9SNStCeEJ4aGNUOVBUTHRxL1JoR3lTRjZXcGZha3lqbUtHRjdRL24wMytvSGhlejJpejhjVXZmcDhsMmdYamtnTUhGUVdSOG0ybzBOdTc5NEhDYzkvV0IiLCJtYWMiOiI4NzczZjRiMWJlY2JmNjA5OWM4NDI2ZWJlNzFlNmQxMmIxZTY4MDhjNzZhZDk1ZDgwYTYxMGRlZWU1M2Q4YzdmIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:40 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-logs-combined" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-logs-combined"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-logs-combined"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-logs-combined" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-logs-combined">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-logs-combined" data-method="GET"
      data-path="api/logs/combined"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-logs-combined', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-logs-combined"
                    onclick="tryItOut('GETapi-logs-combined');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-logs-combined"
                    onclick="cancelTryOut('GETapi-logs-combined');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-logs-combined"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/logs/combined</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-logs-combined"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-logs-combined"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-logs-combined"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="log-POSTapi-logs">POST api/logs</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-logs">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/logs" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"tanggal\": \"2026-05-20T05:58:40\",
    \"factory\": \"architecto\",
    \"shift\": \"B\",
    \"jenis\": \"Machine\",
    \"lokasi\": \"architecto\",
    \"waktu_mulai\": \"05:58\",
    \"waktu_selesai\": \"05:58\",
    \"status\": \"closed\",
    \"deskripsi\": \"architecto\",
    \"cause\": \"architecto\",
    \"countermeasure\": \"architecto\",
    \"pic\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tanggal": "2026-05-20T05:58:40",
    "factory": "architecto",
    "shift": "B",
    "jenis": "Machine",
    "lokasi": "architecto",
    "waktu_mulai": "05:58",
    "waktu_selesai": "05:58",
    "status": "closed",
    "deskripsi": "architecto",
    "cause": "architecto",
    "countermeasure": "architecto",
    "pic": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-logs">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ilp4Mi9LT3I4Q1ZVbGp5RTJJKzFBV2c9PSIsInZhbHVlIjoicWR6N01jNGlJc2xpQWg2Zk5OcGpkQmEzaDRnTTY1ZlBtVWdRemIydjl0ZEc2NTZ6dUN2NGdudDZUS0ppWlVyaDBuZmRUWWIySDVJYm0ybzZscW5WQlZ0bW1DVFZpcWVzRFRzTWdPS0pPYk9uTnU1ekFnUG1xcjZpMFBjYm1ic2EiLCJtYWMiOiI5NzllZjYyMTU5ZTg3MWQ4ODNiNDRmY2ZmMWVlODYxM2RhYzM4OGVhYThhYzM1Yjg5M2MzODMwZjNkOGQwZDkxIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:40 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-logs" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-logs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logs"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-logs" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logs">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-logs" data-method="POST"
      data-path="api/logs"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-logs', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-logs"
                    onclick="tryItOut('POSTapi-logs');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-logs"
                    onclick="cancelTryOut('POSTapi-logs');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-logs"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/logs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-logs"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-logs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-logs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tanggal</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tanggal"                data-endpoint="POSTapi-logs"
               value="2026-05-20T05:58:40"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-05-20T05:58:40</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-logs"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-logs"
               value="B"
               data-component="body">
    <br>
<p>Example: <code>B</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>jenis</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="jenis"                data-endpoint="POSTapi-logs"
               value="Machine"
               data-component="body">
    <br>
<p>Example: <code>Machine</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Machine</code></li> <li><code>Material</code></li> <li><code>Method</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>lokasi</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="lokasi"                data-endpoint="POSTapi-logs"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>waktu_mulai</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="waktu_mulai"                data-endpoint="POSTapi-logs"
               value="05:58"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>H:i</code>. Example: <code>05:58</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>waktu_selesai</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="waktu_selesai"                data-endpoint="POSTapi-logs"
               value="05:58"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>H:i</code>. Example: <code>05:58</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-logs"
               value="closed"
               data-component="body">
    <br>
<p>Example: <code>closed</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>open</code></li> <li><code>closed</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>deskripsi</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="deskripsi"                data-endpoint="POSTapi-logs"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cause</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cause"                data-endpoint="POSTapi-logs"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>countermeasure</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="countermeasure"                data-endpoint="POSTapi-logs"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>pic</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pic"                data-endpoint="POSTapi-logs"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="log-PATCHapi-logs--log_id--close">PATCH api/logs/{log_id}/close</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PATCHapi-logs--log_id--close">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://192.168.28.152:8000/api/logs/1/close" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"countermeasure\": \"b\",
    \"waktu_selesai\": \"05:58\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs/1/close"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "countermeasure": "b",
    "waktu_selesai": "05:58"
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-logs--log_id--close">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ik8vOUFPRCt2SlNUbENOdUVieW9TNWc9PSIsInZhbHVlIjoiTkFhdzhGMzFWa3FLaUZXRUJZZGljdmg2MmRXN3g3eGNTUENlWHIzWlZ3YTBXWUpLWUVwRGRDSFdCMW0yL09ZYXAxa0lNTkxFTEMyNEFtTW42TFhyVjhNTU5KRXJJS21vaWZ4MlhIdm1YWTRzWTI5VnNoKyt2MDZnZkxDdWsrOXciLCJtYWMiOiIxMjdlNjJhMTllMTI2MmJhNTcyNmE5ODQxZjFmZmI4NDViNzc5YWMxY2Q1MjMwMzRhNjYxY2E0NmVkZTFhYzFhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-logs--log_id--close" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-logs--log_id--close"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-logs--log_id--close"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-logs--log_id--close" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-logs--log_id--close">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-logs--log_id--close" data-method="PATCH"
      data-path="api/logs/{log_id}/close"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-logs--log_id--close', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-logs--log_id--close"
                    onclick="tryItOut('PATCHapi-logs--log_id--close');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-logs--log_id--close"
                    onclick="cancelTryOut('PATCHapi-logs--log_id--close');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-logs--log_id--close"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/logs/{log_id}/close</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PATCHapi-logs--log_id--close"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-logs--log_id--close"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-logs--log_id--close"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>log_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="log_id"                data-endpoint="PATCHapi-logs--log_id--close"
               value="1"
               data-component="url">
    <br>
<p>The ID of the log. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>countermeasure</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="countermeasure"                data-endpoint="PATCHapi-logs--log_id--close"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>waktu_selesai</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="waktu_selesai"                data-endpoint="PATCHapi-logs--log_id--close"
               value="05:58"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>H:i</code>. Example: <code>05:58</code></p>
        </div>
        </form>

                    <h2 id="log-PATCHapi-logs--log_id--reopen">PATCH api/logs/{log_id}/reopen</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PATCHapi-logs--log_id--reopen">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://192.168.28.152:8000/api/logs/1/reopen" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs/1/reopen"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PATCH",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-logs--log_id--reopen">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ijl5TFdRdmp3dEdVWGtOQkI3Tk85bFE9PSIsInZhbHVlIjoiTjRUWXFzbTRLaFpzM2RoZjlxdmhxTTkyVm9SNlM5REJ1eHc0NHN0a05WVVZqR3Z6VGVVa0h5YWZWeTFOaTd2RytvRW1PQnBMVzdZQ3pzZEZPMFFVc2lYRk5OMlJqSFpTZ0twaTkvREplQXo0aU9zNUo4OTFiZGFZZjZZcUsveDAiLCJtYWMiOiIzOWFhNDcwYzNhZDJiMjkyZWU4M2UzNDhjM2I3Y2RjNDgxOTYyMjQ5NWUyMmVmOWVjMWE1MTdmZTU1MGE4MWU5IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-logs--log_id--reopen" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-logs--log_id--reopen"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-logs--log_id--reopen"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-logs--log_id--reopen" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-logs--log_id--reopen">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-logs--log_id--reopen" data-method="PATCH"
      data-path="api/logs/{log_id}/reopen"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-logs--log_id--reopen', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-logs--log_id--reopen"
                    onclick="tryItOut('PATCHapi-logs--log_id--reopen');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-logs--log_id--reopen"
                    onclick="cancelTryOut('PATCHapi-logs--log_id--reopen');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-logs--log_id--reopen"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/logs/{log_id}/reopen</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PATCHapi-logs--log_id--reopen"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-logs--log_id--reopen"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-logs--log_id--reopen"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>log_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="log_id"                data-endpoint="PATCHapi-logs--log_id--reopen"
               value="1"
               data-component="url">
    <br>
<p>The ID of the log. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="log-PATCHapi-logs--log_id-">PATCH api/logs/{log_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PATCHapi-logs--log_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://192.168.28.152:8000/api/logs/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"waktu_mulai\": \"05:58\",
    \"waktu_selesai\": \"05:58\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "waktu_mulai": "05:58",
    "waktu_selesai": "05:58"
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-logs--log_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Inczb0JISW4vNllZdHFQTGM4cUtNT3c9PSIsInZhbHVlIjoiU29mSUE5eDcxaFIvNmZvYXB1R0lpOFhoZEZwSWJrRTJIUEs5bElvdnl5TDNJbCtCZCtCNnRsbjF5Y0FaMmJ3UFIreTVtaGtZWVcyZm5SSEJVWXFod21QNzh1ajFjaEhJNSthK3IveDhpV0tURFQ1RnZVT3ZRZnpuWUZJRFM5bHgiLCJtYWMiOiI1ZDhhMjQ5NGYwNTE5OGZhNTU2MWM3YjQyZmRlOGJiYWZkY2M5MDRiZWNhMzAxMjEwYTQ5MWE3MDk1NGU1ZmExIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-logs--log_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-logs--log_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-logs--log_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-logs--log_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-logs--log_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-logs--log_id-" data-method="PATCH"
      data-path="api/logs/{log_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-logs--log_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-logs--log_id-"
                    onclick="tryItOut('PATCHapi-logs--log_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-logs--log_id-"
                    onclick="cancelTryOut('PATCHapi-logs--log_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-logs--log_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/logs/{log_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PATCHapi-logs--log_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-logs--log_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-logs--log_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>log_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="log_id"                data-endpoint="PATCHapi-logs--log_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the log. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>waktu_mulai</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="waktu_mulai"                data-endpoint="PATCHapi-logs--log_id-"
               value="05:58"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>H:i</code>. Example: <code>05:58</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>waktu_selesai</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="waktu_selesai"                data-endpoint="PATCHapi-logs--log_id-"
               value="05:58"
               data-component="body">
    <br>
<p>Must be a valid date in the format <code>H:i</code>. Example: <code>05:58</code></p>
        </div>
        </form>

                    <h2 id="log-DELETEapi-logs--log_id-">DELETE api/logs/{log_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-logs--log_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/logs/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/logs/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-logs--log_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InVrRjdhdExDak9KVSs4dWszckNLQWc9PSIsInZhbHVlIjoicVE3MU5NZUcxUGg3U1QrbE4zRmZpTVNvTlBFaVp6Zy9PRFVKV2owQWsxcGdyMG1lVVpGajczOVMyamNDT3BreEdQTzVOR0ExalhocSttTTFiNDBjV0QzaTFRcnNNaTIzNGlFYnh0OVNla0tkS3hLQlpIUGg2S1JRV3B0NHdMcVEiLCJtYWMiOiIxZDBiNjU3NzQ4YjkwYTIzYzUwMTI5ZWJmMGNhZThmOTlmYjllYmI2Yzg0ZGNjNDdjZTYwZjIwNzQyZjExYWZkIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:41 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-logs--log_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-logs--log_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-logs--log_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-logs--log_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-logs--log_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-logs--log_id-" data-method="DELETE"
      data-path="api/logs/{log_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-logs--log_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-logs--log_id-"
                    onclick="tryItOut('DELETEapi-logs--log_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-logs--log_id-"
                    onclick="cancelTryOut('DELETEapi-logs--log_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-logs--log_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/logs/{log_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-logs--log_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-logs--log_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-logs--log_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>log_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="log_id"                data-endpoint="DELETEapi-logs--log_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the log. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="machine">Machine</h1>

    <p>APIs for managing Machine.</p>

                                <h2 id="machine-POSTapi-machines-photo">Upload foto mesin
POST /admin/machines/photo</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-machines-photo">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/machines/photo" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "factory=architecto"\
    --form "machine_name=architecto"\
    --form "photo=@C:\Users\Daffy\AppData\Local\Temp\phpC983.tmp" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/photo"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('factory', 'architecto');
body.append('machine_name', 'architecto');
body.append('photo', document.querySelector('input[name="photo"]').files[0]);

fetch(url, {
    method: "POST",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-machines-photo">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IjYrOGcwVUh2OE9maHBVeUQ1aTJkTFE9PSIsInZhbHVlIjoiUWFOazNrZHlQaDRlRUhGS0N3SHprQ1V0K1lNcnltU2R1S0pOMjd3cHZoRHFYak9kSEpMME92ZXc1dVVpUHBNK25Tb0V4eWZ3SnlLbllWN1RxTDg1QU9kQzJTYWZ5NTZpYll3aWVJZWxQRG9pMXF5M3NkRlh0QU9FQUNuS0xyZUUiLCJtYWMiOiI1ZWU4MjhlNWQ5MzliMGU1ZDJkOGQ0YjJmZTI5NmIyMTUxMmIxNTg4YzRhMDNlMWRhYTcxYTRiMjBhZGMxM2U3IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:44 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-machines-photo" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-machines-photo"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-machines-photo"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-machines-photo" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-machines-photo">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-machines-photo" data-method="POST"
      data-path="api/machines/photo"
      data-authed="1"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-machines-photo', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-machines-photo"
                    onclick="tryItOut('POSTapi-machines-photo');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-machines-photo"
                    onclick="cancelTryOut('POSTapi-machines-photo');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-machines-photo"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/machines/photo</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-machines-photo"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-machines-photo"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-machines-photo"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-machines-photo"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>machine_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="machine_name"                data-endpoint="POSTapi-machines-photo"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>photo</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="file" style="display: none"
                              name="photo"                data-endpoint="POSTapi-machines-photo"
               value=""
               data-component="body">
    <br>
<p>Must be an image. Must not be greater than 3072 kilobytes. Example: <code>C:\Users\Daffy\AppData\Local\Temp\phpC983.tmp</code></p>
        </div>
        </form>

                    <h2 id="machine-POSTapi-machines-status">Update status mesin
POST /admin/machines/status</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-machines-status">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/machines/status" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"tanggal\": \"2026-05-20T05:58:44\",
    \"factory\": \"architecto\",
    \"shift\": \"A\",
    \"machine_name\": \"architecto\",
    \"status\": \"normal\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/status"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tanggal": "2026-05-20T05:58:44",
    "factory": "architecto",
    "shift": "A",
    "machine_name": "architecto",
    "status": "normal"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-machines-status">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6ImRrTlBrRDBGSWh3cEVnRGJDcHFtVmc9PSIsInZhbHVlIjoiQ2Ixa1I3ZTAwR1lxcnNrdjlNOG9aejVFeHJxV1lJYUJJYS85b09WYzRaSFhCdE1iRmFOd1hFZ3prYVVFVktsZ21ETzhVWkhmY0hBWi9FTXNONnVKTlhPYTRZck5TaEdSdHdDcUlic2liWFE2R29tK2VLQW5KbmN4TjFnMjAxdjkiLCJtYWMiOiJiYjlmOWQ5MDBiYTcwM2MyZmY0ODllZDZjOGJkMjI5Mzg0NzM1NGI1MzBhMDQwYWUyYzhiZWJlNWVmMTFmNmJlIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:44 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-machines-status" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-machines-status"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-machines-status"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-machines-status" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-machines-status">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-machines-status" data-method="POST"
      data-path="api/machines/status"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-machines-status', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-machines-status"
                    onclick="tryItOut('POSTapi-machines-status');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-machines-status"
                    onclick="cancelTryOut('POSTapi-machines-status');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-machines-status"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/machines/status</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-machines-status"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-machines-status"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-machines-status"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tanggal</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tanggal"                data-endpoint="POSTapi-machines-status"
               value="2026-05-20T05:58:44"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-05-20T05:58:44</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-machines-status"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-machines-status"
               value="A"
               data-component="body">
    <br>
<p>Example: <code>A</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>machine_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="machine_name"                data-endpoint="POSTapi-machines-status"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-machines-status"
               value="normal"
               data-component="body">
    <br>
<p>Example: <code>normal</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>normal</code></li> <li><code>man</code></li> <li><code>material</code></li> <li><code>machine</code></li> <li><code>method</code></li></ul>
        </div>
        </form>

                    <h2 id="machine-GETapi-machines-lights">4M lights data  - otomatis berdasarkan:
  1. MachineStatus manual (override)
  2. ProblemLog open
  3. AbsenceRecord absen tanpa AssignmentReplacement → auto &quot;man&quot;</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-machines-lights">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/machines/lights" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/lights"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-machines-lights">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6ImtCT2IzcHFEaFhnRG5QQktSNVpudmc9PSIsInZhbHVlIjoiS1orVW92T082QmkzWTlESDgwdjRTT3VnaW9aS0lJeC9rTG5jMng5VlpSL1JRNy9Hd0dDZUc0QytmcGhqMTNiTmdFdm14THZ3OUxZbWxUaDJyZld5MUFxMWhNVkFHYVFIZ3dYejk4SWkvZ3dpYUt0NEJqS1pmanlsdHd6ZWloNFQiLCJtYWMiOiIyNWNhYmE5YzdhN2M0MWU4NGY2NmNmODkzOTk5MjNkNTQ5ZGQxYjY2NWM1NGQwN2I1ZWFmNWMzODQ3OTA1NDk5IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:44 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-machines-lights" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-machines-lights"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-machines-lights"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-machines-lights" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-machines-lights">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-machines-lights" data-method="GET"
      data-path="api/machines/lights"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-machines-lights', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-machines-lights"
                    onclick="tryItOut('GETapi-machines-lights');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-machines-lights"
                    onclick="cancelTryOut('GETapi-machines-lights');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-machines-lights"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/machines/lights</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-machines-lights"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-machines-lights"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-machines-lights"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="machine-GETapi-machines-statuses">Ambil semua status mesin</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-machines-statuses">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/machines/statuses" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/statuses"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-machines-statuses">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ik84bmhzbXd4L3FJV3FBK01WNWRuOVE9PSIsInZhbHVlIjoiRm1SdGhkMDQ3N3RJK0JCT2hLWjlxZjEyZzhoRFYrR2hJUFJQd0Z3R2QvOEZNU0hyRXhqSTB0cW9zd1dPVDZXdCtmbVNXVnVadFdzQWhJN1V2aDlZS1k2VWE4cjNtdEhDN2EyTjRhS3ZTcWlEeVJzNUZkNSt2WUhwbFVOdmIrdGciLCJtYWMiOiIwZTc3OGFlMTE5N2M4MTg2MjJjMDdmNTljNTVmODBmZjEwOWRhMzllZDVkNWJlZWU2OWEyNTAwZDU0NjhlZTIyIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:44 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-machines-statuses" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-machines-statuses"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-machines-statuses"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-machines-statuses" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-machines-statuses">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-machines-statuses" data-method="GET"
      data-path="api/machines/statuses"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-machines-statuses', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-machines-statuses"
                    onclick="tryItOut('GETapi-machines-statuses');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-machines-statuses"
                    onclick="cancelTryOut('GETapi-machines-statuses');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-machines-statuses"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/machines/statuses</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-machines-statuses"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-machines-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-machines-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="machine-PATCHapi-machines--machine_id--floor-coordinates">Update floor plan coordinates for a machine
PATCH /admin/machines/{id}/floor-coordinates</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PATCHapi-machines--machine_id--floor-coordinates">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://192.168.28.152:8000/api/machines/1/floor-coordinates" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"floor_cx\": 4326.41688,
    \"floor_cy\": 4326.41688,
    \"floor_plan\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/machines/1/floor-coordinates"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "floor_cx": 4326.41688,
    "floor_cy": 4326.41688,
    "floor_plan": "architecto"
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-machines--machine_id--floor-coordinates">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlN6SE1TZzFqWExScWdoRnp0TVd0U3c9PSIsInZhbHVlIjoiUUxHYUpqV0x0VWxQUFNxUktESzQ3WFVtL2JCVG15djZ6M0VOanRKR3NTNFR3dmJHWkNna0xiOXQ0VlRpQmd6bXYxMjFNbEN4dHlERzRPamU2blhvTTVBZVdxQ1lEamE1bnJsSE1ueU4rbWllS2lCaVVPVHAvem9VRTdTaXNTWGUiLCJtYWMiOiJhNGE5ZmUzNDc5ZTc5MDgwY2JlYTUxMDQ3ZjZjOTg0ZGE2ZmI5YjgxYzgyNTlmY2Q2MmZlNWI3NWU5MTIzOGRkIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-machines--machine_id--floor-coordinates" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-machines--machine_id--floor-coordinates"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-machines--machine_id--floor-coordinates"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-machines--machine_id--floor-coordinates" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-machines--machine_id--floor-coordinates">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-machines--machine_id--floor-coordinates" data-method="PATCH"
      data-path="api/machines/{machine_id}/floor-coordinates"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-machines--machine_id--floor-coordinates', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-machines--machine_id--floor-coordinates"
                    onclick="tryItOut('PATCHapi-machines--machine_id--floor-coordinates');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-machines--machine_id--floor-coordinates"
                    onclick="cancelTryOut('PATCHapi-machines--machine_id--floor-coordinates');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-machines--machine_id--floor-coordinates"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/machines/{machine_id}/floor-coordinates</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>machine_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="machine_id"                data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="1"
               data-component="url">
    <br>
<p>The ID of the machine. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>floor_cx</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="floor_cx"                data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="4326.41688"
               data-component="body">
    <br>
<p>Example: <code>4326.41688</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>floor_cy</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="floor_cy"                data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="4326.41688"
               data-component="body">
    <br>
<p>Example: <code>4326.41688</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>floor_plan</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="floor_plan"                data-endpoint="PATCHapi-machines--machine_id--floor-coordinates"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                <h1 id="member">Member</h1>

    <p>APIs for managing Member.</p>

                                <h2 id="member-GETapi-members-list">GET api/members/list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-members-list">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/members/list" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/list"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-members-list">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ii95MlhpZnFCQnVpQVRGWWh2RXRoNEE9PSIsInZhbHVlIjoiT2tNMThWMUlwQnRNcE5tZUtncFpvdVNsZ3lXeXdWSU1taU85RC9GMUhldG1jK28yU2RjTDZ2NFFCMi9aR2Y0WFMvQ1hGT0t6czh4UjRabnF5WDZoMHd0Z2I2c3ltUG1HWVhaSzVUTEx4QU9PYnA2NDR2RjdNcVIrOW1NZ2hPRjUiLCJtYWMiOiJlNDRhYzQyMjAyOTQxMzM5OGY1ZmI4M2JmYzZiN2MyZWY4ZGE3YTdmYWRkMzcyOTdkY2EwNTZmY2ZiMjM4M2UwIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-members-list" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-members-list"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-members-list"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-members-list" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-members-list">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-members-list" data-method="GET"
      data-path="api/members/list"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-members-list', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-members-list"
                    onclick="tryItOut('GETapi-members-list');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-members-list"
                    onclick="cancelTryOut('GETapi-members-list');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-members-list"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/members/list</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-members-list"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-members-list"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-members-list"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="member-GETapi-members--member_id-">GET api/members/{member_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-members--member_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/members/5" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/5"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-members--member_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InV4b1R2ZDRrY29pYWdIdVJlQ0ZWOUE9PSIsInZhbHVlIjoiNTRnSk1QTlllOGdpclczRk45alUrVnNrTXFOR1Y1ZmE5V25qQ3hKWU1TUlJVeXd4eUlFdkNuc25zY3liK3AyRUVUOWgyME1UdkR3cUo1YU1sbC9UdVRnbHB1YmV1RkJ0RmRLUEJhNnBPYzF1NkR0VERKYUtJTmR5VkZVTExUbXYiLCJtYWMiOiJlMDBhMjdhMjVmMGU5ZWVjOTZhNzVlYjkzNTYzZDAwM2E4ZjgxYmNjYzA3MDEwODI2NWFkNzZlODBmMDQ0NDVjIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-members--member_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-members--member_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-members--member_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-members--member_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-members--member_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-members--member_id-" data-method="GET"
      data-path="api/members/{member_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-members--member_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-members--member_id-"
                    onclick="tryItOut('GETapi-members--member_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-members--member_id-"
                    onclick="cancelTryOut('GETapi-members--member_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-members--member_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/members/{member_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-members--member_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-members--member_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-members--member_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>member_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="member_id"                data-endpoint="GETapi-members--member_id-"
               value="5"
               data-component="url">
    <br>
<p>The ID of the member. Example: <code>5</code></p>
            </div>
                    </form>

                    <h2 id="member-POSTapi-members">POST api/members</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-members">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/members" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-members">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Im9ZdzgwbVdvb2d3OWMzd3VETXdHZEE9PSIsInZhbHVlIjoiRnRVMWQ4dmFZYUhkOFdYNmNFZjhpNllheGRkanlQL0JWOG1SQWZ4SkIyYlJPYVNjL2VlaHdxM2owQ1p6SHB4cStlejBKRE5mN3FodnlHb1E4djBqRk4wNkh6NlpobDBwOEwxd0Q0TllLWmJudzdpcFBNS3ZVQVAyWm53Tm9QVlgiLCJtYWMiOiJhNzEyMjE0NDMxNjQyYTJiNjYzZDIxNzljNmE1MjliMGVkM2ZmNmE5OWZhYTY4MjEyZDZjNTEyNzlhOGQ1MDBhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-members" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-members"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-members"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-members" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-members">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-members" data-method="POST"
      data-path="api/members"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-members', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-members"
                    onclick="tryItOut('POSTapi-members');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-members"
                    onclick="cancelTryOut('POSTapi-members');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-members"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/members</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-members"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-members"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-members"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="member-PUTapi-members--member_id-">PUT api/members/{member_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-members--member_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://192.168.28.152:8000/api/members/5" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/5"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-members--member_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IjluamdiNnZhRkVscDZnekNJcVNqOWc9PSIsInZhbHVlIjoib3hwamNzak5mUkl5UGxjNm03c2VYajc2aWxmKzlNWWx5UFFpTFNYckExdzY0YW1NbmQwbGprdDdGeWRxc0tJcjZPTlFWTzluMSsrWFFjUlpIUmxCbmFyWThKcEgvZ0FoSWZQUmhQMCtyNFYrY1BtMy9UdTdPSWx2OUpidThGMGoiLCJtYWMiOiJkYTliOTY2NzVkZGIxZGM3ZTA5NTY1YmY3ZTg4OTQ0ZTNlOTdhZDFjM2U5NDE2OWRhZjI4NjRkOTY2YzdlNzhiIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-members--member_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-members--member_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-members--member_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-members--member_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-members--member_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-members--member_id-" data-method="PUT"
      data-path="api/members/{member_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-members--member_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-members--member_id-"
                    onclick="tryItOut('PUTapi-members--member_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-members--member_id-"
                    onclick="cancelTryOut('PUTapi-members--member_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-members--member_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/members/{member_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PUTapi-members--member_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-members--member_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-members--member_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>member_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="member_id"                data-endpoint="PUTapi-members--member_id-"
               value="5"
               data-component="url">
    <br>
<p>The ID of the member. Example: <code>5</code></p>
            </div>
                    </form>

                    <h2 id="member-DELETEapi-members-clear-all">DELETE api/members/clear-all</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-members-clear-all">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/members/clear-all" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/clear-all"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-members-clear-all">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InpCNUN1bkRtTEpxamxqN1g5T0JodHc9PSIsInZhbHVlIjoiT2F5eGppZ1ByU0hnV1J6cFVxbW1NNmR2OHdhazNmZ3ZsS292NmE5cUk1Y3BlSGRRU3BudWtCYlljVmJ0YXRoYndkT1NyT25pYjNERnAzeGlucE5VeHNBVEZnZUlKUms0S2JwdlErK0dZTGZ5ejkrQ3lRWHNROUo2TkRFd3NMTUIiLCJtYWMiOiI2ZGIwNDcxNWIzMWY4NGU3OTg2MzFhY2ViM2ZlZGI4YjRiMGUyMTMwZTc3ZTUxMTc0NWUxODM3Y2Q4MTkyYzgyIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-members-clear-all" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-members-clear-all"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-members-clear-all"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-members-clear-all" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-members-clear-all">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-members-clear-all" data-method="DELETE"
      data-path="api/members/clear-all"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-members-clear-all', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-members-clear-all"
                    onclick="tryItOut('DELETEapi-members-clear-all');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-members-clear-all"
                    onclick="cancelTryOut('DELETEapi-members-clear-all');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-members-clear-all"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/members/clear-all</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-members-clear-all"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-members-clear-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-members-clear-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="member-DELETEapi-members--member_id-">DELETE api/members/{member_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-members--member_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/members/5" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/5"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-members--member_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkZrNlVCSXhlTjFyOTNHbHR1bFI0b0E9PSIsInZhbHVlIjoicm1BZjUwVEY0SWc2Zm5oUUpoTlpJcVNEQ2N0N21iK2gyZEtldzVheFZMQmV1K29QTHpOaDY3dSswdG5ERWJGRWI4bTFXK0R2TWU5UFE0dUtzRUdWLzFFOUJOSDBoVGlsK3BUR0tqMVFiMHpyMENPSEtGWXBRV1FsNXBGNWM3UU8iLCJtYWMiOiI1ZThhYmM1YzZhYzI2ZGYxZDU5Y2MyODQ0OWM5Njk1MjI1NWNhZGViYTEyMzVmZTY2ZThjM2MyNDYyMTJkNzI2IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-members--member_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-members--member_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-members--member_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-members--member_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-members--member_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-members--member_id-" data-method="DELETE"
      data-path="api/members/{member_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-members--member_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-members--member_id-"
                    onclick="tryItOut('DELETEapi-members--member_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-members--member_id-"
                    onclick="cancelTryOut('DELETEapi-members--member_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-members--member_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/members/{member_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-members--member_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-members--member_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-members--member_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>member_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="member_id"                data-endpoint="DELETEapi-members--member_id-"
               value="5"
               data-component="url">
    <br>
<p>The ID of the member. Example: <code>5</code></p>
            </div>
                    </form>

                    <h2 id="member-POSTapi-members-import">POST api/members/import</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-members-import">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/members/import" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"members\": [
        {
            \"name\": \"b\",
            \"factory\": \"architecto\",
            \"shift\": \"B\"
        }
    ],
    \"replace\": false
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/import"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "members": [
        {
            "name": "b",
            "factory": "architecto",
            "shift": "B"
        }
    ],
    "replace": false
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-members-import">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ik9qcmlUSWdPcDdzRTZRR3pzZ2h1aHc9PSIsInZhbHVlIjoiS25NVkxZRXlpY1NqdUMwbSsrQTRkR0RjWmRPKzJXZTRtczgwa1hxSE1lTVZUNzdMZVpGWTcybHZ4TUY3bU0vSXE2YythZzdtdDNDS0JQZS9QNExzdlF1SWl0ZEErL01tNVVZcnBYRVlnQmZWZ2U1MjhpcG1wcWU4TytMejM1TFYiLCJtYWMiOiIxOTM5NmI0Nzg1NjcxMjRiYmJiODI0ZjI4OGZlYjdkMmI4ZjA4ZDk4ZWQwNWQ2YjhhYTYxZDYwZGFjNTQ2OTY3IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-members-import" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-members-import"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-members-import"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-members-import" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-members-import">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-members-import" data-method="POST"
      data-path="api/members/import"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-members-import', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-members-import"
                    onclick="tryItOut('POSTapi-members-import');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-members-import"
                    onclick="cancelTryOut('POSTapi-members-import');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-members-import"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/members/import</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-members-import"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-members-import"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-members-import"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>members</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Must have at least 1 items.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="members.0.name"                data-endpoint="POSTapi-members-import"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>b</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="members.0.factory"                data-endpoint="POSTapi-members-import"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="members.0.shift"                data-endpoint="POSTapi-members-import"
               value="B"
               data-component="body">
    <br>
<p>Example: <code>B</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>replace</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-members-import" style="display: none">
            <input type="radio" name="replace"
                   value="true"
                   data-endpoint="POSTapi-members-import"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-members-import" style="display: none">
            <input type="radio" name="replace"
                   value="false"
                   data-endpoint="POSTapi-members-import"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
        </div>
        </form>

                    <h2 id="member-GETapi-members-export">GET api/members/export</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-members-export">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/members/export" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/export"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-members-export">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Im11SC84UG94eHRneW5XWmxJYjdNRUE9PSIsInZhbHVlIjoicTUzNWg5VDArbGYwOCs3eVRQamFTMHlYbFlWSU5aZWI1eDBSM0NHbitnTkFPZWhvMWVOcFVkR3AxRENzQm8vWkRYUjRpbitCSDV6YjlsMlRjLys2aU43dUlJa29pbnh4V3JxblprYWlOdmJjVWdjTENCYUhRWHJrY0FlZkdtdEEiLCJtYWMiOiI3MTMzNmE2NWIzY2VmYzMzODM0MTJiMGRlOWM0ZGEzYjIxY2M2NjE0MDczZGMwM2RmZGZmZjk0MTdjY2ZhNDk2IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:42 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-members-export" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-members-export"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-members-export"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-members-export" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-members-export">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-members-export" data-method="GET"
      data-path="api/members/export"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-members-export', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-members-export"
                    onclick="tryItOut('GETapi-members-export');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-members-export"
                    onclick="cancelTryOut('GETapi-members-export');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-members-export"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/members/export</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-members-export"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-members-export"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-members-export"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="member-GETapi-members-template">GET api/members/template</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-members-template">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/members/template" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/members/template"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-members-template">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlQ5Wjh2Rm1DZG5KWHcvLzJ5aW9Pb3c9PSIsInZhbHVlIjoic0ZDUHNFYzVXcUw4Y0NPQkhzM3BHd3N2bmFoc2lzOCt4aGdCOVN0OXpQQU1vbG1ROU0xU2tBemZYTU5ZRjl4L1FzU3JmaUg2S010NjJmL0hXWWN5NDdyZzduS0pkRW52empHQWpkUXR3akFqL3B6djJWUVc3S3BpaU1TS2J3ZGwiLCJtYWMiOiI5YTc5ZWUyZGVjMzZjYTk2YWYwMTkyOGUxNzBiNTlkZjQwODQ3OTNhNGFkYzJjOTg1NDEwOGU3N2JjNDJkYTM1IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:43 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-members-template" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-members-template"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-members-template"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-members-template" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-members-template">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-members-template" data-method="GET"
      data-path="api/members/template"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-members-template', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-members-template"
                    onclick="tryItOut('GETapi-members-template');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-members-template"
                    onclick="cancelTryOut('GETapi-members-template');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-members-template"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/members/template</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-members-template"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-members-template"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-members-template"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="replacement">Replacement</h1>

    <p>APIs for managing Replacement.</p>

                                <h2 id="replacement-GETapi-replacements">Ambil pengganti aktif (hanya yang member aslinya masih absen)
GET /admin/replacements?tanggal=&amp;factory=&amp;shift=</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Sekaligus auto-cleanup: hapus dari DB jika member asli sudah hadir.
"Member asli" = member yang shift/factory-nya sama dengan target_machine
dan statusnya sudah tidak absen di absence_records.
Fixed bug by Rizky</p>

<span id="example-requests-GETapi-replacements">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/replacements" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/replacements"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-replacements">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkxOQ0xtaWlmQ0ZhdVNEa1BYWTlYSFE9PSIsInZhbHVlIjoid3gxNTRrUkFINHVhcU5wU1Exb3FHTjNtOEdDcWRDcmd2WTdPSkhTSG5VVmVlbG9BYjRJMVVVWFNoZnJLcUpBNk5KQkY5L3dUeHJoM3NYUVdaRVI1bmhNOHhtZUhsOXQvUkFiY1QzMndYdHhhNXVJcFFSbTBpLzZWMHc0SjVOMHAiLCJtYWMiOiIxMGRlODBjNGU2MjRmNmFhNWNiOWExNjQyNGZlMzdiYzkxMjEwYmQwODc3NjY4ZDU0NmM3NmU0ODI2Yjc4MDA4IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:38 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-replacements" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-replacements"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-replacements"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-replacements" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-replacements">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-replacements" data-method="GET"
      data-path="api/replacements"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-replacements', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-replacements"
                    onclick="tryItOut('GETapi-replacements');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-replacements"
                    onclick="cancelTryOut('GETapi-replacements');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-replacements"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/replacements</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-replacements"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-replacements"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-replacements"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="replacement-POSTapi-replacements">Simpan pengganti yang dipilih
POST /admin/replacements
develop by rizky</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>FIXED: Handle mesin_secondary  - jika member yang di-replace punya mesin_secondary,
create replacement records untuk KEDUA mesin, bukan hanya target_machine.</p>

<span id="example-requests-POSTapi-replacements">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/replacements" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"tanggal\": \"2026-05-20T05:58:38\",
    \"factory\": \"architecto\",
    \"shift\": \"B\",
    \"target_machine\": \"architecto\",
    \"member_id\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/replacements"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tanggal": "2026-05-20T05:58:38",
    "factory": "architecto",
    "shift": "B",
    "target_machine": "architecto",
    "member_id": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-replacements">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InZIcytLL3FPb09mOWFDVWZNdWl0N2c9PSIsInZhbHVlIjoiMXI2cElpU1Y2WjJnY3RHRGdYZWduV1FOV2FHT0VoSUt6R1lpc1VRem4wMVFuQ2VOQkRxbEhXNFRsaEhhNlIxb3JNU0tMTmEvZm8vWEJJZzJHbFVwYnBwU1RGN1ltdFRKME02V0R1NEFqWjN0bzRnMTRydi9DZTArNCtaTXJUem8iLCJtYWMiOiJlNGUwYjZjNzE5MmVmYTJjNmVhNGE2YzQ2NmEyMGE5N2RkZmFjNDk0N2ZhNmFhYzVlZWQ0YjExMGZlNmU1OTIwIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:38 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-replacements" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-replacements"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-replacements"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-replacements" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-replacements">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-replacements" data-method="POST"
      data-path="api/replacements"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-replacements', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-replacements"
                    onclick="tryItOut('POSTapi-replacements');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-replacements"
                    onclick="cancelTryOut('POSTapi-replacements');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-replacements"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/replacements</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-replacements"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-replacements"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-replacements"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tanggal</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tanggal"                data-endpoint="POSTapi-replacements"
               value="2026-05-20T05:58:38"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-05-20T05:58:38</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-replacements"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-replacements"
               value="B"
               data-component="body">
    <br>
<p>Example: <code>B</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>A</code></li> <li><code>B</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>target_machine</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="target_machine"                data-endpoint="POSTapi-replacements"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>member_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="member_id"                data-endpoint="POSTapi-replacements"
               value="architecto"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the members table. Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="replacement-DELETEapi-replacements--replacement_id-">Hapus pengganti manual (tombol batalkan)
DELETE /admin/replacements/{id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-replacements--replacement_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/replacements/9" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/replacements/9"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-replacements--replacement_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlQrcUE2L3pvTFBHSkxwVGVqMTJ3dVE9PSIsInZhbHVlIjoiQUprMFBENGVjYmZWVkRWWk92b1F2NUhuMWZYYVlqa1lhM0dRaDVYUWJ2VzBjVWxzbGt3R2JmMzVQU2ZxeFIzQ0V4NDZaY2JzS1JLNnM5eURSTnJsbUR5V29MdkhDb0J4S3djQlYxc3dzcDBYMFNxVUIxNFlqNkp5RnN5YWQvbzAiLCJtYWMiOiIyYzgwNTc1YWY0NDQ5ZWFjMWI1MmZmNjM0ZWMzNzliNjhlNjAxMWU3Y2FiOTlhYzU4YWVjMDY5YzNkOWUyMTAyIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:40 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-replacements--replacement_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-replacements--replacement_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-replacements--replacement_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-replacements--replacement_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-replacements--replacement_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-replacements--replacement_id-" data-method="DELETE"
      data-path="api/replacements/{replacement_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-replacements--replacement_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-replacements--replacement_id-"
                    onclick="tryItOut('DELETEapi-replacements--replacement_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-replacements--replacement_id-"
                    onclick="cancelTryOut('DELETEapi-replacements--replacement_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-replacements--replacement_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/replacements/{replacement_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-replacements--replacement_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-replacements--replacement_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-replacements--replacement_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>replacement_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="replacement_id"                data-endpoint="DELETEapi-replacements--replacement_id-"
               value="9"
               data-component="url">
    <br>
<p>The ID of the replacement. Example: <code>9</code></p>
            </div>
                    </form>

                <h1 id="section">Section</h1>

    <p>APIs for managing Section.</p>

                                <h2 id="section-GETapi-sections">GET /admin/api/sections?factory_id=X  - list for dropdowns</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-sections">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/sections" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/sections"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-sections">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InVFOWZaQzdTdFpodEcydkVqam5FQmc9PSIsInZhbHVlIjoiN09aV3BRRk1QRWNTTjUzNFVkQVVVMCtDRTB3NWZOV2FZSGJNbENPZVZhcThjaW9SMWQwbGxGSVh2ZWozc2ppRGdKRGE0VUFkL3lhSzR2K0JFM1RYb2JwZVd0cElNcUNGMXM0RnlIWTYwSXQ5ZG9sQXZPZmE1T2dSMUdEQmtLeTMiLCJtYWMiOiIyNGZhOWIwMTM5YWEwMGJhMjg2NmIyMTNlMjM5MjQ0MThkN2ZlYTY2NzFjZjFiOTA5OTljMWMwYjBjMTdkZTcyIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-sections" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-sections"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-sections"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-sections" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-sections">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-sections" data-method="GET"
      data-path="api/sections"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-sections', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-sections"
                    onclick="tryItOut('GETapi-sections');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-sections"
                    onclick="cancelTryOut('GETapi-sections');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-sections"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/sections</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-sections"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-sections"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-sections"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="section-POSTapi-sections">POST /admin/api/sections</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-sections">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/sections" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"factory_id\": 16,
    \"name\": \"n\",
    \"code\": \"g\",
    \"type\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/sections"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "factory_id": 16,
    "name": "n",
    "code": "g",
    "type": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-sections">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InVkTXRGOE93c0ZyZkQ1eGw0WHkxeWc9PSIsInZhbHVlIjoickNYQnFpZkw4NitqakhNdjVETi9nZ2orejJEZWM3QmVnWmgvVUtaMFpOUUluUGpPUVdlU1pxRGpVNTlPVTA3NFFFWVVqQWtibERYNTZ0dFZkZk9JblJnMTB0T0Z6KzhTek1zMVI2U0VPcU04ZG5McC9jY2RqeGFLOUlUNkdNQUoiLCJtYWMiOiI1MDZhZTJhNWRiM2NlNzVmMTIxYTdjZTk5MDllNjZlZGVkNTE3MzA1ZTM0NDc0NDUyMmIyMGM2NDQ0NTMwZTFhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-sections" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-sections"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-sections"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-sections" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-sections">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-sections" data-method="POST"
      data-path="api/sections"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-sections', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-sections"
                    onclick="tryItOut('POSTapi-sections');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-sections"
                    onclick="cancelTryOut('POSTapi-sections');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-sections"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/sections</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-sections"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-sections"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-sections"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="factory_id"                data-endpoint="POSTapi-sections"
               value="16"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the factories table. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-sections"
               value="n"
               data-component="body">
    <br>
<p>Must not be greater than 150 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="code"                data-endpoint="POSTapi-sections"
               value="g"
               data-component="body">
    <br>
<p>Must not be greater than 50 characters. Example: <code>g</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="POSTapi-sections"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="section-PUTapi-sections--section_id-">PUT /admin/api/sections/{section}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-sections--section_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://192.168.28.152:8000/api/sections/9" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"code\": \"n\",
    \"type\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/sections/9"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "code": "n",
    "type": "architecto"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-sections--section_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Iis2YjZVeHpRVndqVFBTdFFxcXlvZnc9PSIsInZhbHVlIjoiU3NXVjRKaXVXczFoTk5mQTdGZmVLQ3krc2RpWkZ4eDdVQlJDdTlBTWRSSFh4ZVJITEVWY3BLaGxzVk05VzhmVTlaLzl3NllFZ0lEWjJxRFNOSmlHb0xWWnAwTEREV0pTYjJiZHBxUVlXNWhkS2N5SlRBL3ZsWEJqcEFHTnhQZkIiLCJtYWMiOiIwZjYzYjhkZDVhYzE3N2FkZDk1NjQyMDFhMWMzOTE1ZDA4ZmY5ZjA4YjQ0Y2U0NjA0OTU1NzFkNjkzMzc1YjRhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-sections--section_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-sections--section_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-sections--section_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-sections--section_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-sections--section_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-sections--section_id-" data-method="PUT"
      data-path="api/sections/{section_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-sections--section_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-sections--section_id-"
                    onclick="tryItOut('PUTapi-sections--section_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-sections--section_id-"
                    onclick="cancelTryOut('PUTapi-sections--section_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-sections--section_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/sections/{section_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PUTapi-sections--section_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-sections--section_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-sections--section_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>section_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="section_id"                data-endpoint="PUTapi-sections--section_id-"
               value="9"
               data-component="url">
    <br>
<p>The ID of the section. Example: <code>9</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-sections--section_id-"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 150 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="code"                data-endpoint="PUTapi-sections--section_id-"
               value="n"
               data-component="body">
    <br>
<p>Must not be greater than 50 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="PUTapi-sections--section_id-"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="section-DELETEapi-sections--section_id-">DELETE /admin/api/sections/{section}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-sections--section_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/sections/9" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/sections/9"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-sections--section_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6ImJPNHhyU0hCVjY2TWlQZ2c0dVBHSEE9PSIsInZhbHVlIjoicm1kSnIvRk0wTTJrSHNUQWNXUG5aYllLT2F5d0hDUWNuOG9KRlJaZnRkNGVzM0lTZ0lpM1dzWnFmdHU0VUNROE1QVWJ5dFRDa2Ntb08wZVY4NUhkeGhjaURCb0xacjZTVEtOQzhQVCtmUUxORWFRelluZ0hMTWJBQjdnWWVDZ2UiLCJtYWMiOiI0ODBmMTAwMTUzNmM0YWY1ZjhkZTE5NmY5ZDI1MDY0NmRmMmVjMjgwOTY2NDZmNjdmNTI0YTI3NTNlNzc0MDZmIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-sections--section_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-sections--section_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-sections--section_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-sections--section_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-sections--section_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-sections--section_id-" data-method="DELETE"
      data-path="api/sections/{section_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-sections--section_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-sections--section_id-"
                    onclick="tryItOut('DELETEapi-sections--section_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-sections--section_id-"
                    onclick="cancelTryOut('DELETEapi-sections--section_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-sections--section_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/sections/{section_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-sections--section_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-sections--section_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-sections--section_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>section_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="section_id"                data-endpoint="DELETEapi-sections--section_id-"
               value="9"
               data-component="url">
    <br>
<p>The ID of the section. Example: <code>9</code></p>
            </div>
                    </form>

                    <h2 id="section-PATCHapi-sections-reorder">PATCH /admin/api/sections/reorder  - bulk update order_index</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PATCHapi-sections-reorder">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://192.168.28.152:8000/api/sections/reorder" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"order\": [
        {
            \"id\": 16,
            \"order_index\": 16
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/sections/reorder"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "order": [
        {
            "id": 16,
            "order_index": 16
        }
    ]
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-sections-reorder">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6Ik1QOWxmN3VzYlhRc1E1UXlKZjk4aEE9PSIsInZhbHVlIjoicEdBYk12U0ViWk8vNjFYa2hmOEcwSVh6d2lSNkRjQ1FTZ0lZVnM2YTlKMXgvZE5YQTlmWnFhVHNzdGt6YTJnNDIwMTBiWE03UmZtZThYQjROckhnbUdJc3M4aTd6ZTdyMzd4a2t4NldzeUlCVDdlUURtMFZMQWFMaXVxSFIrZk0iLCJtYWMiOiJhNWMyMDRiZmEyZTJhZGJlYzU0YjI1NWRkMjJhZWFiZjdmMTUxZWMwMTk3MWZlMWJhOGM3OGYzYWNkZjdiZGU0IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-sections-reorder" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-sections-reorder"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-sections-reorder"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-sections-reorder" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-sections-reorder">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-sections-reorder" data-method="PATCH"
      data-path="api/sections/reorder"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-sections-reorder', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-sections-reorder"
                    onclick="tryItOut('PATCHapi-sections-reorder');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-sections-reorder"
                    onclick="cancelTryOut('PATCHapi-sections-reorder');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-sections-reorder"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/sections/reorder</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PATCHapi-sections-reorder"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-sections-reorder"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-sections-reorder"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>order</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order.0.id"                data-endpoint="PATCHapi-sections-reorder"
               value="16"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the sections table. Example: <code>16</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>order_index</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order.0.order_index"                data-endpoint="PATCHapi-sections-reorder"
               value="16"
               data-component="body">
    <br>
<p>Example: <code>16</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                <h1 id="status">Status</h1>

    <p>APIs for managing Status.</p>

                                <h2 id="status-GETapi-statuses">GET /admin/api/statuses</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-statuses">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/statuses" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/statuses"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-statuses">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkNPMTNKV0ZQWEtPc0dNS3NkSjhONHc9PSIsInZhbHVlIjoiUW1vWGptMitYVlBHdEl6UXhDc2RjY0JjY3RCWklRNWMxdXRUVjdXUjFJUXdxOGs3Mit2eEY5eTFwT0swL0NFeTd6elJPMXZxRXR6N1V3amJGcDJCVzAwQUZMNXR3eHVjbEJmNjAxMHhzVTZKNVN0U2kvbUFXd2ZZVzhla3RBYjgiLCJtYWMiOiI3OGUxZGNkZGIwNDBjOTE0ZTViOGMxNTUyMjYzZDQwNjNjODUxZDFhNjJmNDFkMGJiMGRiMWFmOTM2ZDMzY2FhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-statuses" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-statuses"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-statuses"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-statuses" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-statuses">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-statuses" data-method="GET"
      data-path="api/statuses"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-statuses', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-statuses"
                    onclick="tryItOut('GETapi-statuses');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-statuses"
                    onclick="cancelTryOut('GETapi-statuses');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-statuses"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/statuses</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-statuses"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="status-POSTapi-statuses">POST /admin/api/statuses</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-statuses">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/statuses" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"key\": \"b\",
    \"label\": \"n\",
    \"icon\": \"gzmiyv\",
    \"color\": \"d\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/statuses"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "key": "b",
    "label": "n",
    "icon": "gzmiyv",
    "color": "d"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-statuses">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkhHTy9aeXQxaVB3VjJtVWJxMDJ6Qmc9PSIsInZhbHVlIjoiQUo0TGNSVlFka3hwUVdCcG1hQmVXZHdWQ0VSdWdSN0IxUUJkZ1FRQTNaUU9xRk8rMnFYRUg2K0FwNENUNUNvTU9VL01yTVo4elUzaFpWVFc3UFNqWnJqY3JqMWpwNERpc2hvZHBsSUo5TGg4c3ZybnE3NGhSbms3SFBQc2lGRVIiLCJtYWMiOiI4YzJlYjA0MzkxZjIxNTUzYjdjZTJmMDE4N2ZlNTc3Yjg0YjcyZjJlMzIxYzQ2OTRhYjZlOTYxOWFmMGY4NzdhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:45 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-statuses" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-statuses"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-statuses"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-statuses" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-statuses">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-statuses" data-method="POST"
      data-path="api/statuses"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-statuses', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-statuses"
                    onclick="tryItOut('POSTapi-statuses');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-statuses"
                    onclick="cancelTryOut('POSTapi-statuses');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-statuses"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/statuses</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-statuses"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>key</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="key"                data-endpoint="POSTapi-statuses"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 50 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="label"                data-endpoint="POSTapi-statuses"
               value="n"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>icon</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="icon"                data-endpoint="POSTapi-statuses"
               value="gzmiyv"
               data-component="body">
    <br>
<p>Must not be greater than 10 characters. Example: <code>gzmiyv</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>color</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="color"                data-endpoint="POSTapi-statuses"
               value="d"
               data-component="body">
    <br>
<p>Must not be greater than 30 characters. Example: <code>d</code></p>
        </div>
        </form>

                    <h2 id="status-PUTapi-statuses--status_id-">PUT /admin/api/statuses/{status}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-statuses--status_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://192.168.28.152:8000/api/statuses/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"label\": \"b\",
    \"icon\": \"ngzmiy\",
    \"color\": \"v\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/statuses/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "label": "b",
    "icon": "ngzmiy",
    "color": "v"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-statuses--status_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IlY5NkVhSDUrSitTYmFmRE9ZSmEwaVE9PSIsInZhbHVlIjoibSs5YUN2RmJhdTg3aHlRVGNkM2RvS2tJaGZ3OHc0bnZvTG8rMTJPOGY4R1Z0R3BYMW5pZ0NSVWU2L3VqeW9XNjhTZUYyZkVheG5SVS84UFc3aE5BQXpvenMxZzNZZENFVit2bVV3YXQ5YzMxUnpWVW1ITVkvMTVmSjY3REQrc1YiLCJtYWMiOiI4YmRmNmNjNzJjY2U1YjQ3NjA4NTA5OGU0NjVkMmNmMmE1YmFiMTcyNDViMzYxNDEyMTNhY2FhYjgyYTU3ZjQ3IiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-statuses--status_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-statuses--status_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-statuses--status_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-statuses--status_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-statuses--status_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-statuses--status_id-" data-method="PUT"
      data-path="api/statuses/{status_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-statuses--status_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-statuses--status_id-"
                    onclick="tryItOut('PUTapi-statuses--status_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-statuses--status_id-"
                    onclick="cancelTryOut('PUTapi-statuses--status_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-statuses--status_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/statuses/{status_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PUTapi-statuses--status_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-statuses--status_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-statuses--status_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="status_id"                data-endpoint="PUTapi-statuses--status_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the status. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>key</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="key"                data-endpoint="PUTapi-statuses--status_id-"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="label"                data-endpoint="PUTapi-statuses--status_id-"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>icon</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="icon"                data-endpoint="PUTapi-statuses--status_id-"
               value="ngzmiy"
               data-component="body">
    <br>
<p>Must not be greater than 10 characters. Example: <code>ngzmiy</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>color</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="color"                data-endpoint="PUTapi-statuses--status_id-"
               value="v"
               data-component="body">
    <br>
<p>Must not be greater than 30 characters. Example: <code>v</code></p>
        </div>
        </form>

                    <h2 id="status-DELETEapi-statuses--status_id-">DELETE /admin/api/statuses/{status}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-statuses--status_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/statuses/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/statuses/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-statuses--status_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InNsajNKVXBWYis0ZEVualdVV1R1K1E9PSIsInZhbHVlIjoicUJlQTZCR3FZa0NtU0l1aXJlYWVvTzMrdndQY2xRUHFyVjBPRjVyZ2E3ajRHQjh2RlIzVk8vNHB1LzJKUld5RjJNbFBHSFBKbytRNEo0c3BWaFZMRXc0VjN1QkFUN3JESEpvYytxUm1IQ3VTRWRUYjQwakNhcmtMeHo3dis2S0EiLCJtYWMiOiI1ZjE4YWI4YjdjYmUyMDJkMjE3Y2QzZGNhYzM2YmYwZGI2YTZhZmYzNzYwMzFlZTYyZjViZGFjNGE0Y2I1YjkwIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-statuses--status_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-statuses--status_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-statuses--status_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-statuses--status_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-statuses--status_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-statuses--status_id-" data-method="DELETE"
      data-path="api/statuses/{status_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-statuses--status_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-statuses--status_id-"
                    onclick="tryItOut('DELETEapi-statuses--status_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-statuses--status_id-"
                    onclick="cancelTryOut('DELETEapi-statuses--status_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-statuses--status_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/statuses/{status_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-statuses--status_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-statuses--status_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-statuses--status_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="status_id"                data-endpoint="DELETEapi-statuses--status_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the status. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="user">User</h1>

    <p>APIs for managing User.</p>

                                <h2 id="user-GETapi-users">GET api/users</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-users">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/users" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/users"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-users">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InYzU1BnUmFjeERLWWhHTlh1Y0dNREE9PSIsInZhbHVlIjoiUmxxOHhlZUd0ajFZa0NpYkVHcGNCM0JRMVZiNEFnMTVjMEUwOHRMYWxXeUUxMmtlR2dYRWNRU2Q1eHcxR2FYTEJtV1hqbjk0RUNCUm00TDhxQVA4dnd4dnZoT0JId3NJTjhWdkgwcjM0RmRGOVEzWUVHay9YK2VIUW95SEt0OGciLCJtYWMiOiI4MDU3YjZkMGQ3NTBhYjRjODEwNDQwYzE5YjQ4NjgwZTZmN2JjODE4ZDI0MzdmM2Q3N2U1NDFjMmM3YjVhZjBmIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-users" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-users"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-users" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-users" data-method="GET"
      data-path="api/users"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-users', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-users"
                    onclick="tryItOut('GETapi-users');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-users"
                    onclick="cancelTryOut('GETapi-users');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-users"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/users</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-users"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-users"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-users"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="user-GETapi-users--user_id-">GET api/users/{user_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-users--user_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://192.168.28.152:8000/api/users/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/users/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-users--user_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IkZ6R1hzVjdCRXdWV2RkWG8vVXh3dXc9PSIsInZhbHVlIjoiUlF3T0YzUXN4TXRvU1RVVnM4dmlNTVlQM1BjUjdGQzNwdnExU0N2cHdQR1cvcFNERUlqK1V0a1o1WGdyY1FVUzdnZ0Irc3BsYTZna0RzZGhkUVRURzJkOFJvbWdBQ1JOMjBxak5FVGlMRktjdEYwUnB0RDY3OWJJZ0x4VmJkOXgiLCJtYWMiOiJhNTE5ODJlMDViMWNkNzAxNjhjYzc0MDQyYzU4ZTcyMzUzOTQ3YzRiMTgxODg1NDE5MGNlN2MzYjBhNWIyNzNiIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-users--user_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-users--user_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users--user_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-users--user_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users--user_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-users--user_id-" data-method="GET"
      data-path="api/users/{user_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-users--user_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-users--user_id-"
                    onclick="tryItOut('GETapi-users--user_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-users--user_id-"
                    onclick="cancelTryOut('GETapi-users--user_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-users--user_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/users/{user_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="GETapi-users--user_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-users--user_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-users--user_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>user_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="user_id"                data-endpoint="GETapi-users--user_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="user-POSTapi-users">POST api/users</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-users">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://192.168.28.152:8000/api/users" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"username\": \"n\",
    \"password\": \"|{+-0pBNvYgx\",
    \"role\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/users"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "username": "n",
    "password": "|{+-0pBNvYgx",
    "role": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-users">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6InVlcTNYdXpEd2dUY3B2YmNjaU1sUlE9PSIsInZhbHVlIjoiVjdleHJyZTNlMmxtTGZHK0FHNXUxbXVtL2Z5WE9lck5oZDFCSk5rZXlPZmFtOXUxenFIdWNZZ0w2cngwYXJ4QjRyR04va3loeHNrUlNEYyswU2JvRHJWSm50aUs1b1pja2lDUDNzcjNia2IxWGQrZHJiWVdwUzVOcFV3Nm0rTGoiLCJtYWMiOiI2ZjQ3NDE4ZDQ4ZWY2OTY2OWVkOTE4YWQ1ZWY3Yzc3ZDEwYWJlZDM1YjgzYTFhZDljMTEzODgzNmY0MjM1ZGNhIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-users" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-users"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-users"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-users" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-users">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-users" data-method="POST"
      data-path="api/users"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-users', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-users"
                    onclick="tryItOut('POSTapi-users');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-users"
                    onclick="cancelTryOut('POSTapi-users');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-users"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/users</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="POSTapi-users"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-users"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-users"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-users"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>username</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="username"                data-endpoint="POSTapi-users"
               value="n"
               data-component="body">
    <br>
<p>Must contain only letters, numbers, dashes and underscores. Must not be greater than 50 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-users"
               value="|{+-0pBNvYgx"
               data-component="body">
    <br>
<p>Must be at least 6 characters. Example: <code>|{+-0pBNvYgx</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>role</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="role"                data-endpoint="POSTapi-users"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="POSTapi-users"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="POSTapi-users"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="user-PUTapi-users--user_id-">PUT api/users/{user_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-users--user_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://192.168.28.152:8000/api/users/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"username\": \"n\",
    \"role\": \"architecto\",
    \"password\": \"]|{+-0pBNvYg\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/users/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "username": "n",
    "role": "architecto",
    "password": "]|{+-0pBNvYg"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-users--user_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6IjhQa0xGeXV0ZGJkNjQ0bzIxMEl1d2c9PSIsInZhbHVlIjoiWEwzcjc0bWF1czRVcWV4eCtocXdOempKeWw0QjBMWmxHcU5ZSFg0WDR6TTVFS2M4TzhZQzRYdHh0T1lMSmZjS051cjZSSjI0SmpJZWdkb3hDbHR5Y2ZoT3R0R25UdUlPbjlyNHpyZXdsclYxbGtjUGJ1VjFLbG9IRDk2clZIT2IiLCJtYWMiOiI0Y2I4OGJmNThhOTA2MWY0ZGU2OTlhZmNmYmM0OWE1MmVkZmI1MGRiNGI3YmVjZmRiNDdmNGVhNDg2NDYzMTQwIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-users--user_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-users--user_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-users--user_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-users--user_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-users--user_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-users--user_id-" data-method="PUT"
      data-path="api/users/{user_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-users--user_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-users--user_id-"
                    onclick="tryItOut('PUTapi-users--user_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-users--user_id-"
                    onclick="cancelTryOut('PUTapi-users--user_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-users--user_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/users/{user_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="PUTapi-users--user_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-users--user_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-users--user_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>user_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="user_id"                data-endpoint="PUTapi-users--user_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-users--user_id-"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 100 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>username</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="username"                data-endpoint="PUTapi-users--user_id-"
               value="n"
               data-component="body">
    <br>
<p>Must contain only letters, numbers, dashes and underscores. Must not be greater than 50 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>role</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="role"                data-endpoint="PUTapi-users--user_id-"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="PUTapi-users--user_id-"
               value="]|{+-0pBNvYg"
               data-component="body">
    <br>
<p>Must be at least 6 characters. Example: <code>]|{+-0pBNvYg</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>factory</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="factory"                data-endpoint="PUTapi-users--user_id-"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shift</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="shift"                data-endpoint="PUTapi-users--user_id-"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="user-DELETEapi-users--user_id-">DELETE api/users/{user_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-DELETEapi-users--user_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://192.168.28.152:8000/api/users/1" \
    --header "X-App-Secret: {HMAC_SHA256_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://192.168.28.152:8000/api/users/1"
);

const headers = {
    "X-App-Secret": "{HMAC_SHA256_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-users--user_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
set-cookie: henkaten-session=eyJpdiI6ImkwUkF5RExhU3NpVmh4dE5vbjlPa0E9PSIsInZhbHVlIjoidjl3aCt6MTJUcVNudWNHUVY0a0p6OXoyYUZxakxpUWlRVmYvajhXUXVacE9DMzJNc0tvKzNaNUlNU1ppdUFZNm44MzlhcW1MT3I1RlYwZERLaERzTHY5MVFISUJxcUYyQ2pzdnNZQldkTjFhVi9xYUNZT1NsR1Yxclh6QzA1MXgiLCJtYWMiOiJjZGMxYzNhZDA1ZGQ2ZTA2OWU1N2UwY2I2MjNlYThhMDM3NmUyZjg4MTNiZDVlNTAyY2E5NTk1MDNiYjgyNDBjIiwidGFnIjoiIn0%3D; expires=Wed, 20 May 2026 07:58:46 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-users--user_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-users--user_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-users--user_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-users--user_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-users--user_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-users--user_id-" data-method="DELETE"
      data-path="api/users/{user_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-users--user_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-users--user_id-"
                    onclick="tryItOut('DELETEapi-users--user_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-users--user_id-"
                    onclick="cancelTryOut('DELETEapi-users--user_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-users--user_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/users/{user_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-App-Secret</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-App-Secret" class="auth-value"               data-endpoint="DELETEapi-users--user_id-"
               value="{HMAC_SHA256_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>{HMAC_SHA256_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-users--user_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-users--user_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>user_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="user_id"                data-endpoint="DELETEapi-users--user_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
