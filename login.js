const form =
    document.getElementById("cadetLoginForm") ||
    document.getElementById("officerLoginForm") ||
    document.getElementById("adminLoginForm");

const isOfficerLogin =
    form && form.id === "officerLoginForm";

const isAdminLogin =
    form && form.id === "adminLoginForm";

const message = document.getElementById("message");
const loginButton = document.getElementById("loginButton");

// Displays a status message on the login form.
function showLoginMessage(text, type) {
    message.textContent = text;
    message.className = `message ${type}`;
}


/* =========================
   SHOW / HIDE PASSWORD
========================= */

// Toggles visibility of the login password field.
function togglePassword() {

    const passwordInput = document.getElementById("password");

    if (passwordInput.type === "password") {

        passwordInput.type = "text";

    } else {

        passwordInput.type = "password";

    }

}


/* =========================
   LOGIN FORM
========================= */

if (form) form.addEventListener("submit", async function (event) {

    event.preventDefault();


    const email = document
        .getElementById("email")
        .value
        .trim();

    const password = document
        .getElementById("password")
        .value;


    message.textContent = "";

    loginButton.disabled = true;
    loginButton.textContent = "Logging in...";


    try {

        const response = await fetch(
            isAdminLogin
                ? "../backend/admin-login.php"
                : isOfficerLogin
                ? "../officer/login.php"
                : "../cadet/login.php",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    email: email,
                    password: password
                })
            }
        );


        const responseText = await response.text();

        console.log("Server Response:", responseText);


        let data;

        try {

            data = JSON.parse(responseText);

        } catch (error) {

            throw new Error(
                "Server returned an invalid response."
            );

        }


        if (data.success) {

            if (isAdminLogin) {

                sessionStorage.setItem(
                    "rotc_logged_admin",
                    JSON.stringify({
                        id: data.admin_id,
                        name: data.full_name,
                        email: data.email,
                        role: data.role
                    })
                );

                showLoginMessage("Login successful! Redirecting to admin dashboard...", "success");

                setTimeout(() => {
                    window.location.href = "admin-dashboard.php";
                }, 1000);

                return;
            }

            if (isOfficerLogin) {

                sessionStorage.setItem(
                    "rotc_logged_officer",
                    JSON.stringify({
                        id: data.officer_id,
                        name: data.full_name,
                        email: data.email,
                        role: data.role
                    })
                );

                showLoginMessage("Login successful! Redirecting to officer dashboard...", "success");

                setTimeout(() => {
                    window.location.href = "officers-dashboard.php";
                }, 1000);

                return;
            }

            sessionStorage.setItem(
                "rotc_logged_cadet",
                JSON.stringify({
                    id: data.cadet_id,
                    name: data.full_name,
                    year: data.year_level,
                    section: data.section,
                    company: data.company,
                    platoon: data.platoon,
                    status: data.status
                })
            );

            message.textContent =
                "Login successful! Redirecting...";

            message.style.color = "green";


            setTimeout(() => {

                // CHANGE THIS TO YOUR CADET DASHBOARD
                window.location.href = "cadet-dashboard.php";

            }, 1000);


        } else {

            showLoginMessage(data.action
                ? `${data.message} ${data.action}`
                : data.message, "error");

            loginButton.disabled = false;

            loginButton.innerHTML =
                `Login <span>→</span>`;

        }


    } catch (error) {

        console.error("Login Error:", error);

        showLoginMessage("Login Error: " + error.message, "error");

        loginButton.disabled = false;

        loginButton.innerHTML =
            `Login <span>→</span>`;

    }

});