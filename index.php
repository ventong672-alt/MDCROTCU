<?php
header('Location: admin-dashboard.php', true, 302);
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROTC Management Information System</title>

    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
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

<body>

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
                <span>⌂</span> Dashboard
            </a>

            <a href="#" class="nav-link" data-page="cadets">
                <span>♙</span> Cadet Information
            </a>

            <a href="#" class="nav-link" data-page="attendance">
                <span>✓</span> Attendance
            </a>

            <a href="#" class="nav-link" data-page="training">
                <span>▣</span> Training Schedule
            </a>

            <a href="#" class="nav-link" data-page="grades">
                <span>★</span> Grades & Performance
            </a>

            <a href="#" class="nav-link" data-page="conduct">
                <span>✦</span> Merits & Demerits
            </a>

            <a href="#" class="nav-link" data-page="reports">
                <span>▤</span> Reports
            </a>

            <p class="menu-title">SYSTEM</p>

            <a href="#" class="nav-link" data-page="users">
                <span>⚙</span> User Management
            </a>

            <a href="#" class="nav-link" data-page="settings">
                <span>◉</span> Settings
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="avatar">A</div>
                <div>
                    <strong>Administrator</strong>
                    <small>System Admin</small>
                </div>
            </div>

            <button class="logout-btn" id="logoutBtn" type="button">
                ⇥ Logout
            </button>
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">

            <button class="menu-btn" id="menuBtn">☰</button>

            <div>
                <h1 id="pageTitle">Dashboard</h1>
                <p id="pageDescription">
                    Overview of ROTC activities and records
                </p>
            </div>

            <div class="topbar-right">
                <button class="notification" id="notificationBtn">
                    🔔
                    <span class="notification-count">3</span>
                </button>

                <div class="profile">
                    <div class="avatar">A</div>
                    <div>
                        <strong>Administrator</strong>
                        <small>Admin</small>
                    </div>
                </div>
            </div>

        </header>


        <!-- PAGE CONTENT -->
        <section class="content" id="content">

            <!-- DASHBOARD -->
            <div class="page active-page" id="dashboard">

                <div class="welcome">
                    <div>
                        <h2>Welcome back, Administrator!</h2>
                        <p>Here's what's happening with your ROTC records today.</p>
                    </div>

                </div>


                <!-- STATISTICS -->
                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-icon blue">♙</div>
                        <div>
                            <span>Total Cadets</span>
                            <h2 id="totalCadets">3</h2>
                            <small>Registered cadets</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">✓</div>
                        <div>
                            <span>Present Today</span>
                            <h2 id="presentCount">235</h2>
                            <small>Attendance recorded</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">▣</div>
                        <div>
                            <span>Upcoming Training</span>
                            <h2>3</h2>
                            <small>This week</small>
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

                        <div class="attendance-chart">

                            <div class="chart-bar">
                                <div class="bar" style="height: 85%"></div>
                                <span>Mon</span>
                            </div>

                            <div class="chart-bar">
                                <div class="bar" style="height: 92%"></div>
                                <span>Tue</span>
                            </div>

                            <div class="chart-bar">
                                <div class="bar" style="height: 78%"></div>
                                <span>Wed</span>
                            </div>

                            <div class="chart-bar">
                                <div class="bar" style="height: 96%"></div>
                                <span>Thu</span>
                            </div>

                            <div class="chart-bar">
                                <div class="bar" style="height: 90%"></div>
                                <span>Fri</span>
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

                        <div class="training-list">

                            <div class="training-item">
                                <div class="date-box">
                                    <strong>22</strong>
                                    <span>AUG</span>
                                </div>

                                <div>
                                    <strong>ROTC Formation</strong>
                                    <p>07:00 AM • Training Ground</p>
                                </div>
                            </div>

                            <div class="training-item">
                                <div class="date-box">
                                    <strong>29</strong>
                                    <span>AUG</span>
                                </div>

                                <div>
                                    <strong>Military Drill</strong>
                                    <p>07:00 AM • Training Ground</p>
                                </div>
                            </div>

                            <div class="training-item">
                                <div class="date-box">
                                    <strong>05</strong>
                                    <span>SEP</span>
                                </div>

                                <div>
                                    <strong>Leadership Training</strong>
                                    <p>08:00 AM • Classroom 1</p>
                                </div>
                            </div>

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

                    <div class="activity-list">

                        <div class="activity">
                            <div class="activity-icon green">✓</div>

                            <div>
                                <strong>Attendance recorded</strong>
                                <p>ROTC Formation - August 15, 2026</p>
                            </div>

                            <span>10 mins ago</span>
                        </div>

                        <div class="activity">
                            <div class="activity-icon blue">♙</div>

                            <div>
                                <strong>New cadet registered</strong>
                                <p>Cadet information was added</p>
                            </div>

                            <span>1 hour ago</span>
                        </div>

                        <div class="activity">
                            <div class="activity-icon purple">★</div>

                            <div>
                                <strong>Grades updated</strong>
                                <p>First semester grades were updated</p>
                            </div>

                            <span>2 hours ago</span>
                        </div>

                    </div>

                </div>

            </div>


            <!-- CADETS -->
            <div class="page" id="cadets">
                <div class="page-heading">
                    <div>
                        <h2>Cadet Information</h2>
                        <p>Select a platoon to access and record its cadets.</p>
                    </div>
                </div>

                <div id="platoonListView">
                    <div class="platoon-grid">
                        <div class="card platoon-card">
                            <div class="platoon-icon">A</div><h3>Alpha Platoon</h3>
                            <p><strong class="platoon-count" data-platoon="Alpha">2</strong> Cadets</p>
                            <button class="primary-btn" onclick="openPlatoon('Alpha')">Open Platoon</button>
                        </div>
                        <div class="card platoon-card">
                            <div class="platoon-icon">B</div><h3>Bravo Platoon</h3>
                            <p><strong class="platoon-count" data-platoon="Bravo">1</strong> Cadets</p>
                            <button class="primary-btn" onclick="openPlatoon('Bravo')">Open Platoon</button>
                        </div>
                        <div class="card platoon-card">
                            <div class="platoon-icon">C</div><h3>Charlie Platoon</h3>
                            <p><strong class="platoon-count" data-platoon="Charlie">0</strong> Cadets</p>
                            <button class="primary-btn" onclick="openPlatoon('Charlie')">Open Platoon</button>
                        </div>
                        <div class="card platoon-card">
                            <div class="platoon-icon">D</div><h3>Delta Platoon</h3>
                            <p><strong class="platoon-count" data-platoon="Delta">0</strong> Cadets</p>
                            <button class="primary-btn" onclick="openPlatoon('Delta')">Open Platoon</button>
                        </div>
                    </div>
                </div>

                <div id="platoonDetailView" style="display:none;">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <button class="secondary-btn" onclick="backToPlatoons()">← Back to Platoons</button>
                                <h3 id="selectedPlatoonTitle" style="margin-top:15px;">Alpha Platoon</h3>
                                <p id="selectedPlatoonSummary">Cadets assigned to this platoon.</p>
                            </div>
                            <button class="primary-btn" onclick="exportCurrentPlatoon()">Generate Excel</button>
                        </div>
                        <div class="toolbar">
                            <input type="text" id="platoonCadetSearch" placeholder="Search cadet name or ID..." oninput="filterPlatoonCadets()">
                            <select id="platoonYearFilter" onchange="filterPlatoonCadets()">
                                <option value="All">All Year Levels</option>
                                <option>First Year</option>
                                <option>Second Year</option>
                                <option>Third Year</option>
                                <option>Fourth Year</option>
                            </select>
                        </div>
                        <div class="table-container">
                            <table id="platoonCadetTable">
                                <thead>
                                    <tr>
                                        <th>Cadet ID</th><th>Name</th><th>Year Level</th>
                                        <th>Section</th><th>Status</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="platoonCadetBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ATTENDANCE -->
            <div class="page" id="attendance">

                <div class="page-heading">
                    <div>
                        <h2>Attendance Management</h2>
                        <p>Record and monitor cadet attendance.</p>
                    </div>

                    <button class="primary-btn">
                        + Record Attendance
                    </button>
                </div>

                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-icon green">✓</div>
                        <div>
                            <span>Present</span>
                            <h2>235</h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">×</div>
                        <div>
                            <span>Absent</span>
                            <h2>10</h2>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">!</div>
                        <div>
                            <span>Late</span>
                            <h2>5</h2>
                        </div>
                    </div>

                </div>

                <div class="card">

                    <div class="card-header">
                        <div>
                            <h3>Attendance Records</h3>
                            <p>August 21, 2026</p>
                        </div>
                    </div>

                    <div class="table-container">

                        <table>

                            <thead>
                                <tr>
                                    <th>Cadet ID</th>
                                    <th>Name</th>
                                    <th>Training</th>
                                    <th>Time In</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr><td colspan="5">No attendance records found.</td></tr>
                            </tbody>

                        </table>

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

                    <button class="primary-btn">
                        + Add Training
                    </button>

                </div>

                <div class="training-cards">

                    <div class="schedule-card">

                        <div class="schedule-date">
                            <strong>22</strong>
                            <span>AUG</span>
                        </div>

                        <div class="schedule-info">
                            <span class="badge">FORMATION</span>
                            <h3>ROTC Formation</h3>
                            <p>📅 August 22, 2026</p>
                            <p>⏰ 07:00 AM - 12:00 PM</p>
                            <p>📍 Training Ground</p>
                        </div>

                        <button class="action-btn">Manage</button>

                    </div>


                    <div class="schedule-card">

                        <div class="schedule-date">
                            <strong>29</strong>
                            <span>AUG</span>
                        </div>

                        <div class="schedule-info">
                            <span class="badge">DRILL</span>
                            <h3>Military Drill</h3>
                            <p>📅 August 29, 2026</p>
                            <p>⏰ 07:00 AM - 12:00 PM</p>
                            <p>📍 Training Ground</p>
                        </div>

                        <button class="action-btn">Manage</button>

                    </div>

                </div>

            </div>


            <!-- GRADES -->
            <div class="page" id="grades">

                <div class="page-heading">

                    <div>
                        <h2>Grades & Performance</h2>
                        <p>Manage cadet grades and performance records.</p>
                    </div>

                    <button class="primary-btn">
                        + Add Grade
                    </button>

                </div>

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
                                    <th>Military Science</th>
                                    <th>Leadership</th>
                                    <th>Performance</th>
                                    <th>Average</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>—</td>
                                    <td>No cadet records</td>
                                    <td>92</td>
                                    <td>90</td>
                                    <td>94</td>
                                    <td><strong>92.0%</strong></td>
                                </tr>

                                <tr>
                                    <td>—</td>
                                    <td>No cadet records</td>
                                    <td>88</td>
                                    <td>91</td>
                                    <td>89</td>
                                    <td><strong>89.3%</strong></td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>



            <!-- MERITS / DEMERITS -->
            <div class="page" id="conduct">
                <div class="page-heading">
                    <div>
                        <h2>Merits & Demerits</h2>
                        <p>Record and monitor cadet discipline, achievements, and conduct.</p>
                    </div>
                    <button class="primary-btn" onclick="openConductModal()">+ Add Record</button>
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
                            <p>Use the action button to add a merit or demerit to a cadet.</p>
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

            <!-- REPORTS -->
            <div class="page" id="reports">

                <div class="page-heading">

                    <div>
                        <h2>Reports</h2>
                        <p>Generate administrative ROTC reports.</p>
                    </div>

                </div>

                <div class="report-grid">

                    <div class="report-card">
                        <div class="report-icon">♙</div>
                        <h3>Cadet Report</h3>
                        <p>Generate a complete list of registered cadets.</p>
                        <button class="primary-btn" onclick="exportReportToExcel('cadet')">Generate Excel</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">✓</div>
                        <h3>Attendance Report</h3>
                        <p>Generate attendance records and summaries.</p>
                        <button class="primary-btn" onclick="exportReportToExcel('attendance')">Generate Excel</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">★</div>
                        <h3>Grades Report</h3>
                        <p>Generate cadet grades and performance reports.</p>
                        <button class="primary-btn" onclick="exportReportToExcel('grades')">Generate Excel</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">✦</div>
                        <h3>Merits & Demerits Report</h3>
                        <p>Generate cadet conduct, merit, and demerit records.</p>
                        <button class="primary-btn" onclick="exportReportToExcel('conduct')">Generate Excel</button>
                    </div>

                    <div class="report-card">
                        <div class="report-icon">▣</div>
                        <h3>Training Report</h3>
                        <p>Generate scheduled training activities.</p>
                        <button class="primary-btn" onclick="exportReportToExcel('training')">Generate Excel</button>
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

                    <button class="primary-btn">
                        + Add User
                    </button>

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
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>Administrator</td>
                                    <td>admin@mdci.edu.ph</td>
                                    <td><span class="role admin">Admin</span></td>
                                    <td><span class="status active">Active</span></td>
                                </tr>

                                <tr>
                                    <td>ROTC Officer</td>
                                    <td>officer@mdci.edu.ph</td>
                                    <td><span class="role officer">Officer</span></td>
                                    <td><span class="status active">Active</span></td>
                                </tr>

                                <tr>
                                    <td>Cadet</td>
                                    <td>cadet@mdci.edu.ph</td>
                                    <td><span class="role cadet">Cadet</span></td>
                                    <td><span class="status active">Active</span></td>
                                </tr>

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

let currentPlatoon = "";
let selectedCadetId = "";

function openPlatoon(platoon) {
    currentPlatoon = platoon;
    document.getElementById("platoonListView").style.display = "none";
    document.getElementById("platoonDetailView").style.display = "block";
    document.getElementById("selectedPlatoonTitle").textContent = platoon + " Platoon";
    document.getElementById("platoonCadetSearch").value = "";
    document.getElementById("platoonYearFilter").value = "All";
    renderPlatoonCadets();
}

function backToPlatoons() {
    currentPlatoon = "";
    document.getElementById("platoonDetailView").style.display = "none";
    document.getElementById("platoonListView").style.display = "block";
    updatePlatoonCounts();
}

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

function filterPlatoonCadets() {
    renderPlatoonCadets();
}

function updatePlatoonCounts() {
    document.querySelectorAll(".platoon-count").forEach(el => {
        el.textContent = cadetRecords.filter(c => c.platoon === el.dataset.platoon).length;
    });
}

function getConductTotals(cadetId) {
    const records = conductRecords.filter(r => r.cadetId === cadetId);
    const merits = records.filter(r => r.type === "Merit").reduce((sum, r) => sum + Number(r.points), 0);
    const demerits = records.filter(r => r.type === "Demerit").reduce((sum, r) => sum + Number(r.points), 0);
    return { merits, demerits, net: merits - demerits };
}

function getAttendanceRate(cadetId) {
    const records = attendanceRecords.filter(r => r.cadetId === cadetId);
    if (!records.length) return 0;
    const present = records.filter(r => r.status === "Present" || r.status === "Excused").length;
    return Math.round((present / records.length) * 100);
}

function initials(name) {
    return name.split(/\s+/).filter(Boolean).slice(0, 2).map(x => x[0]).join("").toUpperCase();
}

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

function showCadetConductHistory() {
    document.getElementById("conductSearch").value = selectedCadetId;
    document.getElementById("conductTypeFilter").value = "All";
    closeModal("recordModal");
    showPage("conduct");
    renderConductRecords();
}

function saveCadetRecord() {
    if (!selectedCadetId) return;

    const date = document.getElementById("recordDate").value;
    const attendance = document.getElementById("recordAttendance").value;
    const type = document.getElementById("recordConductType").value;
    const points = Number(document.getElementById("recordPoints").value || 1);
    const remarks = document.getElementById("recordRemarks").value.trim();

    attendanceRecords.push({
        cadetId: selectedCadetId,
        date,
        status: attendance,
        remarks
    });

    if (type) {
        conductRecords.push({
            id: Date.now(),
            cadetId: selectedCadetId,
            date,
            type,
            points,
            reason: remarks || "No reason provided",
            recordedBy: "Administrator"
        });
    }

    closeModal("recordModal");
    renderConductRecords();
    updateConductStats();
    alert("Cadet record saved successfully.");
}

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

document.getElementById("conductForm").addEventListener("submit", function(e) {
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

    conductRecords.push({
        id: Date.now(),
        cadetId,
        date,
        type,
        points,
        reason,
        recordedBy: "Administrator"
    });

    closeModal("conductModal");
    renderConductRecords();
    updateConductStats();
    alert(`${type} record saved successfully.`);
});

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

function deleteConductRecord(id) {
    if (!confirm("Delete this merit/demerit record?")) return;
    conductRecords = conductRecords.filter(r => r.id !== id);
    renderConductRecords();
    updateConductStats();

    if (selectedCadetId && document.getElementById("recordModal").classList.contains("show")) {
        renderCadetConductHistory(selectedCadetId);
    }
}

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

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, char => ({
        "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
    }[char]));
}

document.getElementById("cadetForm").addEventListener("submit", function(e) {
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

function exportReportToExcel(type) {
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

    downloadExcel(rows, fileName, sheetName);
}
</script>
<script>
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
                  page?.querySelector(".welcome h2")?.textContent?.replace("Welcome back, ","").replace("!","") ||
                  pageId;
    if (pageTitle) pageTitle.textContent = title;
    if (pageDescription) pageDescription.textContent =
        page?.querySelector(".page-heading p")?.textContent || "Overview of ROTC activities and records";

    if (pageId === "conduct") {
        renderConductRecords();
        updateConductStats();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".nav-link").forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const page = this.dataset.page;
            if (page === "cadets") {
                document.getElementById("platoonListView").style.display = "block";
                document.getElementById("platoonDetailView").style.display = "none";
            }
            showPage(page);
        });
    });

    const menuBtn = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");
    if (menuBtn && sidebar) menuBtn.addEventListener("click", () => sidebar.classList.toggle("open"));

    document.querySelectorAll("[data-page-link]").forEach(btn => {
        btn.addEventListener("click", () => showPage(btn.dataset.pageLink));
    });

    showPage("dashboard");
    updatePlatoonCounts();
    updateConductStats();
    renderConductRecords();

    const totalEl = document.getElementById("totalCadets");
    if (totalEl) totalEl.textContent = cadetRecords.length;
});

function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add("show");
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove("show");
}

document.querySelectorAll(".modal-overlay").forEach(overlay => {
    overlay.addEventListener("click", function(e) {
        if (e.target === overlay) overlay.classList.remove("show");
    });
});
</script>
</body>
</html>