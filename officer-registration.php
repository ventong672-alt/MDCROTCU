<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Registration | MDC ROTC</title>
    <link rel="stylesheet" href="registration.css?v=9">
</head>
<body>
<div class="registration-card">
    <div class="logo-container">
        <img src="MDCLOGO.png" class="logo" alt="MDC Logo">
        <img src="ROTCLOGO.png" class="logo" alt="ROTC Logo">
    </div>

    <div class="header">
        <h1>OFFICER REGISTRATION</h1>
        <p>MDC ROTC Management Information System</p>
    </div>

    <form id="officerRegistrationForm">
        <div class="form-group">
            <label for="officer_id">Officer ID</label>
            <div class="input-wrapper">
                <span class="input-icon">🪪</span>
                <input type="text" id="officer_id" placeholder="Enter your officer ID" required>
            </div>
        </div>

        <div class="form-group">
            <label for="full_name">Full Name</label>
            <div class="input-wrapper">
                <span class="input-icon">👤</span>
                <input type="text" id="full_name" placeholder="Enter your full name" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">MDCI Email</label>
            <div class="input-wrapper">
                <span class="input-icon">✉</span>
                <input type="email" id="email" placeholder="name@mdci.edu.ph" pattern="^[^@\s]+@mdci\.edu\.ph$" title="Use your @mdci.edu.ph email address." required>
            </div>
        </div>

        <div class="form-group">
            <label for="year_level">Year Level</label>
            <div class="input-wrapper">
                <span class="input-icon">📅</span>
                <select id="year_level" required>
                    <option value="">Select Year Level</option>
                    <option value="First Year">First Year</option>
                    <option value="Second Year">Second Year</option>
                    <option value="Third Year">Third Year</option>
                    <option value="Fourth Year">Fourth Year</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <div class="input-wrapper">
                <span class="input-icon">🎓</span>
                <select id="course" required>
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

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <span class="input-icon">🔒</span>
                <input type="password" id="password" minlength="8" placeholder="Create a password" required>
                <button type="button" class="show-password" onclick="togglePassword('password')">Show</button>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-wrapper">
                <span class="input-icon">🔒</span>
                <input type="password" id="confirm_password" minlength="8" placeholder="Confirm your password" required>
                <button type="button" class="show-password" onclick="togglePassword('confirm_password')">Show</button>
            </div>
        </div>

        <div id="message" class="message"></div>
        <button type="submit" class="register-button" id="registerButton">
            Register as Officer <span>→</span>
        </button>
    </form>

    <div class="login-link">
        <p>Already registered? <a href="officer-login.php">Login here</a></p>
    </div>

    <div class="footer">Metro Dumaguete College • Army ROTC Unit</div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === "password" ? "text" : "password";
}

const form = document.getElementById("officerRegistrationForm");
const message = document.getElementById("message");
const button = document.getElementById("registerButton");

form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const email = document.getElementById("email").value.trim();

    if (!email.toLowerCase().endsWith("@mdci.edu.ph")) {
        message.textContent = "Only @mdci.edu.ph email addresses are allowed.";
        message.style.color = "red";
        return;
    }

    if (password !== confirmPassword) {
        message.textContent = "Passwords do not match.";
        message.style.color = "red";
        return;
    }

    button.disabled = true;
    button.textContent = "Registering...";
    message.textContent = "";

    try {
        const response = await fetch("../officer/register.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                officer_id: document.getElementById("officer_id").value.trim(),
                full_name: document.getElementById("full_name").value.trim(),
                email: document.getElementById("email").value.trim(),
                year_level: document.getElementById("year_level").value,
                course: document.getElementById("course").value,
                password
            })
        });
        const data = await response.json();
        message.textContent = data.message;
        message.style.color = data.success ? "green" : "red";

        if (data.success) {
            form.reset();
            setTimeout(() => { window.location.href = "officer-login.php"; }, 1200);
        }
    } catch (error) {
        message.textContent = "Unable to contact the server.";
        message.style.color = "red";
    } finally {
        button.disabled = false;
        button.innerHTML = 'Register as Officer <span>→</span>';
    }
});
</script>
</body>
</html>
