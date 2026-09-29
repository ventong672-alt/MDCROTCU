<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | MDC ROTC</title>
    <link rel="stylesheet" href="login.css?v=9">
</head>
<body>
<div class="login-card">
    <div class="logo-container">
        <img src="MDCLOGO.png" class="logo" alt="MDC Logo">
        <img src="ROTCLOGO.png" class="logo" alt="ROTC Logo">
    </div>

    <div class="header">
        <h1>RESET PASSWORD</h1>
        <p>Receive a one-time password by email</p>
    </div>

    <form id="forgotPasswordForm">
        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrapper">
                <span class="input-icon">✉</span>
                <input type="email" id="email" placeholder="name@mdci.edu.ph" required>
            </div>
        </div>

        <div id="message" class="message"></div>
        <div id="otpFields" hidden>
            <div class="form-group">
                <label for="otp">Email OTP</label>
                <div class="input-wrapper">
                    <input type="text" id="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="Enter 6-digit OTP">
                </div>
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" minlength="8" placeholder="At least 8 characters">
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirmPassword" minlength="8" placeholder="Repeat your password">
                </div>
            </div>
        </div>

        <div id="resetMessage" class="message"></div>

        <button type="submit" class="login-button" id="resetButton">
            Send OTP <span>→</span>
        </button>
    </form>

    <div class="footer">
        <a href="cadet-login.php">Return to login</a>
    </div>
</div>

<script>
const form = document.getElementById("forgotPasswordForm");
const message = document.getElementById("message");
const otpFields = document.getElementById("otpFields");
const resetMessage = document.getElementById("resetMessage");
const button = document.getElementById("resetButton");
let otpSent = false;

form.addEventListener("submit", async (event) => {
    event.preventDefault();
    button.disabled = true;
    button.textContent = otpSent ? "Resetting..." : "Sending OTP...";
    message.textContent = "";
    resetMessage.textContent = "";

    try {
        const endpoint = otpSent ? "../cadet/reset-password.php" : "../cadet/forgot-password.php";
        const payload = otpSent ? {
            email: document.getElementById("email").value.trim(),
            otp: document.getElementById("otp").value.trim(),
            password: document.getElementById("password").value
        } : {
            email: document.getElementById("email").value.trim()
        };

        if (otpSent && payload.password !== document.getElementById("confirmPassword").value) {
            throw new Error("Passwords do not match.");
        }

        const response = await fetch(endpoint, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        message.textContent = data.message;
        message.style.color = data.success ? "green" : "red";

        if (data.success && !otpSent) {
            otpSent = true;
            otpFields.hidden = false;
            button.innerHTML = 'Reset password <span>→</span>';
            message.textContent = "OTP sent. Check your email and enter it below.";
        } else if (data.success && otpSent) {
            form.reset();
            otpFields.hidden = true;
            otpSent = false;
            button.innerHTML = 'Send OTP <span>→</span>';
        }
    } catch (error) {
        message.textContent = error.message || "Unable to contact the server.";
        message.style.color = "red";
    } finally {
        button.disabled = false;
        if (!otpSent) {
            button.innerHTML = 'Send OTP <span>→</span>';
        } else {
            button.innerHTML = 'Reset password <span>→</span>';
        }
    }
});
</script>
</body>
</html>
