const pendingCadetsBody = document.getElementById("pendingCadetsBody");
const refreshPendingCadets = document.getElementById("refreshPendingCadets");
const viewTotalCadets = document.getElementById("viewTotalCadets");
const totalCadetsBody = document.getElementById("totalCadetsBody");
const registeredCadetsBody = document.getElementById("registeredCadetsBody");
const registeredCadetRecords = new Map();

// Escapes approval data before inserting it into generated HTML.
function escapeApprovalText(value) {
    const element = document.createElement("span");
    element.textContent = value ?? "";
    return element.innerHTML;
}

// Loads cadets already registered with the officer roster.
async function loadRegisteredCadets() {
    if (!registeredCadetsBody) {
        return;
    }

    try {
        const response = await fetch("../backend/officer-cadets.php", { cache: "no-store" });
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || "Unable to load registered cadets.");
        }

        data.cadets.forEach((cadet) => {
            const record = {
                dbId: String(cadet.id),
                id: cadet.cadet_id,
                name: cadet.full_name,
                year: cadet.year_level || "Not specified",
                section: cadet.section || "Not specified",
                company: cadet.company || "",
                platoon: cadet.platoon_name || platoonNameFromId(cadet.platoon_id),
                status: cadet.status
            };
            registeredCadetRecords.set(cadet.cadet_id, record);
            if (!cadetRecords.some((existingRecord) => existingRecord.id === cadet.cadet_id)) {
                cadetRecords.push(record);
            }
        });

        registeredCadetsBody.innerHTML = data.cadets.length ? data.cadets.map((cadet) => `
            <tr>
                <td>${escapeApprovalText(cadet.full_name)}</td>
                <td>${escapeApprovalText(cadet.email)}</td>
                <td><span class="role cadet">Cadet</span></td>
                <td><span class="status ${String(cadet.status).toLowerCase()}">${escapeApprovalText(cadet.status)}</span></td>
                <td><button type="button" class="action-btn registered-cadet-action" data-cadet-id="${escapeApprovalText(cadet.cadet_id)}" data-db-id="${escapeApprovalText(cadet.id)}">Drop Cadet</button></td>
            </tr>
        `).join("") : '<tr><td colspan="5">No registered cadets found.</td></tr>';
    } catch (error) {
        registeredCadetsBody.innerHTML = `<tr><td colspan="5">${escapeApprovalText(error.message)}</td></tr>`;
    }
}

registeredCadetsBody?.addEventListener("click", (event) => {
    const button = event.target.closest(".registered-cadet-action");
    if (!button) return;

    const cadetId = button.dataset.cadetId;
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    if (!officer?.id || !button.dataset.dbId) return;
    if (!confirm(`Drop ${cadetId} from the current roster?`)) return;

    button.disabled = true;
    fetch("../backend/officer-assign-platoon.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "drop", officer_id: officer.id, cadet_id: button.dataset.dbId })
    })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            if (data.success) {
                loadRegisteredCadets();
                if (typeof loadAttendanceRoster === "function") loadAttendanceRoster();
                if (typeof renderOfficerDashboard === "function") renderOfficerDashboard();
            } else {
                button.disabled = false;
            }
        })
        .catch(() => {
            alert("Unable to drop the cadet. Please try again.");
            button.disabled = false;
        });
});

// Loads all cadets available for platoon assignment.
async function loadTotalCadets() {
    if (!totalCadetsBody) {
        return;
    }

    totalCadetsBody.innerHTML = '<tr><td colspan="6">Loading cadets...</td></tr>';

    try {
        const response = await fetch("../backend/unassigned-cadets.php", { cache: "no-store" });
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || "Unable to load cadets.");
        }

        const cadets = [...(data.cadets || [])].sort((firstCadet, secondCadet) =>
            String(firstCadet.full_name || "").localeCompare(String(secondCadet.full_name || ""), undefined, { sensitivity: "base" })
        );

        totalCadetsBody.innerHTML = cadets.length ? cadets.map((cadet) => {
            const isUnassigned = !cadet.platoon_id || !cadet.company;
            const action = `<div class="platoon-assignment-action"><select class="company-assignment-select" data-cadet-id="${escapeApprovalText(cadet.id)}" onchange="updatePlatoonOptions('${escapeApprovalText(cadet.id)}')">${companyOptionsHtml(cadet.company)}</select><select class="platoon-assignment-select" data-cadet-id="${escapeApprovalText(cadet.id)}" id="platoon-select-${escapeApprovalText(cadet.id)}">${platoonOptionsHtml(cadet.company, cadet.platoon_name)}</select><button type="button" class="primary-btn assign-platoon-action" data-cadet-id="${escapeApprovalText(cadet.id)}">${isUnassigned ? "Assign" : "Change"}</button></div>`;
            return `
            <tr>
                <td>${escapeApprovalText(cadet.cadet_id)}</td>
                <td>${escapeApprovalText(cadet.full_name)}</td>
                <td>${escapeApprovalText(cadet.year_level || "Not specified")}</td>
                <td>${escapeApprovalText(cadet.company || "Unassigned")}</td>
                <td>${escapeApprovalText(platoonNameFromId(cadet.platoon_id))}</td>
                <td>${action}</td>
            </tr>
        `;
        }).join("") : '<tr><td colspan="6">No unassigned cadets found.</td></tr>';
    } catch (error) {
        totalCadetsBody.innerHTML = `<tr><td colspan="6">${escapeApprovalText(error.message)}</td></tr>`;
    }
}

// Builds company options for the assignment selector.
function companyOptionsHtml(selectedCompany = '') {
    const companies = ['Alpha Company', 'Bravo Company', 'Charlie Company', 'Delta Company', 'Echo Company', 'Foxtrot Company', 'HQ'];
    return `<option value="">Choose company</option>${companies.map(company => `<option value="${company}" ${selectedCompany === company ? 'selected' : ''}>${company}</option>`).join('')}`;
}

// Builds platoon options for the selected company.
function platoonOptionsHtml(company = '', selectedPlatoon = '') {
    const base = String(company || '').replace(/ Company$/, '');
    const platoons = base === 'HQ' ? ['HQ', 'Medics'] : base ? [`${base} First`, `${base} Second`, `${base} Third`] : [];
    return `<option value="">Choose platoon</option>${platoons.map(platoon => `<option value="${platoon}" ${selectedPlatoon === platoon ? 'selected' : ''}>${platoon}</option>`).join('')}`;
}

// Refreshes platoon options after a company changes.
function updatePlatoonOptions(cadetId) {
    const companySelect = totalCadetsBody.querySelector(`.company-assignment-select[data-cadet-id="${cadetId}"]`);
    const platoonSelect = document.getElementById(`platoon-select-${cadetId}`);
    if (companySelect && platoonSelect) platoonSelect.innerHTML = platoonOptionsHtml(companySelect.value);
}

// Updates dashboard labels with the unassigned cadet count.
async function loadUnassignedCadetCount() {
    try {
        const response = await fetch("../backend/unassigned-cadets.php", { cache: "no-store" });
        const data = await response.json();
        if (!data.success) return;

        const label = `Unassigned Cadets (${data.cadets.length})`;
        document.getElementById("dashboardTotalCadetsButton")?.replaceChildren(document.createTextNode(label));
        document.getElementById("viewTotalCadets")?.replaceChildren(document.createTextNode(label));
    } catch (error) {
        console.error("Unable to load unassigned cadet count:", error);
    }
}

totalCadetsBody?.addEventListener("click", async (event) => {
    const button = event.target.closest(".assign-platoon-action");
    if (!button) return;

    const companySelect = totalCadetsBody.querySelector(`.company-assignment-select[data-cadet-id="${button.dataset.cadetId}"]`);
    const platoonSelect = document.getElementById(`platoon-select-${button.dataset.cadetId}`);
    const officer = JSON.parse(sessionStorage.getItem("rotc_logged_officer") || "null");
    if (!companySelect?.value || !platoonSelect?.value || !officer?.id) {
        alert("Select both a company and a platoon first.");
        return;
    }

    button.disabled = true;
    const response = await fetch("../backend/officer-assign-platoon.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ officer_id: officer.id, cadet_id: button.dataset.cadetId, company: companySelect.value, platoon_name: platoonSelect.value })
    });
    const data = await response.json();
    alert(data.message);
    if (data.success) {
        await loadTotalCadets();
        if (typeof loadAttendanceRoster === "function") await loadAttendanceRoster();
        if (typeof updatePlatoonCounts === "function") updatePlatoonCounts();
        if (typeof renderOfficerDashboard === "function") renderOfficerDashboard();
    }
    else button.disabled = false;
});

viewTotalCadets?.addEventListener("click", () => {
    openModal("totalCadetsModal");
    loadTotalCadets();
});

async function loadPendingCadets() {
    if (!pendingCadetsBody) {
        return;
    }

    pendingCadetsBody.innerHTML = '<tr><td colspan="6">Loading pending registrations...</td></tr>';

    try {
        const response = await fetch("../cadet/pending.php", { cache: "no-store" });
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || "Unable to load registrations.");
        }

        if (data.cadets.length === 0) {
            pendingCadetsBody.innerHTML = '<tr><td colspan="6">No pending registrations.</td></tr>';
            return;
        }

        pendingCadetsBody.innerHTML = data.cadets.map((cadet) => `
            <tr>
                <td>${escapeApprovalText(cadet.cadet_id)}</td>
                <td>${escapeApprovalText(cadet.full_name)}</td>
                <td>${escapeApprovalText(cadet.email)}</td>
                <td>${escapeApprovalText(cadet.course)}</td>
                <td>${escapeApprovalText(cadet.created_at)}</td>
                <td>
                    <button type="button" class="primary-btn approval-action" data-cadet-id="${escapeApprovalText(cadet.cadet_id)}" data-action="approve">Approve</button>
                    <button type="button" class="danger-btn approval-action" data-cadet-id="${escapeApprovalText(cadet.cadet_id)}" data-action="reject">Reject</button>
                </td>
            </tr>
        `).join("");
    } catch (error) {
        pendingCadetsBody.innerHTML = `<tr><td colspan="6">${escapeApprovalText(error.message)}</td></tr>`;
    }
}

async function reviewCadet(cadetId, action) {
    const response = await fetch("../cadet/approve.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cadet_id: cadetId, action })
    });
    const data = await response.json();

    if (!data.success) {
        throw new Error(data.message || "Unable to review registration.");
    }

    await loadPendingCadets();
    await loadUnassignedCadetCount();
}

pendingCadetsBody?.addEventListener("click", async (event) => {
    const button = event.target.closest(".approval-action");
    if (!button) {
        return;
    }

    button.disabled = true;

    try {
        await reviewCadet(button.dataset.cadetId, button.dataset.action);
    } catch (error) {
        alert(error.message);
        button.disabled = false;
    }
});

refreshPendingCadets?.addEventListener("click", loadPendingCadets);
loadPendingCadets();
loadRegisteredCadets();
loadUnassignedCadetCount();

window.setInterval(() => {
    if (document.visibilityState !== "visible") return;
    loadPendingCadets();
    loadRegisteredCadets();
    loadUnassignedCadetCount();
}, 10000);
