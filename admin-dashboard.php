<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Main Dashboard | MDC ROTC</title>
    <link rel="stylesheet" href="style.css?v=20260916-admin-glass-1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .nav-link,
        .logout-btn,
        .header-left p,
        .search-input,
        .search-button,
        .scan-button,
        .cadet-status,
        .info-label,
        .info-value,
        .action-btn,
        .stat-label {
            font-size: 14px !important;
        }

        .logo-group small,
        .admin-role {
            font-size: 12px !important;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="logo-group">
                <img src="ROTCLOGO.png" alt="MDC ROTC Unit Logo" class="sidebar-logo">
                <div>
                    <span class="app-title">ARMY ROTC MIS</span>
                    <small>Management System</small>
                </div>
            </div>
        </div><br>

        <nav class="sidebar-nav">
            <a href="#" class="nav-link active" data-section="dashboard">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a><br>
            <a href="#" class="nav-link" data-section="cadets">
                <span class="nav-icon">👥</span>
                <span>View Cadets</span>
            </a><br>
            <a href="#" class="nav-link" data-section="officers">
                <span class="nav-icon">🎖️</span>
                <span>Manage Officers</span>
            </a><br>
            <a href="#" class="nav-link" data-section="training">
                <span class="nav-icon">📅</span>
                <span>Training Sessions</span>
            </a><br>
            <a href="#" class="nav-link" data-section="reports">
                <span class="nav-icon">📄</span>
                <span>Reports</span>
            </a><br>
        </nav>

        <div class="sidebar-footer">
            <button id="adminLogoutButton" type="button" class="logout-btn">
                ⇥ Logout
            </button>
        </div>
    </aside>

    <div class="modal-overlay" id="adminLogoutModal">
        <div class="modal">
            <div class="modal-icon">
                ⇥
            </div>

            <h2>Logout?</h2>

            <p>
                Are you sure you want to logout from the Admin Portal?
            </p>

            <div class="modal-actions">
                <button class="secondary-btn" id="cancelAdminLogout" type="button">
                    Cancel
                </button>
                <button class="danger-btn" id="confirmAdminLogout" type="button">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="top-header">
            <button class="menu-btn" id="adminMenuBtn" type="button" aria-label="Open menu">☰</button>
            <div class="header-left">
                <h1>Admin Dashboard</h1>
                <p>Manage cadets, officers, and ROTC operations</p>
            </div>
            <div class="header-right">
                <div class="admin-profile">
                    <img src="admin%20profile.jpg" alt="Administrator profile photo" class="avatar admin-avatar">
                    <div class="admin-info">
                        <p class="admin-name" id="adminName">Administrator</p>
                        <p class="admin-role">System Admin</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">

            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section active">
                <div class="welcome">
                    <div>
                        <h2>Welcome back, Administrator!</h2>
                        <p>Here is the current overview of your ROTC records and operations.</p>
                    </div>
                    <div class="welcome-actions">
                        <button class="primary-btn" type="button" onclick="openInstructionModal()">Add Instructions</button>
                        <button class="secondary-btn" type="button" onclick="openGradingSettingsModal()">Grading Settings</button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <span>♙</span>
                        </div>
                        <div>
                            <span>Total Cadets</span>
                            <h2 id="totalCadets">0</h2>
                            <small>Registered cadets</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <span>✓</span>
                        </div>
                        <div>
                            <span>Active Cadets</span>
                            <h2 id="activeCadets">0</h2>
                            <small>Currently active</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <span>▣</span>
                        </div>
                        <div>
                            <span>Pending Approval</span>
                            <h2 id="pendingCadets">0</h2>
                            <small>Needs review</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <span>★</span>
                        </div>
                        <div>
                            <span>Officers</span>
                            <h2 id="totalOfficers">0</h2>
                            <small>Registered officers</small>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3>Cadet Readiness Overview</h3>
                                <p>Current active and pending registration status</p>
                            </div>
                        </div>
                        <div class="readiness-summary" aria-label="Cadet readiness summary">
                            <div class="readiness-summary-row readiness-officer-row">
                                <span class="readiness-detail-icon">♙</span>
                                <div><strong>Officers</strong><small>Registered active officers</small></div>
                                <strong id="officerReadinessCount">0</strong>
                            </div>
                            <div class="readiness-summary-row readiness-pending-row">
                                <span class="readiness-detail-icon">!</span>
                                <div><strong>Pending Officers</strong><small>Officer registrations awaiting approval</small></div>
                                <strong id="pendingOfficerReadinessCount">0</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3>Administrator Quick Actions</h3>
                                <p>Common ROTC management tasks</p>
                            </div>
                        </div>
                        <div class="training-list">
                            <div class="training-item">
                                <div class="date-box"><strong>✓</strong><span>REVIEW</span></div>
                                <div><strong>Review Approvals</strong><p>Check pending registrations.</p></div>
                                <button class="action-btn" type="button" onclick="document.querySelector('[data-section=cadets]').click()">Open</button>
                            </div>
                            <div class="training-item">
                                <div class="date-box"><strong>▣</strong><span>REPORT</span></div>
                                <div><strong>Open Reports</strong><p>Review ROTC activity reports.</p></div>
                                <button class="action-btn" type="button" onclick="document.querySelector('[data-section=reports]').click()">Open</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Cadets Section -->
            <section id="cadets-section" class="content-section">
                <div class="section-heading">
                    <div>
                        <h2>Manage Cadets</h2>
                        <p>Review cadet registrations and maintain ROTC records.</p>
                    </div>
                </div>
                <div class="card action-panel">
                    <h3>Cadet records</h3>
                    <div class="records-toolbar">
                        <input type="search" id="cadetRecordFilter" placeholder="Filter cadets by name, ID, or email">
                        <button class="action-btn" type="button" onclick="loadAdminRecords()">Refresh</button>
                    </div>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead>
                                <tr><th>Cadet ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="cadetRecordsBody"><tr><td colspan="7">Loading cadet records...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Officers Section -->
            <section id="officers-section" class="content-section">
                <div class="section-heading">
                    <div>
                        <h2>Manage Officers</h2>
                        <p>Review registered officer accounts and their current status.</p>
                    </div>
                </div>
                <div class="card action-panel">
                    <h3>Officer administration</h3>
                    <div class="records-toolbar">
                        <input type="search" id="officerRecordFilter" placeholder="Filter officers by name or email">
                        <button class="action-btn" type="button" onclick="loadAdminRecords()">Refresh</button>
                    </div>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead>
                                <tr><th>Officer ID</th><th>Name</th><th>Email</th><th>Status</th><th>Registered</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="officerRecordsBody"><tr><td colspan="6">Loading officer records...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="panel-actions">
                        <button class="action-btn" type="button" onclick="openInstructionModal()">Add Instruction</button>
                        <button class="action-btn" type="button" onclick="openAdminAnnouncementModal()">Add Announcement</button>
                    </div>
                    <div id="adminAnnouncementReplies" class="admin-announcement-replies"></div>
                </div>
            </section>

            <!-- Training Sessions Section -->
            <section id="training-section" class="content-section">
                <div class="section-heading">
                    <div>
                        <h2>Training Sessions</h2>
                        <p>Create and manage ROTC schedules from the officer workspace.</p>
                    </div>
                </div>
                <div class="card action-panel">
                    <h3>Training schedule</h3>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>Date</th><th>Training</th><th>Time</th><th>Location</th><th>Actions</th></tr></thead>
                            <tbody id="trainingRecordsBody"><tr><td colspan="5">Loading training schedules...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports-section" class="content-section">
                <div class="section-heading">
                    <div>
                        <h2>Reports</h2>
                        <p>Download a current summary of cadet and officer records.</p>
                    </div>
                    <button class="primary-btn" type="button" onclick="downloadAdminReport()">⬇ Download Report</button>
                </div>
                <div class="card action-panel">
                    <h3>Current system summary</h3>
                    <p id="reportSummary">Statistics are loaded from the dashboard overview.</p>
                    <div class="panel-actions">
                        <button class="action-btn" type="button" onclick="loadDashboardData(); updateReportSummary()">↻ Refresh Statistics</button>
                        <button class="action-btn" type="button" onclick="downloadAdminReport()">Download CSV</button>
                    </div>
                </div>
                <div class="card action-panel">
                    <div class="card-header">
                        <div>
                            <h3>Officer-submitted reports</h3>
                            <p>Reports sent by officers for administrative review.</p>
                        </div>
                        <button class="action-btn" type="button" onclick="loadOfficerReports()">Refresh</button>
                    </div>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>Date</th><th>Officer</th><th>Type</th><th>Title</th><th>Report</th><th>Actions</th></tr></thead>
                            <tbody id="officerReportsBody"><tr><td colspan="6">Loading officer reports...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div>
    </main>
</div>

<div id="adminAnnouncementModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Announcement for Officers</h2>
            <button class="modal-close" type="button" onclick="closeModal('adminAnnouncementModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="adminAnnouncementTitle">Title</label>
                <input type="text" id="adminAnnouncementTitle" maxlength="150" placeholder="Announcement title">
            </div>
            <div class="form-group">
                <label for="adminAnnouncementMessage">Message</label>
                <textarea id="adminAnnouncementMessage" rows="5" maxlength="2000" placeholder="Write the announcement for officers..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" type="button" onclick="closeModal('adminAnnouncementModal')">Cancel</button>
            <button class="modal-btn confirm" type="button" onclick="publishAdminAnnouncement()">Publish</button>
        </div>
    </div>
</div>

<div id="gradingSettingsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Grading Component Weights</h2>
            <button class="modal-close" type="button" onclick="closeModal('gradingSettingsModal')">&times;</button>
        </div>
        <div class="modal-body" id="gradingSettingsBody">Loading grading settings...</div>
        <div class="modal-footer">
            <button class="modal-btn cancel" type="button" onclick="closeModal('gradingSettingsModal')">Cancel</button>
            <button class="modal-btn confirm" type="button" onclick="saveGradingSettings()">Save Weights</button>
        </div>
    </div>
</div>

<div id="instructionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Send Officer Instruction</h2>
            <button class="modal-close" onclick="closeModal('instructionModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="instructionOfficer">Officer</label>
                <select id="instructionOfficer"></select>
            </div>
            <div class="form-group">
                <label for="instructionMessage">Instruction</label>
                <textarea id="instructionMessage" rows="5" placeholder="Write an instruction for this officer..."></textarea>
            </div>
            <div class="form-group">
                <label for="instructionFile">Attach report format or reference file</label>
                <input type="file" id="instructionFile">
                <small>Any file type is accepted up to 50 MB.</small>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" onclick="closeModal('instructionModal')">Cancel</button>
            <button class="modal-btn confirm" onclick="sendOfficerInstruction()">Send</button>
        </div>
    </div>
</div>

<div id="approveOfficerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Approve Officer</h2>
            <button class="modal-close" onclick="closeModal('approveOfficerModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="approveOfficerMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" onclick="closeModal('approveOfficerModal')">Cancel</button>
            <button class="modal-btn confirm" onclick="confirmApproveOfficer()">Approve</button>
        </div>
    </div>
</div>

<!-- Modal: View Cadet Details -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="cadetDetailsTitle">Cadet Credentials</h2>
            <button class="modal-close" onclick="closeModal('detailsModal')">&times;</button>
        </div>
        <div class="modal-body" id="detailsModalBody">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" onclick="closeModal('detailsModal')">Close</button>
        </div>
    </div>
</div>

<!-- Modal: View Officer Details -->
<div id="officerDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="officerDetailsTitle">Officer Credentials</h2>
            <button class="modal-close" onclick="closeModal('officerDetailsModal')">&times;</button>
        </div>
        <div class="modal-body" id="officerDetailsModalBody"></div>
        <div class="modal-footer">
            <button class="modal-btn cancel" onclick="closeModal('officerDetailsModal')">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Approve Cadet -->
<div id="approveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Approve Cadet Registration</h2>
            <button class="modal-close" onclick="closeModal('approveModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="approveMessage" style="color: #111827; margin-bottom: 16px;"></p>
            <div class="form-group">
                <label for="approveNotes">Notes (Optional):</label>
                <textarea id="approveNotes" placeholder="Add any approval notes..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" onclick="closeModal('approveModal')">Cancel</button>
            <button class="modal-btn confirm" onclick="confirmApproveCadet()">✓ Approve</button>
        </div>
    </div>
</div>

<!-- Modal: Edit Cadet -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Cadet Information</h2>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="editFullName">Full Name:</label>
                <input type="text" id="editFullName" placeholder="Enter full name">
            </div>
            <div class="form-group">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="editCourse">Course:</label>
                <input type="text" id="editCourse" placeholder="Enter course">
            </div>
            <div class="form-group">
                <label for="editYearLevel">Year Level:</label>
                <select id="editYearLevel">
                    <option value="">Select Year Level</option>
                    <option value="First Year">First Year</option>
                    <option value="Second Year">Second Year</option>
                    <option value="Third Year">Third Year</option>
                    <option value="Fourth Year">Fourth Year</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editSection">Section:</label>
                <input type="text" id="editSection" placeholder="Enter section">
            </div>
            <div class="form-group">
                <label for="editPlatoon">Company:</label>
                <select id="editPlatoon">
                    <option value="">Select Company</option>
                    <option value="1">Alpha</option>
                    <option value="2">Bravo</option>
                    <option value="3">Charlie</option>
                    <option value="4">Delta</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editStatus">Status:</label>
                <select id="editStatus">
                    <option value="Pending">Pending</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Graduated">Graduated</option>
                    <option value="Withdrawn">Withdrawn</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn cancel" onclick="closeModal('editModal')">Cancel</button>
            <button class="modal-btn confirm" onclick="confirmEditCadet()">💾 Save Changes</button>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f5f7fa;
        color: #333;
    }

    .section-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .section-heading h2 {
        margin-bottom: 6px;
    }

    .section-heading p,
    .action-panel p {
        color: #6b7280;
    }

    .welcome-actions {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        flex-shrink: 0;
    }

    .action-panel {
        width: 100%;
        max-width: none;
        padding: 22px;
    }

    .action-panel h3 {
        margin-bottom: 8px;
    }

    .panel-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .panel-actions .action-btn {
        min-width: 140px;
    }

    .records-toolbar {
        display: flex;
        gap: 10px;
        margin: 16px 0;
    }

    .records-toolbar input {
        flex: 1;
        min-width: 0;
        padding: 10px 12px;
        border: 1px solid #dfe5ec;
        border-radius: 8px;
    }

    @media (max-width: 640px) {
        .section-heading,
        .welcome {
            align-items: stretch;
            flex-direction: column;
        }

        .welcome-actions,
        .welcome-actions .primary-btn,
        .section-heading .primary-btn {
            width: 100%;
        }

        .records-toolbar {
            flex-direction: column;
        }

        .records-toolbar .action-btn {
            width: 100%;
        }
    }

    .records-table-wrap {
        overflow-x: auto;
    }

    .records-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
        font-size: 12px;
        white-space: nowrap;
    }

    .records-table th,
    .records-table td {
        padding: 11px 12px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .records-table th {
        color: #475569;
        background: #f7f9fc;
        font-size: 11px;
    }

    .records-table td {
        color: #334155;
    }

    .report-content-cell {
        max-width: 360px;
        white-space: normal;
        line-height: 1.5;
    }

    .report-attachments {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
    }

    .report-attachments a {
        display: block;
        width: 48px;
        height: 48px;
        overflow: hidden;
        border: 1px solid #dbe5df;
        border-radius: 6px;
        background: #f4f8f5;
    }

    .report-attachments img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .records-table small {
        color: #64748b;
        font-size: 10px;
    }

    .table-action {
        padding: 6px 8px;
        border: 1px solid #d7dee7;
        border-radius: 6px;
        background: #fff;
        color: #234f3b;
        cursor: pointer;
        font-size: 11px;
    }

    .table-action:hover {
        background: #edf6f0;
    }

    .record-status {
        font-weight: 700;
    }

    .details-card {
        padding: 22px;
        border: 1px solid #e4e9ef;
        border-radius: 14px;
        background: #f8fafc;
    }

    .details-hero {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-bottom: 18px;
        margin-bottom: 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .details-avatar {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        border-radius: 12px;
        background: #dceee3;
        color: #155b37;
        font-size: 18px;
        font-weight: 800;
    }

    .details-hero h3 {
        margin-bottom: 6px;
        color: #172b21;
        font-size: 18px;
    }

    .details-hero p {
        color: #64748b;
        font-size: 12px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .detail-item {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #fff;
    }

    .detail-item label {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .detail-item strong {
        color: #1e293b;
        font-size: 13px;
    }

    @media (max-width: 600px) {
        .section-heading {
            flex-direction: column;
        }

        .section-heading .primary-btn {
            width: 100%;
        }

        .records-toolbar {
            flex-direction: column;
        }

        .records-toolbar .action-btn {
            width: 100%;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    .dashboard-container {
        display: flex;
        height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 260px;
        background: #1f2937;
        color: white;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .logo-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-logo {
        width: 40px;
        height: 40px;
        border-radius: 6px;
    }

    .app-title {
        font-weight: 700;
        font-size: 16px;
    }

    .sidebar-nav {
        flex: 1;
        padding: 16px 0;
        overflow-y: auto;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: all 0.2s;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255,255,255,0.1);
        color: white;
        border-left: 3px solid #3b82f6;
        padding-left: 17px;
    }

    .nav-icon {
        font-size: 20px;
    }

    .sidebar-footer {
        padding: 16px 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .logout-btn {
        width: 100%;
        padding: 10px;
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .logout-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Main Content */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .top-header {
        background: white;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
    }

    .header-left h1 {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .admin-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
    }

    .admin-info {
        text-align: right;
    }

    .admin-name {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .admin-role {
        font-size: 12px;
        color: #6b7280;
    }

    .dashboard-content {
        flex: 1;
        overflow-y: auto;
        padding: 30px;
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* SCAN SECTION */
    .scan-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .scan-container h2 {
        margin-bottom: 24px;
        color: #111827;
        font-size: 20px;
    }

    .scan-search-box {
        margin-bottom: 30px;
    }

    .search-input-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
    }

    .search-button,
    .scan-button {
        padding: 12px 20px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .search-button:hover {
        background: #2563eb;
    }

    .scan-button {
        background: #10b981;
    }

    .scan-button:hover {
        background: #059669;
    }

    .scan-result-container {
        min-height: 200px;
    }

    .cadet-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 16px;
    }

    .cadet-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .cadet-name {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .cadet-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-graduated {
        background: #dbeafe;
        color: #0c4a6e;
    }

    .status-withdrawn {
        background: #f3e8ff;
        color: #581c87;
    }

    .cadet-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .info-item {
        background: white;
        padding: 12px;
        border-radius: 6px;
    }

    .info-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .cadet-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 10px 16px;
        border: none;
        background: #6b7280;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: #4b5563;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .action-btn.approve {
        background: #10b981;
    }

    .action-btn.approve:hover {
        background: #059669;
    }

    .action-btn.edit {
        background: #3b82f6;
    }

    .action-btn.edit:hover {
        background: #2563eb;
    }

    .action-btn.view {
        background: #8b5cf6;
    }

    .action-btn.view:hover {
        background: #7c3aed;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }

    .modal.show {
        display: block;
    }

    #adminLogoutModal .modal {
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

    #adminLogoutModal .modal-icon {
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

    #adminLogoutModal h2 {
        margin: 0 0 8px;
        color: #202b24;
        font-size: 18px;
    }

    #adminLogoutModal p {
        margin: 0 0 25px;
        color: #718096;
        font-size: 11px;
        line-height: 1.5;
    }

    #adminLogoutModal .modal-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    #adminLogoutModal .secondary-btn,
    #adminLogoutModal .danger-btn {
        border: 0;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 11px;
        cursor: pointer;
    }

    #adminLogoutModal .secondary-btn {
        color: #4b5563;
        background: #edf0ed;
    }

    #adminLogoutModal .danger-btn {
        color: #fff;
        background: #d94b4b;
    }

    #gradingSettingsModal .modal-content,
    #instructionModal .modal-content,
    #detailsModal .modal-content,
    #officerDetailsModal .modal-content {
        background: rgba(255, 255, 255, 0.66);
        border: 1px solid rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        border-radius: 18px;
        width: min(90%, 620px);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 15px;
    }

    .modal-header h2 {
        margin: 0;
        color: #111827;
        font-size: 20px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
    }

    .modal-close:hover {
        color: #111827;
    }

    #gradingSettingsModal .modal-header,
    #instructionModal .modal-header,
    #detailsModal .modal-header,
    #officerDetailsModal .modal-header {
        border-bottom: 1px solid rgba(148, 163, 184, 0.35);
        padding-bottom: 14px;
    }

    #gradingSettingsModal .modal-body,
    #instructionModal .modal-body,
    #detailsModal .modal-body,
    #officerDetailsModal .modal-body {
        margin-bottom: 18px;
        padding-top: 4px;
    }

    #gradingSettingsModal .form-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.28);
    }

    #gradingSettingsModal .form-group span {
        flex: 1;
        color: #1f2937;
        font-weight: 600;
    }

    #gradingSettingsModal .grading-weight-input {
        width: 110px;
        text-align: right;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.5);
        background: rgba(255, 255, 255, 0.68);
    }

    #instructionModal .form-group {
        margin-bottom: 18px;
    }

    #instructionModal .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #1f2937;
        font-weight: 700;
    }

    #instructionModal .form-group select,
    #instructionModal .form-group textarea,
    #instructionModal .form-group input[type="file"] {
        width: 100%;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.6);
        background: rgba(255, 255, 255, 0.72);
        color: #111827;
        box-sizing: border-box;
    }

    #instructionModal .form-group textarea {
        min-height: 120px;
        resize: vertical;
    }

    #instructionModal .form-group small {
        display: block;
        margin-top: 8px;
        color: #64748b;
    }

    #detailsModal .cadet-summary,
    #detailsModal .profile-card,
    #officerDetailsModal .profile-card {
        background: rgba(255, 255, 255, 0.28);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 14px;
        padding: 16px;
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .modal-btn.cancel {
        background: #e5e7eb;
        color: #111827;
    }

    .modal-btn.cancel:hover {
        background: #d1d5db;
    }

    .modal-btn.confirm {
        background: #10b981;
        color: white;
    }

    .modal-btn.confirm:hover {
        background: #059669;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #111827;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .no-results {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .search-input-group {
            flex-direction: column;
        }

        .search-input {
            min-width: 100%;
        }

        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
    }
</style>

<style>
    /* Keep the admin workflow, but use the officer dashboard visual system. */
    body {
        font-family: "Inter", Arial, sans-serif;
        color: var(--text);
        background: var(--bg);
    }

    .dashboard-container { display: block; min-height: 100vh; }
    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: var(--sidebar);
        padding: 20px 14px 16px;
        background: radial-gradient(circle at 100% 0, rgba(214,174,66,.16), transparent 28%), linear-gradient(180deg, #123c27 0%, #092719 100%);
        box-shadow: 8px 0 35px rgba(0,0,0,.14);
        z-index: 1200;
    }
    .sidebar-header { padding: 7px 10px 20px; border-bottom: 1px solid rgba(255,255,255,.10); }
    .logo-group { gap: 12px; }
    .sidebar-logo { width: 52px; height: 52px; display: block; flex-shrink: 0; object-fit: contain; background: transparent; padding: 0; border-radius: 0; }
    .app-title { display: block; color: #fff; font-size: 18px; font-weight: 800; letter-spacing: -.4px; }
    .logo-group small { display: block; margin-top: 3px; color: rgba(255,255,255,.55); font-size: 9px; }
    .sidebar-nav { padding: 18px 0 0; }
    .sidebar-nav::before { content: "MAIN MENU"; display: block; margin: 0 9px 8px; color: rgba(255,255,255,.42); font-size: 9px; font-weight: 700; letter-spacing: 1.4px; }
    .nav-link { min-height: 43px; margin: 3px 0; padding: 10px 12px; border-radius: 11px; border-left: 0; color: rgba(255,255,255,.68); font-size: 11px; font-weight: 600; }
    .nav-link:hover { color: #fff; background: rgba(255,255,255,.08); transform: translateX(2px); }
    .nav-link.active { padding-left: 12px; color: #fff; background: linear-gradient(90deg,rgba(255,255,255,.16),rgba(255,255,255,.06)); border-left: 0; box-shadow: inset 3px 0 0 var(--gold), 0 5px 18px rgba(0,0,0,.08); }
    .sidebar-footer { margin-top: auto; padding: 16px 5px 0; border-top: 1px solid rgba(255,255,255,.10); }
    .logout-btn { padding: 10px 12px; border-radius: 9px; border-color: rgba(220,38,38,.7); background: #dc2626; color: #fff; font-size: 10px; box-shadow: 0 8px 16px rgba(220,38,38,.18); }
    .logout-btn:hover { background: #b91c1c; border-color: rgba(220,38,38,.8); box-shadow: 0 10px 18px rgba(185,28,28,.25); }
    .main-content { margin-left: var(--sidebar); min-height: 100vh; overflow: visible; }
    .top-header { position: sticky; top: 0; z-index: 900; min-height: 78px; padding: 14px 30px; background: rgba(255,255,255,.88); backdrop-filter: blur(18px); border-bottom: 1px solid var(--border); }
    .header-left h1 { font-size: 19px; color: var(--text); letter-spacing: -.5px; }
    .header-left p { margin-top: 4px; color: var(--muted); font-size: 10px; }
    .admin-profile { gap: 9px; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(145deg,#fff,#dcebe1); color: var(--green-800); }
    .admin-avatar { border: 2px solid var(--gold); object-fit: cover; object-position: center 22%; }
    .admin-info { text-align: left; }
    .admin-name { font-size: 10px; color: var(--text); }
    .admin-role { margin-top: 3px; color: var(--muted); font-size: 8px; }
    .dashboard-content { min-height: calc(100vh - 78px); padding: 28px; overflow: visible; background-image: linear-gradient(rgba(245,247,246,.76),rgba(245,247,246,.76)), url("mdc.jpg"); background-size: cover; background-position: center; background-attachment: fixed; }
    .content-section { display: none; animation: pageIn .25s ease; }
    .content-section.active { display: block; }
    .scan-container, .stat-card, .cadet-card { border: 1px solid rgba(255,255,255,.78); border-radius: var(--radius); background: rgba(255,255,255,.90); backdrop-filter: blur(13px); box-shadow: var(--shadow); }
    .scan-container { padding: 20px; }
    .scan-container h2, .content-section > h2 { margin-bottom: 17px; font-size: 20px; color: var(--text); }
    .scan-search-box { margin-bottom: 17px; }
    .search-input-group { display: flex; gap: 9px; }
    .search-input { min-height: 38px; border: 1px solid var(--border); border-radius: 9px; background: rgba(255,255,255,.96); padding: 9px 11px; font-size: 9px; }
    .search-button, .scan-button { min-height: 38px; padding: 9px 16px; border-radius: 9px; font-size: 10px; }
    .search-button { background: linear-gradient(135deg,var(--green-700),var(--green-900)); }
    .scan-button { background: #f1f4f2; color: var(--text); }
    .cadet-card { padding: 18px; margin-bottom: 12px; }
    .cadet-card-header { margin-bottom: 14px; }
    .cadet-name { font-size: 13px; color: var(--text); }
    .cadet-status { padding: 5px 8px; border-radius: 30px; font-size: 7px; }
    .info-item { padding: 10px; border-radius: 9px; background: #f7faf8; }
    .info-label { font-size: 8px; }
    .info-value { font-size: 9px; color: var(--text); }
    .action-btn { padding: 7px 11px; border-radius: 8px; font-size: 9px; }
    .stats-grid { grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 14px; }
    .stat-card { padding: 18px; }
    .stat-icon { width: 46px; height: 46px; border-radius: 12px; }
    .stat-label { font-size: 9px; }
    .stat-value { font-size: 23px; color: var(--text); }
    .menu-btn { display: none; }
    @media (max-width: 900px) {
        .sidebar { transform: translateX(-105%); }
        .sidebar.open { transform: translateX(0); }
        .main-content { margin-left: 0; }
        .menu-btn { display: grid; place-items: center; width: 40px; height: 40px; border: 1px solid var(--border); border-radius: 10px; background: #fff; color: var(--green-800); font-size: 18px; }
        .top-header { padding: 12px 18px; }
        .dashboard-content { padding: 22px; background-attachment: scroll; }
    }
    @media (max-width: 600px) {
        .top-header { min-height: 68px; padding: 10px 13px; }
        .header-right { margin-left: auto; }
        .admin-profile { display: flex; }
        .dashboard-content { padding: 13px; }
        .stats-grid { grid-template-columns: 1fr; gap: 9px; }
        .search-input-group { flex-direction: column; }
        .search-input { min-width: 100%; }
    }
</style>

<script>
    // Check if admin is logged in
    function checkAdminSession() {
        const admin = sessionStorage.getItem('rotc_logged_admin');
        if (!admin) {
            window.location.href = 'admin-login.php';
            return;
        }

        const adminData = JSON.parse(admin);
        document.getElementById('adminName').textContent = adminData.name;
    }

    function openAdminLogoutModal() {
        const adminLogoutModal = document.getElementById('adminLogoutModal');
        if (adminLogoutModal) adminLogoutModal.classList.add('show');
    }

    function closeAdminLogoutModal() {
        const adminLogoutModal = document.getElementById('adminLogoutModal');
        if (adminLogoutModal) adminLogoutModal.classList.remove('show');
    }

    // Logout function
    function logoutAdmin() {
        sessionStorage.removeItem('rotc_logged_admin');
        window.location.href = 'admin-login.php';
    }

    // Search Cadet Function
    async function searchCadet(searchOverride = null, status = null) {
        const searchInput = searchOverride === null
            ? document.getElementById('searchInput').value.trim()
            : searchOverride;
        const resultContainer = document.getElementById('scanResultContainer');

        if (!searchInput && !status) {
            resultContainer.innerHTML = '<div class="no-results">Please enter a search term</div>';
            return;
        }

        resultContainer.innerHTML = '<div class="no-results">Searching...</div>';

        try {
            const response = await fetch('../backend/search-cadet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    search: searchInput,
                    status: status
                })
            });

            const data = await response.json();

            if (!data.success || data.results.length === 0) {
                resultContainer.innerHTML = '<div class="no-results">No cadets found matching your search</div>';
                return;
            }

            // Display results
            let html = '';
            data.results.forEach(cadet => {
                const statusClass = 'status-' + cadet.status.toLowerCase();
                const platoonName = getPlatoonName(cadet.platoon_id);
                
                html += `
                    <div class="cadet-card">
                        <div class="cadet-card-header">
                            <span class="cadet-name">${escapeHtml(cadet.full_name)}</span>
                            <span class="cadet-status ${statusClass}">${cadet.status}</span>
                        </div>
                        <div class="cadet-info-grid">
                            <div class="info-item">
                                <div class="info-label">Cadet ID</div>
                                <div class="info-value">${escapeHtml(cadet.cadet_id)}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">${escapeHtml(cadet.email)}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Course</div>
                                <div class="info-value">${escapeHtml(cadet.course)}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Year Level</div>
                                <div class="info-value">${cadet.year_level || 'N/A'}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Section</div>
                                <div class="info-value">${cadet.section || 'N/A'}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Platoon</div>
                                <div class="info-value">${platoonName}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value">${escapeHtml(cadet.gender)}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Registered</div>
                                <div class="info-value">${formatDate(cadet.created_at)}</div>
                            </div>
                        </div>
                        <div class="cadet-actions">
                            <button class="action-btn view" onclick="viewCadetDetails(${cadet.id})">👁️ View Details</button>
                            ${cadet.status === 'Pending' ? `<button class="action-btn approve" onclick="openApproveModal(${cadet.id})">✓ Approve</button>` : ''}
                            <button class="action-btn edit" onclick="openEditModal(${cadet.id})">✏️ Edit</button>
                        </div>
                    </div>
                `;
            });

            resultContainer.innerHTML = html;
        } catch (error) {
            console.error('Search Error:', error);
            resultContainer.innerHTML = '<div class="no-results">Error searching. Please try again.</div>';
        }
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Get platoon name from ID
    function getPlatoonName(platoonId) {
        const platoons = {
            1: 'Alpha',
            2: 'Bravo',
            3: 'Charlie',
            4: 'Delta'
        };
        return platoons[platoonId] || 'N/A';
    }

    // Format date
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    let adminRecords = { officers: [], cadets: [] };
    let adminTrainingRecords = [];
    let adminOfficerReports = [];

    function recordValue(value) {
        return value === null || value === undefined || value === '' ? 'N/A' : String(value);
    }

    function downloadTextFile(filename, content) {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([content], { type: 'text/plain;charset=utf-8' }));
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function viewTrainingRecord(trainingId) {
        const session = adminTrainingRecords.find(record => Number(record.id) === Number(trainingId));
        if (!session) return;

        document.getElementById('cadetDetailsTitle').textContent = 'Training Schedule';
        document.getElementById('detailsModalBody').innerHTML = `
            <div class="details-card">
                <div class="details-grid">
                    <div class="detail-item"><label>Date</label><strong>${escapeHtml(recordValue(session.training_date))}</strong></div>
                    <div class="detail-item"><label>Training</label><strong>${escapeHtml(recordValue(session.title))}</strong></div>
                    <div class="detail-item"><label>Type</label><strong>${escapeHtml(recordValue(session.training_type))}</strong></div>
                    <div class="detail-item"><label>Time</label><strong>${escapeHtml(recordValue(session.start_time))} - ${escapeHtml(recordValue(session.end_time))}</strong></div>
                    <div class="detail-item"><label>Location</label><strong>${escapeHtml(recordValue(session.location))}</strong></div>
                </div>
            </div>`;
        openModal('detailsModal');
    }

    function viewOfficerReport(reportId) {
        const report = adminOfficerReports.find(record => Number(record.id) === Number(reportId));
        if (!report) return;

        const attachments = (report.attachments || []).map(attachment =>
            `<li><a href="../${escapeHtml(attachment.file_path)}" target="_blank" rel="noopener">${escapeHtml(attachment.original_name)}</a></li>`
        ).join('');
        document.getElementById('cadetDetailsTitle').textContent = 'Officer Report';
        document.getElementById('detailsModalBody').innerHTML = `
            <div class="details-card">
                <div class="details-grid">
                    <div class="detail-item"><label>Date</label><strong>${escapeHtml(formatDate(report.created_at))}</strong></div>
                    <div class="detail-item"><label>Officer</label><strong>${escapeHtml(recordValue(report.officer_name))}</strong></div>
                    <div class="detail-item"><label>Type</label><strong>${escapeHtml(recordValue(report.report_type))}</strong></div>
                    <div class="detail-item"><label>Title</label><strong>${escapeHtml(recordValue(report.title))}</strong></div>
                </div>
                <div class="detail-item"><label>Report</label><p>${escapeHtml(recordValue(report.content))}</p></div>
                ${attachments ? `<div class="detail-item"><label>Attachments</label><ul>${attachments}</ul></div>` : ''}
            </div>`;
        openModal('detailsModal');
    }

    function downloadOfficerReport(reportId) {
        const report = adminOfficerReports.find(record => Number(record.id) === Number(reportId));
        if (!report) return;

        const attachments = (report.attachments || []).map(attachment => attachment.original_name).join(', ');
        const content = [
            'Officer Report',
            `Date: ${formatDate(report.created_at)}`,
            `Officer: ${recordValue(report.officer_name)} (${recordValue(report.officer_id)})`,
            `Type: ${recordValue(report.report_type)}`,
            `Title: ${recordValue(report.title)}`,
            '',
            recordValue(report.content),
            attachments ? `\nAttachments: ${attachments}` : ''
        ].join('\n');
        downloadTextFile(`officer-report-${report.id}.txt`, content);
    }

    function getOfficerDisplayId(officer) {
        return officer.officer_id || officer.id;
    }

    function renderOfficerRecords() {
        const body = document.getElementById('officerRecordsBody');
        const filter = document.getElementById('officerRecordFilter')?.value.toLowerCase() || '';
        if (!body) return;

        const officers = adminRecords.officers.filter(officer =>
            `${officer.full_name} ${officer.email}`.toLowerCase().includes(filter)
        );

        body.innerHTML = officers.length ? officers.map(officer => `
            <tr>
                <td>${escapeHtml(recordValue(getOfficerDisplayId(officer)))}</td>
                <td>${escapeHtml(recordValue(officer.full_name))}</td>
                <td>${escapeHtml(recordValue(officer.email))}</td>
                <td class="record-status">${escapeHtml(recordValue(officer.status))}</td>
                <td>${escapeHtml(formatDate(officer.created_at))}</td>
                <td>
                    <button class="table-action" type="button" onclick="viewOfficerDetails(${officer.id})">View</button>
                    ${officer.status === 'Pending' ? `<button class="table-action" type="button" onclick="openApproveOfficer(${officer.id})">Approve</button>` : ''}
                </td>
            </tr>
        `).join('') : '<tr><td colspan="6">No officers found.</td></tr>';
    }

    function renderCadetRecords() {
        const body = document.getElementById('cadetRecordsBody');
        const filter = document.getElementById('cadetRecordFilter')?.value.toLowerCase() || '';
        if (!body) return;

        const cadets = adminRecords.cadets.filter(cadet =>
            `${cadet.cadet_id} ${cadet.full_name} ${cadet.email}`.toLowerCase().includes(filter)
        ).sort((firstCadet, secondCadet) =>
            recordValue(firstCadet.full_name).localeCompare(recordValue(secondCadet.full_name), undefined, { sensitivity: 'base' })
        );

        body.innerHTML = cadets.length ? cadets.map(cadet => `
            <tr>
                <td>${escapeHtml(recordValue(cadet.cadet_id))}</td>
                <td>${escapeHtml(recordValue(cadet.full_name))}</td>
                <td>${escapeHtml(recordValue(cadet.email))}</td>
                <td>${escapeHtml(recordValue(cadet.course))}</td>
                <td>${escapeHtml(recordValue(cadet.year_level))}</td>
                <td class="record-status">${escapeHtml(recordValue(cadet.status))}</td>
                <td>
                    <button class="table-action" type="button" onclick="viewCadetDetails(${cadet.id})">View</button>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="7">No cadets found.</td></tr>';
    }

    function openTotalCadets() {
        document.querySelector('[data-section="cadets"]')?.click();
        const filter = document.getElementById('cadetRecordFilter');
        if (filter) {
            filter.value = '';
        }
        renderCadetRecords();
    }

    async function loadTrainingRecords() {
        const body = document.getElementById('trainingRecordsBody');
        if (!body) return;
        try {
            const response = await fetch('../backend/admin-training.php', { cache: 'no-store' });
            const data = await response.json();
            if (!data.success) throw new Error(data.message);
            adminTrainingRecords = data.sessions || [];
            body.innerHTML = data.sessions.length ? data.sessions.map(session => `
                <tr>
                    <td>${escapeHtml(recordValue(session.training_date))}</td>
                    <td><strong>${escapeHtml(recordValue(session.title))}</strong><br><small>${escapeHtml(recordValue(session.training_type))}</small></td>
                    <td>${escapeHtml(recordValue(session.start_time))} - ${escapeHtml(recordValue(session.end_time))}</td>
                    <td>${escapeHtml(recordValue(session.location))}</td>
                    <td class="table-actions"><button class="action-btn view" type="button" onclick="viewTrainingRecord(${Number(session.id)})">View</button></td>
                </tr>
            `).join('') : '<tr><td colspan="5">No officer-created training schedules found.</td></tr>';
        } catch (error) {
            console.error('Error loading training schedules:', error);
            body.innerHTML = '<tr><td colspan="5">Unable to load training schedules.</td></tr>';
        }
    }

    async function loadOfficerReports() {
        const body = document.getElementById('officerReportsBody');
        if (!body) return;
        try {
            const response = await fetch('../backend/admin-reports.php');
            const data = await response.json();
            if (!data.success) throw new Error(data.message);
            adminOfficerReports = data.reports || [];
            body.innerHTML = data.reports.length ? data.reports.map(report => `
                <tr>
                    <td>${escapeHtml(formatDate(report.created_at))}</td>
                    <td>${escapeHtml(recordValue(report.officer_name))}<br><small>${escapeHtml(recordValue(report.officer_id))}</small></td>
                    <td>${escapeHtml(recordValue(report.report_type))}</td>
                    <td><strong>${escapeHtml(recordValue(report.title))}</strong></td>
                    <td class="report-content-cell">
                        <div>${escapeHtml(recordValue(report.content))}</div>
                        ${(report.attachments || []).length ? `<div class="report-attachments">${report.attachments.map(attachment => `
                            <a href="../${escapeHtml(attachment.file_path)}" target="_blank" rel="noopener" title="${escapeHtml(attachment.original_name)}">
                                <img src="../${escapeHtml(attachment.file_path)}" alt="${escapeHtml(attachment.original_name)}">
                            </a>
                        `).join('')}</div>` : ''}
                    </td>
                    <td class="table-actions"><button class="action-btn view" type="button" onclick="viewOfficerReport(${Number(report.id)})">View</button><button class="action-btn" type="button" onclick="downloadOfficerReport(${Number(report.id)})">Download</button></td>
                </tr>
            `).join('') : '<tr><td colspan="6">No officer reports submitted yet.</td></tr>';
        } catch (error) {
            console.error('Error loading officer reports:', error);
            body.innerHTML = '<tr><td colspan="6">Unable to load officer reports.</td></tr>';
        }
    }

    function openInstructionModal() {
        const select = document.getElementById('instructionOfficer');
        select.innerHTML = adminRecords.officers.map(officer =>
            `<option value="${officer.id}">${escapeHtml(recordValue(officer.full_name))} (${escapeHtml(recordValue(getOfficerDisplayId(officer)))})</option>`
        ).join('');
        document.getElementById('instructionMessage').value = '';
        openModal('instructionModal');
    }

    function openAdminAnnouncementModal() {
        document.getElementById('adminAnnouncementTitle').value = '';
        document.getElementById('adminAnnouncementMessage').value = '';
        openModal('adminAnnouncementModal');
    }

    async function openGradingSettingsModal() {
        const body = document.getElementById('gradingSettingsBody');
        body.textContent = 'Loading grading settings...';
        openModal('gradingSettingsModal');
        const response = await fetch('../backend/weighted-evaluation.php', { cache: 'no-store' });
        const data = await response.json();
        if (!data.success) {
            body.textContent = data.message || 'Unable to load grading settings.';
            return;
        }
        body.innerHTML = `<p>Weights must total exactly 100%.</p>${data.components.map(component => `<label class="form-group"><span>${escapeHtml(component.label)}</span><input class="grading-weight-input" data-component="${escapeHtml(component.component_key)}" type="number" min="0" max="100" step="0.01" value="${Number(component.weight)}"></label>`).join('')}<strong id="gradingWeightTotal"></strong>`;
        body.querySelectorAll('input').forEach(input => input.addEventListener('input', updateGradingWeightTotal));
        updateGradingWeightTotal();
    }

    function updateGradingWeightTotal() {
        const total = Array.from(document.querySelectorAll('.grading-weight-input')).reduce((sum, input) => sum + Number(input.value || 0), 0);
        const element = document.getElementById('gradingWeightTotal');
        if (element) element.textContent = `Total: ${total.toFixed(2)}%`;
    }

    async function saveGradingSettings() {
        const admin = JSON.parse(sessionStorage.getItem('rotc_logged_admin') || 'null');
        const components = Array.from(document.querySelectorAll('.grading-weight-input')).map(input => ({ component_key: input.dataset.component, weight: input.value }));
        const response = await fetch('../backend/weighted-evaluation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'configure', admin_id: admin?.id, components })
        });
        const data = await response.json();
        alert(data.message);
        if (data.success) closeModal('gradingSettingsModal');
    }

    async function sendOfficerInstruction() {
        const officerId = document.getElementById('instructionOfficer').value;
        const message = document.getElementById('instructionMessage').value.trim();
        const fileInput = document.getElementById('instructionFile');
        if (!officerId || !message) {
            alert('Select an officer and write an instruction.');
            return;
        }

        const formData = new FormData();
        formData.append('officer_id', officerId);
        formData.append('message', message);
        if (fileInput.files[0]) formData.append('attachment', fileInput.files[0]);

        const response = await fetch('../backend/admin-instructions.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        alert(data.message);
        if (data.success) {
            fileInput.value = '';
            closeModal('instructionModal');
        }
    }

    async function publishAdminAnnouncement() {
        const admin = JSON.parse(sessionStorage.getItem('rotc_logged_admin') || 'null');
        const titleInput = document.getElementById('adminAnnouncementTitle');
        const messageInput = document.getElementById('adminAnnouncementMessage');
        const title = titleInput.value.trim();
        const message = messageInput.value.trim();
        if (!admin?.id || !title || !message) {
            alert('Enter an announcement title and message.');
            return;
        }

        const response = await fetch('../backend/admin-announcements.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ admin_id: admin.id, title, message })
        });
        const data = await response.json();
        alert(data.message);
        if (data.success) {
            titleInput.value = '';
            messageInput.value = '';
            closeModal('adminAnnouncementModal');
            loadAdminAnnouncementReplies();
        }
    }

    async function loadAdminAnnouncementReplies() {
        const container = document.getElementById('adminAnnouncementReplies');
        if (!container) return;
        try {
            const response = await fetch('../backend/admin-announcements.php', { cache: 'no-store' });
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Unable to load announcement replies.');
            container.innerHTML = data.announcements.map(item => `
                <div class="admin-message-thread">
                    <div class="admin-message-thread-heading"><strong>${escapeHtml(item.title)}</strong><span>Admin announcement</span></div>
                    <div class="admin-chat-bubble admin-chat-bubble-sent"><b>Administrator</b><p>${escapeHtml(item.message)}</p><small>${escapeHtml(item.created_at)}</small></div>
                    ${(item.replies || []).length ? item.replies.map(reply => `<div class="admin-chat-bubble ${reply.sender_role === 'admin' ? 'admin-chat-bubble-sent' : 'admin-chat-bubble-received'}"><b>${escapeHtml(reply.officer_name)} · ${escapeHtml(reply.sender_role || 'officer')}</b><p>${escapeHtml(reply.message)}</p><small>${escapeHtml(reply.created_at)}</small></div>`).join('') : '<p class="muted">No replies yet.</p>'}
                    <div class="admin-message-composer"><textarea id="admin-reply-${Number(item.id)}" rows="2" placeholder="Reply in this conversation..."></textarea><button class="action-btn" type="button" onclick="replyToOfficerAnnouncement(${Number(item.id)})">Send Reply</button></div>
                </div>
            `).join('') || '<p class="muted">No admin announcements yet.</p>';
        } catch (error) {
            container.textContent = error.message;
        }
    }

    async function replyToOfficerAnnouncement(announcementId) {
        const admin = JSON.parse(sessionStorage.getItem('rotc_logged_admin') || 'null');
        const input = document.getElementById(`admin-reply-${announcementId}`);
        const message = input?.value.trim() || '';
        if (!admin?.id || !message) {
            alert('Write a reply first.');
            return;
        }
        const response = await fetch('../backend/admin-announcements.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'reply', admin_id: admin.id, announcement_id: announcementId, message })
        });
        const data = await response.json();
        alert(data.message);
        if (data.success) loadAdminAnnouncementReplies();
    }

    async function loadAdminRecords() {
        const officerBody = document.getElementById('officerRecordsBody');
        const cadetBody = document.getElementById('cadetRecordsBody');
        if (officerBody) officerBody.innerHTML = '<tr><td colspan="6">Loading officer records...</td></tr>';
        if (cadetBody) cadetBody.innerHTML = '<tr><td colspan="7">Loading cadet records...</td></tr>';

        try {
            const response = await fetch('../backend/admin-records.php');
            const data = await response.json();
            if (!data.success) throw new Error(data.message || 'Unable to load records.');

            adminRecords = {
                officers: data.officers || [],
                cadets: data.cadets || []
            };
            renderOfficerRecords();
            renderCadetRecords();
        } catch (error) {
            console.error('Error loading admin records:', error);
            if (officerBody) officerBody.innerHTML = '<tr><td colspan="6">Unable to load officer records.</td></tr>';
            if (cadetBody) cadetBody.innerHTML = '<tr><td colspan="7">Unable to load cadet records.</td></tr>';
        }
    }

    // View cadet details
    async function viewCadetDetails(cadetId) {
        try {
            const response = await fetch('../backend/search-cadet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    search: cadetId
                })
            });

            const data = await response.json();
            
            if (data.success && data.results.length > 0) {
                const cadet = data.results[0];
                const statusClass = 'status-' + cadet.status.toLowerCase();
                
                const detailsHtml = `
                    <div class="details-card">
                        <div class="details-hero">
                            <div class="details-avatar">${escapeHtml(recordValue(cadet.full_name).slice(0, 1).toUpperCase())}</div>
                            <div>
                                <h3>${escapeHtml(recordValue(cadet.full_name))}</h3>
                                <p>${escapeHtml(recordValue(cadet.cadet_id))} · <span class="cadet-status ${statusClass}">${escapeHtml(recordValue(cadet.status))}</span></p>
                            </div>
                        </div>
                        <div class="details-grid">
                            <div class="detail-item"><label>Email</label><strong>${escapeHtml(recordValue(cadet.email))}</strong></div>
                            <div class="detail-item"><label>Course</label><strong>${escapeHtml(recordValue(cadet.course))}</strong></div>
                            <div class="detail-item"><label>Year Level</label><strong>${escapeHtml(recordValue(cadet.year_level))}</strong></div>
                            <div class="detail-item"><label>Gender</label><strong>${escapeHtml(recordValue(cadet.gender))}</strong></div>
                            <div class="detail-item"><label>Registered</label><strong>${escapeHtml(formatDate(cadet.created_at))}</strong></div>
                        </div>
                    </div>
                `;
                
                document.getElementById('detailsModalBody').innerHTML = detailsHtml;
                document.getElementById('cadetDetailsTitle').textContent = 'Cadet Credentials';
                openModal('detailsModal');
            } else {
                alert('Could not load cadet details.');
            }
        } catch (error) {
            console.error('Error loading cadet details:', error);
            alert('Error loading cadet details. Please try again.');
        }
    }

    function viewOfficerDetails(officerId) {
        const officer = adminRecords.officers.find(record => Number(record.id) === Number(officerId));
        if (!officer) {
            alert('Could not load officer details.');
            return;
        }

        const detailsHtml = `
            <div class="details-card">
                <div class="details-hero">
                    <div class="details-avatar">${escapeHtml(recordValue(officer.full_name).slice(0, 1).toUpperCase())}</div>
                    <div>
                        <h3>${escapeHtml(recordValue(officer.full_name))}</h3>
                        <p>${escapeHtml(recordValue(getOfficerDisplayId(officer)))} · Officer account · <span class="cadet-status status-${String(officer.status).toLowerCase()}">${escapeHtml(recordValue(officer.status))}</span></p>
                    </div>
                </div>
                <div class="details-grid">
                    <div class="detail-item"><label>Officer Name</label><strong>${escapeHtml(recordValue(officer.full_name))}</strong></div>
                    <div class="detail-item"><label>Officer ID</label><strong>${escapeHtml(recordValue(getOfficerDisplayId(officer)))}</strong></div>
                    <div class="detail-item"><label>Role</label><strong>${escapeHtml(recordValue(officer.role))}</strong></div>
                    <div class="detail-item"><label>Email</label><strong>${escapeHtml(recordValue(officer.email))}</strong></div>
                    <div class="detail-item"><label>Registered</label><strong>${escapeHtml(formatDate(officer.created_at))}</strong></div>
                </div>
            </div>
        `;

        document.getElementById('officerDetailsModalBody').innerHTML = detailsHtml;
        document.getElementById('officerDetailsTitle').textContent = 'Officer Credentials';
        openModal('officerDetailsModal');
    }

    let pendingOfficerId = null;

    function openApproveOfficer(officerId) {
        const officer = adminRecords.officers.find(record => Number(record.id) === Number(officerId));
        if (!officer) return;

        pendingOfficerId = officerId;
        document.getElementById('approveOfficerMessage').textContent = `Approve ${recordValue(officer.full_name)} (${recordValue(getOfficerDisplayId(officer))})?`;
        openModal('approveOfficerModal');
    }

    async function confirmApproveOfficer() {
        if (!pendingOfficerId) return;

        const response = await fetch('../backend/approve-officer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ officer_id: pendingOfficerId })
        });
        const data = await response.json();
        alert(data.message);

        if (data.success) {
            closeModal('approveOfficerModal');
            pendingOfficerId = null;
            loadAdminRecords();
            loadDashboardData();
        }
    }

    // Store current cadet ID for modal operations
    let currentCadetId = null;

    // Open approve modal
    function openApproveModal(cadetId) {
        currentCadetId = cadetId;
        document.getElementById('approveMessage').textContent = `Are you sure you want to approve cadet ID ${cadetId}? This will activate their account.`;
        document.getElementById('approveNotes').value = '';
        openModal('approveModal');
    }

    // Confirm approve cadet
    async function confirmApproveCadet() {
        if (!currentCadetId) return;

        const notes = document.getElementById('approveNotes').value.trim();
        
        try {
            const response = await fetch('../cadet/approve.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cadet_id: currentCadetId,
                    notes: notes
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('✓ Cadet approved successfully!');
                closeModal('approveModal');
                // Refresh search results
                searchCadet();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve cadet'));
            }
        } catch (error) {
            console.error('Error approving cadet:', error);
            alert('Error approving cadet. Please try again.');
        }
    }

    // Open edit modal
    async function openEditModal(cadetId) {
        currentCadetId = cadetId;
        
        try {
            // Fetch cadet details to populate the form
            const response = await fetch('../backend/search-cadet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    search: cadetId
                })
            });

            const data = await response.json();
            
            if (data.success && data.results.length > 0) {
                const cadet = data.results[0];
                
                // Populate form fields
                document.getElementById('editFullName').value = cadet.full_name;
                document.getElementById('editEmail').value = cadet.email;
                document.getElementById('editCourse').value = cadet.course;
                document.getElementById('editYearLevel').value = cadet.year_level || '';
                document.getElementById('editSection').value = cadet.section || '';
                document.getElementById('editPlatoon').value = cadet.platoon_id || '';
                document.getElementById('editStatus').value = cadet.status;
                
                openModal('editModal');
            } else {
                alert('Could not load cadet information for editing.');
            }
        } catch (error) {
            console.error('Error loading cadet for edit:', error);
            alert('Error loading cadet information. Please try again.');
        }
    }

    // Confirm edit cadet
    async function confirmEditCadet() {
        if (!currentCadetId) return;

        const updatedData = {
            cadet_id: currentCadetId,
            full_name: document.getElementById('editFullName').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            course: document.getElementById('editCourse').value.trim(),
            year_level: document.getElementById('editYearLevel').value,
            section: document.getElementById('editSection').value.trim(),
            platoon_id: document.getElementById('editPlatoon').value,
            status: document.getElementById('editStatus').value
        };

        // Validation
        if (!updatedData.full_name || !updatedData.email || !updatedData.course) {
            alert('Please fill in all required fields (Full Name, Email, Course)');
            return;
        }

        try {
            const response = await fetch('../backend/update-cadet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updatedData)
            });

            const data = await response.json();

            if (data.success) {
                alert('✓ Cadet information updated successfully!');
                closeModal('editModal');
                // Refresh search results
                searchCadet();
            } else {
                alert('Error: ' + (data.message || 'Failed to update cadet'));
            }
        } catch (error) {
            console.error('Error updating cadet:', error);
            alert('Error updating cadet information. Please try again.');
        }
    }

    async function deleteCadet(cadetId) {
        const cadet = adminRecords.cadets.find(record => Number(record.id) === Number(cadetId));
        const cadetName = cadet ? recordValue(cadet.full_name) : 'this cadet';
        if (!confirm(`Delete ${cadetName}? This action cannot be undone.`)) return;

        try {
            const response = await fetch('../backend/delete-cadet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cadet_id: cadetId })
            });
            const data = await response.json();
            if (!data.success) {
                alert(data.message || 'Unable to delete cadet.');
                return;
            }

            await loadAdminRecords();
        } catch (error) {
            console.error('Error deleting cadet:', error);
            alert('Unable to delete cadet. Please try again.');
        }
    }

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('show');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    function navigateToSection(sectionName) {
        const link = document.querySelector(`[data-section="${sectionName}"]`);
        if (link) {
            link.click();
        }
    }

    function openOfficerWorkspace() {
        window.location.href = 'officers-dashboard.php';
    }

    function openCadetRegistration() {
        window.location.href = 'registration.php';
    }

    function openTrainingWorkspace() {
        window.location.href = 'officers-dashboard.php#training';
    }

    function updateReportSummary() {
        const summary = document.getElementById('reportSummary');
        if (!summary) return;

        summary.textContent = `Total cadets: ${document.getElementById('totalCadets').textContent} | Active: ${document.getElementById('activeCadets').textContent} | Pending: ${document.getElementById('pendingCadets').textContent} | Officers: ${document.getElementById('totalOfficers').textContent}`;
    }

    function downloadAdminReport() {
        const reportRows = [
            ['ROTC Admin Summary', 'Value'],
            ['Generated', new Date().toLocaleString()],
            ['Total Cadets', document.getElementById('totalCadets').textContent],
            ['Active Cadets', document.getElementById('activeCadets').textContent],
            ['Pending Cadets', document.getElementById('pendingCadets').textContent],
            ['Active Officers', document.getElementById('totalOfficers').textContent]
        ];
        const csv = reportRows.map(row => row.map(value => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
        const downloadUrl = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
        const downloadLink = document.createElement('a');
        downloadLink.href = downloadUrl;
        downloadLink.download = `rotc-admin-summary-${new Date().toISOString().slice(0, 10)}.csv`;
        downloadLink.click();
        URL.revokeObjectURL(downloadUrl);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
        }
    });

    // Navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            const sectionName = this.dataset.section;
            const section = document.getElementById(sectionName + '-section');
            if (section) {
                section.classList.add('active');
            }

            if (sectionName === 'officers' || sectionName === 'cadets') {
                loadAdminRecords();
            }
            if (sectionName === 'training') {
                loadTrainingRecords();
            }
            if (sectionName === 'reports') {
                loadOfficerReports();
            }

            const sidebar = document.getElementById('adminSidebar');
            if (sidebar) {
                sidebar.classList.remove('open');
            }
        });
    });

    // Load dashboard data
    async function loadDashboardData() {
        try {
            const response = await fetch('../backend/get-stats.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                document.getElementById('totalCadets').textContent = data.totalCadets || '0';
                document.getElementById('activeCadets').textContent = data.activeCadets || '0';
                document.getElementById('pendingCadets').textContent = data.pendingCadets || '0';
                document.getElementById('totalOfficers').textContent = data.totalOfficers || '0';
                updateReadinessChart(data.activeCadets, data.pendingCadets, data.totalOfficers, data.pendingOfficers);
                updateReportSummary();
            } else {
                updateReadinessChart(0, 0, 0);
            }
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
            document.getElementById('totalCadets').textContent = '0';
            document.getElementById('activeCadets').textContent = '0';
            document.getElementById('pendingCadets').textContent = '0';
            document.getElementById('totalOfficers').textContent = '0';
            updateReadinessChart(0, 0, 0);
            updateReportSummary();
        }
    }

    function updateReadinessChart(activeCadets, pendingCadets, totalOfficers, pendingOfficers) {
        document.getElementById('officerReadinessCount').textContent = Number(totalOfficers) || 0;
        document.getElementById('pendingOfficerReadinessCount').textContent = Number(pendingOfficers) || 0;
    }

    let adminRefreshInFlight = false;

    async function refreshAdminDashboard() {
        if (adminRefreshInFlight || document.visibilityState !== 'visible') return;
        adminRefreshInFlight = true;
        try {
            await Promise.all([
                loadDashboardData(),
                loadAdminRecords(),
                loadTrainingRecords(),
                loadOfficerReports(),
                loadAdminAnnouncementReplies()
            ]);
        } finally {
            adminRefreshInFlight = false;
        }
    }

    window.setInterval(refreshAdminDashboard, 10000);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') refreshAdminDashboard();
    });

    // Initialize on page load
    window.addEventListener('load', function() {
        document.getElementById('adminLogoutButton')?.addEventListener('click', openAdminLogoutModal);
        document.getElementById('cancelAdminLogout')?.addEventListener('click', closeAdminLogoutModal);
        document.getElementById('confirmAdminLogout')?.addEventListener('click', function() {
            closeAdminLogoutModal();
            logoutAdmin();
        });
        document.getElementById('adminLogoutModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeAdminLogoutModal();
        });

        checkAdminSession();
        loadDashboardData();
        loadAdminRecords();
        loadTrainingRecords();
        loadOfficerReports();
        loadAdminAnnouncementReplies();

        document.getElementById('officerRecordFilter')?.addEventListener('input', renderOfficerRecords);
        document.getElementById('cadetRecordFilter')?.addEventListener('input', renderCadetRecords);

        const menuButton = document.getElementById('adminMenuBtn');
        const sidebar = document.getElementById('adminSidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', () => sidebar.classList.toggle('open'));
        }
    });
</script>

</body>
</html>
