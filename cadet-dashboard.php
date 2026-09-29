<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadet Portal - ROTC MIS</title>

    <link rel="stylesheet" href="cadet.css?v=20260915-training-modal-3">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">

            <div class="brand-logo">
                <img src="ROTCLOGO.png" alt="ROTC Logo">
            </div>

            <div class="brand-text">
                <h2>ARMY ROTC MIS</h2>
                <span>Cadet Portal</span>
            </div>

        </div>

        <nav>

            <p class="menu-title">MY RECORDS</p>

            <a href="#dashboard" class="nav-link active">
                <span>▣</span>
                My Dashboard
            </a><br>

            <a href="#grades" class="nav-link">
                <span>★</span>
                My Grades
            </a><br>

            <a href="#conduct" class="nav-link">
                <span>✦</span>
                My Merits & Demerits
            </a><br>

            <a href="#attendance" class="nav-link">
                <span>✓</span>
                My Attendance
            </a><br>


            <a href="#announcements" class="nav-link" data-page="announcements">
                <span>!</span> Announcements/Training Schedule
            </a><br><br>

            <a href="#evaluation" class="nav-link" data-page="evaluation">
                <span>★</span> My Evaluation of Grades
            </a><br><br>

        </nav>

        <div class="sidebar-bottom">

            
            </div>

            <button
                class="logout-btn"
                id="logoutButton"
                type="button">
                ⇥ Logout
            </button>

        </div>

    </aside>


    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">

            <button class="menu-btn" id="menuBtn">
                ☰
            </button>

            <div>
                
                <p>View your ROTC academic, conduct, and attendance records</p>
            </div>

            <div class="profile">

                <div class="avatar" id="topAvatar">
                    —
                </div>

                <div>
                    <strong id="topName">—</strong>
                    <small>Cadet • View Only</small>
                </div>

            </div>

        </header>


        <div class="content">

            <!-- DASHBOARD -->
            <section id="dashboard" class="dashboard-section active-section">

                <div class="page-heading">

                    <div>
                        <h2>My Dashboard</h2>
                        <p>Your personal ROTC records.</p>
                    </div>

                </div>


                <!-- WELCOME -->
                <div class="welcome-card">

                    <div class="welcome-avatar" id="studentAvatar">
                        —
                    </div>

                    <div class="welcome-info">

                        <h2 id="studentName">—</h2>

                        <p id="studentMeta">—</p>

                    </div>

                    <div class="view-only">
                        VIEW ONLY
                    </div>

                </div>


                <!-- NOTICE -->
                <div class="notice">

                    <strong>Cadet Access:</strong>

                    You can only view your own
                    <strong>Grades, Merits, Demerits, and Attendance.</strong>

                    You cannot add, edit, or delete records.

                </div>


                <!-- SUMMARY -->
                <div class="kpi-grid">

                    <div class="kpi attendance">

                        <span>Attendance</span>

                        <strong id="studentAttendance">
                            —
                        </strong>

                        <small>
                            Present / Excused rate
                        </small>

                    </div>


                    <div class="kpi">

                        <span>Final Grade</span>

                        <strong id="studentGradeAverage">
                            —
                        </strong>

                        <small>
                            Exams 50% · Attendance 25% · Attitude 25%
                        </small>

                    </div>


                    <div class="kpi merit">

                        <span>Merits</span>

                        <strong id="studentMerits">
                            —
                        </strong>

                        <small>
                            Positive points
                        </small>

                    </div>


                    <div class="kpi demerit">

                        <span>Demerits</span>

                        <strong id="studentDemerits">
                            —
                        </strong>

                        <small>
                            Disciplinary points
                        </small>

                    </div>

                </div>


                <!-- PERSONAL INFORMATION -->
                <div class="card">

                    <div class="card-header">

                        <div>
                            <h3>My Information</h3>
                            <p>Personal cadet information.</p>
                        </div>

                        <span class="view-label">
                            View Only
                        </span>

                    </div>


                    <div class="info-grid">

                        <div class="info-item">
                            <span>Cadet ID</span>
                            <strong id="studentInfoId">—</strong>
                        </div>

                        <div class="info-item">
                            <span>Full Name</span>
                            <strong id="studentInfoName">—</strong>
                        </div>

                        <div class="info-item">
                            <span>Year Level</span>
                            <strong id="studentInfoYear">—</strong>
                        </div>

                        <div class="info-item">
                            <span>Company</span>
                            <strong id="studentInfoCompany">—</strong>
                        </div>

                        <div class="info-item">
                            <span>Platoon</span>
                            <strong id="studentInfoPlatoon">—</strong>
                        </div>

                        <div class="info-item">
                            <span>Status</span>
                            <strong id="studentInfoStatus">—</strong>
                        </div>

                    </div>

                </div>

                <div class="card cadet-qr-card">
                    <div class="card-header">
                        <div>
                            <h3>My Attendance QR</h3>
                            <p>Show this QR code to an officer during formation.</p>
                        </div>
                        <button type="button" class="secondary-btn" id="downloadCadetQr">Download QR</button>
                    </div>
                    <div id="cadetQr" class="cadet-qr"></div>
                </div>


                <!-- GRADES -->
                <div class="card dashboard-section" id="grades">

                    <div class="card-header">

                        <div>
                            <h3>My Grades</h3>
                            <p>View-only academic and training grades.</p>
                        </div>

                        <div style="display:flex; align-items:center; gap:10px;">
                            <label class="grade-semester-control"><span>Semester</span><select id="cadetSemesterSelect"><option>First Semester</option><option>Second Semester</option></select></label>
                            <button type="button" class="secondary-btn" id="viewCadetEvaluationBtn">View Evaluation</button>
                            <span class="view-label">
                                View Only
                            </span>
                        </div>

                    </div>

                    <div id="studentEvaluationSummary" style="display:none; margin-top:12px; padding:12px 14px; border:1px solid #dfe8df; border-radius:10px; background:#f8faf8;"></div>

                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin:12px 0; padding:12px 14px; border:1px solid #dfe8df; border-radius:10px; background:#f8faf8;">
                        <div><strong>Semester GPA</strong><small style="display:block; color:#5f6f60; margin-top:3px;">4.00 scale based on recorded grades</small></div>
                        <strong id="studentGpa" style="font-size:24px; color:#17351d;">—</strong>
                    </div>

                    <div id="studentGradesList"></div>

                </div>


                <!-- EVALUATION -->
                <div class="card dashboard-section" id="evaluation">

                    <div class="card-header">

                        <div>
                            <h3>My Evaluation of Grades</h3>
                            <p>ROTC evaluation based on grade, attendance, and conduct.</p>
                        </div>

                        <button type="button" class="secondary-btn" id="viewCadetEvaluationBtnDetailed">View Evaluation</button>

                    </div>

                    <div id="studentEvaluationSummaryDetailed" style="display:none; margin-top:12px; padding:12px 14px; border:1px solid #dfe8df; border-radius:10px; background:#f8faf8;"></div>
                    <div id="studentWeightedEvaluationCard" class="cadet-weighted-card" hidden></div>

                </div>

                <!-- MERITS / DEMERITS -->
                <div class="card dashboard-section" id="conduct">

                    <div class="card-header">

                        <div>
                            <h3>My Merits & Demerits</h3>

                            <p>
                                Conduct records recorded by the administrator.
                            </p>
                        </div>

                        <span class="view-label">
                            View Only
                        </span>

                    </div>


                    <div id="studentConductList"></div>

                </div>


                <!-- ATTENDANCE -->
                <div class="card dashboard-section" id="attendance">

                    <div class="card-header">

                        <div>
                            <h3>My Attendance</h3>

                            <p>
                                Attendance history is view-only.
                            </p>
                        </div>

                        <span class="view-label">
                            View Only
                        </span>

                    </div>


                    <div class="table-container">

                        <table>

                            <thead>

                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                </tr>

                            </thead>

                            <tbody id="studentAttendanceList"></tbody>

                        </table>

                    </div>

                </div>

                <!-- ANNOUNCEMENTS -->
                <div class="card dashboard-section" id="announcements">
                    <div class="card-header">
                        <div>
                            <h3>Announcements</h3>
                            <p>Important ROTC updates published by officers.</p>
                        </div>
                        <span class="view-label">View Only</span>
                    </div>
                    <div id="cadetAnnouncementsList" class="training-list">
                        <p class="empty-state">Loading announcements...</p>
                    </div>
                </div>

                <!-- TRAINING SCHEDULES -->
                <div class="card dashboard-section" id="training">
                    <div class="card-header">
                        <div>
                            <h3>Training Schedules</h3>
                            <p>Official ROTC activities published by officers.</p>
                        </div>
                        <span class="view-label">View Only</span>
                    </div>
                    <div id="cadetTrainingScheduleList" class="training-list">
                        <p class="empty-state">Loading training schedules...</p>
                    </div>
                </div>

            </section>

        </div>

    </main>


    <div class="modal-overlay" id="cadetTrainingViewModal">
        <div class="modal cadet-training-view-modal" role="dialog" aria-modal="true" aria-labelledby="cadetTrainingViewTitle">
            <div class="card-header">
                <div id="cadetTrainingViewContent"></div>
                <button type="button" class="modal-close" onclick="closeCadetTrainingView()">&times;</button>
            </div>
            <div class="modal-actions">
                <button type="button" class="secondary-btn" onclick="closeCadetTrainingView()">Close</button>
            </div>
        </div>
    </div>

    <!-- LOGOUT MODAL -->
    <div class="modal-overlay" id="logoutModal">

        <div class="modal">

            <div class="modal-icon">
                ⇥
            </div>

            <h2>Logout?</h2>

            <p>
                Are you sure you want to logout from the Cadet Portal?
            </p>

            <div class="modal-actions">

                <button class="secondary-btn" id="cancelLogout">
                    Cancel
                </button>

                <button class="danger-btn" id="confirmLogout">
                    Logout
                </button>

            </div>

        </div>

    </div>


    <script src="cadet.js?v=18"></script>

</body>
</html>