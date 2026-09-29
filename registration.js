const form = document.getElementById("cadetRegistrationForm");
const message = document.getElementById("message");
const registerButton = document.getElementById("registerButton");


/* =========================
   SHOW / HIDE PASSWORD
========================= */

// Toggles visibility of a registration password field.
function togglePassword(inputId) {

    const passwordInput = document.getElementById(inputId);

    if (passwordInput.type === "password") {

        passwordInput.type = "text";

    } else {

        passwordInput.type = "password";

    }

}


/* =========================
   REGISTRATION FORM
========================= */

form.addEventListener("submit", async function (event) {

    // Prevent page refresh
    event.preventDefault();


    /* =========================
       GET FORM DATA
    ========================= */

    const cadet_id = document
        .getElementById("cadet_id")
        .value
        .trim();

    const full_name = document
        .getElementById("full_name")
        .value
        .trim();

    const email = document
        .getElementById("email")
        .value
        .trim();

    const gender = document
        .getElementById("gender")
        .value;

    const year_level = document
        .getElementById("year_level")
        .value;

    const course = document
        .getElementById("course")
        .value
        .trim();

    const password = document
        .getElementById("password")
        .value;

    const confirm_password = document
        .getElementById("confirm_password")
        .value;

    if (!email.toLowerCase().endsWith("@mdci.edu.ph")) {

        message.textContent = "Only @mdci.edu.ph email addresses are allowed.";
        message.style.color = "red";

        return;
    }


    /* =========================
       CHECK PASSWORD
    ========================= */

    if (password !== confirm_password) {

        message.textContent = "Passwords do not match.";
        message.style.color = "red";

        return;
    }


    /* =========================
       DISABLE REGISTER BUTTON
    ========================= */

    registerButton.disabled = true;

    registerButton.textContent = "Registering...";

    message.textContent = "";


    /* =========================
       PREPARE DATA
    ========================= */

    const formData = {

        cadet_id: cadet_id,

        full_name: full_name,

        email: email,

        gender: gender,

        year_level: year_level,

        course: course,

        password: password

    };


    try {


        /* =========================
           SEND DATA TO PHP
        ========================= */

        const response = await fetch(

            "../cadet/register.php",

            {

                method: "POST",

                headers: {

                    "Content-Type": "application/json"

                },

                body: JSON.stringify(formData)

            }

        );


        /* =========================
           GET SERVER RESPONSE
        ========================= */

        const responseText = await response.text();

        console.log("Server Response:", responseText);


        let data;


        try {

            data = JSON.parse(responseText);

        } catch (jsonError) {

            throw new Error(
                "Server returned an invalid response."
            );

        }


        /* =========================
           SUCCESSFUL REGISTRATION
        ========================= */

        if (data.success) {

            message.textContent =
                "Registration successful. Save your attendance QR below.";

            message.style.color = "green";
            form.querySelectorAll(".form-group").forEach(group => {
                group.hidden = true;
            });
            registerButton.hidden = true;

            const qrCard = document.getElementById("registrationQrCard");
            const qrContainer = document.getElementById("registrationQr");
            const qrName = document.getElementById("registrationQrName");
            const downloadButton = document.getElementById("downloadRegistrationQr");

            if (qrCard && qrContainer && typeof QRCode !== "undefined") {
                qrCard.hidden = false;
                qrName.textContent = full_name;
                new QRCode(qrContainer, {
                    text: cadet_id,
                    width: 220,
                    height: 220,
                    colorDark: "#17351d",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                downloadButton.onclick = () => {
                    const canvas = qrContainer.querySelector("canvas");
                    const image = qrContainer.querySelector("img");
                    const link = document.createElement("a");
                    link.download = `cadet-${cadet_id}-attendance-qr.png`;

                    if (canvas) {
                        const downloadCanvas = document.createElement("canvas");
                        const context = downloadCanvas.getContext("2d");
                        const nameHeight = 32;
                        const qrPadding = 8;
                        downloadCanvas.width = canvas.width + (qrPadding * 2);
                        downloadCanvas.height = canvas.height + nameHeight + qrPadding;
                        context.fillStyle = "#ffffff";
                        context.fillRect(0, 0, downloadCanvas.width, downloadCanvas.height);
                        context.fillStyle = "#17351d";
                        context.font = "700 16px Arial, sans-serif";
                        context.textAlign = "center";
                        context.textBaseline = "middle";
                        context.fillText(full_name, downloadCanvas.width / 2, nameHeight / 2, downloadCanvas.width - 16);
                        context.drawImage(canvas, qrPadding, nameHeight);
                        link.href = downloadCanvas.toDataURL("image/png");
                    } else {
                        link.href = image?.src;
                    }

                    link.click();
                };
            }


        } else {


            /* =========================
               REGISTRATION FAILED
            ========================= */

            message.textContent = data.message;

            message.style.color = "red";


            // Enable button again
            registerButton.disabled = false;

            registerButton.innerHTML =
                `Register <span>→</span>`;

        }


    } catch (error) {


        /* =========================
           SERVER ERROR
        ========================= */

        console.error(
            "Registration Error:",
            error
        );


        message.textContent =
            "Registration Error: " + error.message;

        message.style.color = "red";


        // Enable button again
        registerButton.disabled = false;

        registerButton.innerHTML =
            `Register <span>→</span>`;

    }

});