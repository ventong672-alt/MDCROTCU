const loggedOfficer = JSON.parse(
    sessionStorage.getItem("rotc_logged_officer") || "null"
);

if (!loggedOfficer || !loggedOfficer.id) {
    window.location.replace("officer-login.php");
} else {
    const officerName = loggedOfficer.name || loggedOfficer.full_name || "Officer";

    const welcomeName = document.getElementById("officerWelcomeName");
    if (welcomeName) {
        welcomeName.textContent = officerName;
    }

    document.querySelectorAll(".user-mini strong").forEach((element) => {
        element.textContent = officerName;
    });

    const logoutButton = document.getElementById("logoutBtn");
    const logoutModal = document.getElementById("officerLogoutModal");
    const cancelLogout = document.getElementById("cancelOfficerLogout");
    const confirmLogout = document.getElementById("confirmOfficerLogout");

    // Opens the officer logout confirmation modal.
    function openOfficerLogoutModal() {
        logoutModal?.classList.add("show");
    }

    // Closes the officer logout confirmation modal.
    function closeOfficerLogoutModal() {
        logoutModal?.classList.remove("show");
    }

    logoutButton?.addEventListener("click", openOfficerLogoutModal);
    cancelLogout?.addEventListener("click", closeOfficerLogoutModal);
    confirmLogout?.addEventListener("click", () => {
        closeOfficerLogoutModal();
        sessionStorage.removeItem("rotc_logged_officer");
        window.location.replace("officer-login.php");
    });
    logoutModal?.addEventListener("click", (event) => {
        if (event.target === logoutModal) closeOfficerLogoutModal();
    });
}
