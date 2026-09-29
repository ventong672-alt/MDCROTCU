
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadet Login | MDC ROTC</title>

    <link rel="stylesheet" href="login.css?v=9">
</head>

<body>

<div class="login-card">

    <!-- LOGOS -->
    <div class="logo-container">
        <img src="MDCLOGO.png" class="logo" alt="MDC Logo">
        <img src="ROTCLOGO.png" class="logo" alt="ROTC Logo">
    </div>

    <!-- HEADER -->
    <div class="header">
        <h1>CADET LOGIN</h1>
        <p>MDC ROTC Management Information System</p>
    </div>

    <!-- LOGIN FORM -->
    <form id="cadetLoginForm">

        <input type="hidden" id="role" value="cadet">

        <!-- EMAIL -->
        <div class="form-group">
            <label for="email">MDCI Email</label>

            <div class="input-wrapper">
                <span class="input-icon">✉</span>

                <input 
                    type="email" 
                    id="email" 
                    placeholder="name@mdci.edu.ph" 
                    required
                >
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
                    placeholder="Enter your password" 
                    required
                >

                <button 
                    type="button" 
                    class="show-password" 
                    onclick="togglePassword()">
                    Show
                </button>
            </div>
        </div>

        <!-- FORGOT PASSWORD -->
        <div class="forgot">
            <a href="forgot-password.php">
                Forgot password?
            </a>
        </div>

        <!-- REGISTRATION LINK -->
        <div class="register">
            <p>
                Don't have an account?
                <a href="registration.php">Register here</a>
            </p>
        </div>

        <!-- MESSAGE -->
        <div id="message" class="message"></div>

        <!-- LOGIN BUTTON -->
        <button 
            type="submit" 
            class="login-button" 
            id="loginButton">

            Login
            <span>→</span>

        </button>

    </form>

    <!-- FOOTER -->
    <div class="footer">
        Metro Dumaguete College • Army ROTC Unit
    </div>

</div>

<script src="login.js"></script>

</body>
</html>

