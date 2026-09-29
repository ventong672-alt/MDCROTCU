
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadet Registration | MDC ROTC</title>

    <link rel="stylesheet" href="registration.css?v=9">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>

<div class="registration-card">

    <!-- LOGOS -->
    <div class="logo-container">
        <img src="MDCLOGO.png" class="logo" alt="MDC Logo">
        <img src="ROTCLOGO.png" class="logo" alt="ROTC Logo">
    </div>

    <!-- HEADER -->
    <div class="header">
        <h1>CADET REGISTRATION</h1>
        <p>MDC ROTC Management Information System</p>
    </div>

    <!-- REGISTRATION FORM -->
    <form id="cadetRegistrationForm">

        <!-- CADET ID -->
        <div class="form-group">
            <label for="cadet_id">Cadet ID</label>

            <div class="input-wrapper">
                <span class="input-icon">🪪</span>

                <input
                    type="text"
                    id="cadet_id"
                    name="cadet_id"
                    placeholder="Enter your Cadet ID"
                    required>
            </div>
        </div>


        <!-- FULL NAME -->
        <div class="form-group">
            <label for="full_name">Full Name</label>

            <div class="input-wrapper">
                <span class="input-icon">👤</span>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    required>
            </div>
        </div>


        <!-- EMAIL -->
        <div class="form-group">
            <label for="email">MDCI Email</label>

            <div class="input-wrapper">
                <span class="input-icon">✉</span>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="name@mdci.edu.ph"
                    pattern="^[^@\s]+@mdci\.edu\.ph$"
                    title="Use your @mdci.edu.ph email address."
                    required>
            </div>
        </div>


        <!-- GENDER -->
        <div class="form-group">
            <label for="gender">Gender</label>

            <div class="input-wrapper">
                <span class="input-icon">👤</span>

                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>


        <!-- YEAR LEVEL -->
        <div class="form-group">
            <label for="year_level">Year Level</label>

            <div class="input-wrapper">
                <span class="input-icon">📅</span>

                <select id="year_level" name="year_level" required>
                    <option value="">Select Year Level</option>
                    <option value="First Year">First Year</option>
                    <option value="Second Year">Second Year</option>
                    <option value="Third Year">Third Year</option>
                    <option value="Fourth Year">Fourth Year</option>
                </select>
            </div>
        </div>


        <!-- COURSE -->
        <div class="form-group">
            <label for="course">Course</label>

            <div class="input-wrapper">
                <span class="input-icon">🎓</span>

                <select id="course" name="course" required>
                    <option value="">Select MDC Course</option>
                    <option value="Diploma in Software Engineering">Diploma in Software Engineering</option>
                    <option value="Diploma in Digital Arts">Diploma in Digital Arts</option>
                    <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
                    <option value="Bachelor of Information Technology">Bachelor of Information Technology</option>
                    <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                    <option value="Diploma in Tourism Management">Diploma in Tourism Management</option>
                    <option value="College of Tourism Management">College of Tourism Management</option>
                    <option value="Bachelor of Business Administration">Bachelor of Business Administration</option>
                    <option value="College of Arts and Education">College of Arts and Education</option>
                </select>
            </div>
        </div>


        <!-- PASSWORD -->
        <div class="form-group">
            <label for="password">Password</label>

            <div class="input-wrapper">
                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    required>

                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword('password')">
                    Show
                </button>
            </div>
        </div>


        <!-- CONFIRM PASSWORD -->
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>

            <div class="input-wrapper">
                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required>

                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword('confirm_password')">
                    Show
                </button>
            </div>
        </div>


        <!-- MESSAGE -->
        <div id="message" class="message"></div>

        <div id="registrationQrCard" class="registration-qr-card" hidden>
            <h2>Your Cadet Attendance QR</h2>
            <p>Save this QR code and show it to an officer during formation attendance.</p>
            <div id="registrationQrName" class="registration-qr-name"></div>
            <div id="registrationQr" class="registration-qr"></div>
            <button type="button" id="downloadRegistrationQr" class="register-button">Download QR</button>
        </div>


        <!-- REGISTER BUTTON -->
        <button
            type="submit"
            class="register-button"
            id="registerButton">

            Register
            <span>→</span>

        </button>

    </form>


    <!-- LOGIN LINK -->
    <div class="login-link">
        <p>
            Already have an account?
            <a href="cadet-login.php">Login here</a>
        </p>
    </div>


    <!-- FOOTER -->
    <div class="footer">
        Metro Dumaguete College • Army ROTC Unit
    </div>

</div>

<script src="registration.js"></script>

</body>
</html>

