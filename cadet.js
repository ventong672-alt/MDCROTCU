/* =========================================================
    AUTHENTICATED CADET DATA
========================================================= */

const loggedCadet = JSON.parse(
    sessionStorage.getItem("rotc_logged_cadet") || "null"
);

if (!loggedCadet || !loggedCadet.id || !loggedCadet.name) {
    window.location.replace("cadet-login.php");
}

// Resolves the company name represented by a platoon.
function getCompanyFromPlatoon(platoon) {
    const platoonName = String(platoon || "").replace(/\s+Platoon$/i, "").trim();
    if (!platoonName) return "";
    if (platoonName === "HQ" || platoonName === "Medics") return "HQ";

    const companyName = platoonName.split(/\s+/)[0];
    return companyName ? `${companyName} Company` : "";
}

const currentCadet = {

    id: loggedCadet?.id || "",

    name: loggedCadet?.name || "",

    year: loggedCadet?.year || "",

    section: loggedCadet?.section || "",

    company: loggedCadet?.company || getCompanyFromPlatoon(loggedCadet?.platoon),

    platoon: loggedCadet?.platoon
        ? `${loggedCadet.platoon} Platoon`
        : "",

    status: loggedCadet?.status || "",

    grades: [],

    conduct: [],

    attendance: [],

    weightedEvaluation: null,

    weightedEvaluations: {}

};


/* =========================================================
   HELPER
========================================================= */

// Creates display initials from a cadet name.
function getInitials(name) {

    return name
        .split(" ")
        .map(word => word.charAt(0))
        .join("")
        .substring(0, 2)
        .toUpperCase();

}

// Updates the cadet identity shown in the portal.
function updateCadetDisplay() {
    const initials = getInitials(currentCadet.name);
    document.getElementById("topAvatar").textContent = initials;
    document.getElementById("studentAvatar").textContent = initials;
    document.getElementById("topName").textContent = currentCadet.name;
    document.getElementById("studentName").textContent = currentCadet.name;
    document.getElementById("studentMeta").textContent =
        `Cadet ID: ${currentCadet.id} • ${currentCadet.company} • ${currentCadet.platoon}`;
    document.getElementById("studentInfoId").textContent = currentCadet.id;
    document.getElementById("studentInfoName").textContent = currentCadet.name;
    document.getElementById("studentInfoYear").textContent = currentCadet.year;
    document.getElementById("studentInfoCompany").textContent = currentCadet.company;
    document.getElementById("studentInfoPlatoon").textContent = currentCadet.platoon;
    document.getElementById("studentInfoStatus").textContent = currentCadet.status;
}

updateCadetDisplay();


/* =========================================================
   LOAD CADET INFORMATION
========================================================= */

// Loads the logged-in cadet's profile and records.
async function loadCadetInformation() {
    try {
        const response = await fetch("../backend/cadet-records.php", {
            cache: "no-store"
        });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || "Unable to load cadet records.");

        const cadet = data.cadet;
        currentCadet.id = cadet.cadet_id;
        currentCadet.name = cadet.full_name;
        currentCadet.year = cadet.year_level || "—";
        currentCadet.section = cadet.section || "—";
        currentCadet.company = cadet.company || getCompanyFromPlatoon(cadet.platoon) || "—";
        currentCadet.platoon = cadet.platoon || "—";
        currentCadet.status = cadet.status || "—";
        currentCadet.weightedEvaluation = cadet.weighted_evaluation || null;
        currentCadet.weightedEvaluations = data.weighted_evaluations || {};

        updateCadetDisplay();

        currentCadet.grades = data.grades || [];
        currentCadet.conduct = data.conduct || [];
        currentCadet.attendance = data.attendance || [];
        renderCadetPortal();
    } catch (error) {
        console.error("Unable to load approved cadet records:", error);
        renderCadetPortal();
    }

    renderCadetQr();

}

let cadetRefreshInFlight = false;

// Reloads cadet data and redraws the portal.
async function refreshCadetPortal() {
    if (cadetRefreshInFlight || document.visibilityState !== "visible") return;
    cadetRefreshInFlight = true;
    try {
        await loadCadetInformation();
    } finally {
        cadetRefreshInFlight = false;
    }
}

// Renders the cadet attendance QR code.
function renderCadetQr() {
    const container = document.getElementById("cadetQr");
    const downloadButton = document.getElementById("downloadCadetQr");
    if (!container || !currentCadet.id || typeof QRCode === "undefined") return;

    container.innerHTML = "";
    new QRCode(container, {
        text: currentCadet.id,
        width: 220,
        height: 220,
        colorDark: "#17351d",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    downloadButton?.addEventListener("click", () => {
        const canvas = container.querySelector("canvas");
        const image = container.querySelector("img");
        const link = document.createElement("a");
        link.download = `cadet-${currentCadet.id}-attendance-qr.png`;
        link.href = canvas ? canvas.toDataURL("image/png") : image?.src;
        link.click();
    }, { once: true });
}


/* =========================================================
   SUMMARY
========================================================= */

// Calculates the cadet's grade, attendance, and conduct summary.
function calculateSummary() {

    /* EXAMS: Prelim 30%, Midterm 30%, Finals 40%. */
    const examWeights = { prelim: 0.30, midterm: 0.30, finals: 0.40 };
    const examScores = {};
    Object.keys(examWeights).forEach(term => {
        const scores = getSelectedSemesterGrades()
            .filter(item => normalizeTerm(item.term || item.category) === term)
            .map(item => Number(item.grade))
            .filter(score => Number.isFinite(score));
        if (scores.length) {
            examScores[term] = scores.reduce((sum, score) => sum + score, 0) / scores.length;
        }
    });

    const availableExamWeight = Object.keys(examScores)
        .reduce((sum, term) => sum + examWeights[term], 0);
    const weightedExamTotal = Object.keys(examScores)
        .reduce((sum, term) => sum + examScores[term] * examWeights[term], 0);
    const examAverage = availableExamWeight ? weightedExamTotal / availableExamWeight : 0;


    /* MERITS */

    const totalMerits = currentCadet.conduct

        .filter(item => item.type === "merit")

        .reduce(
            (sum, item) => sum + Number(item.points),
            0
        );


    document.getElementById("studentMerits").textContent =
        currentCadet.conduct.length ? totalMerits : "—";


    /* DEMERITS */

    const totalDemerits = currentCadet.conduct

        .filter(item => item.type === "demerit")

        .reduce(
            (sum, item) => sum + Number(item.points),
            0
        );


    document.getElementById("studentDemerits").textContent =
        currentCadet.conduct.length ? totalDemerits : "—";


    /* ATTENDANCE */

    const totalAttendance =
        currentCadet.attendance.length;

    const presentOrExcused =
        currentCadet.attendance.filter(item =>
            item.status === "Present" ||
            item.status === "Excused"
        ).length;


    let attendanceRate = 0;

    if (totalAttendance > 0) {

        attendanceRate =
            (presentOrExcused / totalAttendance) * 100;

    }


    document.getElementById("studentAttendance").textContent =
        totalAttendance ? Math.round(attendanceRate) + "%" : "—";

    const conductScore = Math.max(0, Math.min(100, 100 + totalMerits - totalDemerits));
    const finalGrade = getSelectedSemesterGrades().length || totalAttendance || currentCadet.conduct.length
        ? (examAverage * 0.50) + (attendanceRate * 0.25) + (conductScore * 0.25)
        : 0;

    document.getElementById("studentGradeAverage").textContent =
        getSelectedSemesterGrades().length || totalAttendance || currentCadet.conduct.length
            ? finalGrade.toFixed(2)
            : "—";

}

// Normalizes a grade term for consistent comparison.
function normalizeTerm(term) {
    const value = String(term || "").toLowerCase().replace(/[^a-z]/g, "");
    if (value.includes("prelim")) return "prelim";
    if (value.includes("midterm") || value === "mid") return "midterm";
    if (value.includes("final")) return "finals";
    return value;
}

// Returns grades for the selected semester.
function getSelectedSemesterGrades() {
    const semester = document.getElementById("cadetSemesterSelect")?.value || "First Semester";
    return currentCadet.grades.filter(item => (item.semester || "First Semester") === semester);
}

// Returns the weighted evaluation for the selected semester.
function getSelectedWeightedEvaluation() {
    const semester = document.getElementById("cadetSemesterSelect")?.value || "First Semester";
    return currentCadet.weightedEvaluations[semester] || null;
}


/* =========================================================
   RENDER GRADES
========================================================= */

// Converts an evaluation score to a display label.
function getEvaluationLabel(score) {
    if (score >= 90) return "Outstanding";
    if (score >= 85) return "Very Satisfactory";
    if (score >= 75) return "Satisfactory";
    if (score >= 60) return "Needs Improvement";
    return "Unsatisfactory";
}

// Binds the cadet evaluation visibility toggle.
function bindCadetEvaluationToggle() {
    const buttons = [
        document.getElementById("viewCadetEvaluationBtnDetailed"),
        document.getElementById("viewCadetEvaluationBtn")
    ].filter(Boolean);

    buttons.forEach(button => {
        button.onclick = null;
        button.addEventListener("click", () => {
            const targetId = button.id === "viewCadetEvaluationBtnDetailed" ? "studentEvaluationSummaryDetailed" : "studentEvaluationSummary";
            const target = document.getElementById(targetId);
            if (!target) return;
            const isHidden = target.style.display === "none";
            target.style.display = isHidden ? "block" : "none";
        });
    });
}

// Renders the cadet evaluation summary.
function renderEvaluationSummary() {
    bindCadetEvaluationToggle();

    const container = document.getElementById("studentEvaluationSummaryDetailed") || document.getElementById("studentEvaluationSummary");
    const button = document.getElementById("viewCadetEvaluationBtnDetailed") || document.getElementById("viewCadetEvaluationBtn");

    if (!container || !button) return;

    const examWeights = { prelim: 0.30, midterm: 0.30, finals: 0.40 };
    const examScores = {};
    Object.keys(examWeights).forEach(term => {
        const scores = getSelectedSemesterGrades()
            .filter(item => normalizeTerm(item.term || item.category) === term)
            .map(item => Number(item.grade))
            .filter(score => Number.isFinite(score));
        if (scores.length) {
            examScores[term] = scores.reduce((sum, score) => sum + score, 0) / scores.length;
        }
    });

    const availableExamWeight = Object.keys(examScores)
        .reduce((sum, term) => sum + examWeights[term], 0);
    const weightedExamTotal = Object.keys(examScores)
        .reduce((sum, term) => sum + examScores[term] * examWeights[term], 0);
    const examAverage = availableExamWeight ? weightedExamTotal / availableExamWeight : 0;

    const totalAttendance = currentCadet.attendance.length;
    const presentOrExcused = currentCadet.attendance.filter(item => item.status === "Present" || item.status === "Excused").length;
    const attendanceRate = totalAttendance ? (presentOrExcused / totalAttendance) * 100 : 0;

    const totalMerits = currentCadet.conduct.filter(item => item.type === "merit").reduce((sum, item) => sum + Number(item.points), 0);
    const totalDemerits = currentCadet.conduct.filter(item => item.type === "demerit").reduce((sum, item) => sum + Number(item.points), 0);
    const attitudeScore = Math.max(0, Math.min(100, 100 + totalMerits - totalDemerits));
    const evaluationScore = (examAverage * 0.50) + (attendanceRate * 0.25) + (attitudeScore * 0.25);

    const selectedWeightedEvaluation = getSelectedWeightedEvaluation()
        || (Object.keys(currentCadet.weightedEvaluations).length ? null : currentCadet.weightedEvaluation);
    const weightedFinal = Number.isFinite(Number(selectedWeightedEvaluation?.final_grade))
        ? Number(selectedWeightedEvaluation.final_grade)
        : null;
    renderWeightedEvaluationCard(selectedWeightedEvaluation);
    const displayedEvaluationScore = weightedFinal ?? evaluationScore;
    const evaluationLabel = getEvaluationLabel(displayedEvaluationScore);
    const selectedGrades = getSelectedSemesterGrades();
    const latestOfficerEvaluation = selectedGrades.slice().reverse().find(item => item.professional_evaluation)?.professional_evaluation || "No officer evaluation yet";
    const weightedScores = selectedWeightedEvaluation?.scores || {};
    const hasWeightedScores = Object.keys(weightedScores).length > 0;
    const hasAnyRecord = selectedGrades.length || currentCadet.attendance.length || currentCadet.conduct.length || hasWeightedScores;

    if (!hasAnyRecord) {
        container.innerHTML = '<div style="color:#5f6f60; font-size:12px;">No evaluation data yet.</div>';
        container.style.display = "block";
        button.onclick = () => {
            const isHidden = container.style.display === "none";
            container.style.display = isHidden ? "block" : "none";
        };
        return;
    }

    const gradeBreakdown = [
        { label: "Prelim", value: examScores.prelim ? examScores.prelim.toFixed(2) : "—" },
        { label: "Midterm", value: examScores.midterm ? examScores.midterm.toFixed(2) : "—" },
        { label: "Finals", value: examScores.finals ? examScores.finals.toFixed(2) : "—" }
    ];

    container.innerHTML = `
        <div style="display:flex; flex-direction:column; gap:12px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                <div>
                    <strong style="display:block; font-size:14px; color:#17351d; margin-bottom:4px;">ROTC Evaluation Summary</strong>
                    <div style="font-size:12px; color:#5f6f60;">Evaluation based on ROTC grade, attendance, and conduct.</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:22px; font-weight:700; color:#17351d;">${displayedEvaluationScore.toFixed(2)}</div>
                    <div style="font-size:11px; color:#3d5b47; font-weight:600; text-transform:uppercase;">${evaluationLabel}</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:10px;">
                <div style="background:#ffffff; border:1px solid #dfe8df; border-radius:10px; padding:10px 12px;">
                    <div style="font-size:11px; color:#5f6f60;">Grade</div>
                    <div style="font-size:18px; font-weight:700; color:#17351d; margin-top:4px;">${examAverage ? examAverage.toFixed(2) : "0.00"}</div>
                    <div style="font-size:11px; color:#5f6f60; margin-top:4px;">${gradeBreakdown.map(item => `${item.label}: ${item.value}`).join(" • ")}</div>
                </div>

                <div style="background:#ffffff; border:1px solid #dfe8df; border-radius:10px; padding:10px 12px;">
                    <div style="font-size:11px; color:#5f6f60;">Attendance</div>
                    <div style="font-size:18px; font-weight:700; color:#17351d; margin-top:4px;">${Math.round(attendanceRate)}%</div>
                    <div style="font-size:11px; color:#5f6f60; margin-top:4px;">Present / Excused: ${presentOrExcused} / ${totalAttendance || 0}</div>
                </div>

                <div style="background:#ffffff; border:1px solid #dfe8df; border-radius:10px; padding:10px 12px;">
                    <div style="font-size:11px; color:#5f6f60;">Conduct</div>
                    <div style="font-size:18px; font-weight:700; color:#17351d; margin-top:4px;">${totalMerits - totalDemerits}</div>
                    <div style="font-size:11px; color:#5f6f60; margin-top:4px;">Merits: ${totalMerits} • Demerits: ${totalDemerits}</div>
                </div>

                <div style="background:#ffffff; border:1px solid #dfe8df; border-radius:10px; padding:10px 12px;">
                    <div style="font-size:11px; color:#5f6f60;">Final ROTC Evaluation Rating</div>
                    <div style="font-size:18px; font-weight:700; color:#17351d; margin-top:4px;">${displayedEvaluationScore.toFixed(2)}</div>
                    <div style="font-size:11px; color:#5f6f60; margin-top:4px;">${evaluationLabel}</div>
                </div>
            </div>

            <div style="background:#ffffff; border:1px solid #dfe8df; border-radius:10px; padding:10px 12px;">
                <div style="font-size:11px; color:#5f6f60; margin-bottom:6px;">Officer Professional Evaluation</div>
                <div style="font-size:16px; font-weight:700; color:#17351d;">${latestOfficerEvaluation}</div>
            </div>

            ${hasWeightedScores ? `<div style="background:#eef6f0; border:1px solid #d5e7d8; border-radius:10px; padding:10px 12px;"><div style="font-size:11px; color:#5f6f60; margin-bottom:6px;">Weighted Grade Breakdown</div><div style="display:grid; gap:5px;">${(selectedWeightedEvaluation.components || []).map(component => `<div style="display:flex; justify-content:space-between; gap:10px; font-size:11px;"><span>${escapeHTML(component.label)} (${Number(component.weight).toFixed(0)}%)</span><strong>${Number.isFinite(Number(selectedWeightedEvaluation.scores[component.component_key])) ? Number(selectedWeightedEvaluation.scores[component.component_key]).toFixed(2) : "—"}</strong></div>`).join('')}</div><div style="margin-top:8px; font-size:13px; font-weight:800; color:#17351d;">Final weighted grade: ${weightedFinal === null ? "Incomplete" : weightedFinal.toFixed(2)}</div></div>` : ''}
        </div>
    `;

    container.style.display = "none";
    button.onclick = () => {
        const isHidden = container.style.display === "none";
        container.style.display = isHidden ? "block" : "none";
    };
}

// Renders the weighted evaluation details card.
function renderWeightedEvaluationCard(evaluation = getSelectedWeightedEvaluation()) {
    const card = document.getElementById('studentWeightedEvaluationCard');
    if (!card || !evaluation?.components?.length || !Object.keys(evaluation.scores || {}).length) {
        if (card) card.hidden = true;
        return;
    }

    card.hidden = false;
    card.innerHTML = `
        <div class="cadet-weighted-card-header">
            <div><strong>Weighted Evaluation</strong><small>Official component-based ROTC grade</small></div>
            <div class="cadet-weighted-final">${evaluation.final_grade === null ? '—' : Number(evaluation.final_grade).toFixed(2)}</div>
        </div>
        <div class="cadet-weighted-components">
            ${evaluation.components.map(component => `<div><span>${escapeHTML(component.label)}</span><strong>${Number(evaluation.scores[component.component_key]).toFixed(2)} <small>${Number(component.weight).toFixed(0)}%</small></strong></div>`).join('')}
        </div>`;
}

// Renders grades and computed academic summaries.
function renderGrades() {

    const container =
        document.getElementById("studentGradesList");

    const terms = [
        { key: "prelim", label: "Prelim" },
        { key: "midterm", label: "Midterm" },
        { key: "finals", label: "Finals" }
    ];
    const semesterGrades = getSelectedSemesterGrades();
    const gradeScores = semesterGrades
        .map(item => Number(item.grade))
        .filter(score => Number.isFinite(score));
    const semesterAverage = gradeScores.length
        ? gradeScores.reduce((sum, score) => sum + score, 0) / gradeScores.length
        : null;
    const gpa = semesterAverage === null ? null : percentageToGpa(semesterAverage);
    const gpaElement = document.getElementById("studentGpa");
    if (gpaElement) gpaElement.textContent = gpa === null ? "—" : gpa.toFixed(2);

    const gradesByTerm = terms.map(term => ({
        ...term,
        records: semesterGrades.filter(item => normalizeTerm(item.term || item.category) === term.key)
    }));


    if (gradesByTerm.every(term => term.records.length === 0)) {

        container.innerHTML = `
            <div class="empty-state">
                No grade records found.
            </div>
        `;

        return;

    }


    container.innerHTML = gradesByTerm.map(term => {
        const scores = term.records.map(item => Number(item.grade)).filter(score => Number.isFinite(score));
        const average = scores.length
            ? scores.reduce((sum, score) => sum + score, 0) / scores.length
            : null;

        return `
            <div class="grade-item">
                <div class="grade-subject"><strong>${term.label}</strong><small>${term.records.length ? `${term.records.length} record(s)` : "Not recorded"}</small></div>
                <div class="grade-score">${average === null ? "—" : average.toFixed(2)}</div>
            </div>
            ${term.records.map(item => `
                <div class="grade-item grade-record-detail">
                    <div class="grade-subject"><strong>${escapeHTML(item.subject || "ROTC")}</strong><small>${escapeHTML(item.professional_evaluation || item.category || "No evaluation")}</small></div>
                    <div class="grade-score">${Number.isFinite(Number(item.grade)) ? Number(item.grade).toFixed(2) : "—"}</div>
                </div>
            `).join("")}
        `;

    }).join("");

}

// Converts a percentage score to the portal GPA scale.
function percentageToGpa(score) {
    if (score >= 97) return 4.00;
    if (score >= 93) return 3.70;
    if (score >= 90) return 3.30;
    if (score >= 87) return 3.00;
    if (score >= 83) return 2.70;
    if (score >= 80) return 2.30;
    if (score >= 77) return 2.00;
    if (score >= 73) return 1.70;
    if (score >= 70) return 1.30;
    if (score >= 60) return 1.00;
    return 0.00;
}


/* =========================================================
   RENDER MERITS / DEMERITS
========================================================= */

// Renders the cadet conduct record summary.
function renderConduct() {

    const container =
        document.getElementById("studentConductList");


    if (currentCadet.conduct.length === 0) {

        container.innerHTML = `
            <div class="empty-state">
                No merits or demerits recorded.
            </div>
        `;

        return;

    }


    container.innerHTML =
        currentCadet.conduct.map(item => {

            const isMerit =
                item.type === "merit";

            return `
                <div class="conduct-item">

                    <div class="conduct-icon ${isMerit ? "merit" : "demerit"}">
                        ${isMerit ? "+" : "-"}
                    </div>

                    <div class="conduct-info">

                        <strong>
                            ${escapeHTML(item.title)}
                        </strong>

                        <small>
                            ${escapeHTML(item.date)}
                        </small>

                    </div>

                    <div class="conduct-points ${isMerit ? "positive" : "negative"}">

                        ${isMerit ? "+" : "-"}${item.points}

                    </div>

                </div>
            `;

        }).join("");

}


/* =========================================================
   RENDER ATTENDANCE
========================================================= */

// Renders the cadet attendance history and rate.
function renderAttendance() {

    const tbody =
        document.getElementById("studentAttendanceList");


    if (currentCadet.attendance.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="empty-state">No attendance records.</td>
            </tr>
        `;

        return;

    }


    tbody.innerHTML =
        currentCadet.attendance.map(item => {

            const statusClass =
                item.status.toLowerCase();


            return `
                <tr>

                    <td>
                        ${escapeHTML(item.date)}
                    </td>

                    <td>

                        <span class="status ${statusClass}">
                            ${escapeHTML(item.status)}
                        </span>

                    </td>

                    <td>${escapeHTML(item.time_in || "—")}</td>

                    <td>${escapeHTML(item.time_out || "—")}</td>

                </tr>
            `;

        }).join("");

}


/* =========================================================
   RENDER EVERYTHING
========================================================= */

// Loads the cadet training schedule.
async function loadCadetTrainingSchedule() {

    const container = document.getElementById("cadetTrainingScheduleList");

    if (!container) return;

    try {
        const response = await fetch("../backend/admin-training.php", {
            cache: "no-store"
        });
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || "Unable to load training schedules.");
        }

        const sessions = [...(data.sessions || [])].sort((a, b) => {
            const dateA = new Date(`${a.training_date}T00:00:00`);
            const dateB = new Date(`${b.training_date}T00:00:00`);
            return dateA - dateB;
        });
        window.cadetTrainingSessions = sessions;

        if (!sessions.length) {
            if (container) container.innerHTML = '<div class="empty-state">No training schedules published yet.</div>';
            return;
        }

        const scheduleMarkup = sessions.map(session => {
            return `
                <div class="training-card">
                    <div class="training-info">
                        <h4>${escapeHTML(session.title)}</h4>
                        <p>${escapeHTML(session.training_date)} · ${escapeHTML(session.start_time)} - ${escapeHTML(session.end_time)}</p>
                        <p>${escapeHTML(session.location)}</p>
                    </div>
                </div>
            `;
        }).join("");
        if (container) container.innerHTML = scheduleMarkup;

    } catch (error) {
        console.error("Unable to load training schedules for cadet:", error);
        if (container) container.innerHTML = '<div class="empty-state">Unable to load the published training schedule.</div>';
    }

}

// Opens details for a selected training session.
function viewCadetTraining(trainingId) {
    const session = (window.cadetTrainingSessions || []).find(item => Number(item.id) === Number(trainingId));
    const modal = document.getElementById("cadetTrainingViewModal");
    const content = document.getElementById("cadetTrainingViewContent");
    if (!session || !modal || !content) return;

    content.innerHTML = `
        <h2>${escapeHTML(session.title)}</h2>
    `;
    modal.classList.add("show");
}

// Closes the training session details view.
function closeCadetTrainingView() {
    document.getElementById("cadetTrainingViewModal")?.classList.remove("show");
}

// Loads announcements visible to cadets.
async function loadCadetAnnouncements() {
    const container = document.getElementById("cadetAnnouncementsList");
    if (!container) return;

    try {
        const response = await fetch("../backend/cadet-announcements.php", { cache: "no-store" });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || "Unable to load announcements.");

        container.innerHTML = data.announcements.length ? data.announcements.map(item => `
            <div class="training-card">
                <div class="training-info">
                    <h3>${escapeHTML(item.title)}</h3>
                    <p>${escapeHTML(item.message).replace(/\r?\n/g, '<br>')}</p>
                    ${(item.attachments || []).length ? `<div class="announcement-attachments">${(item.attachments || []).map(attachment => `<a href="../${escapeHTML(attachment.file_path)}" download="${escapeHTML(attachment.original_name)}">Download ${escapeHTML(attachment.original_name)}</a>`).join('')}</div>` : ''}
                    <small>Published by ${escapeHTML(item.officer_name)} · ${escapeHTML(item.created_at)}</small>
                </div>
            </div>
        `).join("") : '<div class="empty-state">No announcements published yet.</div>';
    } catch (error) {
        console.error("Unable to load announcements for cadet:", error);
        container.innerHTML = '<div class="empty-state">Unable to load announcements.</div>';
    }
}

// Renders all current cadet portal sections.
function renderCadetPortal() {

    renderEvaluationSummary();
    renderGrades();

    renderConduct();

    renderAttendance();

    calculateSummary();

    loadCadetTrainingSchedule();
    loadCadetAnnouncements();

}


/* =========================================================
   NAVIGATION
========================================================= */

const navLinks =
    document.querySelectorAll(".nav-link");


navLinks.forEach(link => {

    link.addEventListener("click", function(event) {

        event.preventDefault();


        navLinks.forEach(item => {

            item.classList.remove("active");

        });


        this.classList.add("active");


        const target =
            this.getAttribute("href");


        if (target) {
            const section = document.querySelector(target);
            if (section) {
                showCadetSection(section.id);
                window.scrollTo({ top: 0, behavior: "smooth" });
            }
        }

    });

});


/* =========================================================
   UPDATE ACTIVE NAVIGATION WHILE SCROLLING
========================================================= */

const sections = [

    document.getElementById("dashboard"),

    document.getElementById("grades"),

    document.getElementById("conduct"),

    document.getElementById("attendance"),

    document.getElementById("evaluation"),

    document.getElementById("training"),

    document.getElementById("announcements"),

];

// Switches the visible cadet portal section.
function showCadetSection(sectionId) {
    const dashboard = document.getElementById("dashboard");
    if (!dashboard) return;

    sections.forEach(section => section?.classList.remove("active-section"));
    dashboard.classList.add("active-section");

    if (sectionId === "dashboard") {
        dashboard.classList.remove("child-section-view");
        return;
    }

    dashboard.classList.add("child-section-view");
    document.getElementById(sectionId)?.classList.add("active-section");
}


/* Scroll tracking is unnecessary because navigation now switches one section at a time. */
/* window.addEventListener("scroll", function() {

    let current = "dashboard";


    sections.forEach(section => {

        if (!section) return;


        const sectionTop =
            section.offsetTop - 130;


        if (window.scrollY >= sectionTop) {

            current = section.id;

        }

    });


    navLinks.forEach(link => {

        link.classList.remove("active");


        const href =
            link.getAttribute("href");


        if (href === "#" + current) {

            link.classList.add("active");

        }

    });

}); */


/* =========================================================
   MOBILE MENU
========================================================= */

const sidebar =
    document.querySelector(".sidebar");

const menuBtn =
    document.getElementById("menuBtn");


menuBtn.addEventListener("click", function(event) {

    event.stopPropagation();

    sidebar.classList.toggle("show");

});


/* =========================================================
   CLOSE MOBILE MENU
========================================================= */

document.addEventListener("click", function(event) {

    if (window.innerWidth <= 850) {

        if (
            !sidebar.contains(event.target) &&
            event.target !== menuBtn
        ) {

            sidebar.classList.remove("show");

        }

    }

});


/* =========================================================
   LOGOUT
========================================================= */

const logoutModal =
    document.getElementById("logoutModal");

const logoutButton =
    document.getElementById("logoutButton");

const cancelLogout =
    document.getElementById("cancelLogout");

const confirmLogout =
    document.getElementById("confirmLogout");


// Clears the cadet session and returns to login.
function logout() {

    if (!logoutModal) {
        sessionStorage.removeItem("rotc_logged_cadet");
        window.location.replace("cadet-login.php");
        return;
    }

    logoutModal.classList.add("show");

}


logoutButton?.addEventListener("click", logout);


cancelLogout?.addEventListener("click", function() {

    logoutModal.classList.remove("show");

});


confirmLogout?.addEventListener("click", function() {

    logoutModal.classList.remove("show");

    sessionStorage.removeItem("rotc_logged_cadet");

    window.location.replace("cadet-login.php");

});


logoutModal?.addEventListener("click", function(event) {

    if (event.target === logoutModal) {

        logoutModal.classList.remove("show");

    }

});


/* =========================================================
   ESC KEY
========================================================= */

document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {

        logoutModal.classList.remove("show");

        sidebar.classList.remove("show");

    }

});


/* =========================================================
   SECURITY / HTML ESCAPE
========================================================= */

// Escapes text before inserting it into portal HTML.
function escapeHTML(value) {

    return String(value)

        .replace(/&/g, "&amp;")

        .replace(/</g, "&lt;")

        .replace(/>/g, "&gt;")

        .replace(/"/g, "&quot;")

        .replace(/'/g, "&#039;");

}


/* =========================================================
   START PORTAL
========================================================= */

document.addEventListener("DOMContentLoaded", function() {

    document.getElementById("cadetSemesterSelect")?.addEventListener("change", () => {
        renderCadetPortal();
    });

    showCadetSection("dashboard");

    loadCadetInformation();

    renderCadetPortal();

    window.setInterval(refreshCadetPortal, 10000);

    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") refreshCadetPortal();
    });

});