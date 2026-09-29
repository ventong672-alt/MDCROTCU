<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROTC Management Information System</title>

    <link rel="stylesheet" href="style.css?v=20260916-officer-glass-1">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
.cadet-record-modal {
    width: min(900px, 95vw);
    max-height: 90vh;
    overflow-y: auto;
}

.cadet-record-box {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 20px 0;
}

.record-box {
    border: 1px solid #dfe5ec;
    border-radius: 12px;
    background: #fff;
    padding: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.record-box-title {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .5px;
    color: #1f2937;
    padding-bottom: 12px;
    margin-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.record-info-grid,
.record-form-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.record-info-grid > div {
    background: #f7f9fc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
}

.record-info-grid span {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 5px;
}

.record-info-grid strong {
    font-size: 14px;
    color: #111827;
}

.record-box textarea {
    width: 100%;
    box-sizing: border-box;
    resize: vertical;
}

.platoon-assignment-action {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 190px;
}

.platoon-assignment-action select {
    min-width: 105px;
    padding: 7px 8px;
}

.platoon-assignment-action .primary-btn {
    white-space: nowrap;
    padding: 7px 10px;
}

@media (max-width: 700px) {
    .record-info-grid,
    .record-form-grid {
        grid-template-columns: 1fr;
    }

    .cadet-record-modal {
        width: 94vw;
    }
}

.modal-overlay.show {
    display: flex;
}

#officerLogoutModal .modal {
    display: block;
    position: relative;
    width: min(420px, calc(100vw - 32px));
    height: auto;
    margin: 0;
    padding: 28px;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 24px 60px rgba(0, 0, 0, .24);
    text-align: center;
}

#officerLogoutModal .modal-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    border-radius: 50%;
    background: #ffe5e5;
    color: #dc2626;
    font-size: 22px;
}

#officerLogoutModal h2 { margin: 0 0 8px; color: #202b24; font-size: 18px; }
#officerLogoutModal p { margin: 0 0 25px; color: #718096; font-size: 11px; line-height: 1.5; }
#officerLogoutModal .modal-actions { display: flex; justify-content: center; gap: 10px; }
#officerLogoutModal .secondary-btn,
#officerLogoutModal .danger-btn { border: 0; padding: 10px 18px; border-radius: 6px; font-size: 11px; cursor: pointer; }
#officerLogoutModal .secondary-btn { color: #4b5563; background: #edf0ed; }
#officerLogoutModal .danger-btn { color: #fff; background: #d94b4b; }

.page-heading-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.total-cadets-modal {
    width: min(1100px, 96vw);
    max-height: 85vh;
    overflow-y: auto;
}

.total-cadets-modal .table-container {
    overflow-x: auto;
}

.total-cadets-modal table {
    min-width: 980px;
}

.report-preview-modal {
    width: min(1080px, 96vw);
    max-height: 88vh;
    display: flex;
    flex-direction: column;
    padding: 24px;
    border: 1px solid rgba(255,255,255,.82);
    border-radius: 16px;
    background: rgba(255,255,255,.76);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    box-shadow: 0 25px 70px rgba(8,42,25,.25);
}

.report-form-modal {
    border: 1px solid rgba(255,255,255,.82);
    background: rgba(255,255,255,.76);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    box-shadow: 0 25px 70px rgba(8,42,25,.25);
}

.report-preview-modal .modal-header {
    margin-bottom: 14px;
    padding-bottom: 14px;
    border-bottom: 1px solid #e8eee9;
}

.report-preview-modal .modal-header h2 {
    margin: 0;
    color: #193b28;
    font-size: 17px;
}

.report-preview-modal .modal-body {
    min-height: 0;
    overflow: hidden;
}

.report-preview-table-wrap {
    max-height: 56vh;
    overflow: auto;
    border-color: #dfe8e1;
    border-radius: 10px;
}

.report-preview-table-wrap table {
    min-width: 720px;
}

.report-preview-table-wrap th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #edf5ef;
    color: #31543d;
    font-size: 9px;
}

.report-preview-table-wrap td {
    color: #33443a;
    font-size: 10px;
}

.report-preview-table-wrap tbody tr:nth-child(even) {
    background: #fafcfb;
}

.report-preview-modal .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid #e8eee9;
}

.report-preview-modal .modal-footer button:disabled {
    cursor: not-allowed;
    opacity: .5;
}

@media (max-width: 700px) {
    .report-preview-modal {
        width: 96vw;
        padding: 16px;
    }

    .report-preview-table-wrap {
        max-height: 62vh;
    }

    .report-preview-modal .modal-footer {
        flex-direction: column-reverse;
    }

    .report-preview-modal .modal-footer button {
        width: 100%;
    }
}

/* ===== NEW CADET RECORD / MERITS DESIGN ===== */
.record-modal-header p {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 13px;
}

.cadet-profile-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px;
    margin: 4px 0 18px;
    border: 1px solid #e3e8ef;
    border-radius: 16px;
    background: linear-gradient(135deg, #f7faff, #ffffff);
}

.cadet-profile-avatar {
    width: 62px;
    height: 62px;
    flex: 0 0 62px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: #173b67;
    color: #fff;
    font-weight: 800;
    font-size: 20px;
}

.cadet-profile-main {
    flex: 1;
    min-width: 0;
}

.record-label {
    display: block;
    color: #6b7280;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
}

.cadet-profile-main h2 {
    margin: 3px 0 8px;
    font-size: 20px;
}

.cadet-profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.cadet-profile-meta span {
    padding: 5px 9px;
    border-radius: 999px;
    background: #eef3f8;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
}

.instruction-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 12px;
    border-left: 3px solid #d6ae42;
    border-radius: 8px;
    background: #fffaf0;
    color: #334155;
}

.instruction-item + .instruction-item {
    margin-top: 8px;
}

.instruction-item small {
    color: #64748b;
    font-size: 10px;
}

.instruction-attachment {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 9px;
    padding: 8px 10px;
    border: 1px solid #dce8df;
    border-radius: 8px;
    background: #f7fbf8;
    font-size: 10px;
}

.instruction-attachment span {
    flex: 1;
    overflow: hidden;
    color: #405247;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.instruction-attachment a {
    color: #1f6b3c;
    font-weight: 800;
    text-decoration: none;
}

.training-modal {
    width: min(620px, 100%);
}

.training-modal-body {
    padding: 4px 0 2px;
}

.training-modal-intro {
    margin: -5px 0 18px;
    color: #64748b;
    font-size: 11px;
    line-height: 1.5;
}

.training-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.training-form-grid label {
    display: flex;
    flex-direction: column;
    gap: 7px;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
}

.training-form-grid input,
.training-form-grid select {
    width: 100%;
    min-height: 42px;
    box-sizing: border-box;
    padding: 10px 11px;
    border: 1px solid #d8e0e8;
    border-radius: 9px;
    background: #f8fafc;
    color: #1e293b;
    font: inherit;
    font-size: 12px;
    outline: none;
}

.training-form-grid input:focus,
.training-form-grid select:focus {
    border-color: #2d7650;
    box-shadow: 0 0 0 3px rgba(45, 118, 80, .12);
    background: #fff;
}

.training-field-wide {
    grid-column: 1 / -1;
}

@media (max-width: 600px) {
    .training-form-grid {
        grid-template-columns: 1fr;
        gap: 13px;
    }

    .training-field-wide {
        grid-column: auto;
    }
}

.record-status-pill {
    padding: 7px 11px;
    border-radius: 999px;
    background: #e9f8ef;
    color: #18733d;
    font-size: 12px;
    font-weight: 700;
}

.record-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.record-summary-box {
    padding: 14px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
}

.record-summary-box span,
.record-summary-box small {
    display: block;
    color: #6b7280;
}

.record-summary-box span {
    font-size: 11px;
    font-weight: 600;
}

.record-summary-box strong {
    display: block;
    margin: 5px 0 2px;
    font-size: 23px;
    color: #111827;
}

.record-summary-box small {
    font-size: 10px;
}

.merit-summary strong { color: #16834b; }
.demerit-summary strong { color: #c0392b; }

.record-history-box .record-box-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mini-history {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.history-row {
    display: grid;
    grid-template-columns: 90px 90px 1fr auto;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: 1px solid #edf0f4;
    border-radius: 9px;
    background: #fafbfc;
    font-size: 12px;
}

.history-type {
    font-weight: 800;
}

.history-type.merit { color: #16834b; }
.history-type.demerit { color: #c0392b; }

.conduct-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
}

.conduct-badge.merit {
    background: #e8f7ee;
    color: #18733d;
}

.conduct-badge.demerit {
    background: #fdebea;
    color: #b42318;
}

@media (max-width: 850px) {
    .record-summary-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
    .cadet-profile-card { align-items: flex-start; flex-wrap: wrap; }
    .record-status-pill { margin-left: 78px; }
    .record-summary-grid { grid-template-columns: 1fr 1fr; }
    .history-row { grid-template-columns: 1fr 1fr; }
}

</style>



</head>

<body class="officer-dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <div class="brand">
            <div class="brand-logo">
    <img src="ROTCLOGO.png" alt="MDC ROTC Unit Logo">
</div>
            <div>
                <h2>ARMY ROTC MIS</h2>
                <span>Management System</span>
            </div>
        </div>

        <nav>
            <p class="menu-title">MAIN MENU</p>
            <a href="#" class="nav-link active" data-page="dashboard">
                 <span></span> DASHBOARD
            </a>

            <a href="#" class="nav-link" data-page="scan">
                <span>⌕</span> Scan Cadet
            </a><br>

            <a href="#" class="nav-link" data-page="approvals">
                <span>✓</span> Cadet Approvals
            </a><br>
            <a href="#" class="nav-link" data-page="attendance">
                <span>✓</span> Attendance
            </a><br>

            

            <a href="#" class="nav-link" data-page="announcements">
                <span>!</span> Announcements/Training Schedules
            </a><br>

            <a href="#" class="nav-link" data-page="grades">
                <span>★</span> Cadet Grades
            </a><br>

            <a href="#" class="nav-link" data-page="conduct">
                <span>✦</span> Merits & Demerits
            </a><br>

            
            <a href="#" class="nav-link" data-page="reports">
                <span>▤</span> Reports
            </a><br>

            <a href="#" class="nav-link" data-page="roster">
                <span>✓</span> Roster
            </a><br>

            <p class="menu-title">SYSTEM</p>

            <a href="#" class="nav-link" data-page="users">
                <span>⚙</span> User Management
            </a><br><br>

        </nav>
            </div>
                </div>
            </div>

            <button class="logout-btn" id="logoutBtn" type="button">
                ⇥ Logout
            </button>
        </div>

    </aside>

    <div class="modal-overlay" id="officerLogoutModal">
        <div class="modal">
            <div class="modal-icon">
                ⇥
            </div>

            <h2>Logout?</h2>

            <p>
                Are you sure you want to logout from the Officer Portal?
            </p>

            <div class="modal-actions">
                <button class="secondary-btn" id="cancelOfficerLogout" type="button">
                    Cancel
                </button>
                <button class="danger-btn" id="confirmOfficerLogout" type="button">
                    Logout
                </button>
            </div>
        </div>
    </div>


    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">

            <button class="menu-btn" id="menuBtn">☰</button>

            <div>
                <p id="pageDescription">
                    Overview of ROTC activities and records
                </p>
            </div>

            <div class="topbar-right">
                <button class="notification" id="notificationBtn">
                    🔔
                    <span class="notification-count" id="notificationCount">0</span>
                </button>

                <div class="profile">
                    <div class="avatar">O</div>
                    <div>
                        <strong>Officer Dashboard</strong>
                        <small>Officer</small>
                    </div>
                </div>
            </div>

        </header>


        <!-- PAGE CONTENT -->
        <section class="content" id="content">

            <!-- DASHBOARD -->
            <div class="page" id="scan">
                <div class="page-heading">
                    <div>
                        <h2>Scan & Search Cadets</h2>
                        <p>Find a cadet record by QR code, ID, email, or name.</p>
                    </div>
                </div>

                <div class="card scan-card">
                    <div class="card-header">
                        <div>
                            <h3>Cadet lookup</h3>
                            <p>Search the active cadet records available to officers.</p>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <select id="officerScanFormationSelect" onchange="syncOfficerScanFormation(this.value)" aria-label="Formation for QR scan"></select>
                            <select id="officerScanPeriodSelect" onchange="syncOfficerScanPeriod(this.value)" aria-label="Period for QR scan"></select>
                            <select id="attendanceSessionTypeInput" onchange="renderAttendanceRoster()" aria-label="Formation number" hidden></select>
                            <select id="attendanceSessionPeriodInput" onchange="renderAttendanceRoster()" aria-label="Formation period" hidden></select>
                            <select id="attendanceCompanyFilter" aria-label="Company filter" hidden><option value="">All Companies</option></select>
                            <select id="attendancePlatoonFilter" aria-label="Platoon filter" hidden><option value="">All Platoons</option></select>
                            <button class="secondary-btn" type="button" onclick="openOfficerScanner('attendance')">▣ Start QR Scan</button>
                            <button class="text-btn" id="stopOfficerScanner" type="button" onclick="stopCadetScanner()" hidden>Stop Camera</button>
                        </div>
                    </div>
                    <div class="toolbar scan-toolbar">
                        <input type="search" id="officerSearchInput" placeholder="Cadet ID, email, or full name" onkeydown="if (event.key === 'Enter') searchOfficerCadet();">
                        <button class="primary-btn" type="button" onclick="searchOfficerCadet()">Search</button>
                    </div>
                    <div id="officerQrReader" class="qr-reader" hidden></div>
                    <div id="officerScanResults" class="scan-results" aria-live="polite">
                        <p class="scan-empty">Search for a cadet or start a QR scan.</p>
                    </div>
                </div>
            </div>

            <!-- DASHBOARD -->
            <div class="page active-page" id="dashboard">

                <div class="welcome">
                    <div>
                        <h2>Welcome, <span id="officerWelcomeName">Officer</span>!</h2>
                        <p>Here's what's happening with your ROTC records today.</p>
                    </div>
                    <div class="page-heading-actions">
                        <button class="primary-btn" type="button" id="dashboardTotalCadetsButton" onclick="openTotalCadetsModal()">Unassigned Cadets</button>
                    </div>
                </div>

                <div class="card" id="officerInstructionsCard" hidden>
                    <div class="card-header">
                        <div>
                            <h3>Admin Instructions</h3>
                            <p>Messages sent to you by the administrator.</p>
                        </div>
                    </div>
                    <div id="officerInstructionsList"></div>
                </div>


                <!-- STATISTICS -->
                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-icon blue">♙</div>
                        <div>
                            <span>Total Cadets</span>
                            <h2 id="totalCadets">0</h2>
                            <small>Registered cadets</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">✓</div>
                        <div>
                            <span>Present Today</span>
                            <h2 id="presentCount"></h2>
                            <small>Attendance recorded</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">▣</div>
                        <div>
                            <span>Upcoming Training</span>
                            <h2 id="upcomingTrainingCount">0</h2>
                            <small>Scheduled sessions</small>
                        </div>
                    </div>

                </div>


                <!-- DASHBOARD GRID -->
                <div class="dashboard-grid">

                    <!-- ATTENDANCE -->
                    <div class="card">

                        <div class="card-header">
                            <div>
                                <h3>Attendance Overview</h3>
                                <p>Weekly attendance status</p>
                            </div>
                        </div>

                        <div class="attendance-overview">
                            <div class="attendance-summary">
                                <div class="attendance-summary-header">
                                    <div class="attendance-summary-heading">
                                        <span class="attendance-summary-icon" aria-hidden="true">✓</span>
                                        <div>
                                            <div class="attendance-summary-title">Attendance Rate</div>
                                            <div class="attendance-summary-subtitle">Weekly attendance status</div>
                                        </div>
                                    </div>
                                    <div class="attendance-summary-rate">
                                        <div class="attendance-summary-value" id="attendanceOverviewPercent">0%</div>
                                        <span>Present</span>
                                    </div>
                                </div>
                                <div class="attendance-bar-wrap">
                                    <div class="attendance-bar-track">
                                        <div class="attendance-bar-fill" id="attendanceBarFill"></div>
                                    </div>
                                </div>
                                <div class="attendance-summary-details">
                                    <div class="attendance-summary-row attendance-present-row">
                                        <span class="attendance-detail-icon" aria-hidden="true">✓</span>
                                        <div>
                                            <strong>Present</strong>
                                            <small>Cadets who attended the activity</small>
                                        </div>
                                        <div class="attendance-summary-count">
                                            <strong id="attendanceOverviewPresent">0</strong>
                                            <span>of <span id="attendanceOverviewPresentTotal">0</span></span>
                                        </div>
                                    </div>
                                    <div class="attendance-summary-row attendance-absent-row">
                                        <span class="attendance-detail-icon" aria-hidden="true">×</span>
                                        <div>
                                            <strong>Absent</strong>
                                            <small>Cadets who did not attend the activity</small>
                                        </div>
                                        <div class="attendance-summary-count">
                                            <strong id="attendanceOverviewAbsent">0</strong>
                                            <span>of <span id="attendanceOverviewAbsentTotal">0</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- UPCOMING TRAINING -->
                    <div class="card">

                        <div class="card-header">
                            <div>
                                <h3>Upcoming Training</h3>
                                <p>Scheduled ROTC activities</p>
                            </div>

                            <button class="text-btn" data-page-link="training">
                                View All
                            </button>
                        </div>

                        <div class="training-list" id="upcomingTrainingList">
                            <p class="scan-empty">No registered training schedules.</p>
                        </div>

                    </div>

                </div>


                <!-- RECENT ACTIVITIES -->
                <div class="card">

                    <div class="card-header">
                        <div>
                            <h3>Recent Activities</h3>
                            <p>Latest system activities</p>
                        </div>

                        <button class="text-btn">View All</button>
                    </div>

                    <div class="activity-list" id="recentActivitiesList">
                        <p class="scan-empty">No registered activities available.</p>

                    </div>

                </div>

            </div>

            <!-- CADET APPROVALS -->
            <div class="page" id="approvals">
                <div class="page-heading">
                    <div>
                        <h2>Cadet Approvals</h2>
                        <p>Review cadets who registered through the cadet registration form.</p>
                    </div>
                    <div class="page-heading-actions">
                        <button type="button" class="secondary-btn" id="viewTotalCadets">Unassigned Cadets</button>
                        <button type="button" class="secondary-btn" id="refreshPendingCadets">Refresh</button>
                    </div>
                </div>

                <div class="card">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cadet ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="pendingCadetsBody">
                                <tr><td colspan="6">Loading pending registrations...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="totalCadetsModal">
                <div class="modal total-cadets-modal">
                    <div class="modal-header">
                        <div>
                            <h2>Unassigned Cadets</h2>
                            <p>Cadets without a company or platoon assignment.</p>
                        </div>
                        <button type="button" onclick="closeModal('totalCadetsModal')">×</button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr><th>Cadet ID</th><th>Name</th><th>Year</th><th>Company</th><th>Platoon</th><th>Action</th></tr>
                            </thead>
                            <tbody id="totalCadetsBody">
                                <tr><td colspan="6">Loading cadets...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ROSTER -->
            <div class="page" id="roster">

                <div class="page-heading">
                    <div>
                        <h2>Cadet Roster</h2>
                        <p>View battalion-level cadet assignments and platoon rosters.</p>
                    </div>

                    <div class="page-heading-actions">
                        <button class="secondary-btn" type="button" onclick="openRosterView('battalion')">Battalion</button>
                        <button class="secondary-btn" type="button" onclick="openRosterView('company')">Company</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 id="rosterViewTitle">Battalion Roster</h3>
                            <p id="rosterViewSubtitle">Cadets grouped by battalion assignment.</p>
                        </div>
                    </div>

                    <div id="rosterList" class="training-cards">
                        <p class="scan-empty">Loading roster...</p>
                    </div>
                </div>

            </div>

            <!-- ATTENDANCE -->
            <div class="page" id="attendance">

                <div class="page-heading">
                    <div>
                        <h2>Attendance Management</h2>
                        <p>Morning: scan in 6:40-8:00 AM Present or 8:00-8:30 AM Late, then scan out 11:00 AM-12:00 PM. Afternoon: scan in 12:40-1:30 PM Present or 1:30-2:00 PM Late, then scan out 4:30-5:30 PM.</p>
                    </div>

                    <div>
                        <button class="secondary-btn" type="button" onclick="loadAttendanceRoster()">↻ Refresh Roster</button>
                    </div>
                </div>

                <div class="card attendance-scan-action" id="attendanceScanAction" hidden></div>

                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-icon green">✓</div>
                        <div>
                            <span>Present</span>
                            <h2 id="attendancePresentCount">0</h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">×</div>
                        <div>
                            <span>Absent</span>
                            <h2 id="attendanceAbsentCount">0</h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">!</div>
                        <div>
                            <span>Late</span>
                            <h2 id="attendanceLateCount">0</h2>
                        </div>
                    </div>

                </div>

                <div class="card">

                    <div class="card-header">
                        <div>
                            <h3>Attendance Records</h3>
                            <p id="attendanceSessionDate">Formation session</p>
                        </div>
                    </div>

                    <div id="attendanceRecordsBody" class="attendance-battalion-grid">
                        <p class="scan-empty">Load the roster to begin attendance.</p>
                    </div>

                </div>

            </div>


            <!-- TRAINING -->
            <div class="page" id="training">

                <div class="page-heading">

                    <div>
                        <h2>Training Schedule</h2>
                        <p>Organize and maintain ROTC training activities.</p>
                    </div>

                    <button class="primary-btn" type="button" onclick="openTrainingModal()">
                        + Add Training
                    </button>

                </div>

                <div class="training-cards" id="trainingScheduleList">
                    <p class="scan-empty">No registered training schedules.</p>
                </div>

            </div>

            <!-- ANNOUNCEMENTS -->
            <div class="page" id="announcements">
                <div class="page-heading">
                    <div>
                        <h2>Announcements / Training Schedules</h2>
                        <p>Publish important ROTC updates and review scheduled training activities.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Training Schedules</h3>
                            <p>Training schedules are also published as 5W1H announcements.</p>
                        </div>
                        <button class="primary-btn" type="button" onclick="openTrainingModal()">+ Add Training</button>
                    </div>
                    <div class="training-cards" id="announcementTrainingScheduleList">
                        <p class="scan-empty">No registered training schedules.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>New Announcement</h3>
                            <p>Cadets will see published announcements in their portal.</p>
                        </div>
                    </div>
                    <div class="training-form-grid">
                        <label class="training-field-wide"><span>Title</span><input type="text" id="announcementTitleInput" maxlength="150" placeholder="Announcement title"></label>
                        <label class="training-field-wide"><span>Message</span><textarea id="announcementMessageInput" rows="5" maxlength="2000" placeholder="Write the announcement..."></textarea></label>
                        <label class="training-field-wide"><span>Attachment</span><input type="file" id="announcementFileInput" accept="*/*"></label>
                        <small style="display:block; margin-top:-8px; color:#64748b;">Maximum file size: 50 MB.</small>
                    </div>
                    <div class="modal-actions">
                        <button class="primary-btn" type="button" onclick="publishAnnouncement()">Publish Announcement</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Published Announcements</h3>
                            <p>Cadets can view these announcements in their portal.</p>
                        </div>
                    </div>
                    <div id="officerAnnouncementsList">
                        <p class="scan-empty">Loading announcements...</p>
                    </div>
                </div>

            </div>

            <div class="modal-overlay" id="trainingModal">
                <div class="modal training-modal">
                    <div class="modal-header">
                        <h2 id="trainingModalTitle">Add Training Session</h2>
                        <button class="modal-close" type="button" onclick="closeModal('trainingModal')">&times;</button>
                    </div>
                    <div class="training-modal-body">
                        <p class="training-modal-intro">Add a schedule that will be visible to cadets and administrators.</p>
                        <div class="training-form-grid">
                            <label><span>Date</span><input type="date" id="trainingDateInput" required></label>
                            <label><span>Training type</span><input type="text" id="trainingTypeInput" placeholder="e.g. Field Training" maxlength="80" required></label>
                            <label class="training-field-wide"><span>Title</span><input type="text" id="trainingTitleInput" placeholder="e.g. Weekly formation" required></label>
                            <label class="training-field-wide"><span>Location</span><input type="text" id="trainingLocationInput" placeholder="e.g. Training ground" required></label>
                            <label class="training-field-wide"><span>Instruction</span><textarea id="trainingInstructionInput" rows="3" placeholder="Enter instructions for cadets"></textarea></label>
                            <label><span>Start time</span><input type="time" id="trainingStartInput" required></label>
                            <label><span>End time</span><input type="time" id="trainingEndInput" required></label>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="secondary-btn" type="button" onclick="closeModal('trainingModal')">Cancel</button>
                        <button class="primary-btn" id="trainingSaveButton" type="button" onclick="createTrainingSession()">Save Training</button>
                    </div>
                </div>
            </div>


            <!-- GRADES -->
            <div class="page" id="grades">

                <div class="page-heading">

                    <div>
                        <h2>Evaluation of Grades </h2>
                        <p>Manage cadet ROTC subject performance, professional evaluation, and recommended action.</p>
                    </div>

                    <div class="grade-actions">
                        <button class="primary-btn" type="button" onclick="openGradeModal()">
                            + Add Evaluation
                        </button>
                        <button class="secondary-btn" type="button" onclick="openWeightedEvaluationModal()">
                            Weighted Evaluation
                        </button>
                    </div>

                </div>

                <div id="weightedEvaluationCards" class="weighted-evaluation-cards" aria-live="polite"></div>

                <div class="card">

                    <div class="toolbar">

                        <input type="text" placeholder="Search cadet...">

                        <select>
                            <option>All Training</option>
                            <option>Military Science</option>
                            <option>Leadership</option>
                            <option>Physical Training</option>
                        </select>

                    </div>

                    <div class="table-container">

                        <table>

                            <thead>
                                <tr>
                                    <th>Cadet ID</th>
                                    <th>Name</th>
                                    <th>Semester</th>
                                    <th>Prelim</th>
                                    <th>Midterm</th>
                                    <th>Finals</th>
                                    <th>Average</th>
                                    <th>Evaluation</th>
                                </tr>
                            </thead>

                            <tbody id="gradesRecordsBody">
                                <tr><td colspan="8">No grade records available.</td></tr>
                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="card weighted-evaluations-card">
                    <div class="card-header">
                        <div>
                            <h3>Weighted Evaluations</h3>
                            <p>View the configured component scores and final weighted grade for each cadet.</p>
                        </div>
                        <button class="action-btn" type="button" onclick="loadWeightedEvaluations()">Refresh</button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>Cadet ID</th><th>Name</th><th>Semester</th><th>Components</th><th>Final Grade</th></tr></thead>
                            <tbody id="weightedEvaluationsBody"><tr><td colspan="5">Loading weighted evaluations...</td></tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="modal-overlay" id="gradeModal">
                <div class="modal grade-modal">
                    <div class="modal-header">
                        <h2>Add ROTC Grade Evaluation</h2>
                        <button class="modal-close" type="button" onclick="closeModal('gradeModal')">&times;</button>
                    </div>
                    <div class="training-modal-body">
                        <p class="training-modal-intro">Record ROTC evaluation details for an approved cadet.</p>
                        <div class="training-form-grid">
                            <label class="training-field-wide"><span>Cadet</span><select id="gradeCadetInput" required></select></label>
                            <label class="training-field-wide"><span>Subject</span><input type="text" id="gradeSubjectInput" value="ROTC" readonly></label>
                            <label><span>Semester</span><select id="gradeSemesterInput"><option>First Semester</option><option>Second Semester</option></select></label>
                            <label><span>Exam term</span><select id="gradeTermInput"><option>Prelim</option><option>Midterm</option><option>Finals</option></select></label>
                            <label class="training-field-wide"><span>Score (0-100)</span><input type="number" id="gradeScoreInput" min="0" max="100" step="0.01" placeholder="Enter score" required></label>
                            <label class="training-field-wide"><span>Professional Evaluation</span><select id="gradeEvaluationInput"><option>Outstanding</option><option>Very Satisfactory</option><option>Satisfactory</option><option>Needs Improvement</option><option>Unsatisfactory</option></select></label>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="secondary-btn" type="button" onclick="closeModal('gradeModal')">Cancel</button>
                        <button class="primary-btn" type="button" onclick="saveOfficerGrade()">Save Evaluation</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="weightedEvaluationModal">
                <div class="modal grade-modal">
                    <div class="modal-header">
                        <h2>Weighted ROTC Evaluation</h2>
                        <button class="modal-close" type="button" onclick="closeModal('weightedEvaluationModal')">&times;</button>
                    </div>
                    <div class="training-modal-body">
                        <p class="training-modal-intro">Enter component scores. The final grade uses the configured weights and does not change subject grades.</p>
                        <div class="training-form-grid">
                            <label class="training-field-wide"><span>Cadet</span><select id="weightedCadetInput" required></select></label>
                            <label class="training-field-wide"><span>Semester</span><select id="weightedSemesterInput"><option>First Semester</option><option>Second Semester</option></select></label>
                        </div>
                        <div id="weightedComponentsGrid" class="training-form-grid"></div>
                        <div id="weightedGradePreview" class="weighted-grade-preview">Final Grade: —</div>
                    </div>
                    <div class="modal-actions">
                        <button class="secondary-btn" type="button" onclick="closeModal('weightedEvaluationModal')">Cancel</button>
                        <button class="primary-btn" type="button" onclick="saveWeightedEvaluation()">Save Weighted Grade</button>
                    </div>
                </div>
            </div>



            <!-- MERITS / DEMERITS -->
            <div class="page" id="conduct">
                <div class="page-heading">
                    <div>
                        <h2>Merits & Demerits</h2>
                        <p>Review conduct records added from each cadet's attendance record.</p>
                    </div>
                </div>

                <div class="stats-grid conduct-stats">
                    <div class="stat-card">
                        <div class="stat-icon green">+</div>
                        <div>
                            <span>Total Merits</span>
                            <h2 id="totalMerits">0</h2>
                            <small>Positive records</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">−</div>
                        <div>
                            <span>Total Demerits</span>
                            <h2 id="totalDemerits">0</h2>
                            <small>Disciplinary records</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">★</div>
                        <div>
                            <span>Net Points</span>
                            <h2 id="netConductPoints">0</h2>
                            <small>Merits minus demerits</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Cadet Conduct Records</h3>
                                <p>Merits and demerits appear here after they are saved in Record Attendance.</p>
                        </div>
                    </div>

                    <div class="toolbar">
                        <input type="text" id="conductSearch" placeholder="Search cadet name or ID..." oninput="renderConductRecords()">
                        <select id="conductTypeFilter" onchange="renderConductRecords()">
                            <option value="All">All Records</option>
                            <option value="Merit">Merits Only</option>
                            <option value="Demerit">Demerits Only</option>
                        </select>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Cadet ID</th>
                                    <th>Cadet Name</th>
                                    <th>Type</th>
                                    <th>Points</th>
                                    <th>Reason</th>
                                    <th>Recorded By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="conductBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- OFFICERS DASHBOARD -->
            <div class="page" id="officerDashboard">
                <div class="page-heading">
                    <div>
                        <h2>Officers Dashboard</h2>
                        <p>Command overview of cadet readiness, attendance, conduct, and platoon performance.</p>
                    </div>
                    
                </div>

                <div class="readonly-note">
                    <strong>Officer View:</strong> This dashboard is designed for monitoring and decision-making.
                    Cadet records are shown for viewing; administrative changes remain under the Administrator account.
                </div>

                <div class="officer-profile">
                    <div class="officer-avatar" id="officerInitials">OC</div>
                    <div>
                        <h2>ROTC Command Officer</h2>
                        <p>Officer-in-Charge • Training and Cadet Operations</p>
                    </div>
                    <div class="officer-status">ON DUTY</div>
                </div>

                <div class="officer-kpi-grid">
                    <div class="officer-kpi">
                        <div class="kpi-icon">♟</div>
                        <div><span>Total Cadets</span><h3 id="officerTotalCadets">0</h3><small>Across all platoons</small></div>
                    </div>
                    <div class="officer-kpi">
                        <div class="kpi-icon">✓</div>
                        <div><span>Attendance Rate</span><h3 id="officerAttendance">0%</h3><small>Present / excused</small></div>
                    </div>
                    <div class="officer-kpi">
                        <div class="kpi-icon">★</div>
                        <div><span>Merit Points</span><h3 id="officerMerits">0</h3><small>Positive conduct</small></div>
                    </div>
                    <div class="officer-kpi">
                        <div class="kpi-icon">!</div>
                        <div><span>Demerit Points</span><h3 id="officerDemerits">0</h3><small>For officer monitoring</small></div>
                    </div>
                </div>

                <div class="officer-grid">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3>Platoon Readiness</h3>
                                <p>Quick command overview of each platoon.</p>
                            </div>
                        </div>
                        <div class="command-grid" id="officerPlatoonCards"></div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3>Recent Conduct</h3>
                                <p>Latest merits and demerits for officer awareness.</p>
                            </div>
                        </div>
                        <div class="officer-list" id="officerConductList"></div>
                    </div>
                </div>

                <div class="officer-grid">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3>Cadet Performance</h3>
                                <p>Top positive conduct records and cadets needing attention.</p>
                            </div>
                        </div>
                        <div class="officer-list" id="officerPerformanceList"></div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3>Officer Quick Actions</h3>
                                <p>View-only operational shortcuts.</p>
                            </div>
                        </div>
                        <div class="command-grid">
                            <div class="command-card">
                                <strong>Attendance</strong>
                                <span>Review the current attendance information available in the system.</span>
                                <button class="secondary-btn" style="margin-top:10px" onclick="showPage('attendance')">View Attendance</button>
                            </div>
                            <div class="command-card">
                                <strong>Grades</strong>
                                <span>Review cadet academic and training performance.</span>
                                <button class="secondary-btn" style="margin-top:10px" onclick="showPage('grades')">View Grades</button>
                            </div>
                            <div class="command-card">
                                <strong>Conduct</strong>
                                <span>Monitor merits and demerits recorded by the administrator.</span>
                                <button class="secondary-btn" style="margin-top:10px" onclick="showPage('conduct')">View Conduct</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REPORTS -->
            <div class="page" id="reports">

                <div class="page-heading">

                    <div>
                        <h2>Reports</h2>
                        <p>Generate administrative ROTC reports.</p>
                    </div>
                    <button class="primary-btn" type="button" onclick="openOfficerReportModal()">+ Add Report</button>

                </div>

                <div class="reports-layout">
                <div class="report-grid">

                    <div class="report-card">
                        <div class="report-icon">♙</div>
                        <h3>Cadet Report</h3>
                        <p>Generate a complete list of registered cadets.</p>
                        <button class="primary-btn" onclick="previewReport('cadet')">View Report</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">✓</div>
                        <h3>Attendance Report</h3>
                        <p>Generate attendance records and summaries.</p>
                        <button class="primary-btn" onclick="previewReport('attendance')">View Report</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">★</div>
                        <h3>Grades Report</h3>
                        <p>Generate cadet grades and performance reports.</p>
                        <button class="primary-btn" onclick="previewReport('grades')">View Report</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">✦</div>
                        <h3>Merits & Demerits Report</h3>
                        <p>Generate cadet conduct, merit, and demerit records.</p>
                        <button class="primary-btn" onclick="previewReport('conduct')">View Report</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">▣</div>
                        <h3>Training Report</h3>
                        <p>Generate scheduled training activities.</p>
                        <button class="primary-btn" onclick="previewReport('training')">View Report</button>
                    </div>

                </div>

                <div class="card action-panel">
                    <div class="card-header">
                        <div>
                            <h3>My submitted reports</h3>
                            <p>Edit or delete reports you have submitted.</p>
                        </div>
                        <button class="action-btn" type="button" onclick="loadMyOfficerReports()">Refresh</button>
                    </div>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>Date</th><th>Type</th><th>Title</th><th>Actions</th></tr></thead>
                            <tbody id="myOfficerReportsBody"><tr><td colspan="4">Loading reports...</td></tr></tbody>
                        </table>
                    </div>
                </div>
                </div>

            </div>

            <div class="modal-overlay" id="reportPreviewModal">
                <div class="modal-content report-preview-modal">
                    <div class="modal-header">
                        <h2 id="reportPreviewTitle">Report Preview</h2>
                        <button class="modal-close" type="button" onclick="closeModal('reportPreviewModal')">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="table-container report-preview-table-wrap">
                            <table>
                                <thead id="reportPreviewHead"></thead>
                                <tbody id="reportPreviewBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="modal-btn cancel" type="button" onclick="closeModal('reportPreviewModal')">Close</button>
                        <button class="modal-btn confirm" type="button" onclick="sendPreviewReportToAdmin()">Send to Admin</button>
                        <button class="modal-btn confirm" id="generatePreviewReport" type="button" onclick="downloadPreviewReport()">Generate Excel</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="officerReportModal">
                <div class="modal-content report-form-modal">
                    <div class="modal-header">
                        <h2 id="officerReportModalTitle">Add Report</h2>
                        <button class="modal-close" type="button" onclick="closeModal('officerReportModal')">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-grid">
                            <label>Report type<select id="officerReportType"><option>Cadet</option><option>Attendance</option><option>Grades</option><option>Conduct</option><option>Training</option><option>Cadet concern</option><option>Other</option></select></label>
                            <label>Title<input type="text" id="officerReportTitle" required></label>
                        </div>
                        <label>Report details<textarea id="officerReportContent" rows="6" required></textarea></label>
                        <label>Attach images <small>Select one or more image files.</small><input type="file" id="officerReportImages" accept="image/*" multiple></label>
                    </div>
                    <div class="modal-footer">
                        <button class="modal-btn cancel" type="button" onclick="closeModal('officerReportModal')">Cancel</button>
                        <button class="modal-btn confirm" id="officerReportSaveButton" type="button" onclick="sendOfficerReport()">Save and Send to Admin</button>
                    </div>
                </div>
            </div>


            <!-- USERS -->
            <div class="page" id="users">

                <div class="page-heading">

                    <div>
                        <h2>User Management</h2>
                        <p>Manage system users and access permissions.</p>
                    </div>

                </div>

                <div class="card">

                    <div class="table-container">

                        <table>

                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody id="registeredCadetsBody">
                                <tr><td colspan="5">Loading registered cadets...</td></tr>
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            <!-- SETTINGS -->
            <div class="page" id="settings">

                <div class="page-heading">
                    <div>
                        <h2>System Settings</h2>
                        <p>Configure ROTC MIS settings.</p>
                    </div>
                </div>

                <div class="card settings-card">

                    <label>System Name</label>
                    <input type="text" value="ROTC Management Information System">

                    <label>Institution</label>
                    <input type="text" value="Metro Dumaguete College">

                    <label>Administrator Email</label>
                    <input type="email" value="admin@mdci.edu.ph">

                    <button class="primary-btn">
                        Save Changes
                    </button>

                </div>

            </div>

        </section>

    </main>


    <!-- ADD CADET MODAL -->
    <div class="modal-overlay" id="cadetModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Add New Cadet</h2>
                <button onclick="closeModal('cadetModal')">×</button>
            </div>

            <form id="cadetForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Cadet ID</label>
                        <input type="text" id="cadetId" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="cadetName" required>
                    </div>
                    <div class="form-group">
                        <label>Year Level</label>
                        <select id="yearLevel" required>
                            <option value="">Select Year</option>
                            <option>First Year</option>
                            <option>Second Year</option>
                            <option>Third Year</option>
                            <option>Fourth Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <input type="text" id="section" required>
                    </div>
                    <div class="form-group">
                        <label>Platoon</label>
                        <select id="platoon" required>
                            <option value="">Select Platoon</option>
                            <option>Alpha</option>
                            <option>Bravo</option>
                            <option>Charlie</option>
                            <option>Delta</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="secondary-btn" onclick="closeModal('cadetModal')">Cancel</button>
                    <button type="submit" class="primary-btn">Save Cadet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CADET RECORD MODAL -->
    <div class="modal-overlay" id="recordModal">
        <div class="modal cadet-record-modal">
            <div class="modal-header record-modal-header">
                <div>
                    <h2>Cadet Record</h2>
                    <p>View and record the selected cadet's ROTC performance.</p>
                </div>
                <button onclick="closeModal('recordModal')">×</button>
            </div>

            <div class="cadet-profile-card">
                <div class="cadet-profile-avatar" id="recordCadetInitials">JD</div>
                <div class="cadet-profile-main">
                    <span class="record-label">CADET</span>
                    <h2 id="recordCadetName">No cadet selected</h2>
                    <div class="cadet-profile-meta">
                        <span id="recordCadetId">—</span>
                        <span id="recordCadetPlatoon">Alpha Platoon</span>
                        <span id="recordCadetYear">First Year</span>
                    </div>
                </div>
                <div class="record-status-pill" id="recordCadetStatus">Active</div>
            </div>

            <div class="record-summary-grid">
                <div class="record-summary-box">
                    <span>Attendance</span>
                    <strong id="recordAttendanceSummary">0%</strong>
                    <small>Present rate</small>
                </div>
                <div class="record-summary-box merit-summary">
                    <span>Merits</span>
                    <strong id="recordMeritSummary">0</strong>
                    <small>Positive points</small>
                </div>
                <div class="record-summary-box demerit-summary">
                    <span>Demerits</span>
                    <strong id="recordDemeritSummary">0</strong>
                    <small>Disciplinary points</small>
                </div>
                <div class="record-summary-box">
                    <span>Net Points</span>
                    <strong id="recordNetSummary">0</strong>
                    <small>Overall conduct</small>
                </div>
            </div>

            <div class="record-box">
                <div class="record-box-title">
                    <span>PERSONAL INFORMATION</span>
                </div>
                <div class="record-info-grid">
                    <div><span>Cadet ID</span><strong id="recordInfoId">—</strong></div>
                    <div><span>Full Name</span><strong id="recordInfoName">—</strong></div>
                    <div><span>Year Level</span><strong id="recordInfoYear">—</strong></div>
                    <div><span>Section</span><strong id="recordInfoSection">—</strong></div>
                    <div><span>Platoon</span><strong id="recordInfoPlatoon">—</strong></div>
                    <div><span>Status</span><strong id="recordInfoStatus">—</strong></div>
                </div>
            </div>

            <div class="record-box">
                <div class="record-box-title">
                    <span>QUICK RECORD</span>
                </div>
                <div class="record-form-grid">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="recordDate">
                    </div>
                    <div class="form-group">
                        <label>Attendance</label>
                        <select id="recordAttendance">
                            <option>Present</option>
                            <option>Late</option>
                            <option>Absent</option>
                            <option>Excused</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Conduct Type</label>
                        <select id="recordConductType">
                            <option value="">No conduct record</option>
                            <option value="Merit">Merit</option>
                            <option value="Demerit">Demerit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Points</label>
                        <input type="number" id="recordPoints" min="1" value="1">
                    </div>
                </div>
                <div class="form-group" style="margin-top:14px;">
                    <label>Reason / Remarks</label>
                    <textarea id="recordRemarks" rows="3" placeholder="Enter attendance remarks or merit/demerit reason..."></textarea>
                </div>
            </div>

            <div class="record-box record-history-box">
                <div class="record-box-title">
                    <span>MERITS & DEMERITS HISTORY</span>
                    <button type="button" class="text-btn" onclick="showCadetConductHistory()">View All</button>
                </div>
                <div id="cadetConductHistory" class="mini-history"></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="secondary-btn" onclick="closeModal('recordModal')">Close</button>
                <button type="button" class="primary-btn" onclick="saveCadetRecord()">Save Record</button>
            </div>
        </div>
    </div>

    <!-- ADD MERIT / DEMERIT MODAL -->
    <div class="modal-overlay" id="conductModal">
        <div class="modal">
            <div class="modal-header">
                <div>
                    <h2>Add Merit / Demerit</h2>
                    <p>Record a cadet's achievement or disciplinary action.</p>
                </div>
                <button onclick="closeModal('conductModal')">×</button>
            </div>

            <form id="conductForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Cadet</label>
                        <select id="conductCadet" required></select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="conductDate" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select id="conductType" required>
                            <option value="">Select Type</option>
                            <option value="Merit">Merit</option>
                            <option value="Demerit">Demerit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Points</label>
                        <input type="number" id="conductPoints" min="1" value="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reason / Remarks</label>
                    <textarea id="conductReason" rows="4" placeholder="Example: Outstanding performance during military drill..." required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="secondary-btn" onclick="closeModal('conductModal')">Cancel</button>
                    <button type="submit" class="primary-btn">Save Conduct Record</button>
                </div>
            </form>
        </div>
    </div>


    <script>
const cadetRecords = [];

let conductRecords = [];

let attendanceRecords = [];
let formationFinalizedDate = {};

// Returns the currently logged-in officer ID.
function getLoggedOfficerId() {
    return JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null")?.id || 0;
}

// Loads attendance and conduct records for the officer dashboard.
async function loadOfficerCadetRecords() {
    try {
        const [attendanceResponse, conductResponse] = await Promise.all([
            fetch("../backend/officer-attendance.php", { cache: "no-store" }),
            fetch("../backend/officer-conduct.php", { cache: "no-store" })
        ]);
        const attendanceData = await attendanceResponse.json();
        const conductData = await conductResponse.json();
        if (!attendanceData.success || !conductData.success) throw new Error("Unable to load saved cadet records.");

        attendanceRecords = (attendanceData.attendance || []).map(record => ({
            id: record.id,
            cadetId: String(record.cadet_code),
            date: record.date,
            status: record.status,
            timeIn: record.time_in || null,
            timeOut: record.time_out || null,
            trainingType: record.training_type || "Formation 1",
            remarks: record.remarks || ""
        }));
        localStorage.setItem("rotc_officer_attendance_records", JSON.stringify(attendanceRecords));
        attendanceRecords.forEach(record => {
            if (!record.date || !record.trainingType) return;
            const period = record.status === "Absent" || String(record.timeIn || "").slice(0, 2) < "12" ? "Morning" : "Afternoon";
            const trainingType = normalizeFormationSessionType(record.trainingType, period);
            record.trainingType = trainingType;
            const key = formationAttendanceKey(record.cadetId, record.date, trainingType);
            formationAttendance[key] = {
                date: record.date,
                trainingType,
                status: record.status,
                timeIn: record.timeIn,
                timeOut: record.timeOut
            };
        });
        conductRecords = (conductData.conduct || []).map(record => ({
            id: Number(record.id),
            cadetId: String(record.cadet_code),
            date: record.date,
            type: record.type,
            points: Number(record.points),
            reason: record.reason,
            recordedBy: record.recorded_by || "Officer"
        }));
        if (attendanceRosterLoaded) renderAttendanceRoster();
        renderConductRecords();
        updateConductStats();
        renderAttendanceOverviewChart();
    } catch (error) {
        console.error("Unable to load saved cadet records:", error);
        try {
            attendanceRecords = JSON.parse(localStorage.getItem("rotc_officer_attendance_records") || "[]");
        } catch (cacheError) {
            attendanceRecords = [];
        }
    }
}

// Saves an attendance record through the backend API.
async function persistAttendanceRecord(record) {
    const officerId = getLoggedOfficerId();
    if (!officerId || !record) return null;
    const cachedRecords = attendanceRecords.filter(item => item !== record);
    cachedRecords.push(record);
    localStorage.setItem("rotc_officer_attendance_records", JSON.stringify(cachedRecords));
    try {
        const response = await fetch("../backend/officer-attendance.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                officer_id: officerId,
                cadet_id: record.cadetId,
                date: record.date,
                status: record.status,
                time_in: record.timeIn,
                time_out: record.timeOut,
                training_type: record.trainingType || getAttendanceSessionType()
            })
        });
        const data = await response.json();
        if (data.success && data.record_id) record.id = Number(data.record_id);
        return data;
    } catch (error) {
        console.warn("Attendance saved offline and will remain available on this device:", error);
        return null;
    }
}

// Saves a conduct record through the backend API.
async function persistConductRecord(record) {
    const officerId = getLoggedOfficerId();
    if (!officerId || !record) return null;
    try {
        const response = await fetch("../backend/officer-conduct.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                officer_id: officerId,
                cadet_id: record.cadetId,
                date: record.date,
                type: record.type,
                points: record.points,
                reason: record.reason
            })
        });
        const data = await response.json();
        if (data.success && data.record_id) record.id = Number(data.record_id);
        return data;
    } catch (error) {
        console.error("Unable to save conduct record:", error);
        return null;
    }
}

// Deletes a persisted conduct record owned by the officer.
async function removePersistedConductRecord(recordId) {
    const officerId = getLoggedOfficerId();
    if (!officerId || !Number.isFinite(Number(recordId))) return;
    try {
        await fetch("../backend/officer-conduct.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "delete", officer_id: officerId, record_id: Number(recordId) })
        });
    } catch (error) {
        console.error("Unable to delete conduct record:", error);
    }
}

let currentPlatoon = "";
let selectedCadetId = "";

// Opens the selected platoon roster view.
function openPlatoon(platoon) {
    currentPlatoon = platoon;
    document.getElementById("platoonListView").style.display = "none";
    document.getElementById("platoonDetailView").style.display = "block";
    document.getElementById("selectedPlatoonTitle").textContent = platoon + " Platoon";
    document.getElementById("platoonCadetSearch").value = "";
    document.getElementById("platoonYearFilter").value = "All";
    renderPlatoonCadets();
}

// Returns from a platoon roster to the platoon list.
function backToPlatoons() {
    currentPlatoon = "";
    document.getElementById("platoonDetailView").style.display = "none";
    document.getElementById("platoonListView").style.display = "block";
    updatePlatoonCounts();
}

// Renders cadets assigned to the active platoon.
function renderPlatoonCadets() {
    const body = document.getElementById("platoonCadetBody");
    const search = document.getElementById("platoonCadetSearch").value.toLowerCase();
    const year = document.getElementById("platoonYearFilter").value;

    const records = cadetRecords.filter(c =>
        c.platoon === currentPlatoon &&
        (year === "All" || c.year === year) &&
        (c.name.toLowerCase().includes(search) || c.id.toLowerCase().includes(search))
    );

    body.innerHTML = records.length ? records.map(c => `
        <tr>
            <td>${c.id}</td>
            <td>${c.name}</td>
            <td>${c.year}</td>
            <td>${c.section}</td>
            <td><span class="status active">${c.status}</span></td>
            <td><button class="action-btn" onclick="recordCadet('${c.id}')">View Record</button></td>
        </tr>
    `).join("") : `<tr><td colspan="6" style="text-align:center;">No cadets found in ${currentPlatoon} Platoon.</td></tr>`;

    document.getElementById("selectedPlatoonSummary").textContent =
        `${cadetRecords.filter(c => c.platoon === currentPlatoon).length} cadet(s) assigned to ${currentPlatoon} Platoon.`;
}

// Filters the active platoon roster using the search input.
function filterPlatoonCadets() {
    renderPlatoonCadets();
}

// Updates the displayed cadet count for each platoon.
function updatePlatoonCounts() {
    document.querySelectorAll(".platoon-count").forEach(el => {
        el.textContent = cadetRecords.filter(c => c.platoon === el.dataset.platoon).length;
    });
}

// Calculates merit, demerit, and net conduct points for a cadet.
function getConductTotals(cadetId) {
    const records = conductRecords.filter(r => r.cadetId === cadetId);
    const merits = records.filter(r => r.type === "Merit").reduce((sum, r) => sum + Number(r.points), 0);
    const demerits = records.filter(r => r.type === "Demerit").reduce((sum, r) => sum + Number(r.points), 0);
    return { merits, demerits, net: merits - demerits };
}

// Calculates the attendance rate for a cadet.
function getAttendanceRate(cadetId) {
    const records = attendanceRecords.filter(r => r.cadetId === cadetId);
    if (!records.length) return 0;
    const present = records.filter(r => r.status === "Present" || r.status === "Excused").length;
    return Math.round((present / records.length) * 100);
}

// Calculates the dashboard evaluation score for a cadet.
function calculateCadetEvaluation(cadetId, gradeAverage) {
    const attendanceRecordsForCadet = attendanceRecords.filter(r => r.cadetId === cadetId);
    const hasPresentAttendance = attendanceRecordsForCadet.some(r => r.status === "Present" || r.status === "Excused");
    const attendanceScore = hasPresentAttendance ? 100 : getAttendanceRate(cadetId);

    const conduct = getConductTotals(cadetId);
    const attitudeScore = Math.max(0, Math.min(100, 100 + conduct.merits - conduct.demerits));
    const evaluation = (Number(gradeAverage) * 0.50) + (attendanceScore * 0.25) + (attitudeScore * 0.25);

    return Math.max(0, Math.min(100, evaluation));
}

// Creates display initials from a cadet name.
function initials(name) {
    return name.split(/\s+/).filter(Boolean).slice(0, 2).map(x => x[0]).join("").toUpperCase();
}

// Selects a cadet and opens the record view.
function recordCadet(id) {
    const cadet = cadetRecords.find(c => c.id === id);
    if (!cadet) return;

    selectedCadetId = id;

    document.getElementById("recordCadetId").textContent = cadet.id;
    document.getElementById("recordCadetName").textContent = cadet.name;
    document.getElementById("recordCadetPlatoon").textContent = cadet.platoon + " Platoon";
    document.getElementById("recordCadetYear").textContent = cadet.year;
    document.getElementById("recordCadetStatus").textContent = cadet.status;
    document.getElementById("recordCadetInitials").textContent = initials(cadet.name);

    document.getElementById("recordInfoId").textContent = cadet.id;
    document.getElementById("recordInfoName").textContent = cadet.name;
    document.getElementById("recordInfoYear").textContent = cadet.year;
    document.getElementById("recordInfoSection").textContent = cadet.section;
    document.getElementById("recordInfoPlatoon").textContent = cadet.platoon + " Platoon";
    document.getElementById("recordInfoStatus").textContent = cadet.status;

    const totals = getConductTotals(id);
    document.getElementById("recordAttendanceSummary").textContent = getAttendanceRate(id) + "%";
    document.getElementById("recordMeritSummary").textContent = totals.merits;
    document.getElementById("recordDemeritSummary").textContent = totals.demerits;
    document.getElementById("recordNetSummary").textContent = totals.net;

    document.getElementById("recordDate").value = new Date().toISOString().split("T")[0];
    document.getElementById("recordAttendance").value = "Present";
    document.getElementById("recordConductType").value = "";
    document.getElementById("recordPoints").value = 1;
    document.getElementById("recordRemarks").value = "";

    renderCadetConductHistory(id);
    document.getElementById("recordModal").classList.add("show");
}

// Renders recent conduct records for a cadet.
function renderCadetConductHistory(cadetId) {
    const box = document.getElementById("cadetConductHistory");
    const records = conductRecords.filter(r => r.cadetId === cadetId).slice().reverse().slice(0, 5);

    box.innerHTML = records.length ? records.map(r => `
        <div class="history-row">
            <span>${r.date}</span>
            <span class="conduct-badge ${r.type.toLowerCase()}">${r.type}</span>
            <span>${escapeHtml(r.reason)}</span>
            <strong>${r.type === "Merit" ? "+" : "−"}${r.points}</strong>
        </div>
    `).join("") : `<div style="padding:12px;color:#6b7280;text-align:center;">No merits or demerits recorded.</div>`;
}

// Displays conduct history for the selected cadet.
function showCadetConductHistory() {
    document.getElementById("conductSearch").value = selectedCadetId;
    document.getElementById("conductTypeFilter").value = "All";
    closeModal("recordModal");
    showPage("conduct");
    renderConductRecords();
}

// Validates and saves the selected cadet record.
function saveCadetRecord() {
    if (!selectedCadetId) return;

    const date = document.getElementById("recordDate").value;
    const attendance = document.getElementById("recordAttendance").value;
    const type = document.getElementById("recordConductType").value;
    const points = Number(document.getElementById("recordPoints").value || 1);
    const remarks = document.getElementById("recordRemarks").value.trim();

    markCadetAttendance(selectedCadetId, attendance, date);
    const savedAttendance = attendanceRecords.find(record => record.cadetId === selectedCadetId && record.date === date);
    if (savedAttendance) {
        savedAttendance.remarks = remarks;
        persistAttendanceRecord(savedAttendance);
    }

    if (type) {
        const conductRecord = {
            id: Date.now(),
            cadetId: selectedCadetId,
            date,
            type,
            points,
            reason: remarks || "No reason provided",
            recordedBy: "Administrator"
        };
        conductRecords.push(conductRecord);
        persistConductRecord(conductRecord);
    }

    closeModal("recordModal");
    renderConductRecords();
    updateConductStats();
    alert("Cadet record saved successfully.");
}

// Opens the conduct-record form for a cadet.
function openConductModal(cadetId = "") {
    const select = document.getElementById("conductCadet");
    select.innerHTML = `<option value="">Select Cadet</option>` +
        cadetRecords.map(c => `<option value="${c.id}">${c.id} — ${c.name}</option>`).join("");

    if (cadetId) select.value = cadetId;
    document.getElementById("conductDate").value = new Date().toISOString().split("T")[0];
    document.getElementById("conductType").value = "";
    document.getElementById("conductPoints").value = 1;
    document.getElementById("conductReason").value = "";
    document.getElementById("conductModal").classList.add("show");
}

document.getElementById("conductForm")?.addEventListener("submit", function(e) {
    e.preventDefault();

    const cadetId = document.getElementById("conductCadet").value;
    const type = document.getElementById("conductType").value;
    const points = Number(document.getElementById("conductPoints").value);
    const date = document.getElementById("conductDate").value;
    const reason = document.getElementById("conductReason").value.trim();

    if (!cadetId || !type || !points || !date || !reason) {
        alert("Please complete all conduct fields.");
        return;
    }

    const conductRecord = {
        id: Date.now(),
        cadetId,
        date,
        type,
        points,
        reason,
        recordedBy: "Administrator"
    };
    conductRecords.push(conductRecord);
    persistConductRecord(conductRecord);

    closeModal("conductModal");
    renderConductRecords();
    updateConductStats();
    alert(`${type} record saved successfully.`);
});

// Renders the officer's conduct records table.
function renderConductRecords() {
    const body = document.getElementById("conductBody");
    if (!body) return;

    const search = (document.getElementById("conductSearch")?.value || "").toLowerCase();
    const typeFilter = document.getElementById("conductTypeFilter")?.value || "All";

    const records = conductRecords.filter(r => {
        const cadet = cadetRecords.find(c => c.id === r.cadetId);
        if (!cadet) return false;
        const matchSearch = cadet.name.toLowerCase().includes(search) || cadet.id.toLowerCase().includes(search);
        const matchType = typeFilter === "All" || r.type === typeFilter;
        return matchSearch && matchType;
    });

    body.innerHTML = records.length ? records.slice().reverse().map(r => {
        const cadet = cadetRecords.find(c => c.id === r.cadetId);
        return `
            <tr>
                <td>${r.date}</td>
                <td>${r.cadetId}</td>
                <td>${cadet ? cadet.name : "Unknown Cadet"}</td>
                <td><span class="conduct-badge ${r.type.toLowerCase()}">${r.type}</span></td>
                <td><strong>${r.type === "Merit" ? "+" : "−"}${r.points}</strong></td>
                <td>${escapeHtml(r.reason)}</td>
                <td>${escapeHtml(r.recordedBy)}</td>
                <td>
                    <button class="action-btn" onclick="recordCadet('${r.cadetId}')">View Cadet</button>
                    <button class="action-btn" onclick="deleteConductRecord(${r.id})">Delete</button>
                </td>
            </tr>
        `;
    }).join("") : `<tr><td colspan="8" style="text-align:center;">No conduct records found.</td></tr>`;

    updateConductStats();
}

// Removes a conduct record after confirmation.
function deleteConductRecord(id) {
    if (!confirm("Delete this merit/demerit record?")) return;
    removePersistedConductRecord(id);
    conductRecords = conductRecords.filter(r => r.id !== id);
    renderConductRecords();
    updateConductStats();

    if (selectedCadetId && document.getElementById("recordModal").classList.contains("show")) {
        renderCadetConductHistory(selectedCadetId);
    }
}

// Updates the conduct summary statistics.
function updateConductStats() {
    const merits = conductRecords.filter(r => r.type === "Merit").reduce((sum, r) => sum + Number(r.points), 0);
    const demerits = conductRecords.filter(r => r.type === "Demerit").reduce((sum, r) => sum + Number(r.points), 0);

    const a = document.getElementById("totalMerits");
    const b = document.getElementById("totalDemerits");
    const c = document.getElementById("netConductPoints");

    if (a) a.textContent = merits;
    if (b) b.textContent = demerits;
    if (c) c.textContent = merits - demerits;
}

// Escapes text before inserting it into generated HTML.
function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
        "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
    }[char]));
}

document.getElementById("cadetForm")?.addEventListener("submit", function(e) {
    e.preventDefault();

    const record = {
        id: document.getElementById("cadetId").value.trim(),
        name: document.getElementById("cadetName").value.trim(),
        year: document.getElementById("yearLevel").value,
        section: document.getElementById("section").value.trim(),
        platoon: document.getElementById("platoon").value,
        status: "Active"
    };

    if (cadetRecords.some(c => c.id === record.id)) {
        alert("Cadet ID already exists.");
        return;
    }

    cadetRecords.push(record);
    closeModal("cadetModal");
    this.reset();
    updatePlatoonCounts();

    const totalEl = document.getElementById("totalCadets");
    if (totalEl) totalEl.textContent = cadetRecords.length;

    if (currentPlatoon) renderPlatoonCadets();

    renderConductRecords();
    alert(`${record.name} was added to ${record.platoon} Platoon.`);
});

// Exports the current platoon roster as a report.
function exportCurrentPlatoon() {
    if (!currentPlatoon) {
        alert("Please open a platoon first.");
        return;
    }

    if (typeof XLSX === "undefined") {
        alert("Excel library is not loaded. Please check your internet connection and reload the page.");
        return;
    }

    const rows = cadetRecords.filter(c => c.platoon === currentPlatoon).map(c => {
        const totals = getConductTotals(c.id);
        return {
            "Cadet ID": c.id,
            "Name": c.name,
            "Year Level": c.year,
            "Section": c.section,
            "Platoon": c.platoon,
            "Merits": totals.merits,
            "Demerits": totals.demerits,
            "Net Conduct Points": totals.net,
            "Status": c.status
        };
    });

    if (!rows.length) {
        alert("There are no cadets in this platoon.");
        return;
    }

    downloadExcel(rows, `${currentPlatoon}_Platoon_Cadet_Report.xlsx`, `${currentPlatoon} Platoon`);
}

document.addEventListener("DOMContentLoaded", () => {
    updatePlatoonCounts();
    updateConductStats();
    renderConductRecords();

    const totalEl = document.getElementById("totalCadets");
    if (totalEl) totalEl.textContent = cadetRecords.length;
});
</script>

<script>
/*
 * Excel Report Export
 * Uses SheetJS (XLSX) loaded in the <head>.
 * Clicking a Generate Excel button immediately downloads an .xlsx file.
 */

// Converts an HTML table into row objects for export.
function tableToObjects(table) {
    const headers = Array.from(table.querySelectorAll("thead th"))
        .map(th => th.innerText.trim())
        .filter(h => h && h.toLowerCase() !== "action");

    const rows = [];
    table.querySelectorAll("tbody tr").forEach(tr => {
        const cells = Array.from(tr.querySelectorAll("td"));
        const row = {};

        let headerIndex = 0;
        cells.forEach(cell => {
            const text = cell.innerText.trim();
            const actionOnly = cell.querySelector("button");
            if (actionOnly && !text.replace(/view/gi, "").trim()) return;

            if (headerIndex < headers.length) {
                row[headers[headerIndex]] = text;
                headerIndex++;
            }
        });

        if (Object.keys(row).length) rows.push(row);
    });

    return rows;
}

// Builds and downloads spreadsheet-compatible roster data.
function downloadExcel(rows, fileName, sheetName) {
    if (typeof XLSX === "undefined") {
        alert("Excel export library could not be loaded. Please check your internet connection and try again.");
        return;
    }

    if (!rows || rows.length === 0) {
        alert("There are no records available for this report.");
        return;
    }

    const worksheet = XLSX.utils.json_to_sheet(rows);
    const workbook = XLSX.utils.book_new();

    // Set readable column widths.
    const keys = Object.keys(rows[0]);
    worksheet["!cols"] = keys.map(key => ({
        wch: Math.max(
            key.length + 2,
            ...rows.map(row => String(row[key] ?? "").length + 2)
        )
    }));

    XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
    XLSX.writeFile(workbook, fileName);
}

let pendingReport = null;

// Builds a preview for the selected report type.
function previewReport(type) {
    let rows = [];
    let fileName = "";
    let sheetName = "";

    switch (type) {
        case "cadet": {
            // The platoon redesign no longer uses a single #cadetTable.
            // Export directly from the cadetRecords array instead.
            if (typeof cadetRecords === "undefined") {
                alert("Cadet records are not available.");
                return;
            }

            rows = cadetRecords.map(c => ({
                "Cadet ID": c.id,
                "Name": c.name,
                "Year Level": c.year,
                "Section": c.section,
                "Platoon": c.platoon,
                "Status": c.status
            }));

            fileName = "Cadet_Report.xlsx";
            sheetName = "Cadet Report";
            break;
        }

        case "attendance": {
            const page = document.getElementById("attendance");
            const table = page.querySelector("table");
            rows = tableToObjects(table);
            fileName = "Attendance_Report.xlsx";
            sheetName = "Attendance";
            break;
        }

        case "grades": {
            const page = document.getElementById("grades");
            const table = page.querySelector("table");
            rows = tableToObjects(table);
            fileName = "Grades_Report.xlsx";
            sheetName = "Grades";
            break;
        }

        case "conduct": {
            rows = conductRecords.map(r => {
                const cadet = cadetRecords.find(c => c.id === r.cadetId);
                return {
                    "Date": r.date,
                    "Cadet ID": r.cadetId,
                    "Cadet Name": cadet ? cadet.name : "",
                    "Type": r.type,
                    "Points": r.points,
                    "Reason": r.reason,
                    "Recorded By": r.recordedBy
                };
            });
            fileName = "Merits_Demerits_Report.xlsx";
            sheetName = "Merits & Demerits";
            break;
        }

        case "training": {
            // Training is displayed as cards instead of a table.
            const cards = document.querySelectorAll("#training .schedule-card");

            cards.forEach(card => {
                const title = card.querySelector(".schedule-info h3")?.innerText.trim() || "";
                const details = Array.from(card.querySelectorAll(".schedule-info p"))
                    .map(p => p.innerText.trim());

                rows.push({
                    "Training": title,
                    "Date": details[0] ? details[0].replace(/^📅\s*/, "") : "",
                    "Time": details[1] ? details[1].replace(/^⏰\s*/, "") : "",
                    "Location": details[2] ? details[2].replace(/^📍\s*/, "") : ""
                });
            });

            fileName = "Training_Report.xlsx";
            sheetName = "Training";
            break;
        }

        default:
            alert("Unknown report type.");
            return;
    }

    pendingReport = { rows, fileName, sheetName };
    const title = document.getElementById("reportPreviewTitle");
    const head = document.getElementById("reportPreviewHead");
    const body = document.getElementById("reportPreviewBody");
    const generateButton = document.getElementById("generatePreviewReport");
    const keys = rows.length ? Object.keys(rows[0]) : [];

    if (title) title.textContent = `${sheetName} Preview`;
    if (generateButton) generateButton.disabled = !rows.length;
    if (head) head.innerHTML = keys.length ? `<tr>${keys.map(key => `<th>${escapeHtml(key)}</th>`).join("")}</tr>` : "";
    if (body) {
        body.innerHTML = rows.length ? rows.map(row => `
            <tr>${keys.map(key => `<td>${escapeHtml(row[key])}</td>`).join("")}</tr>
        `).join("") : '<tr><td>No records available for this report.</td></tr>';
    }

    openModal("reportPreviewModal");
}

// Downloads the currently previewed report.
function downloadPreviewReport() {
    if (!pendingReport) return;
    downloadExcel(pendingReport.rows, pendingReport.fileName, pendingReport.sheetName);
}

// Sends the current report preview to an administrator.
function sendPreviewReportToAdmin() {
    if (!pendingReport || !pendingReport.rows.length) {
        alert("There are no records available to send.");
        return;
    }

    const reportType = pendingReport.sheetName === "Merits & Demerits"
        ? "Conduct"
        : pendingReport.sheetName.replace(/ Report$/, "");
    const content = pendingReport.rows.map((row, index) => {
        const details = Object.entries(row)
            .map(([key, value]) => `${key}: ${value ?? ""}`)
            .join(" | ");
        return `${index + 1}. ${details}`;
    }).join("\n");

    document.getElementById("officerReportType").value = reportType;
    document.getElementById("officerReportTitle").value = pendingReport.sheetName;
    document.getElementById("officerReportContent").value = content;
    closeModal("reportPreviewModal");
    openOfficerReportModal();
}
</script>

<script>
let officerQrScanner = null;
let officerScannerTarget = "scan";
let officerQrScanInProgress = false;
let officerLastQrValue = "";
let attendanceRoster = [];
let formationAttendance = {};
let attendanceRosterLoaded = false;
let attendanceCardFilters = {};
const attendanceCompanies = ["Alpha Company", "Bravo Company", "Charlie Company", "Delta Company", "Echo Company", "Foxtrot Company", "HQ"];
const attendancePlatoons = ["First", "Second", "Third"];

// Normalizes company names used by attendance filters.
function normalizeAttendanceCompany(value) {
    const company = String(value || "").trim();
    const aliases = { "A Company": "Alpha Company", "B Company": "Bravo Company", "C Company": "Charlie Company", "D Company": "Delta Company", "E Company": "Echo Company", "F Company": "Foxtrot Company" };
    if (aliases[company]) return aliases[company];
    if (company === "HQ Company") return "HQ";
    return company;
}

// Checks whether a cadet belongs to the selected company.
function attendanceCompanyMatches(cadet, selectedCompany) {
    return !selectedCompany || normalizeAttendanceCompany(cadet.company || "Unassigned") === selectedCompany;
}

// Checks whether a cadet belongs to the selected platoon.
function attendancePlatoonMatches(cadet, selectedPlatoon) {
    if (!selectedPlatoon) return true;
    const platoon = String(cadet.platoon || "Unassigned").trim();
    return platoon === selectedPlatoon || platoon.endsWith(` ${selectedPlatoon}`);
}

// Populates attendance company and platoon filter controls.
function populateAttendanceFilters() {
    const formationInput = document.getElementById("attendanceSessionTypeInput");
    const scanFormationInput = document.getElementById("officerScanFormationSelect");
    const periodInput = document.getElementById("attendanceSessionPeriodInput");
    const scanPeriodInput = document.getElementById("officerScanPeriodSelect");
    const companyInput = document.getElementById("attendanceCompanyFilter");
    if (formationInput && !formationInput.options.length) {
        const numberedFormations = Array.from({ length: 15 }, (_, index) => {
            const number = index + 1;
            const suffix = number === 1 ? "st" : number === 2 ? "nd" : number === 3 ? "rd" : "th";
            return `<option value="Formation ${number}">${number}${suffix} Formation</option>`;
        }).join("");
        formationInput.innerHTML = `<optgroup label="Formation">${numberedFormations}</optgroup><option value="Special Formation">Special Formation</option>`;
    }
    if (scanFormationInput && !scanFormationInput.options.length && formationInput) {
        scanFormationInput.innerHTML = formationInput.innerHTML;
        scanFormationInput.value = formationInput.value || "Formation 1";
    }
    if (periodInput && !periodInput.options.length) {
        periodInput.innerHTML = '<option value="Morning">Morning</option><option value="Afternoon">Afternoon</option>';
        periodInput.value = manilaNow().getHours() < 12 ? "Morning" : "Afternoon";
    }
    if (scanPeriodInput && !scanPeriodInput.options.length && periodInput) {
        scanPeriodInput.innerHTML = periodInput.innerHTML;
        scanPeriodInput.value = periodInput.value;
    }
    if (companyInput) {
        const current = companyInput.value;
        companyInput.innerHTML = '<option value="">All Companies</option>' + attendanceCompanies.map(company => `<option value="${escapeHtml(company)}">${escapeHtml(company)}</option>`).join("");
        companyInput.value = attendanceCompanies.includes(current) ? current : "";
    }
    updateAttendancePlatoonFilter();
}

// Synchronizes the scanner's formation selection.
function syncOfficerScanFormation(value) {
    const formationInput = document.getElementById("attendanceSessionTypeInput");
    if (!formationInput || !value) return;
    formationInput.value = value;
    renderAttendanceRoster();
}

// Synchronizes the scanner's attendance period selection.
function syncOfficerScanPeriod(value) {
    const periodInput = document.getElementById("attendanceSessionPeriodInput");
    if (!periodInput || !value) return;
    periodInput.value = value;
    renderAttendanceRoster();
}

// Refreshes the platoon options for attendance filtering.
function updateAttendancePlatoonFilter() {
    const companyInput = document.getElementById("attendanceCompanyFilter");
    const platoonInput = document.getElementById("attendancePlatoonFilter");
    if (!platoonInput) return;
    const company = companyInput?.value || "";
    const current = platoonInput.value;
    platoonInput.innerHTML = '<option value="">All Platoons</option>' + attendancePlatoons.map(platoon => `<option value="${escapeHtml(platoon)}">${escapeHtml(platoon)}</option>`).join("");
    platoonInput.value = attendancePlatoons.includes(current) ? current : "";
}

// Applies a filter to an attendance card group.
function updateAttendanceCardFilter(gender, field, value) {
    const current = attendanceCardFilters[gender] || { company: "", platoon: "" };
    current[field] = value;
    if (field === "company") current.platoon = "";
    attendanceCardFilters[gender] = current;
    renderAttendanceRoster();
}

// Maps an attendance period to its session type.
function getAttendanceSessionTypeForPeriod(period) {
    const input = document.getElementById("attendanceSessionTypeInput");
    const value = (input?.value || "Formation 1").trim();
    const selectedPeriod = document.getElementById("attendanceSessionPeriodInput")?.value || period;
    if (value === "Formation 1") return `${selectedPeriod} Formation`;
    return `${value} ${selectedPeriod} Formation`;
}

// Keeps legacy and current formation labels on the same attendance key.
function normalizeFormationSessionType(sessionType, period = "Afternoon") {
    const value = String(sessionType || "").trim();
    const normalized = value.toLowerCase();
    if (normalized === "formation" || normalized === "formation 1") return `${period} Formation`;
    if (normalized === "formation 1 morning formation") return "Morning Formation";
    if (normalized === "formation 1 afternoon formation") return "Afternoon Formation";
    return value || `${period} Formation`;
}

// Compares records using the same canonical session label as the roster.
function attendanceRecordMatchesSession(record, sessionType) {
    const period = sessionType.includes("Morning") ? "Morning" : "Afternoon";
    return normalizeFormationSessionType(record?.trainingType, period) === sessionType;
}

// Returns the currently selected attendance session type.
function getAttendanceSessionType() {
    const period = document.getElementById("attendanceSessionPeriodInput")?.value || (manilaNow().getHours() < 12 ? "Morning" : "Afternoon");
    return normalizeFormationSessionType(getAttendanceSessionTypeForPeriod(period), period);
}

// Creates the unique key for a formation attendance record.
function formationAttendanceKey(cadetId, date = todayDate(), sessionType = getAttendanceSessionType()) {
    return `${String(cadetId)}::${date}::${normalizeFormationSessionType(sessionType)}`;
}

// Returns the current date and time in Manila.
function manilaNow() {
    return new Date(new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" }));
}

// Returns today's Manila date in ISO format.
function todayDate() {
    const now = manilaNow();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
}

// Converts a Manila date-time into minutes since midnight.
function minutesSinceMidnight(date = manilaNow()) {
    return date.getHours() * 60 + date.getMinutes();
}

// Determines the default attendance status for a scan.
function formationStatusAtScan() {
    const now = manilaNow();
    const minutes = minutesSinceMidnight(now);
    const sessionType = getAttendanceSessionType();
    const isMorning = sessionType.includes("Morning Formation");
    if (isMorning) {
        return minutes < 8 * 60 ? "Present" : "Late";
    }
    return minutes < 13 * 60 + 30 ? "Present" : "Late";
}

// Determines which phase of formation scanning is active.
function formationScanPhase(sessionType = getAttendanceSessionType(), minutes = minutesSinceMidnight()) {
    const isMorning = sessionType.includes("Morning Formation");
    if (isMorning) {
        if (minutes >= 6 * 60 + 40 && minutes < 8 * 60 + 30) return "timeIn";
        if (minutes >= 11 * 60 && minutes < 12 * 60) return "timeOut";
    } else {
        if (minutes >= 12 * 60 + 40 && minutes < 14 * 60) return "timeIn";
        if (minutes >= 16 * 60 + 30 && minutes < 17 * 60 + 30) return "timeOut";
    }
    return "closed";
}

// Returns the current Manila time for attendance records.
function manilaTimeString() {
    const now = manilaNow();
    return `${String(now.getHours()).padStart(2, "0")}:${String(now.getMinutes()).padStart(2, "0")}`;
}

// Adds the automatic demerit created by a late formation scan.
function addAutomaticLateDemerit(cadetId, date) {
    const alreadyRecorded = conductRecords.some(record =>
        record.cadetId === String(cadetId) &&
        record.date === date &&
        record.type === "Demerit" &&
        record.reason === "Late formation scan"
    );
    if (alreadyRecorded) return;

    conductRecords.push({
        id: `late-${cadetId}-${date}`,
        cadetId: String(cadetId),
        date,
        type: "Demerit",
        points: 10,
        reason: "Late formation scan",
        recordedBy: "Automatic attendance rule"
    });
    persistConductRecord(conductRecords[conductRecords.length - 1]);
    renderConductRecords();
    updateConductStats();
}

// Converts a platoon identifier into its display name.
function platoonNameFromId(platoonId) {
    return ({1: "Alpha", 2: "Bravo", 3: "Charlie", 4: "Delta"})[platoonId] || "Unassigned";
}

// Loads the active cadet roster used by attendance.
function getCachedOfficerRoster() {
    try {
        const cached = JSON.parse(localStorage.getItem("rotc_officer_roster") || "[]");
        return Array.isArray(cached) ? cached : [];
    } catch (error) {
        return [];
    }
}

async function loadAttendanceRoster() {
    const body = document.getElementById("attendanceRecordsBody");
    if (body) body.innerHTML = '<tr><td colspan="7">Loading active cadets...</td></tr>';

    try {
        const response = await fetch("../backend/active-cadets.php", { cache: "no-store" });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || "Unable to load roster.");

        attendanceRoster = data.cadets.map(cadet => ({
            dbId: String(cadet.id),
            id: String(cadet.cadet_id),
            name: cadet.full_name,
            gender: cadet.gender || "",
            year: cadet.year_level || "Not specified",
            section: cadet.section || "Not specified",
            company: cadet.company || "",
            platoon: cadet.platoon_name || platoonNameFromId(cadet.platoon_id),
            status: cadet.status
        }));
        localStorage.setItem("rotc_officer_roster", JSON.stringify(attendanceRoster));
        cadetRecords.length = 0;
        cadetRecords.push(...attendanceRoster);
        const totalCadets = document.getElementById("totalCadets");
        if (totalCadets) totalCadets.textContent = cadetRecords.length;
        attendanceRosterLoaded = true;
        populateAttendanceFilters();
        renderAttendanceRoster();
        renderAttendanceOverviewChart();
    } catch (error) {
        const cachedRoster = getCachedOfficerRoster();
        if (cachedRoster.length) {
            attendanceRoster = cachedRoster;
            cadetRecords.length = 0;
            cadetRecords.push(...attendanceRoster);
            attendanceRosterLoaded = true;
            populateAttendanceFilters();
            renderAttendanceRoster();
            const results = document.getElementById("officerScanResults");
            if (results) results.innerHTML = '<p class="scan-empty">Offline mode: using the last saved cadet roster.</p>';
        } else if (body) {
            body.innerHTML = '<p class="scan-empty">No offline roster is available. Connect once to load the active cadets.</p>';
        }
    }
}

let registeredTrainingSessions = [];
let editingTrainingId = null;
let trainingViewMode = false;

// Loads registered training sessions for the officer.
async function loadRegisteredTraining() {
    const scheduleList = document.getElementById("trainingScheduleList");
    const announcementScheduleList = document.getElementById("announcementTrainingScheduleList");
    const upcomingList = document.getElementById("upcomingTrainingList");
    try {
        const response = await fetch("../backend/admin-training.php", { cache: "no-store" });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || "Unable to load training schedules.");

        const sessions = data.sessions || [];
        registeredTrainingSessions = sessions;
        const count = document.getElementById("upcomingTrainingCount");
        if (count) count.textContent = sessions.length;
        if (!sessions.length) {
            if (announcementScheduleList) announcementScheduleList.innerHTML = '<p class="scan-empty">No registered training schedules.</p>';
            return;
        }

        const formatScheduleTime = value => {
            const [hours, minutes] = String(value || "").split(":").map(Number);
            if (!Number.isFinite(hours) || !Number.isFinite(minutes)) return String(value || "");
            const period = hours >= 12 ? "PM" : "AM";
            const displayHour = hours % 12 || 12;
            return `${displayHour}:${String(minutes).padStart(2, "0")} ${period}`;
        };
        const card = session => {
            const date = new Date(`${session.training_date}T00:00:00`);
            const canManage = String(session.created_by) === String(getLoggedOfficerId());
            return `<div class="schedule-card">
                <div class="schedule-date"><strong>${date.getDate()}</strong><span>${date.toLocaleString("en-US", { month: "short" }).toUpperCase()}</span></div>
                <div class="schedule-info"><span class="badge">${escapeHtml(session.training_type)}</span><h3>${escapeHtml(session.title)}</h3><p>📅 ${escapeHtml(session.training_date)}</p><p>⏰ ${escapeHtml(formatScheduleTime(session.start_time))} - ${escapeHtml(formatScheduleTime(session.end_time))}</p><p>📍 ${escapeHtml(session.location)}</p>${session.instruction ? `<p>📌 ${escapeHtml(session.instruction)}</p>` : ''}<div class="table-actions"><button class="action-btn" type="button" onclick="viewTrainingSession(${Number(session.id)})">View</button>${canManage ? `<button class="action-btn" type="button" onclick="editTrainingSession(${Number(session.id)})">Edit</button><button class="danger-btn" type="button" onclick="deleteTrainingSession(${Number(session.id)})">Delete</button>` : ''}</div></div>
            </div>`;
        };
        if (scheduleList) scheduleList.innerHTML = sessions.map(card).join("");
        if (announcementScheduleList) announcementScheduleList.innerHTML = sessions.map(card).join("");
        if (upcomingList) upcomingList.innerHTML = sessions.slice(0, 3).map(session => `<div class="training-item"><div class="date-box"><strong>${new Date(`${session.training_date}T00:00:00`).getDate()}</strong><span>${new Date(`${session.training_date}T00:00:00`).toLocaleString("en-US", { month: "short" }).toUpperCase()}</span></div><div><strong>${escapeHtml(session.title)}</strong><p>${escapeHtml(formatScheduleTime(session.start_time))} • ${escapeHtml(session.location)}</p>${session.instruction ? `<p>${escapeHtml(session.instruction)}</p>` : ''}</div></div>`).join("");
    } catch (error) {
        console.error("Unable to load registered training schedules:", error);
    }
}

// Loads announcements addressed to officers.
async function loadOfficerAnnouncements() {
    const container = document.getElementById('officerAnnouncementsList');
    if (!container) return;

    try {
        const response = await fetch('../backend/officer-announcements.php', { cache: 'no-store' });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Unable to load announcements.');
        container.innerHTML = data.announcements.length ? data.announcements.map(item => `
            <div class="training-card">
                <div class="training-info">
                    <h3>${item.is_admin_announcement ? 'Admin: ' : ''}${escapeHtml(item.title)}</h3>
                    <p>${escapeHtml(item.message).replace(/\r?\n/g, '<br>')}</p>
                    ${(item.attachments || []).length ? `<div class="announcement-attachments">${(item.attachments || []).map(attachment => `<a href="../${escapeHtml(attachment.file_path)}" target="_blank" rel="noopener">${escapeHtml(attachment.original_name)}</a>`).join('')}</div>` : ''}
                    <small>Published by ${escapeHtml(item.officer_name)} · ${escapeHtml(item.created_at)}</small>
                    ${(item.replies || []).map(reply => `<div class="announcement-reply"><strong>${escapeHtml(reply.officer_name)} (${escapeHtml(reply.sender_role || 'officer')})</strong><span>${escapeHtml(reply.message)}</span><small>${escapeHtml(reply.created_at)}</small></div>`).join('')}
                    ${item.is_admin_announcement ? `<div class="announcement-reply-form"><textarea id="announcement-reply-${Number(item.id)}" rows="2" placeholder="Reply to the administrator..."></textarea><button class="action-btn" type="button" onclick="replyToAdminAnnouncement(${Number(item.id)})">Reply</button></div>` : ''}
                </div>
            </div>
        `).join('') : '<p class="scan-empty">No announcements published yet.</p>';
    } catch (error) {
        container.innerHTML = `<p class="scan-empty">${escapeHtml(error.message)}</p>`;
    }
}

// Sends the officer's reply to an administrator announcement.
async function replyToAdminAnnouncement(announcementId) {
    const officer = JSON.parse(sessionStorage.getItem('rotc_logged_officer') || 'null');
    const input = document.getElementById(`announcement-reply-${announcementId}`);
    const message = input?.value.trim() || '';
    if (!officer?.id || !message) {
        alert('Write a reply first.');
        return;
    }

    const response = await fetch('../backend/officer-announcements.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'reply', officer_id: officer.id, announcement_id: announcementId, message })
    });
    const data = await response.json();
    alert(data.message);
    if (data.success) loadOfficerAnnouncements();
}

// Publishes a new announcement for the officer audience.
async function publishAnnouncement() {
    const officer = JSON.parse(sessionStorage.getItem('rotc_logged_officer') || 'null');
    const titleInput = document.getElementById('announcementTitleInput');
    const messageInput = document.getElementById('announcementMessageInput');
    const title = titleInput.value.trim();
    const message = messageInput.value.trim();

    if (!officer?.id || !title || !message) {
        alert('Enter an announcement title and message.');
        return;
    }

const fileInput = document.getElementById('announcementFileInput');
    const file = fileInput?.files?.[0] || null;
    if (file && file.size > 50 * 1024 * 1024) {
        alert('Attachment must be 50 MB or less.');
        return;
    }

    const formData = new FormData();
    formData.append('officer_id', String(officer.id));
    formData.append('title', title);
    formData.append('message', message);
    if (file) formData.append('attachment', file);

    const response = await fetch('../backend/officer-announcements.php', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    alert(data.message);
    if (data.success) {
        titleInput.value = '';
        messageInput.value = '';
        if (fileInput) fileInput.value = '';
        loadOfficerAnnouncements();
    }
}

// Renders the attendance roster and its status controls.
function renderAttendanceRoster() {
    const body = document.getElementById("attendanceRecordsBody");
    if (!body) return;

    if (!attendanceRoster.length) {
        body.innerHTML = '<p class="scan-empty">No active cadets found.</p>';
        updateAttendanceCounts();
        return;
    }

    const currentDate = todayDate();
    const sessionType = getAttendanceSessionType();
    const battalions = [
        { name: "1st Battalion", subtitle: "Boys", gender: "male" },
        { name: "2nd Battalion", subtitle: "Girls", gender: "female" }
    ];
    const genderForCadet = cadet => {
        const value = String(cadet.gender || cadet.sex || "").trim().toLowerCase();
        if (["male", "m", "boy", "men"].includes(value)) return "male";
        if (["female", "f", "girl", "women"].includes(value)) return "female";
        return "unassigned";
    };
    const formatTime = value => value && value !== "—" ? String(value).slice(0, 5) : "—";
    const formationOptions = [...(document.getElementById("attendanceSessionTypeInput")?.options || [])]
        .map(option => `<option value="${escapeHtml(option.value)}"${option.value === document.getElementById("attendanceSessionTypeInput")?.value ? " selected" : ""}>${escapeHtml(option.textContent)}</option>`)
        .join("");
    const periodOptions = [...(document.getElementById("attendanceSessionPeriodInput")?.options || [])]
        .map(option => `<option value="${escapeHtml(option.value)}"${option.value === document.getElementById("attendanceSessionPeriodInput")?.value ? " selected" : ""}>${escapeHtml(option.textContent)}</option>`)
        .join("");
    const unassignedCadets = attendanceRoster.filter(cadet => genderForCadet(cadet) === "unassigned");
    if (unassignedCadets.length) battalions.push({ name: "Unassigned", subtitle: "Gender not specified", gender: "unassigned" });
    const statusMarkup = status => {
        const statusClass = status.toLowerCase().replace(" ", "-");
        const className = statusClass === "present" ? "active" : statusClass === "late" ? "warning" : statusClass === "absent" ? "danger" : "";
        return `<span class="status ${className}">${escapeHtml(status)}</span>`;
    };
    const cadetRow = (cadet, index) => {
        const key = formationAttendanceKey(cadet.id, currentDate, sessionType);
        const record = formationAttendance[key];
        const status = record?.status || "Not scanned";
        return `<tr class="attendance-cadet-row${index >= 4 ? " attendance-cadet-card-hidden" : ""}">
            <td>${index + 1}</td>
            <td>${escapeHtml(cadet.name)}</td>
            <td>${escapeHtml(cadet.id)}</td>
            <td>${statusMarkup(status)}</td>
        </tr>`;
    };
    body.innerHTML = `<div class="attendance-battalion-grid-inner">${battalions.map(battalion => {
        const cardFilter = attendanceCardFilters[battalion.gender] || { company: "", platoon: "" };
        const sourceRoster = attendanceRoster.filter(cadet => genderForCadet(cadet) === battalion.gender);
        const companies = attendanceCompanies;
        const platoons = attendancePlatoons;
        const cadets = sourceRoster.filter(cadet =>
            attendanceCompanyMatches(cadet, cardFilter.company) &&
            attendancePlatoonMatches(cadet, cardFilter.platoon)
        );
        const presentCount = cadets.filter(cadet => {
            const record = formationAttendance[formationAttendanceKey(cadet.id, currentDate, sessionType)];
            return record?.status === "Present" || record?.status === "Late";
        }).length;
        const listId = `attendance-${battalion.gender}-cadets`;
        return `<section class="attendance-battalion-card">
            <header class="attendance-battalion-header"><div class="attendance-battalion-mark">★</div><div><h3>${battalion.name}</h3><span>CADETS PRESENT</span></div><div class="attendance-battalion-count"><strong>${presentCount}/${cadets.length}</strong><small>PRESENT</small></div></header>
            <div class="attendance-card-controls">
                <label><span>FORMATION</span><select onchange="syncOfficerScanFormation(this.value)">${formationOptions}</select></label>
                <label><span>PERIOD</span><select onchange="syncOfficerScanPeriod(this.value)">${periodOptions}</select></label>
                <label><span>COMPANY</span><select onchange="updateAttendanceCardFilter('${battalion.gender}', 'company', this.value)"><option value="">All Companies</option>${companies.map(company => `<option value="${escapeHtml(company)}"${company === cardFilter.company ? " selected" : ""}>${escapeHtml(company)}</option>`).join("")}</select></label>
                <label><span>PLATOON</span><select onchange="updateAttendanceCardFilter('${battalion.gender}', 'platoon', this.value)"><option value="">All Platoons</option>${platoons.map(platoon => `<option value="${escapeHtml(platoon)}"${platoon === cardFilter.platoon ? " selected" : ""}>${escapeHtml(platoon)}</option>`).join("")}</select></label>
            </div>
            <div class="attendance-session-strip"><div><span>FORMATION</span><strong>${escapeHtml(sessionType)}</strong></div><div><span>SESSION</span><strong>${escapeHtml(currentDate)}</strong></div></div>
            <div class="attendance-battalion-title"><strong>♟ PRESENT CADETS</strong><span>${presentCount} of ${cadets.length}</span></div>
            ${cadets.length ? `<div class="attendance-cadet-list"><table class="attendance-cadet-table"><thead><tr><th>#</th><th>NAME</th><th>CADET ID</th><th>STATUS</th></tr></thead><tbody id="${listId}">${cadets.map((cadet, index) => cadetRow(cadet, index)).join("")}</tbody></table></div>` : '<div class="attendance-cadet-list"><p class="scan-empty">No cadets assigned.</p></div>'}
            ${cadets.length ? `<button class="attendance-view-all" type="button" onclick="toggleAttendanceCadets('${listId}', this)">View All (${cadets.length})</button>` : ""}
        </section>`;
    }).join("")}</div>`;
    updateAttendanceCounts();
}

// Expands or collapses an attendance cadet list.
function toggleAttendanceCadets(listId, button) {
    const list = document.getElementById(listId);
    if (!list || !button) return;
    const hiddenRows = list.querySelectorAll(".attendance-cadet-card-hidden");
    if (!hiddenRows.length) {
        button.textContent = "All Cadets Shown";
        return;
    }
    const expanded = list.classList.toggle("attendance-cadet-list-expanded");
    button.textContent = expanded ? "Show Less" : `View All (${list.children.length})`;
}

// Updates attendance totals shown in the dashboard.
function updateAttendanceCounts() {
    const currentDate = todayDate();
    const sessionType = getAttendanceSessionType();
    const statuses = Object.values(formationAttendance)
        .filter(record => record.date === currentDate && record.trainingType === sessionType)
        .map(record => record.status);
    const present = statuses.filter(status => status === "Present").length;
    const late = statuses.filter(status => status === "Late").length;
    const absent = Math.max(attendanceRoster.length - present - late, 0);
    document.getElementById("attendancePresentCount").textContent = present;
    document.getElementById("attendanceLateCount").textContent = late;
    document.getElementById("attendanceAbsentCount").textContent = absent;
}

// Records or updates a cadet's attendance status.
function markCadetAttendance(cadetId, status = "Present", date = todayDate()) {
    const sessionType = getAttendanceSessionType();
    const key = formationAttendanceKey(cadetId, date, sessionType);
    const attendanceKey = String(cadetId);
    const existingAttendance = formationAttendance[key];
    const existingRecord = attendanceRecords.find(record => record.cadetId === attendanceKey && record.date === date && attendanceRecordMatchesSession(record, sessionType));
    const scanPhase = formationScanPhase(sessionType);

    if (existingAttendance?.timeOut || existingRecord?.timeOut) {
        renderAttendanceRoster();
        return;
    }

    if (existingAttendance?.timeIn || existingRecord?.timeIn) {
        if (scanPhase !== "timeOut") {
            alert(`Time out scanning for ${sessionType} is only available during its logout window.`);
            return;
        }
        const scanTime = manilaTimeString();
        const timeOut = scanTime;
        formationAttendance[key] = {
            date,
            trainingType: sessionType,
            status: existingAttendance?.status || existingRecord.status,
            timeIn: existingAttendance?.timeIn || existingRecord.timeIn,
            timeOut
        };
        if (existingRecord) existingRecord.timeOut = timeOut;
        persistAttendanceRecord(existingRecord || {
            cadetId: attendanceKey,
            date,
            trainingType: sessionType,
            status: formationAttendance[key].status,
            timeIn: formationAttendance[key].timeIn,
            timeOut,
            remarks: "Formation QR attendance"
        });
        renderAttendanceRoster();
        return;
    }

    if (scanPhase !== "timeIn") {
        alert(`Time in scanning for ${sessionType} is outside its allowed window.`);
        return;
    }

    const attendanceStatus = status === "Absent" ? "Absent" : formationStatusAtScan();
    const scanTime = manilaTimeString();
    formationAttendance[key] = {
        date,
        trainingType: sessionType,
        status: attendanceStatus,
        timeIn: existingAttendance?.timeIn || scanTime,
        timeOut: existingAttendance?.timeOut || null
    };

    if (attendanceStatus === "Late") addAutomaticLateDemerit(cadetId, date);

    if (existingRecord) {
        existingRecord.status = attendanceStatus;
        existingRecord.trainingType = sessionType;
        if (!existingRecord.timeIn) existingRecord.timeIn = scanTime;
        if (existingRecord.timeOut === undefined) existingRecord.timeOut = null;
    } else {
        attendanceRecords.push({
            cadetId: attendanceKey,
            date,
            trainingType: sessionType,
            status: attendanceStatus,
            timeIn: formationAttendance[key].timeIn,
            timeOut: null,
            remarks: "Formation QR attendance"
        });
    }

    persistAttendanceRecord(attendanceRecords.find(record => record.cadetId === attendanceKey && record.date === date && attendanceRecordMatchesSession(record, sessionType)));
    renderAttendanceRoster();
}

// Records the time-out value for a cadet's attendance.
function markCadetTimeOut(cadetId, date = todayDate()) {
    const sessionType = getAttendanceSessionType();
    const key = formationAttendanceKey(cadetId, date, sessionType);
    const record = formationAttendance[key];
    if (!record || record.status === "Absent") return;

    record.timeOut = manilaTimeString();
    const savedAttendance = attendanceRecords.find(item => item.cadetId === String(cadetId) && item.date === date && attendanceRecordMatchesSession(item, sessionType));
    if (savedAttendance) savedAttendance.timeOut = record.timeOut;
    if (savedAttendance) persistAttendanceRecord(savedAttendance);
    renderAttendanceRoster();
}

// Finalizes missing attendance records for a formation session.
function finalizeFormationAttendance(sessionType = getAttendanceSessionType(), finalStatus = "Absent") {
    if (!attendanceRosterLoaded) {
        alert("Load the attendance roster before finalizing.");
        return;
    }

    const currentDate = todayDate();
    const recordsToPersist = [];
    attendanceRoster.forEach(cadet => {
        const key = formationAttendanceKey(cadet.id, currentDate, sessionType);
        if (!formationAttendance[key]) {
            formationAttendance[key] = {
                date: currentDate,
                trainingType: sessionType,
                status: finalStatus,
                timeIn: "—",
                timeOut: null
            };
            const absentRecord = {
                cadetId: String(cadet.id),
                date: currentDate,
                trainingType: sessionType,
                status: finalStatus,
                timeIn: null,
                timeOut: null,
                remarks: "Formation attendance finalized"
            };
            attendanceRecords.push(absentRecord);
            recordsToPersist.push(absentRecord);
        }
    });
    formationFinalizedDate[`${currentDate}::${sessionType}`] = true;
    renderAttendanceRoster();
    recordsToPersist.forEach(persistAttendanceRecord);
}

// Finalizes formation attendance when the session window closes.
function automaticallyFinalizeFormation() {
    const now = manilaNow();
    const minutes = minutesSinceMidnight(now);
    const date = todayDate();
    const finalize = (sessionType, finalStatus, key) => {
        if (formationFinalizedDate[key]) return;
        formationFinalizedDate[key] = true;
        if (attendanceRosterLoaded) {
            finalizeFormationAttendance(sessionType, finalStatus);
        } else {
            loadAttendanceRoster().then(() => finalizeFormationAttendance(sessionType, finalStatus));
        }
    };
    if (minutes >= 12 * 60) finalize("Morning Formation", "Absent", `${date}::Morning Formation`);
    if (minutes >= 17 * 60 + 30) finalize("Afternoon Formation", "Late", `${date}::Afternoon Formation`);
}

// Opens the officer QR scanner on the requested page.
function openOfficerScanner(targetPage = "scan") {
    officerScannerTarget = targetPage;
    showPage("scan");
    if (targetPage === "attendance" && !attendanceRosterLoaded) loadAttendanceRoster();
    window.setTimeout(startCadetScanner, 0);
}

// Extracts searchable cadet identifiers from QR content.
function getQrSearchValues(decodedText) {
    const values = [String(decodedText || "").trim()];

    try {
        const parsed = JSON.parse(decodedText);
        [parsed.cadet_id, parsed.cadetId, parsed.id, parsed.code, parsed.value, parsed.data]
            .filter(value => typeof value === "string" || typeof value === "number")
            .forEach(value => values.push(String(value).trim()));
    } catch (error) {
        // QR data is often plain text, so invalid JSON is expected.
    }

    try {
        const url = new URL(decodedText);
        const lastPart = url.pathname.split("/").filter(Boolean).pop();
        if (lastPart) values.push(decodeURIComponent(lastPart));
    } catch (error) {
        // The value is not a URL; the raw text remains a valid search value.
    }

    return [...new Set(values.filter(Boolean))];
}

// Displays available attendance actions for a scanned cadet.
function showAttendanceScanAction(cadet) {
    const action = document.getElementById("attendanceScanAction");
    if (!action) return;

    action.hidden = false;
    action.innerHTML = `
        <div class="card-header">
            <div>
                <h3>QR cadet found</h3>
                <p>${escapeHtml(cadet.name)} (${escapeHtml(cadet.id)}) is ready for attendance.</p>
            </div>
            <span class="badge">Scan processed automatically</span>
        </div>
    `;
}

// Routes decoded QR data into the attendance workflow.
async function routeQrToAttendance(decodedText) {
    const searchValues = getQrSearchValues(decodedText);
    let matchedCadet = null;

    for (const value of searchValues) {
        try {
            const response = await fetch("../backend/search-cadet.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ search: value, status: "Active" })
            });
            const data = await response.json();
            if (data.success && data.results?.length) {
                matchedCadet = data.results[0];
                break;
            }
        } catch (error) {
            // Continue trying other values from the QR payload.
        }
    }

    if (!matchedCadet) {
        const cachedMatch = attendanceRoster.find(cadet => searchValues.some(value =>
            String(cadet.id || "").toLowerCase() === String(value).toLowerCase()
        ));
        if (cachedMatch) {
            matchedCadet = {
                cadet_id: cachedMatch.id,
                full_name: cachedMatch.name,
                year_level: cachedMatch.year,
                section: cachedMatch.section,
                status: cachedMatch.status || "Active"
            };
        }
    }

    const action = document.getElementById("attendanceScanAction");

    if (!matchedCadet || String(matchedCadet.status || "").toLowerCase() !== "active") {
        const results = document.getElementById("officerScanResults");
        if (results) results.innerHTML = `<p class="scan-empty">Only registered and approved cadet QR codes can be scanned.</p>`;
        return;
    }

    const localId = String(matchedCadet.cadet_id);
    if (!attendanceRosterLoaded) {
        await loadAttendanceRoster();
    }
    let localCadet = cadetRecords.find(cadet => cadet.id === localId);
    if (!localCadet) {
        localCadet = {
            id: localId,
            name: matchedCadet.full_name,
            year: matchedCadet.year_level || "Not specified",
            section: matchedCadet.section || "Not specified",
            platoon: ({1: "Alpha", 2: "Bravo", 3: "Charlie", 4: "Delta"})[matchedCadet.platoon_id] || "Unassigned",
            status: matchedCadet.status || "Active"
        };
        cadetRecords.push(localCadet);
    }

    markCadetAttendance(localId, "Present");
    showAttendanceScanAction(localCadet);
    const results = document.getElementById("officerScanResults");
    if (results) results.innerHTML = `<p class="scan-empty">Attendance recorded for ${escapeHtml(localCadet.name)} (${escapeHtml(localCadet.id)}). Scan the next cadet or stop the camera.</p>`;
}

// Renders cadets returned by an officer scan search.
function renderOfficerScanResults(results) {
    const container = document.getElementById("officerScanResults");
    if (!container) return;

    if (!results.length) {
        container.innerHTML = '<p class="scan-empty">No cadet records matched that search.</p>';
        return;
    }

    container.innerHTML = results.map(cadet => `
        <div class="scan-result">
            <div>
                <h4>${escapeHtml(cadet.full_name)}</h4>
                <p>${escapeHtml(cadet.cadet_id)} &bull; ${escapeHtml(cadet.email)} &bull; ${escapeHtml(cadet.course)}</p>
            </div>
            <span class="badge">${escapeHtml(cadet.status)}</span>
        </div>
    `).join("");
}

// Searches the last saved roster without requiring a network connection.
function searchCachedOfficerCadets(search) {
    const query = String(search || "").toLowerCase();
    return attendanceRoster
        .filter(cadet => [cadet.id, cadet.name, cadet.email, cadet.course].some(value => String(value || "").toLowerCase().includes(query)))
        .map(cadet => ({
            full_name: cadet.name,
            cadet_id: cadet.id,
            email: cadet.email || "Offline roster",
            course: cadet.course || "",
            status: cadet.status || "Active"
        }));
}

// Searches active cadets for the officer scanner.
async function searchOfficerCadet(searchValue = "") {
    const input = document.getElementById("officerSearchInput");
    const container = document.getElementById("officerScanResults");
    const search = (searchValue || input?.value || "").trim();

    if (!search) {
        container.innerHTML = '<p class="scan-empty">Enter a cadet ID, email, or name.</p>';
        return;
    }

    if (!navigator.onLine) {
        renderOfficerScanResults(searchCachedOfficerCadets(search));
        return;
    }

    container.innerHTML = '<p class="scan-empty">Searching cadet records...</p>';

    try {
        const response = await fetch("../backend/search-cadet.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ search })
        });
        const data = await response.json();
        renderOfficerScanResults(data.success ? data.results : []);
    } catch (error) {
        renderOfficerScanResults(searchCachedOfficerCadets(search));
    }
}

// Starts the QR scanner used for cadet attendance.
async function startCadetScanner() {
    const reader = document.getElementById("officerQrReader");
    const stopButton = document.getElementById("stopOfficerScanner");
    if (!reader) return;

    if (typeof Html5Qrcode === "undefined") {
        alert("The QR scanner library is unavailable. Use the search field instead.");
        return;
    }

    reader.hidden = false;
    if (officerQrScanner) {
        if (stopButton) stopButton.hidden = false;
        return;
    }

    officerQrScanner = new Html5Qrcode("officerQrReader");
    if (stopButton) stopButton.hidden = false;
    try {
        await officerQrScanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            async decodedText => {
                const qrValue = String(decodedText || "").trim();
                if (!qrValue || officerQrScanInProgress || qrValue === officerLastQrValue) return;

                officerQrScanInProgress = true;
                officerLastQrValue = qrValue;
                try {
                    if (officerScannerTarget === "attendance") {
                        await routeQrToAttendance(qrValue);
                    } else {
                        document.getElementById("officerSearchInput").value = qrValue;
                        await searchOfficerCadet(qrValue);
                    }
                } finally {
                    officerQrScanInProgress = false;
                }
            },
            () => {}
        );
    } catch (error) {
        await stopCadetScanner();
        alert("Camera access was unavailable. Use the search field instead.");
    }
}

// Stops the active QR scanner and releases its camera.
async function stopCadetScanner() {
    const reader = document.getElementById("officerQrReader");
    const stopButton = document.getElementById("stopOfficerScanner");

    if (officerQrScanner) {
        try {
            await officerQrScanner.stop();
            officerQrScanner.clear();
        } catch (error) {
            console.warn("Unable to stop QR scanner cleanly.", error);
        }
        officerQrScanner = null;
    }

    officerQrScanInProgress = false;
    officerLastQrValue = "";

    if (reader) reader.hidden = true;
    if (stopButton) stopButton.hidden = true;
}

</script>

<script id="officer-dashboard-js">
// Renders the attendance overview chart data.
function renderAttendanceOverviewChart() {
    const bar = document.getElementById("attendanceBarFill");
    const percent = document.getElementById("attendanceOverviewPercent");
    const presentEl = document.getElementById("attendanceOverviewPresent");
    const absentEl = document.getElementById("attendanceOverviewAbsent");
    const presentTotalEl = document.getElementById("attendanceOverviewPresentTotal");
    const absentTotalEl = document.getElementById("attendanceOverviewAbsentTotal");

    if (!bar || !percent || !presentEl || !absentEl || !presentTotalEl || !absentTotalEl) return;

    const now = new Date();
    const monday = new Date(now);
    const offset = (now.getDay() + 6) % 7;
    monday.setDate(now.getDate() - offset);
    const mondayIso = monday.toISOString().split("T")[0];

    const weekRecords = attendanceRecords.filter(record => {
        const date = record.date;
        return date >= mondayIso && date <= new Date(monday.getTime() + (6 * 86400000)).toISOString().split("T")[0];
    });

    const uniqueCadets = new Set((attendanceRoster.length ? attendanceRoster : weekRecords.map(r => ({ id: r.cadetId }))).map(c => String(c.id)));
    const presentCadets = new Set();
    weekRecords.forEach(record => {
        if (record.status === "Present" || record.status === "Excused") {
            presentCadets.add(String(record.cadetId));
        }
    });

    const totalCadets = uniqueCadets.size;
    const presentCount = [...presentCadets].filter(id => uniqueCadets.has(id)).length;
    const absentCount = Math.max(totalCadets - presentCount, 0);
    const percentage = totalCadets ? Math.round((presentCount / totalCadets) * 100) : 0;

    bar.style.width = `${percentage}%`;
    percent.textContent = `${percentage}%`;
    presentEl.textContent = presentCount;
    absentEl.textContent = absentCount;
    presentTotalEl.textContent = totalCadets;
    absentTotalEl.textContent = totalCadets;
}

// Refreshes dashboard summaries and recent activity.
function renderOfficerDashboard() {
    renderAttendanceOverviewChart();

    const total = cadetRecords.length;
    const attendanceTotal = attendanceRecords.length;
    const present = attendanceRecords.filter(r => r.status === "Present" || r.status === "Excused").length;
    const attendanceRate = attendanceTotal ? Math.round((present / attendanceTotal) * 100) : 0;
    const merits = conductRecords.filter(r => r.type === "Merit").reduce((s,r) => s + Number(r.points), 0);
    const demerits = conductRecords.filter(r => r.type === "Demerit").reduce((s,r) => s + Number(r.points), 0);

    const set = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
    set("officerTotalCadets", total);
    set("officerAttendance", attendanceRate + "%");
    set("officerMerits", merits);
    set("officerDemerits", demerits);

    const platoonBox = document.getElementById("officerPlatoonCards");
    if (platoonBox) {
        platoonBox.innerHTML = ["Alpha","Bravo","Charlie","Delta"].map(platoon => {
            const cadets = cadetRecords.filter(c => c.platoon === platoon);
            const ids = cadets.map(c => c.id);
            const att = attendanceRecords.filter(a => ids.includes(a.cadetId));
            const p = att.filter(a => a.status === "Present" || a.status === "Excused").length;
            const rate = att.length ? Math.round((p / att.length) * 100) : 0;
            const net = ids.reduce((sum,id) => sum + getConductTotals(id).net, 0);
            return `<div class="command-card">
                <strong>${platoon} Platoon</strong>
                <span>${cadets.length} cadet(s)</span>
                <div style="margin-top:10px;font-size:11px"><b>${rate}%</b> attendance</div>
                <div style="font-size:10px;color:#718096;margin-top:3px">Net conduct: ${net >= 0 ? "+" : ""}${net}</div>
                <button class="secondary-btn" style="margin-top:9px" onclick="openPlatoon('${platoon}')">View Platoon</button>
            </div>`;
        }).join("");
    }

    const conductBox = document.getElementById("officerConductList");
    if (conductBox) {
        const recent = conductRecords.slice().sort((a,b) => String(b.date).localeCompare(String(a.date))).slice(0,5);
        conductBox.innerHTML = recent.length ? recent.map(r => {
            const c = cadetRecords.find(x => x.id === r.cadetId);
            return `<div class="officer-row">
                <div class="rank-badge">${r.type === "Merit" ? "+" : "−"}${r.points}</div>
                <div class="row-main"><strong>${c ? escapeHtml(c.name) : "Unknown Cadet"}</strong><p>${escapeHtml(r.reason)}</p></div>
                <div class="row-value" style="color:${r.type === "Merit" ? "#18733d" : "#b42318"}">${r.type}</div>
            </div>`;
        }).join("") : `<div style="font-size:11px;color:#718096;padding:10px">No conduct records available.</div>`;
    }

    const performanceBox = document.getElementById("officerPerformanceList");
    if (performanceBox) {
        const performance = cadetRecords.map(c => {
            const t = getConductTotals(c.id);
            return { ...c, ...t };
        }).sort((a,b) => b.net - a.net).slice(0,6);

        performanceBox.innerHTML = performance.length ? performance.map(c => `
            <div class="officer-row">
                <div class="rank-badge">${initials(c.name)}</div>
                <div class="row-main">
                    <strong>${escapeHtml(c.name)}</strong>
                    <p>${escapeHtml(c.id)} • ${escapeHtml(c.platoon)} Platoon • ${escapeHtml(c.year)}</p>
                </div>
                <div class="row-value">${c.net >= 0 ? "+" : ""}${c.net}</div>
                <button class="action-btn" onclick="recordCadet('${c.id}')">View</button>
            </div>
        `).join("") : `<div style="font-size:11px;color:#718096;padding:10px">No cadet data available.</div>`;
    }
}
</script>

<script>
// Returns the platoon names available for a company.
function getCompanyPlatoonNames(company) {
    if (company === "Unassigned") return ["Unassigned"];
    if (company === "HQ") return ["HQ", "Medics"];
    return [`${company} First`, `${company} Second`, `${company} Third`];
}

// Filters roster cadets by company and optional battalion.
function getRosterCadetsForCompany(company, battalionGender = null) {
    return cadetRecords.filter(cadet => {
        if (company === "Unassigned") {
            if (cadet.company && cadet.platoon) return false;
            const gender = String(cadet.gender || cadet.sex || "").trim().toLowerCase();
            if (!battalionGender) return true;
            const isMale = ["male", "m", "boy", "men"].includes(gender);
            const isFemale = ["female", "f", "girl", "women"].includes(gender);
            return battalionGender === "male" ? isMale : isFemale;
        }
        const value = String(cadet.company || "").replace(/ Company$/i, "");
        const matchesCompany = value === company;
        if (!matchesCompany) return false;

        if (company === "HQ") return true;

        if (!battalionGender) return true;

        const gender = String(cadet.gender || cadet.sex || "").trim().toLowerCase();
        const isMale = ["male", "m", "boy", "men"].includes(gender);
        const isFemale = ["female", "f", "girl", "women"].includes(gender);

        return battalionGender === "male" ? isMale : isFemale;
    }).slice(0, 37);
}

// Filters roster cadets by company, platoon, and battalion.
function getRosterCadetsForPlatoon(company, platoon, battalionGender = null) {
    const matches = cadetRecords.filter(cadet => {
        if (company === "Unassigned") {
            return !cadet.company || !cadet.platoon;
        }
        const cadetCompany = String(cadet.company || "").replace(/ Company$/i, "");
        const matchesCompany = cadetCompany === company;
        const matchesPlatoon = String(cadet.platoon || "") === platoon;
        if (!matchesCompany || !matchesPlatoon) return false;

        if (!battalionGender) return true;

        const gender = String(cadet.gender || cadet.sex || "").trim().toLowerCase();
        const isMale = ["male", "m", "boy", "men"].includes(gender);
        const isFemale = ["female", "f", "girl", "women"].includes(gender);
        return battalionGender === "male" ? isMale : isFemale;
    });
    return matches.slice(0, 37);
}

// Opens a detailed roster for one platoon.
function openPlatoonRoster(company, platoon, battalionGender = "male") {
    const titleEl = document.getElementById("rosterViewTitle");
    const subtitleEl = document.getElementById("rosterViewSubtitle");
    const rosterListEl = document.getElementById("rosterList");

    if (!titleEl || !subtitleEl || !rosterListEl) return;

    const cadets = getRosterCadetsForPlatoon(company, platoon, battalionGender);
    titleEl.textContent = `${platoon}`;
    subtitleEl.textContent = `${company} Company • ${cadets.length} cadet${cadets.length === 1 ? "" : "s"}`;

    const rows = cadets.length ? cadets.map(cadet => `
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border:1px solid #dfe8df; border-radius:10px; background:#ffffff;">
            <div>
                <strong style="display:block; color:#17351d;">${escapeHtml(cadet.name)}</strong>
                <span style="font-size:11px; color:#657166;">${escapeHtml(cadet.id || "Unknown ID")} • ${escapeHtml(cadet.company || company)} • ${escapeHtml(cadet.gender || cadet.sex || "Unknown")}</span>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                <button class="action-btn" type="button" onclick="recordCadet('${escapeHtml(cadet.id)}')">View Record</button>
                <button class="secondary-btn" type="button" onclick="document.getElementById('officerSearchInput').value='${escapeHtml(cadet.id)}'; searchOfficerCadet('${escapeHtml(cadet.id)}')">Search</button>
            </div>
        </div>
    `).join("") : `
        <div style="padding:18px; border:1px dashed #c7d7ca; border-radius:10px; background:#f8faf8; color:#67786c; text-align:center;">
            No cadets assigned to this platoon.
        </div>
    `;

    rosterListEl.innerHTML = `
        <div style="display:flex; flex-direction:column; gap:12px; width:100%;">
            <button class="secondary-btn" type="button" onclick="openCompanyRoster('${company}')" style="align-self:flex-start; margin-bottom:8px;">← Back to ${company} company</button>
            <div style="display:grid; gap:10px; width:100%;">
                ${rows}
            </div>
        </div>
    `;
}

// Opens the platoon summary for one company.
function openCompanyRoster(company) {
    const titleEl = document.getElementById("rosterViewTitle");
    const subtitleEl = document.getElementById("rosterViewSubtitle");
    const rosterListEl = document.getElementById("rosterList");

    if (!titleEl || !subtitleEl || !rosterListEl) return;

    const battalionGender = document.getElementById("rosterViewTitle")?.dataset?.gender || "male";
    const platoons = getCompanyPlatoonNames(company);

    titleEl.textContent = `${company} Company`;
    subtitleEl.textContent = `Cadets assigned to ${company} company.`;

    const entries = platoons.map(platoon => {
        const platoonCadets = getRosterCadetsForPlatoon(company, platoon, battalionGender);

        return `
            <div class="schedule-card" role="button" tabindex="0" onclick="openPlatoonRoster('${company}', '${platoon}', '${battalionGender}')" onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openPlatoonRoster('${company}', '${platoon}', '${battalionGender}'); }" style="border:1px solid #dfe8df; border-radius:12px; background:#f8faf8; cursor:pointer;">
                <div class="schedule-date">
                    <strong>${platoon.charAt(0)}</strong>
                    <span>${platoon.slice(0, 3).toUpperCase()}</span>
                </div>
                <div class="schedule-info">
                    <span class="badge">${escapeHtml(platoon)}</span>
                    <h3>${platoonCadets.length} Cadet${platoonCadets.length === 1 ? "" : "s"}</h3>
                    <div style="margin-top:10px; display:flex; justify-content:flex-end;">
                        <span style="font-size:11px; color:#1f5e32; font-weight:600;">View cadets →</span>
                    </div>
                </div>
            </div>
        `;
    }).join("");

    rosterListEl.innerHTML = `
        <div style="display:flex; flex-direction:column; gap:12px; width:100%;">
            <button class="secondary-btn" type="button" onclick="openRosterView('battalion')" style="align-self:flex-start; margin-bottom:8px;">← Back to battalion view</button>
            <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:14px; width:100%;">
                ${entries}
            </div>
        </div>
    `;
}

// Opens the requested battalion or company roster view.
function openRosterView(view = "battalion") {
    const titleEl = document.getElementById("rosterViewTitle");
    const subtitleEl = document.getElementById("rosterViewSubtitle");
    const rosterListEl = document.getElementById("rosterList");

    if (!titleEl || !subtitleEl || !rosterListEl) return;

    const companies = ["Alpha", "Bravo", "Charlie", "Delta", "Echo", "Foxtrot", "HQ", "Unassigned"];
    const battalions = [
        { name: "1st Battalion", subtitle: "Boys only", gender: "male" },
        { name: "2nd Battalion", subtitle: "Girls only", gender: "female" }
    ];

    titleEl.textContent = view === "company" ? "Company Roster" : "Battalion Roster";
    subtitleEl.textContent = view === "company"
        ? "Cadets grouped by company under the battalion."
        : "Cadets organized by battalion and company assignment.";

    if (view === "company") {
        const entries = companies.map(company => {
            const cadets = getRosterCadetsForCompany(company);
            return `
                <button class="secondary-btn" type="button" onclick="openCompanyRoster('${company}')" style="display:flex; align-items:center; justify-content:space-between; width:100%; padding:12px 14px; text-align:left; margin:6px 0;">
                    <span>${escapeHtml(company)}</span>
                    <span style="font-size:10px; opacity:0.8;">${cadets.length}</span>
                </button>
            `;
        }).join("");

        rosterListEl.innerHTML = entries || '<p class="scan-empty">No cadets found in the roster.</p>';
        return;
    }

    rosterListEl.innerHTML = `
        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:18px; width:100%;">
            ${battalions.map((battalion) => {
                const companyButtons = companies.map(company => {
                    const cadets = getRosterCadetsForCompany(company, battalion.gender);
                    return `
                        <div style="display:flex; flex-direction:column; gap:8px; background:#f5faf6; border:1px solid #dfe8df; border-radius:12px; padding:12px;">
                            <button class="secondary-btn" type="button" onclick="document.getElementById('rosterViewTitle').dataset.gender='${battalion.gender}'; openCompanyRoster('${company}')" style="display:flex; align-items:center; justify-content:space-between; width:100%; padding:12px 14px; text-align:left;">
                                <span>${escapeHtml(company)}</span>
                                <span style="font-size:10px; opacity:0.8;">${cadets.length}</span>
                            </button>
                        </div>
                    `;
                }).join("");

                return `
                    <div style="background:#f7faf7; border:1px solid #dfe8df; border-radius:14px; padding:18px; box-shadow:0 4px 12px rgba(25,55,38,0.04);">
                        <div style="margin-bottom:12px;">
                            <h3 style="margin:0 0 4px; font-size:16px; color:#17351d;">${battalion.name}</h3>
                            <p style="margin:0; color:#657166; font-size:11px;">${battalion.subtitle}</p>
                        </div>
                        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:10px;">
                            ${companyButtons}
                        </div>
                    </div>
                `;
            }).join("")}
        </div>
    `;
}

// Switches the visible officer dashboard page.
function showPage(pageId) {
    document.querySelectorAll(".page").forEach(p => p.classList.remove("active-page"));
    const page = document.getElementById(pageId);
    if (page) page.classList.add("active-page");

    document.querySelectorAll(".nav-link").forEach(link => {
        link.classList.toggle("active", link.dataset.page === pageId);
    });

    const pageTitle = document.getElementById("pageTitle");
    const pageDescription = document.getElementById("pageDescription");
    const title = page?.querySelector(".page-heading h2")?.textContent ||
                  page?.querySelector(".welcome h2")?.textContent?.replace("Welcome, ","").replace("!","") ||
                  pageId;
    if (pageTitle) pageTitle.textContent = title;
    if (pageDescription) pageDescription.textContent =
        page?.querySelector(".page-heading p")?.textContent || "Overview of ROTC activities and records";

    if (pageId === "roster") {
        openRosterView("battalion");
    }
    if (pageId === "conduct") {
        renderConductRecords();
        updateConductStats();
    }
    if (pageId === "attendance" && !attendanceRosterLoaded) {
        loadAttendanceRoster();
    }
    if (pageId === "officerDashboard") {
        renderOfficerDashboard();
    }
}

document.addEventListener("click", function (event) {
    const link = event.target.closest(".nav-link[data-page]");
    if (!link) return;
    event.preventDefault();
    showPage(link.dataset.page);
});

document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");
    if (menuBtn && sidebar) menuBtn.addEventListener("click", () => sidebar.classList.toggle("open"));

    document.querySelectorAll("[data-page-link]").forEach(btn => {
        btn.addEventListener("click", () => showPage(btn.dataset.pageLink));
    });

    showPage("dashboard");
    openRosterView("battalion");
    loadOfficerCadetRecords();
    loadAttendanceRoster();
    loadRegisteredTraining();
    loadOfficerGrades();
    loadWeightedEvaluations();
    loadOfficerAnnouncements();
    loadMyOfficerReports();
    loadOfficerNotifications();
    updatePlatoonCounts();
    updateConductStats();
    renderConductRecords();

    const totalEl = document.getElementById("totalCadets");
    if (totalEl) totalEl.textContent = cadetRecords.length;
});

// Opens a dashboard modal by element ID.
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add("show");
}
// Closes a dashboard modal by element ID.
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove("show");
}

// Opens the complete cadet roster modal.
function openTotalCadetsModal() {
    openModal("totalCadetsModal");
    loadTotalCadets();
}

// Loads instructions assigned to the officer.
async function loadOfficerInstructions() {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    const card = document.getElementById("officerInstructionsCard");
    const list = document.getElementById("officerInstructionsList");
    if (!officer?.id || !card || !list) return;

    try {
        const response = await fetch(`../backend/admin-instructions.php?officer_id=${encodeURIComponent(officer.id)}`);
        const data = await response.json();
        if (!data.success || !data.instructions.length) return;

        card.hidden = false;
        list.innerHTML = data.instructions.map(instruction => `
            <div class="instruction-item">
                <strong>${escapeHtml(instruction.message)}</strong>
                    ${(instruction.attachments || []).map(attachment => `
                        <div class="instruction-attachment">
                            <span>${escapeHtml(attachment.original_name)}</span>
                            <a href="../${escapeHtml(attachment.file_path)}" target="_blank" rel="noopener">View</a>
                            <a href="../${escapeHtml(attachment.file_path)}" download="${escapeHtml(attachment.original_name)}">Download</a>
                        </div>
                    `).join('')}
                <small>${new Date(instruction.created_at).toLocaleString()} ${Number(instruction.is_read) ? '' : `<button type="button" class="text-btn" onclick="markOfficerInstructionRead(${Number(instruction.id)})">Mark as read</button>`}</small>
            </div>
        `).join("");
    } catch (error) {
        console.error("Unable to load admin instructions:", error);
    }
}

let officerNotificationData = { instructions: [], announcements: [] };

// Loads officer notifications and announcement counts.
async function loadOfficerNotifications() {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    const count = document.getElementById('notificationCount');
    if (!officer?.id) return;

    try {
        const [instructionResponse, announcementResponse] = await Promise.all([
            fetch(`../backend/admin-instructions.php?officer_id=${encodeURIComponent(officer.id)}`, { cache: 'no-store' }),
            fetch('../backend/officer-announcements.php', { cache: 'no-store' })
        ]);
        const instructionData = await instructionResponse.json();
        const announcementData = await announcementResponse.json();
        officerNotificationData = {
            instructions: instructionData.success ? instructionData.instructions : [],
            announcements: announcementData.success ? announcementData.announcements : []
        };
        const unread = officerNotificationData.instructions.filter(item => !Number(item.is_read)).length;
        if (count) {
            count.textContent = unread;
            count.hidden = unread === 0;
        }
    } catch (error) {
        console.error('Unable to load officer notifications:', error);
    }
}

// Marks an officer instruction as read.
async function markOfficerInstructionRead(instructionId) {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    if (!officer?.id) return;
    await fetch('../backend/admin-instructions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_read', officer_id: officer.id, instruction_id: instructionId })
    });
    await loadOfficerInstructions();
    await loadOfficerNotifications();
    renderOfficerNotificationPanel();
}

// Renders officer instructions and announcement notifications.
function renderOfficerNotificationPanel() {
    const oldPanel = document.getElementById('officerNotificationPanel');
    if (oldPanel) oldPanel.remove();

    const panel = document.createElement('div');
    panel.id = 'officerNotificationPanel';
    panel.className = 'notification-panel';
    const instructionItems = officerNotificationData.instructions.map(item => `
        <div class="notification-item ${Number(item.is_read) ? '' : 'unread'}">
            <strong>Admin instruction</strong>
            <p>${escapeHtml(item.message)}</p>
            <small>${new Date(item.created_at).toLocaleString()}</small>
            ${Number(item.is_read) ? '' : `<button type="button" class="text-btn" onclick="markOfficerInstructionRead(${Number(item.id)})">Mark as read</button>`}
        </div>`).join('');
    const announcementItems = officerNotificationData.announcements.slice(0, 10).map(item => `
        <div class="notification-thread">
            <div class="notification-thread-heading">
                <strong>${escapeHtml(item.title)}</strong>
                <button type="button" class="text-btn" onclick="viewOfficerAnnouncement(${Number(item.id)})">View</button>
            </div>
            <div class="chat-bubble admin-bubble">
                <span class="chat-author">${item.is_admin_announcement ? 'Administrator' : escapeHtml(item.officer_name)}</span>
                <p>${escapeHtml(item.message)}</p>
                <small>${new Date(item.created_at).toLocaleString()}</small>
            </div>
            ${(item.replies || []).map(reply => `<div class="chat-bubble ${reply.sender_role === 'admin' ? 'admin-bubble' : 'officer-bubble'}"><span class="chat-author">${escapeHtml(reply.officer_name)}</span><p>${escapeHtml(reply.message)}</p><small>${new Date(reply.created_at).toLocaleString()}</small></div>`).join('')}
        </div>`).join('');
    panel.innerHTML = `<strong class="notification-panel-title">Notifications</strong>${instructionItems}${announcementItems || '<p class="scan-empty">No announcements available.</p>'}`;
    document.body.appendChild(panel);
}

// Opens an announcement selected from the notification panel.
function viewOfficerAnnouncement(announcementId) {
    document.getElementById('officerNotificationPanel')?.remove();
    showPage('announcements');
    loadOfficerAnnouncements();
    document.getElementById('announcements')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

document.getElementById('notificationBtn')?.addEventListener('click', async (event) => {
    event.stopPropagation();
    if (document.getElementById('officerNotificationPanel')) {
        document.getElementById('officerNotificationPanel').remove();
        return;
    }
    await loadOfficerNotifications();
    renderOfficerNotificationPanel();
});

// Opens the training-session form.
function openTrainingModal() {
    editingTrainingId = null;
    trainingViewMode = false;
    document.getElementById('trainingModalTitle').textContent = 'Add Training Session';
    document.getElementById('trainingSaveButton').textContent = 'Save Training';
    document.getElementById('trainingSaveButton').hidden = false;
    ['trainingDateInput', 'trainingTypeInput', 'trainingTitleInput', 'trainingLocationInput', 'trainingInstructionInput', 'trainingStartInput', 'trainingEndInput'].forEach(id => {
        document.getElementById(id).disabled = false;
    });
    openModal('trainingModal');
}

// Opens the officer report submission form.
function openOfficerReportModal() {
    editingOfficerReportId = null;
    document.getElementById('officerReportModalTitle').textContent = 'Add Report';
    document.getElementById('officerReportSaveButton').textContent = 'Save and Send to Admin';
    document.getElementById('officerReportType').value = 'Cadet';
    document.getElementById('officerReportTitle').value = '';
    document.getElementById('officerReportContent').value = '';
    document.getElementById('officerReportImages').value = '';
    openModal('officerReportModal');
}

let editingOfficerReportId = null;
let myOfficerReports = [];

// Loads an existing officer report into the edit form.
function editOfficerReport(reportId) {
    const report = myOfficerReports.find(item => Number(item.id) === Number(reportId));
    if (!report) return;

    editingOfficerReportId = report.id;
    document.getElementById('officerReportModalTitle').textContent = 'Edit Report';
    document.getElementById('officerReportSaveButton').textContent = 'Save Changes';
    document.getElementById('officerReportType').value = report.report_type;
    document.getElementById('officerReportTitle').value = report.title;
    document.getElementById('officerReportContent').value = report.content;
    document.getElementById('officerReportImages').value = '';
    openModal('officerReportModal');
}

// Deletes an officer report through the backend API.
async function deleteOfficerReport(reportId) {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    if (!officer?.id || !confirm('Delete this report?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('officer_id', officer.id);
    formData.append('report_id', reportId);
    const response = await fetch('../backend/officer-reports.php', { method: 'POST', body: formData });
    const data = await response.json();
    alert(data.message);
    if (data.success) loadMyOfficerReports();
}

// Loads reports submitted by the current officer.
async function loadMyOfficerReports() {
    const body = document.getElementById('myOfficerReportsBody');
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    if (!body || !officer?.id) return;

    try {
        const response = await fetch(`../backend/officer-reports.php?officer_id=${encodeURIComponent(officer.id)}`);
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Unable to load reports.');
        myOfficerReports = data.reports || [];
        body.innerHTML = myOfficerReports.length ? myOfficerReports.map(report => `
            <tr>
                <td>${escapeHtml(formatOfficerDate(report.created_at))}</td>
                <td>${escapeHtml(report.report_type)}</td>
                <td><strong>${escapeHtml(report.title)}</strong></td>
                <td class="table-actions"><button class="action-btn" type="button" onclick="editOfficerReport(${Number(report.id)})">Edit</button><button class="danger-btn" type="button" onclick="deleteOfficerReport(${Number(report.id)})">Delete</button></td>
            </tr>
        `).join('') : '<tr><td colspan="4">No reports submitted yet.</td></tr>';
    } catch (error) {
        console.error('Unable to load officer reports:', error);
        body.innerHTML = '<tr><td colspan="4">Unable to load reports.</td></tr>';
    }
}

// Formats a report date for officer-facing displays.
function formatOfficerDate(value) {
    return value ? new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A';
}

// Creates or updates the current officer report.
async function sendOfficerReport() {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    const payload = {
        officer_id: officer?.id,
        action: editingOfficerReportId ? 'update' : 'create',
        report_id: editingOfficerReportId || '',
        report_type: document.getElementById('officerReportType').value,
        title: document.getElementById('officerReportTitle').value.trim(),
        content: document.getElementById('officerReportContent').value.trim()
    };

    if (!payload.officer_id || !payload.title || !payload.content) {
        alert('Complete the report title and details.');
        return;
    }

    const formData = new FormData();
    Object.entries(payload).forEach(([key, value]) => formData.append(key, value));
    Array.from(document.getElementById('officerReportImages').files).forEach(file => {
        formData.append('images[]', file);
    });

    const response = await fetch('../backend/officer-reports.php', { method: 'POST', body: formData });
    const data = await response.json();
    alert(data.message);
    if (data.success) {
        closeModal('officerReportModal');
        editingOfficerReportId = null;
        document.getElementById('officerReportTitle').value = '';
        document.getElementById('officerReportContent').value = '';
        document.getElementById('officerReportImages').value = '';
        loadMyOfficerReports();
    }
}

// Creates a training session from the officer form.
async function createTrainingSession() {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    const payload = {
        officer_id: officer?.id,
        action: editingTrainingId ? 'update' : 'create',
        training_id: editingTrainingId || '',
        training_date: document.getElementById('trainingDateInput').value,
        training_type: document.getElementById('trainingTypeInput').value,
        title: document.getElementById('trainingTitleInput').value.trim(),
        location: document.getElementById('trainingLocationInput').value.trim(),
        instruction: document.getElementById('trainingInstructionInput').value.trim(),
        start_time: document.getElementById('trainingStartInput').value,
        end_time: document.getElementById('trainingEndInput').value
    };

    if (!payload.officer_id || !payload.training_date || !payload.title || !payload.location || !payload.start_time || !payload.end_time) {
        alert('Complete all training fields.');
        return;
    }

    const response = await fetch('../backend/officer-training.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const data = await response.json();
    alert(data.message);
    if (data.success) {
        closeModal('trainingModal');
        editingTrainingId = null;
        ['trainingDateInput', 'trainingTitleInput', 'trainingLocationInput', 'trainingInstructionInput', 'trainingStartInput', 'trainingEndInput'].forEach(id => {
            document.getElementById(id).value = '';
        });
        loadRegisteredTraining();
    }
}

// Fills training modal fields and applies read-only state.
function setTrainingModalFields(session, readOnly) {
    editingTrainingId = readOnly ? null : Number(session.id);
    trainingViewMode = readOnly;
    document.getElementById('trainingModalTitle').textContent = readOnly ? 'View Training Session' : 'Edit Training Session';
    document.getElementById('trainingSaveButton').textContent = 'Save Changes';
    document.getElementById('trainingSaveButton').hidden = readOnly;
    document.getElementById('trainingDateInput').value = session.training_date || '';
    document.getElementById('trainingTypeInput').value = session.training_type || '';
    document.getElementById('trainingTitleInput').value = session.title || '';
    document.getElementById('trainingLocationInput').value = session.location || '';
    document.getElementById('trainingInstructionInput').value = session.instruction || '';
    document.getElementById('trainingStartInput').value = session.start_time || '';
    document.getElementById('trainingEndInput').value = session.end_time || '';
    ['trainingDateInput', 'trainingTypeInput', 'trainingTitleInput', 'trainingLocationInput', 'trainingInstructionInput', 'trainingStartInput', 'trainingEndInput'].forEach(id => {
        document.getElementById(id).disabled = readOnly;
    });
    openModal('trainingModal');
}

// Displays a training session in read-only mode.
function viewTrainingSession(trainingId) {
    const session = registeredTrainingSessions.find(item => Number(item.id) === Number(trainingId));
    if (session) setTrainingModalFields(session, true);
}

// Loads a training session into the editable form.
function editTrainingSession(trainingId) {
    const session = registeredTrainingSessions.find(item => Number(item.id) === Number(trainingId));
    if (session && String(session.created_by) === String(getLoggedOfficerId())) setTrainingModalFields(session, false);
}

// Deletes a training session through the backend API.
async function deleteTrainingSession(trainingId) {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    if (!officer?.id || !confirm('Delete this training session?')) return;
    const response = await fetch('../backend/officer-training.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', officer_id: officer.id, training_id: trainingId })
    });
    const data = await response.json();
    alert(data.message);
    if (data.success) loadRegisteredTraining();
}

// Opens the officer grade entry modal.
function openGradeModal() {
    const select = document.getElementById('gradeCadetInput');
    select.innerHTML = attendanceRoster.length
        ? attendanceRoster.map(cadet => `<option value="${escapeHtml(cadet.dbId)}">${escapeHtml(cadet.name)} (${escapeHtml(cadet.id)})</option>`).join('')
        : '<option value="">No approved cadets available</option>';
    document.getElementById('gradeSubjectInput').value = 'ROTC';
    document.getElementById('gradeEvaluationInput').value = 'Satisfactory';
    document.getElementById('gradeScoreInput').value = '';
    openModal('gradeModal');
}

// Saves a grade entered by the officer.
async function saveOfficerGrade() {
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    const payload = {
        officer_id: officer?.id,
        cadet_id: document.getElementById('gradeCadetInput').value,
        subject: document.getElementById('gradeSubjectInput').value,
        semester: document.getElementById('gradeSemesterInput').value,
        term: document.getElementById('gradeTermInput').value,
        score: document.getElementById('gradeScoreInput').value,
        professional_evaluation: document.getElementById('gradeEvaluationInput').value
    };

    if (!payload.officer_id || !payload.cadet_id || payload.score === '' || Number(payload.score) < 0 || Number(payload.score) > 100) {
        alert('Select an approved cadet and enter a score from 0 to 100.');
        return;
    }

    const response = await fetch('../backend/officer-grade.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const data = await response.json();
    alert(data.message);
    if (data.success) {
        closeModal('gradeModal');
        loadOfficerGrades();
    }
}

let weightedEvaluationComponents = [];

// Loads and opens the weighted evaluation modal.
async function openWeightedEvaluationModal() {
    const select = document.getElementById('weightedCadetInput');
    select.innerHTML = attendanceRoster.length
        ? attendanceRoster.map(cadet => `<option value="${escapeHtml(cadet.dbId)}">${escapeHtml(cadet.name)} (${escapeHtml(cadet.id)})</option>`).join('')
        : '<option value="">No approved cadets available</option>';
    const response = await fetch('../backend/weighted-evaluation.php', { cache: 'no-store' });
    const data = await response.json();
    if (!data.success) {
        alert(data.message || 'Unable to load grading components.');
        return;
    }
    weightedEvaluationComponents = data.components || [];
    renderWeightedComponents(data.scores || {});
    openModal('weightedEvaluationModal');
}

// Renders weighted evaluation component inputs and scores.
function renderWeightedComponents(scores = {}) {
    const container = document.getElementById('weightedComponentsGrid');
    container.innerHTML = weightedEvaluationComponents.map(component => `
        <label><span>${escapeHtml(component.label)} (${Number(component.weight).toFixed(0)}%)</span><input class="weighted-score-input" data-component="${escapeHtml(component.component_key)}" type="number" min="0" max="100" step="0.01" value="${scores[component.component_key] ?? ''}" placeholder="0-100"></label>
    `).join('');
    container.querySelectorAll('input').forEach(input => input.addEventListener('input', updateWeightedGradePreview));
    updateWeightedGradePreview();
}

// Updates the calculated weighted grade preview.
function updateWeightedGradePreview() {
    const totalWeight = weightedEvaluationComponents.reduce((sum, component) => sum + Number(component.weight), 0);
    const weightedTotal = weightedEvaluationComponents.reduce((sum, component) => {
        const input = document.querySelector(`.weighted-score-input[data-component="${component.component_key}"]`);
        return sum + (input?.value === '' ? 0 : Number(input.value) * Number(component.weight) / 100);
    }, 0);
    document.getElementById('weightedGradePreview').textContent = totalWeight === 100 && weightedTotal ? `Final Grade: ${weightedTotal.toFixed(2)}` : 'Final Grade: —';
}

// Saves the weighted evaluation for the selected cadet.
async function saveWeightedEvaluation() {
    const officer = JSON.parse(sessionStorage.getItem('rotc_logged_officer') || 'null');
    const saveButton = document.querySelector('#weightedEvaluationModal .primary-btn');
    const scores = {};
    document.querySelectorAll('.weighted-score-input').forEach(input => { scores[input.dataset.component] = input.value; });
    const componentCount = weightedEvaluationComponents.length;
    const complete = componentCount > 0 && weightedEvaluationComponents.every(component => {
        const score = Number(scores[component.component_key]);
        return Number.isFinite(score) && score >= 0 && score <= 100;
    });
    if (!officer?.id || !document.getElementById('weightedCadetInput').value || !complete) {
        alert('Select a cadet and enter a score from 0 to 100 for every component.');
        return;
    }

    if (saveButton) {
        saveButton.disabled = true;
        saveButton.textContent = 'Saving...';
    }
    try {
        const response = await fetch('../backend/weighted-evaluation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ officer_id: officer.id, cadet_id: document.getElementById('weightedCadetInput').value, semester: document.getElementById('weightedSemesterInput').value, scores })
        });
        const data = await response.json();
        alert(data.message || 'Unable to save weighted evaluation.');
        if (data.success) {
            closeModal('weightedEvaluationModal');
            loadWeightedEvaluations();
        }
    } catch (error) {
        console.error('Unable to save weighted evaluation:', error);
        alert('Unable to save weighted evaluation. Check your connection and try again.');
    } finally {
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.textContent = 'Save Weighted Grade';
        }
    }
}

// Loads grades recorded by the current officer.
async function loadOfficerGrades() {
    const body = document.getElementById('gradesRecordsBody');
    if (!body) return;

    try {
        const response = await fetch('../backend/officer-grades.php', { cache: 'no-store' });
        const data = await response.json();
        if (!data.success) throw new Error(data.message);

        const grouped = {};
        (data.grades || []).forEach(grade => {
            const key = `${grade.cadet_id}-${grade.subject}-${grade.semester}`;
            grouped[key] ??= { ...grade };
            grouped[key][String(grade.term).toLowerCase()] = Number(grade.score);
        });

        const rows = Object.values(grouped).map(grade => {
            const scores = [grade.prelim, grade.midterm, grade.finals].filter(score => Number.isFinite(score));
            const weights = { prelim: 0.30, midterm: 0.30, finals: 0.40 };
            const availableWeight = Object.keys(weights).filter(term => Number.isFinite(grade[term])).reduce((sum, term) => sum + weights[term], 0);
            const weighted = Object.keys(weights).filter(term => Number.isFinite(grade[term])).reduce((sum, term) => sum + grade[term] * weights[term], 0);
            const average = availableWeight ? weighted / availableWeight : 0;
            const evaluation = calculateCadetEvaluation(grade.cadet_id, average);
            return `<tr><td>${escapeHtml(grade.cadet_code)}</td><td>${escapeHtml(grade.full_name)}</td><td>${escapeHtml(grade.semester || 'First Semester')}</td><td>${grade.prelim ?? '—'}</td><td>${grade.midterm ?? '—'}</td><td>${grade.finals ?? '—'}</td><td><strong>${average.toFixed(2)}</strong></td><td><strong>${evaluation.toFixed(2)}</strong></td></tr>`;
        });

        body.innerHTML = rows.length ? rows.join('') : '<tr><td colspan="8">No grade records available.</td></tr>';
    } catch (error) {
        console.error('Unable to load officer grades:', error);
        body.innerHTML = '<tr><td colspan="8">Unable to load grade records.</td></tr>';
    }
}

// Loads weighted evaluations for the officer dashboard.
async function loadWeightedEvaluations() {
    const body = document.getElementById('weightedEvaluationsBody');
    if (!body) return;
    try {
        const response = await fetch('../backend/weighted-evaluation.php', { cache: 'no-store' });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Unable to load weighted evaluations.');
        const cards = document.getElementById('weightedEvaluationCards');
        if (cards) {
            cards.innerHTML = data.evaluations.length ? data.evaluations.map(evaluation => `
                <div class="weighted-evaluation-card">
                    <div class="weighted-evaluation-card-header">
                        <div><strong>${escapeHtml(evaluation.full_name)}</strong><small>${escapeHtml(evaluation.cadet_code)} · ${escapeHtml(evaluation.semester)}</small></div>
                        <div class="weighted-evaluation-final">${evaluation.final_grade === null ? 'Incomplete' : Number(evaluation.final_grade).toFixed(2)}</div>
                    </div>
                    <div class="weighted-evaluation-components">${data.components.map(component => `<span>${escapeHtml(component.label)}: <b>${evaluation.scores[component.component_key] ?? '—'}</b></span>`).join('')}</div>
                </div>
            `).join('') : '';
        }
        body.innerHTML = data.evaluations.length ? data.evaluations.map(evaluation => `
            <tr>
                <td>${escapeHtml(evaluation.cadet_code)}</td>
                <td>${escapeHtml(evaluation.full_name)}</td>
                <td>${escapeHtml(evaluation.semester)}</td>
                <td>${data.components.map(component => `${escapeHtml(component.label)}: ${evaluation.scores[component.component_key] ?? '—'}`).join('<br>')}</td>
                <td><strong>${evaluation.final_grade === null ? 'Incomplete' : Number(evaluation.final_grade).toFixed(2)}</strong></td>
            </tr>
        `).join('') : '<tr><td colspan="5">No weighted evaluations saved yet.</td></tr>';
    } catch (error) {
        console.error('Unable to load weighted evaluations:', error);
        body.innerHTML = '<tr><td colspan="5">Unable to load weighted evaluations.</td></tr>';
    }
}

let officerRefreshInFlight = false;

// Reloads dashboard data and redraws officer views.
async function refreshOfficerDashboard() {
    if (officerRefreshInFlight || document.visibilityState !== 'visible') return;
    officerRefreshInFlight = true;
    try {
        await Promise.all([
            loadAttendanceRoster(),
            loadRegisteredTraining(),
            loadOfficerGrades(),
            loadWeightedEvaluations(),
            loadOfficerAnnouncements()
        ]);
    } finally {
        officerRefreshInFlight = false;
    }
}

window.setInterval(refreshOfficerDashboard, 10000);
window.setInterval(automaticallyFinalizeFormation, 60000);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        refreshOfficerDashboard();
        automaticallyFinalizeFormation();
    }
});

document.querySelectorAll(".modal-overlay").forEach(overlay => {
    overlay.addEventListener("click", function(e) {
        if (e.target === overlay) overlay.classList.remove("show");
    });
});

document.addEventListener("DOMContentLoaded", loadOfficerInstructions);
</script>
<script src="officer-session.js?v=2"></script>
<script src="officer-approvals.js?v=20260916-drop-cadet1"></script>
</body>
</html>