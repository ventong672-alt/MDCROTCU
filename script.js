/* =========================================================
   ROTC MIS - COMPLETE FUNCTIONAL SCRIPT
   Front-end only
   Uses localStorage for temporary/realtime browser data
   ========================================================= */

"use strict";

/* =========================================================
   STORAGE
   ========================================================= */

const storage = {

    get(key, fallback = []) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : fallback;
        } catch (error) {
            console.error("Storage read error:", error);
            return fallback;
        }
    },

    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (error) {
            console.error("Storage save error:", error);
        }
    }

};


/* =========================================================
   HTML SECURITY
   ========================================================= */

function escapeHTML(value) {

    if (value === null || value === undefined) {
        return "";
    }

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


/* =========================================================
   TOAST MESSAGE
   ========================================================= */

function showToast(message, type = "success") {

    const oldToast =
        document.querySelector(".rotc-toast");

    if (oldToast) {
        oldToast.remove();
    }

    const toast =
        document.createElement("div");

    toast.className =
        `rotc-toast ${type}`;

    toast.innerHTML = `
        <span>
            ${type === "success" ? "✓" : "!"}
        </span>

        <div>
            ${escapeHTML(message)}
        </div>
    `;

    toast.style.cssText = `
        position: fixed;
        right: 25px;
        bottom: 25px;
        z-index: 100000;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        min-width: 250px;
        max-width: 380px;
        border-radius: 10px;
        background: ${type === "success" ? "#166534" : "#991b1b"};
        color: white;
        font-family: Inter, Arial, sans-serif;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 10px 30px rgba(0,0,0,.2);
        animation: rotcToastIn .25s ease;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {

        toast.style.opacity = "0";
        toast.style.transform = "translateY(10px)";

        setTimeout(() => {
            toast.remove();
        }, 250);

    }, 2500);
}


/* =========================================================
   MODAL FUNCTIONS
   ========================================================= */

function openModal(id) {

    const modal =
        document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.add("show");

    modal.style.display = "flex";

    document.body.style.overflow = "hidden";
}


function closeModal(id) {

    const modal =
        document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.remove("show");

    modal.style.display = "none";

    document.body.style.overflow = "";
}


/* =========================================================
   CADETS
   ========================================================= */

let cadets = storage.get("rotc_cadets", []);

if (!Array.isArray(cadets)) {
    cadets = [];
}


/* =========================================================
   RENDER CADETS
   ========================================================= */

function renderCadets(list = cadets) {

    const tbody =
        document.querySelector(
            "#cadetTable tbody"
        );

    if (!tbody) {
        return;
    }

    tbody.innerHTML = "";

    if (list.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="6"
                    style="text-align:center;padding:30px;">
                    No cadet records found.
                </td>
            </tr>
        `;

        updateDashboardStats();

        return;
    }

    list.forEach((cadet) => {

        const realIndex =
            cadets.findIndex(
                item => item.id === cadet.id
            );

        const row =
            document.createElement("tr");

        row.innerHTML = `

            <td>
                ${escapeHTML(cadet.id)}
            </td>

            <td>
                ${escapeHTML(cadet.name)}
            </td>

            <td>
                ${escapeHTML(cadet.year)}
            </td>

            <td>
                ${escapeHTML(cadet.section)}
            </td>

            <td>
                <span class="status ${
                    cadet.status === "Active"
                        ? "active"
                        : "warning"
                }">
                    ${escapeHTML(cadet.status)}
                </span>
            </td>

            <td>

                <button
                    type="button"
                    class="action-btn view-cadet"
                    data-index="${realIndex}">
                    View
                </button>

            </td>
        `;

        tbody.appendChild(row);
    });

    updateDashboardStats();
}


/* =========================================================
   ADD CADET
   ========================================================= */

const cadetForm =
    document.getElementById("cadetForm");

if (cadetForm) {

    cadetForm.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();

            const id =
                document.getElementById(
                    "cadetId"
                ).value.trim();

            const name =
                document.getElementById(
                    "cadetName"
                ).value.trim();

            const year =
                document.getElementById(
                    "yearLevel"
                ).value;

            const section =
                document.getElementById(
                    "section"
                ).value.trim();


            if (!id || !name || !year || !section) {

                showToast(
                    "Please complete all cadet information.",
                    "error"
                );

                return;
            }


            const existing =
                cadets.some(
                    cadet =>
                        cadet.id.toLowerCase() ===
                        id.toLowerCase()
                );


            if (existing) {

                showToast(
                    "Cadet ID already exists.",
                    "error"
                );

                return;
            }


            cadets.push({

                id,
                name,
                year,
                section,
                status: "Active"

            });


            storage.set(
                "rotc_cadets",
                cadets
            );


            renderCadets();

            updateAttendanceCadetOptions();

            updateGradeCadetOptions();

            closeModal("cadetModal");

            cadetForm.reset();


            addActivity(
                "New cadet registered",
                `${name} was added to the ROTC records.`,
                "blue"
            );


            showToast(
                "Cadet successfully added.",
                "success"
            );

        }
    );
}


/* =========================================================
   CADET SEARCH
   ========================================================= */

const cadetSearch =
    document.getElementById(
        "cadetSearch"
    );

if (cadetSearch) {

    cadetSearch.addEventListener(
        "input",
        function () {

            const query =
                this.value
                    .toLowerCase()
                    .trim();

            const filtered =
                cadets.filter(
                    cadet =>
                        cadet.name
                            .toLowerCase()
                            .includes(query)
                        ||
                        cadet.id
                            .toLowerCase()
                            .includes(query)
                );

            renderCadets(filtered);

        }
    );
}


/* =========================================================
   CADET YEAR FILTER
   ========================================================= */

const cadetPage =
    document.getElementById("cadets");

if (cadetPage) {

    const select =
        cadetPage.querySelector(
            ".toolbar select"
        );

    if (select) {

        select.addEventListener(
            "change",
            function () {

                if (
                    this.value ===
                    "All Cadets"
                ) {

                    renderCadets(cadets);

                    return;
                }

                const filtered =
                    cadets.filter(
                        cadet =>
                            cadet.year ===
                            this.value
                    );

                renderCadets(filtered);

            }
        );
    }
}


/* =========================================================
   ATTENDANCE
   ========================================================= */

let attendanceRecords =
    storage.get(
        "rotc_attendance",
        []
    );


/* =========================================================
   DEFAULT ATTENDANCE
   ========================================================= */

if (!Array.isArray(attendanceRecords)) {

    attendanceRecords = [];

}


/* =========================================================
   RENDER ATTENDANCE
   ========================================================= */

function renderAttendance() {

    const attendancePage =
        document.getElementById(
            "attendance"
        );

    if (!attendancePage) {
        return;
    }

    const tbody =
        attendancePage.querySelector(
            "table tbody"
        );

    if (!tbody) {
        return;
    }

    tbody.innerHTML = "";


    if (attendanceRecords.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="5"
                    style="
                        text-align:center;
                        padding:30px;
                    ">
                    No attendance records yet.
                </td>
            </tr>
        `;

        updateAttendanceStats();

        return;
    }


    attendanceRecords.forEach(
        record => {

            const row =
                document.createElement("tr");

            row.innerHTML = `

                <td>
                    ${escapeHTML(record.id)}
                </td>

                <td>
                    ${escapeHTML(record.name)}
                </td>

                <td>
                    ${escapeHTML(record.training)}
                </td>

                <td>
                    ${escapeHTML(record.time)}
                </td>

                <td>

                    <span class="status ${
                        record.status === "Present"
                            ? "active"
                            : record.status === "Late"
                                ? "warning"
                                : "inactive"
                    }">

                        ${escapeHTML(record.status)}

                    </span>

                </td>
            `;

            tbody.appendChild(row);

        }
    );


    updateAttendanceStats();
}


/* =========================================================
   ATTENDANCE STATISTICS
   ========================================================= */

function updateAttendanceStats() {

    const present =
        attendanceRecords.filter(
            record =>
                record.status ===
                "Present"
        ).length;


    const absent =
        attendanceRecords.filter(
            record =>
                record.status ===
                "Absent"
        ).length;


    const late =
        attendanceRecords.filter(
            record =>
                record.status ===
                "Late"
        ).length;


    const cards =
        document.querySelectorAll(
            "#attendance .stats-grid .stat-card h2"
        );


    if (cards[0]) {
        cards[0].textContent =
            present;
    }


    if (cards[1]) {
        cards[1].textContent =
            absent;
    }


    if (cards[2]) {
        cards[2].textContent =
            late;
    }


    const dashboardPresent =
        document.getElementById(
            "presentCount"
        );


    if (dashboardPresent) {

        dashboardPresent.textContent =
            present;
    }
}


/* =========================================================
   ATTENDANCE MODAL
   ========================================================= */

function openAttendanceModal() {

    const old =
        document.getElementById(
            "attendanceDynamicModal"
        );

    if (old) {
        old.remove();
    }


    const modal =
        document.createElement("div");

    modal.id =
        "attendanceDynamicModal";

    modal.className =
        "modal-overlay show";

    modal.style.display = "flex";


    const options =
        cadets.map(
            cadet => `
                <option value="${escapeHTML(cadet.id)}">
                    ${escapeHTML(cadet.name)}
                    (${escapeHTML(cadet.id)})
                </option>
            `
        ).join("");


    modal.innerHTML = `

        <div class="modal">

            <div class="modal-header">

                <h2>
                    Record Attendance
                </h2>

                <button
                    type="button"
                    id="closeAttendanceModal">
                    ×
                </button>

            </div>


            <form id="attendanceForm">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Cadet
                        </label>

                        <select
                            id="attendanceCadet"
                            required>

                            <option value="">
                                Select Cadet
                            </option>

                            ${options}

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Training
                        </label>

                        <input
                            type="text"
                            id="attendanceTraining"
                            value="ROTC Formation"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Date
                        </label>

                        <input
                            type="date"
                            id="attendanceDate"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Time In
                        </label>

                        <input
                            type="time"
                            id="attendanceTime"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <select
                            id="attendanceStatus"
                            required>

                            <option value="Present">
                                Present
                            </option>

                            <option value="Late">
                                Late
                            </option>

                            <option value="Absent">
                                Absent
                            </option>

                        </select>

                    </div>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="secondary-btn"
                        id="cancelAttendance">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="primary-btn">
                        Save Attendance
                    </button>

                </div>

            </form>

        </div>

    `;


    document.body.appendChild(modal);

    document.body.style.overflow =
        "hidden";


    /* Current date */

    const now =
        new Date();


    const localDate =
        now.getFullYear() +
        "-" +
        String(
            now.getMonth() + 1
        ).padStart(2, "0") +
        "-" +
        String(
            now.getDate()
        ).padStart(2, "0");


    document.getElementById(
        "attendanceDate"
    ).value = localDate;


    /* Current time */

    document.getElementById(
        "attendanceTime"
    ).value =
        String(
            now.getHours()
        ).padStart(2, "0") +
        ":" +
        String(
            now.getMinutes()
        ).padStart(2, "0");


    /* Close */

    document.getElementById(
        "closeAttendanceModal"
    ).addEventListener(
        "click",
        () =>
            closeAttendanceModal()
    );


    document.getElementById(
        "cancelAttendance"
    ).addEventListener(
        "click",
        () =>
            closeAttendanceModal()
    );


    modal.addEventListener(
        "click",
        function (event) {

            if (
                event.target === modal
            ) {

                closeAttendanceModal();

            }

        }
    );


    /* Submit */

    document.getElementById(
        "attendanceForm"
    ).addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const cadetID =
                document.getElementById(
                    "attendanceCadet"
                ).value;


            const cadet =
                cadets.find(
                    item =>
                        item.id ===
                        cadetID
                );


            if (!cadet) {

                showToast(
                    "Please select a cadet.",
                    "error"
                );

                return;
            }


            const date =
                document.getElementById(
                    "attendanceDate"
                ).value;


            const time =
                document.getElementById(
                    "attendanceTime"
                ).value;


            const training =
                document.getElementById(
                    "attendanceTraining"
                ).value.trim();


            const status =
                document.getElementById(
                    "attendanceStatus"
                ).value;


            if (
                !date ||
                !time ||
                !training
            ) {

                showToast(
                    "Please complete all attendance fields.",
                    "error"
                );

                return;
            }


            const formattedTime =
                formatTime(time);


            /*
             * Prevent duplicate attendance
             * for the same cadet,
             * training and date.
             */

            const duplicate =
                attendanceRecords.some(
                    record =>
                        record.id === cadetID &&
                        record.date === date &&
                        record.training.toLowerCase() ===
                            training.toLowerCase()
                );


            if (duplicate) {

                showToast(
                    "Attendance for this cadet and training has already been recorded.",
                    "error"
                );

                return;
            }


            const newRecord = {

                id: cadet.id,

                name: cadet.name,

                training,

                date,

                time: formattedTime,

                status

            };


            attendanceRecords.unshift(
                newRecord
            );


            storage.set(
                "rotc_attendance",
                attendanceRecords
            );


            renderAttendance();

            updateDashboardStats();


            addActivity(
                "Attendance recorded",
                `${cadet.name} - ${training}`,
                "green"
            );


            closeAttendanceModal();


            showToast(
                "Attendance successfully recorded.",
                "success"
            );

        }
    );
}


/* =========================================================
   CLOSE ATTENDANCE MODAL
   ========================================================= */

function closeAttendanceModal() {

    const modal =
        document.getElementById(
            "attendanceDynamicModal"
        );

    if (modal) {
        modal.remove();
    }

    document.body.style.overflow = "";
}


/* =========================================================
   FORMAT TIME
   ========================================================= */

function formatTime(time) {

    if (!time) {
        return "";
    }

    const parts =
        time.split(":");

    let hour =
        Number(parts[0]);

    const minute =
        parts[1];

    const ampm =
        hour >= 12
            ? "PM"
            : "AM";

    hour =
        hour % 12 || 12;

    return `${String(hour).padStart(2, "0")}:${minute} ${ampm}`;
}


/* =========================================================
   TRAINING
   ========================================================= */

let trainings =
    storage.get(
        "rotc_trainings",
        []
    );


function formatDate(date) {

    if (!date) {
        return "";
    }

    return new Date(
        `${date}T00:00:00`
    ).toLocaleDateString(
        "en-US",
        {
            month: "long",
            day: "numeric",
            year: "numeric"
        }
    );
}


function renderTrainings() {

    const container =
        document.querySelector(
            "#training .training-cards"
        );

    if (!container) {
        return;
    }

    container.innerHTML = "";


    trainings
        .sort(
            (a, b) =>
                a.date.localeCompare(
                    b.date
                )
        )
        .forEach(
            (training, index) => {

                const date =
                    new Date(
                        `${training.date}T00:00:00`
                    );


                const day =
                    String(
                        date.getDate()
                    ).padStart(2, "0");


                const month =
                    date.toLocaleDateString(
                        "en-US",
                        {
                            month: "short"
                        }
                    ).toUpperCase();


                const card =
                    document.createElement(
                        "div"
                    );


                card.className =
                    "schedule-card";


                card.innerHTML = `

                    <div class="schedule-date">

                        <strong>
                            ${day}
                        </strong>

                        <span>
                            ${month}
                        </span>

                    </div>


                    <div class="schedule-info">

                        <span class="badge">
                            ${escapeHTML(
                                training.type
                            )}
                        </span>

                        <h3>
                            ${escapeHTML(
                                training.title
                            )}
                        </h3>

                        <p>
                            📅
                            ${formatDate(
                                training.date
                            )}
                        </p>

                        <p>
                            ⏰
                            ${escapeHTML(
                                training.time
                            )}
                        </p>

                        <p>
                            📍
                            ${escapeHTML(
                                training.location
                            )}
                        </p>

                    </div>


                    <button
                        type="button"
                        class="action-btn manage-training"
                        data-index="${index}">
                        Manage
                    </button>

                `;


                container.appendChild(
                    card
                );

            }
        );
}


/* =========================================================
   ADD TRAINING MODAL
   ========================================================= */

function openTrainingModal(
    editIndex = null
) {

    const old =
        document.getElementById(
            "trainingDynamicModal"
        );

    if (old) {
        old.remove();
    }


    const existing =
        editIndex !== null
            ? trainings[editIndex]
            : null;


    const training =
        existing || {

            date:
                new Date()
                    .toISOString()
                    .split("T")[0],

            type: "FORMATION",

            title: "",

            time:
                "07:00 AM - 12:00 PM",

            location:
                "Training Ground"

        };


    const modal =
        document.createElement(
            "div"
        );


    modal.id =
        "trainingDynamicModal";


    modal.className =
        "modal-overlay show";


    modal.style.display =
        "flex";


    modal.innerHTML = `

        <div class="modal">

            <div class="modal-header">

                <h2>
                    ${
                        editIndex === null
                            ? "Add Training"
                            : "Edit Training"
                    }
                </h2>

                <button
                    type="button"
                    id="closeTrainingModal">
                    ×
                </button>

            </div>


            <form id="trainingForm">

                <div class="form-grid">

                    <div class="form-group">

                        <label>Date</label>

                        <input
                            type="date"
                            id="trainingDate"
                            value="${escapeHTML(
                                training.date
                            )}"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Training Type
                        </label>

                        <select
                            id="trainingType">

                            <option ${
                                training.type ===
                                "FORMATION"
                                    ? "selected"
                                    : ""
                            }>
                                FORMATION
                            </option>

                            <option ${
                                training.type ===
                                "DRILL"
                                    ? "selected"
                                    : ""
                            }>
                                DRILL
                            </option>

                            <option ${
                                training.type ===
                                "LEADERSHIP"
                                    ? "selected"
                                    : ""
                            }>
                                LEADERSHIP
                            </option>

                            <option ${
                                training.type ===
                                "OTHER"
                                    ? "selected"
                                    : ""
                            }>
                                OTHER
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Training Title
                        </label>

                        <input
                            type="text"
                            id="trainingTitle"
                            value="${escapeHTML(
                                training.title
                            )}"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Time
                        </label>

                        <input
                            type="text"
                            id="trainingTime"
                            value="${escapeHTML(
                                training.time
                            )}"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Location
                        </label>

                        <input
                            type="text"
                            id="trainingLocation"
                            value="${escapeHTML(
                                training.location
                            )}"
                            required
                        >

                    </div>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="secondary-btn"
                        id="cancelTraining">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="primary-btn">

                        ${
                            editIndex === null
                                ? "Add Training"
                                : "Save Changes"
                        }

                    </button>

                </div>

            </form>

        </div>
    `;


    document.body.appendChild(
        modal
    );


    document.body.style.overflow =
        "hidden";


    document.getElementById(
        "closeTrainingModal"
    ).onclick =
        () =>
            closeTrainingModal();


    document.getElementById(
        "cancelTraining"
    ).onclick =
        () =>
            closeTrainingModal();


    document.getElementById(
        "trainingForm"
    ).addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const newTraining = {

                date:
                    document.getElementById(
                        "trainingDate"
                    ).value,

                type:
                    document.getElementById(
                        "trainingType"
                    ).value,

                title:
                    document.getElementById(
                        "trainingTitle"
                    ).value.trim(),

                time:
                    document.getElementById(
                        "trainingTime"
                    ).value.trim(),

                location:
                    document.getElementById(
                        "trainingLocation"
                    ).value.trim()

            };


            if (editIndex === null) {

                trainings.push(
                    newTraining
                );

                showToast(
                    "Training successfully added.",
                    "success"
                );

            } else {

                trainings[editIndex] =
                    newTraining;

                showToast(
                    "Training successfully updated.",
                    "success"
                );

            }


            storage.set(
                "rotc_trainings",
                trainings
            );


            renderTrainings();


            addActivity(
                editIndex === null
                    ? "Training added"
                    : "Training updated",
                newTraining.title,
                "orange"
            );


            closeTrainingModal();

        }
    );
}


function closeTrainingModal() {

    const modal =
        document.getElementById(
            "trainingDynamicModal"
        );

    if (modal) {
        modal.remove();
    }

    document.body.style.overflow = "";
}


/* =========================================================
   TRAINING MANAGER
   ========================================================= */

function openTrainingManager(index) {

    const training =
        trainings[index];

    if (!training) {
        return;
    }


    const choice =
        confirm(
            `Manage "${training.title}"\n\nOK = Edit\nCancel = Delete`
        );


    if (choice) {

        openTrainingModal(index);

    } else {

        const deleteConfirm =
            confirm(
                `Delete "${training.title}"?`
            );


        if (!deleteConfirm) {
            return;
        }


        trainings.splice(
            index,
            1
        );


        storage.set(
            "rotc_trainings",
            trainings
        );


        renderTrainings();


        showToast(
            "Training deleted.",
            "success"
        );

    }
}


/* =========================================================
   GRADES
   ========================================================= */

let grades = storage.get("rotc_grades", []);


function calculateAverage(grade) {

    return (
        (
            Number(grade.military) +
            Number(grade.leadership) +
            Number(grade.performance)
        ) / 3
    ).toFixed(1);
}


function renderGrades(list = grades) {

    const tbody =
        document.querySelector(
            "#grades table tbody"
        );

    if (!tbody) {
        return;
    }


    tbody.innerHTML = "";


    if (list.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="6"
                    style="text-align:center;padding:30px;">
                    No grade records found.
                </td>
            </tr>
        `;

        return;
    }


    list.forEach(
        grade => {

            const row =
                document.createElement("tr");


            row.innerHTML = `

                <td>
                    ${escapeHTML(grade.id)}
                </td>

                <td>
                    ${escapeHTML(grade.name)}
                </td>

                <td>
                    ${grade.military}
                </td>

                <td>
                    ${grade.leadership}
                </td>

                <td>
                    ${grade.performance}
                </td>

                <td>
                    <strong>
                        ${calculateAverage(
                            grade
                        )}%
                    </strong>
                </td>

            `;


            tbody.appendChild(
                row
            );

        }
    );


    updateDashboardAverage();
}


/* =========================================================
   ADD GRADE
   ========================================================= */

function openGradeModal() {

    const modal =
        document.createElement(
            "div"
        );


    modal.className =
        "modal-overlay show";


    modal.style.display =
        "flex";


    modal.id =
        "gradeDynamicModal";


    const options =
        cadets.map(
            cadet => `
                <option value="${escapeHTML(cadet.id)}">
                    ${escapeHTML(cadet.name)}
                    (${escapeHTML(cadet.id)})
                </option>
            `
        ).join("");


    modal.innerHTML = `

        <div class="modal">

            <div class="modal-header">

                <h2>
                    Add Grade
                </h2>

                <button
                    type="button"
                    id="closeGrade">
                    ×
                </button>

            </div>


            <form id="gradeForm">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Cadet
                        </label>

                        <select
                            id="gradeCadet"
                            required>

                            <option value="">
                                Select Cadet
                            </option>

                            ${options}

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Military Science
                        </label>

                        <input
                            type="number"
                            id="gradeMilitary"
                            min="0"
                            max="100"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Leadership
                        </label>

                        <input
                            type="number"
                            id="gradeLeadership"
                            min="0"
                            max="100"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Performance
                        </label>

                        <input
                            type="number"
                            id="gradePerformance"
                            min="0"
                            max="100"
                            required
                        >

                    </div>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="secondary-btn"
                        id="cancelGrade">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="primary-btn">
                        Save Grade
                    </button>

                </div>

            </form>

        </div>

    `;


    document.body.appendChild(
        modal
    );


    document.body.style.overflow =
        "hidden";


    document.getElementById(
        "closeGrade"
    ).onclick =
        closeGradeModal;


    document.getElementById(
        "cancelGrade"
    ).onclick =
        closeGradeModal;


    document.getElementById(
        "gradeForm"
    ).addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const id =
                document.getElementById(
                    "gradeCadet"
                ).value;


            const cadet =
                cadets.find(
                    item =>
                        item.id === id
                );


            if (!cadet) {

                showToast(
                    "Please select a cadet.",
                    "error"
                );

                return;
            }


            const grade = {

                id,

                name:
                    cadet.name,

                military:
                    Number(
                        document.getElementById(
                            "gradeMilitary"
                        ).value
                    ),

                leadership:
                    Number(
                        document.getElementById(
                            "gradeLeadership"
                        ).value
                    ),

                performance:
                    Number(
                        document.getElementById(
                            "gradePerformance"
                        ).value
                    )

            };


            const existing =
                grades.findIndex(
                    item =>
                        item.id === id
                );


            if (existing >= 0) {

                grades[existing] =
                    grade;

            } else {

                grades.push(
                    grade
                );

            }


            storage.set(
                "rotc_grades",
                grades
            );


            renderGrades();


            updateDashboardAverage();


            addActivity(
                "Grades updated",
                `${cadet.name}'s grades were updated.`,
                "purple"
            );


            closeGradeModal();


            showToast(
                "Grade successfully saved.",
                "success"
            );

        }
    );
}


function closeGradeModal() {

    const modal =
        document.getElementById(
            "gradeDynamicModal"
        );

    if (modal) {
        modal.remove();
    }

    document.body.style.overflow = "";
}


/* =========================================================
   GRADE SEARCH
   ========================================================= */

const gradeSearch =
    document.querySelector(
        "#grades .toolbar input"
    );


if (gradeSearch) {

    gradeSearch.addEventListener(
        "input",
        function () {

            const query =
                this.value
                    .toLowerCase()
                    .trim();


            const filtered =
                grades.filter(
                    grade =>
                        grade.id
                            .toLowerCase()
                            .includes(query)
                        ||
                        grade.name
                            .toLowerCase()
                            .includes(query)
                );


            renderGrades(
                filtered
            );

        }
    );
}


/* =========================================================
   USERS
   ========================================================= */

let users =
    storage.get(
        "rotc_users",
        [
            {
                name: "Administrator",
                email: "admin@mdci.edu.ph",
                role: "Admin",
                status: "Active"
            },

            {
                name: "ROTC Officer",
                email: "officer@mdci.edu.ph",
                role: "Officer",
                status: "Active"
            },

            {
                name: "Cadet",
                email: "cadet@mdci.edu.ph",
                role: "Cadet",
                status: "Active"
            }
        ]
    );


function renderUsers() {

    const tbody =
        document.querySelector(
            "#users table tbody"
        );

    if (!tbody) {
        return;
    }


    tbody.innerHTML = "";


    users.forEach(
        (user, index) => {

            const row =
                document.createElement(
                    "tr"
                );


            row.innerHTML = `

                <td>
                    ${escapeHTML(
                        user.name
                    )}
                </td>

                <td>
                    ${escapeHTML(
                        user.email
                    )}
                </td>

                <td>
                    <span class="role ${
                        user.role.toLowerCase()
                    }">
                        ${escapeHTML(
                            user.role
                        )}
                    </span>
                </td>

                <td>

                    <button
                        type="button"
                        class="status ${
                            user.status ===
                            "Active"
                                ? "active"
                                : "warning"
                        } user-status"
                        data-index="${index}">

                        ${escapeHTML(
                            user.status
                        )}

                    </button>

                </td>

            `;


            tbody.appendChild(
                row
            );

        }
    );
}


/* =========================================================
   ADD USER
   ========================================================= */

function openUserModal() {

    const modal =
        document.createElement(
            "div"
        );


    modal.className =
        "modal-overlay show";


    modal.style.display =
        "flex";


    modal.id =
        "userDynamicModal";


    modal.innerHTML = `

        <div class="modal">

            <div class="modal-header">

                <h2>
                    Add User
                </h2>

                <button
                    type="button"
                    id="closeUser">
                    ×
                </button>

            </div>


            <form id="userForm">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="userName"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            id="userEmail"
                            placeholder="name@mdci.edu.ph"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Role
                        </label>

                        <select
                            id="userRole">

                            <option>
                                Officer
                            </option>

                            <option>
                                Cadet
                            </option>

                            <option>
                                Admin
                            </option>

                        </select>

                    </div>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="secondary-btn"
                        id="cancelUser">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="primary-btn">
                        Add User
                    </button>

                </div>

            </form>

        </div>
    `;


    document.body.appendChild(
        modal
    );


    document.body.style.overflow =
        "hidden";


    document.getElementById(
        "closeUser"
    ).onclick =
        closeUserModal;


    document.getElementById(
        "cancelUser"
    ).onclick =
        closeUserModal;


    document.getElementById(
        "userForm"
    ).addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const name =
                document.getElementById(
                    "userName"
                ).value.trim();


            const email =
                document.getElementById(
                    "userEmail"
                ).value.trim();


            const role =
                document.getElementById(
                    "userRole"
                ).value;


            if (
                !email
                    .toLowerCase()
                    .endsWith(
                        "@mdci.edu.ph"
                    )
            ) {

                showToast(
                    "Please use an @mdci.edu.ph email.",
                    "error"
                );

                return;
            }


            users.push({

                name,

                email,

                role,

                status: "Active"

            });


            storage.set(
                "rotc_users",
                users
            );


            renderUsers();

            closeUserModal();


            showToast(
                "User successfully added.",
                "success"
            );

        }
    );
}


function closeUserModal() {

    const modal =
        document.getElementById(
            "userDynamicModal"
        );

    if (modal) {
        modal.remove();
    }

    document.body.style.overflow = "";
}


/* =========================================================
   SETTINGS
   ========================================================= */

function saveSettings() {

    const settingsPage =
        document.getElementById(
            "settings"
        );

    if (!settingsPage) {
        return;
    }


    const inputs =
        settingsPage.querySelectorAll(
            "input"
        );


    if (inputs.length < 3) {
        return;
    }


    const data = {

        systemName:
            inputs[0].value.trim(),

        institution:
            inputs[1].value.trim(),

        adminEmail:
            inputs[2].value.trim()

    };


    if (
        !data.systemName ||
        !data.institution ||
        !data.adminEmail
    ) {

        showToast(
            "Please complete all settings.",
            "error"
        );

        return;
    }


    storage.set(
        "rotc_settings",
        data
    );


    showToast(
        "System settings saved successfully.",
        "success"
    );
}


function loadSettings() {

    const data =
        storage.get(
            "rotc_settings",
            null
        );


    if (!data) {
        return;
    }


    const settingsPage =
        document.getElementById(
            "settings"
        );


    if (!settingsPage) {
        return;
    }


    const inputs =
        settingsPage.querySelectorAll(
            "input"
        );


    if (inputs.length < 3) {
        return;
    }


    inputs[0].value =
        data.systemName;


    inputs[1].value =
        data.institution;


    inputs[2].value =
        data.adminEmail;
}


/* =========================================================
   DASHBOARD STATISTICS
   ========================================================= */

function updateDashboardStats() {

    const total =
        document.getElementById(
            "totalCadets"
        );


    if (total) {

        total.textContent =
            cadets.length;

    }


    const present =
        document.getElementById(
            "presentCount"
        );


    if (present) {

        present.textContent =
            attendanceRecords.filter(
                record =>
                    record.status ===
                    "Present"
            ).length;

    }


    const upcoming =
        document.querySelector(
            "#dashboard .stat-card:nth-child(3) h2"
        );


    if (upcoming) {

        const today =
            new Date();

        today.setHours(
            0, 0, 0, 0
        );


        const count =
            trainings.filter(
                training =>
                    new Date(
                        `${training.date}T00:00:00`
                    ) >= today
            ).length;


        upcoming.textContent =
            count;

    }
}


/* =========================================================
   DASHBOARD AVERAGE
   ========================================================= */

function updateDashboardAverage() {

    const element =
        document.querySelector(
            "#dashboard .stat-card:nth-child(4) h2"
        );


    if (!element) {
        return;
    }


    if (!grades.length) {

        element.textContent =
            "0%";

        return;
    }


    const total =
        grades.reduce(
            (
                sum,
                grade
            ) =>
                sum +
                Number(
                    calculateAverage(
                        grade
                    )
                ),
            0
        );


    element.textContent =
        `${(
            total / grades.length
        ).toFixed(1)}%`;
}


/* =========================================================
   UPDATE ATTENDANCE CADET OPTIONS
   ========================================================= */

function updateAttendanceCadetOptions() {

    const select =
        document.getElementById(
            "attendanceCadet"
        );

    if (!select) {
        return;
    }


    const current =
        select.value;


    select.innerHTML = `

        <option value="">
            Select Cadet
        </option>

        ${
            cadets.map(
                cadet => `
                    <option value="${escapeHTML(
                        cadet.id
                    )}">
                        ${escapeHTML(
                            cadet.name
                        )}
                        (${escapeHTML(
                            cadet.id
                        )})
                    </option>
                `
            ).join("")
        }

    `;


    select.value =
        current;
}


function updateGradeCadetOptions() {
    /* Options are generated when grade modal opens. */
}


/* =========================================================
   RECENT ACTIVITIES
   ========================================================= */

let activities =
    storage.get(
        "rotc_activities",
        []
    );


function addActivity(
    title,
    description,
    icon = "blue"
) {

    activities.unshift({

        title,

        description,

        icon,

        time:
            new Date()
                .toISOString()

    });


    activities =
        activities.slice(
            0,
            10
        );


    storage.set(
        "rotc_activities",
        activities
    );


    renderActivities();
}


function renderActivities() {

    const container =
        document.querySelector(
            "#dashboard .activity-list"
        );


    if (!container) {
        return;
    }


    if (!activities.length) {
        return;
    }


    container.innerHTML =
        activities
            .slice(0, 5)
            .map(
                activity => `

                    <div class="activity">

                        <div class="activity-icon ${
                            escapeHTML(
                                activity.icon
                            )
                        }">
                            ${
                                activity.icon === "green"
                                    ? "✓"
                                    : activity.icon === "purple"
                                        ? "★"
                                        : "♙"
                            }
                        </div>

                        <div>

                            <strong>
                                ${escapeHTML(
                                    activity.title
                                )}
                            </strong>

                            <p>
                                ${escapeHTML(
                                    activity.description
                                )}
                            </p>

                        </div>

                        <span>
                            ${timeAgo(
                                activity.time
                            )}
                        </span>

                    </div>

                `
            )
            .join("");
}


function timeAgo(dateString) {

    const date =
        new Date(dateString);

    const seconds =
        Math.floor(
            (
                Date.now() -
                date.getTime()
            ) / 1000
        );


    if (seconds < 60) {
        return "Just now";
    }


    const minutes =
        Math.floor(
            seconds / 60
        );


    if (minutes < 60) {
        return `${minutes} min ago`;
    }


    const hours =
        Math.floor(
            minutes / 60
        );


    if (hours < 24) {
        return `${hours} hour${hours > 1 ? "s" : ""} ago`;
    }


    const days =
        Math.floor(
            hours / 24
        );


    return `${days} day${days > 1 ? "s" : ""} ago`;
}


/* =========================================================
   REPORT GENERATION
   ========================================================= */

function generateReport(type) {

    let title = "";

    let content = "";


    if (type === "cadet") {

        title =
            "Cadet Report";


        content = `

            <table>

                <thead>

                    <tr>
                        <th>Cadet ID</th>
                        <th>Name</th>
                        <th>Year Level</th>
                        <th>Section</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    ${
                        cadets.map(
                            cadet => `

                                <tr>

                                    <td>
                                        ${escapeHTML(
                                            cadet.id
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            cadet.name
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            cadet.year
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            cadet.section
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            cadet.status
                                        )}
                                    </td>

                                </tr>
                            `
                        ).join("")
                    }

                </tbody>

            </table>
        `;

    }


    else if (type === "attendance") {

        title =
            "Attendance Report";


        content = `

            <table>

                <thead>

                    <tr>
                        <th>Cadet ID</th>
                        <th>Name</th>
                        <th>Training</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    ${
                        attendanceRecords.map(
                            record => `

                                <tr>

                                    <td>
                                        ${escapeHTML(
                                            record.id
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            record.name
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            record.training
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            record.date || ""
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            record.time
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            record.status
                                        )}
                                    </td>

                                </tr>

                            `
                        ).join("")
                    }

                </tbody>

            </table>

        `;

    }


    else if (type === "grades") {

        title =
            "Grades Report";


        content = `

            <table>

                <thead>

                    <tr>
                        <th>Cadet ID</th>
                        <th>Name</th>
                        <th>Military Science</th>
                        <th>Leadership</th>
                        <th>Performance</th>
                        <th>Average</th>
                    </tr>

                </thead>

                <tbody>

                    ${
                        grades.map(
                            grade => `

                                <tr>

                                    <td>
                                        ${escapeHTML(
                                            grade.id
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            grade.name
                                        )}
                                    </td>

                                    <td>
                                        ${grade.military}
                                    </td>

                                    <td>
                                        ${grade.leadership}
                                    </td>

                                    <td>
                                        ${grade.performance}
                                    </td>

                                    <td>
                                        ${calculateAverage(
                                            grade
                                        )}%
                                    </td>

                                </tr>

                            `
                        ).join("")
                    }

                </tbody>

            </table>

        `;

    }


    else {

        title =
            "Training Report";


        content = `

            <table>

                <thead>

                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Training</th>
                        <th>Time</th>
                        <th>Location</th>
                    </tr>

                </thead>

                <tbody>

                    ${
                        trainings.map(
                            training => `

                                <tr>

                                    <td>
                                        ${formatDate(
                                            training.date
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            training.type
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            training.title
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            training.time
                                        )}
                                    </td>

                                    <td>
                                        ${escapeHTML(
                                            training.location
                                        )}
                                    </td>

                                </tr>

                            `
                        ).join("")
                    }

                </tbody>

            </table>

        `;

    }


    const reportWindow =
        window.open(
            "",
            "_blank",
            "width=1100,height=750"
        );


    if (!reportWindow) {

        showToast(
            "Please allow pop-ups to generate reports.",
            "error"
        );

        return;
    }


    reportWindow.document.write(`

        <!DOCTYPE html>

        <html>

        <head>

            <title>
                ${title}
            </title>

            <style>

                * {
                    box-sizing: border-box;
                }

                body {
                    font-family:
                        Arial,
                        sans-serif;

                    padding: 35px;

                    color: #222;

                    background: white;
                }

                .header {
                    text-align: center;

                    border-bottom:
                        2px solid #222;

                    padding-bottom: 20px;

                    margin-bottom: 25px;
                }

                .logos {
                    display: flex;

                    justify-content: center;

                    align-items: center;

                    gap: 25px;

                    margin-bottom: 15px;
                }

                .logos img {
                    width: 85px;

                    height: 85px;

                    object-fit: contain;
                }

                .school-name {
                    font-size: 21px;

                    font-weight: bold;

                    margin: 5px 0;
                }

                .unit-name {
                    font-size: 15px;

                    margin: 5px 0;
                }

                h2 {
                    margin-top: 25px;

                    text-align: center;
                }

                .generated {
                    text-align: center;

                    color: #666;

                    font-size: 13px;
                }

                table {
                    width: 100%;

                    border-collapse:
                        collapse;

                    margin-top: 25px;
                }

                th,
                td {
                    border:
                        1px solid #bbb;

                    padding: 9px;

                    text-align: left;

                    font-size: 13px;
                }

                th {
                    font-weight: bold;

                    background: #f2f2f2;
                }

                .print-btn {
                    display: block;

                    margin: 25px auto;

                    padding: 11px 22px;

                    border: none;

                    border-radius: 6px;

                    cursor: pointer;

                    font-weight: bold;
                }

                @media print {

                    body {
                        padding: 15px;
                    }

                    .print-btn {
                        display: none;
                    }

                }

            </style>

        </head>

        <body>

            <div class="header">

                <div class="logos">

                    <img
                        src="MDCLOGO.png"
                        alt="Metro Dumaguete College Logo"
                        onerror="
                            this.style.display='none';
                        "
                    >

                    <img
                        src="ROTCLOGO.png"
                        alt="MDC ROTC Logo"
                        onerror="
                            this.style.display='none';
                        "
                    >

                </div>

                <div class="school-name">
                    METRO DUMAGUETE COLLEGE
                </div>

                <div class="unit-name">
                    ARMY ROTC UNIT
                </div>

                <div class="unit-name">
                    ROTC MANAGEMENT INFORMATION SYSTEM
                </div>

            </div>


            <h2>
                ${title}
            </h2>


            <p class="generated">
                Generated:
                ${new Date().toLocaleString()}
            </p>


            ${content}


            <button
                class="print-btn"
                onclick="window.print()">

                Print Report

            </button>

        </body>

        </html>

    `);


    reportWindow.document.close();


    showToast(
        `${title} generated successfully.`,
        "success"
    );
}


/* =========================================================
   NAVIGATION
   ========================================================= */

const pageInfo = {

    dashboard: [
        "Dashboard",
        "Overview of ROTC activities and records"
    ],

    cadets: [
        "Cadet Information",
        "Manage and maintain cadet records."
    ],

    attendance: [
        "Attendance Management",
        "Record and monitor cadet attendance."
    ],

    training: [
        "Training Schedule",
        "Organize and maintain ROTC training activities."
    ],

    grades: [
        "Grades & Performance",
        "Manage cadet grades and performance records."
    ],

    reports: [
        "Reports",
        "Generate administrative ROTC reports."
    ],

    users: [
        "User Management",
        "Manage system users and access permissions."
    ],

    settings: [
        "System Settings",
        "Configure ROTC MIS settings."
    ]

};


function showPage(pageID) {

    const pages =
        document.querySelectorAll(
            ".page"
        );


    pages.forEach(
        page =>
            page.classList.remove(
                "active-page"
            )
    );


    const target =
        document.getElementById(
            pageID
        );


    if (target) {

        target.classList.add(
            "active-page"
        );

    }


    document.querySelectorAll(
        ".nav-link"
    ).forEach(
        link => {

            link.classList.toggle(
                "active",
                link.dataset.page ===
                    pageID
            );

        }
    );


    const title =
        document.getElementById(
            "pageTitle"
        );


    const description =
        document.getElementById(
            "pageDescription"
        );


    if (
        title &&
        description &&
        pageInfo[pageID]
    ) {

        title.textContent =
            pageInfo[pageID][0];

        description.textContent =
            pageInfo[pageID][1];

    }


    /* Close mobile sidebar */

    const sidebar =
        document.getElementById(
            "sidebar"
        );


    if (sidebar) {

        sidebar.classList.remove(
            "open"
        );

    }
}


/* =========================================================
   NAVIGATION EVENTS
   ========================================================= */

document.querySelectorAll(
    ".nav-link"
).forEach(
    link => {

        link.addEventListener(
            "click",
            function (event) {

                event.preventDefault();

                showPage(
                    this.dataset.page
                );

            }
        );

    }
);


/* =========================================================
   VIEW ALL / PAGE LINKS
   ========================================================= */

document.addEventListener(
    "click",
    function (event) {

        const link =
            event.target.closest(
                "[data-page-link]"
            );


        if (!link) {
            return;
        }


        event.preventDefault();


        showPage(
            link.dataset.pageLink
        );

    }
);


/* =========================================================
   MENU BUTTON
   ========================================================= */

const menuBtn =
    document.getElementById(
        "menuBtn"
    );


const sidebar =
    document.getElementById(
        "sidebar"
    );


if (
    menuBtn &&
    sidebar
) {

    menuBtn.addEventListener(
        "click",
        function () {

            sidebar.classList.toggle(
                "open"
            );

        }
    );

}


/* =========================================================
   BUTTON HANDLER
   ========================================================= */

document.addEventListener(
    "click",
    function (event) {

        const button =
            event.target.closest(
                "button"
            );


        if (!button) {
            return;
        }


        const text =
            button.textContent
                .trim()
                .replace(
                    /\s+/g,
                    " "
                );


        /* Record Attendance */

        if (
            text.includes(
                "Record Attendance"
            )
        ) {

            event.preventDefault();

            openAttendanceModal();

            return;
        }


        /* Add Training */

        if (
            text.includes(
                "Add Training"
            )
        ) {

            event.preventDefault();

            openTrainingModal();

            return;
        }


        /* Add Grade */

        if (
            text.includes(
                "Add Grade"
            )
        ) {

            event.preventDefault();

            openGradeModal();

            return;
        }


        /* Add User */

        if (
            text.includes(
                "Add User"
            )
        ) {

            event.preventDefault();

            openUserModal();

            return;
        }


        /* Manage Training */

        if (
            button.classList.contains(
                "manage-training"
            )
        ) {

            const index =
                Number(
                    button.dataset.index
                );


            openTrainingManager(
                index
            );

            return;
        }


        /* View Cadet */

        if (
            button.classList.contains(
                "view-cadet"
            )
        ) {

            const index =
                Number(
                    button.dataset.index
                );


            const cadet =
                cadets[index];


            if (!cadet) {
                return;
            }


            alert(
                `CADET INFORMATION\n\n` +
                `Cadet ID: ${cadet.id}\n` +
                `Name: ${cadet.name}\n` +
                `Year: ${cadet.year}\n` +
                `Section: ${cadet.section}\n` +
                `Status: ${cadet.status}`
            );


            return;
        }


        /* User Status */

        if (
            button.classList.contains(
                "user-status"
            )
        ) {

            const index =
                Number(
                    button.dataset.index
                );


            if (!users[index]) {
                return;
            }


            users[index].status =
                users[index].status ===
                "Active"
                    ? "Inactive"
                    : "Active";


            storage.set(
                "rotc_users",
                users
            );


            renderUsers();


            showToast(
                `User status changed to ${users[index].status}.`,
                "success"
            );


            return;
        }


        /* Save Settings */

        if (
            text ===
                "Save Changes"
        ) {

            if (
                button.closest(
                    "#settings"
                )
            ) {

                event.preventDefault();

                saveSettings();

                return;

            }

        }


        /* Reports */

        if (
            text ===
                "Generate"
        ) {

            const card =
                button.closest(
                    ".report-card"
                );


            if (!card) {
                return;
            }


            const heading =
                card.querySelector(
                    "h3"
                );


            if (!heading) {
                return;
            }


            const name =
                heading.textContent
                    .toLowerCase();


            if (
                name.includes(
                    "cadet"
                )
            ) {

                generateReport(
                    "cadet"
                );

            }

            else if (
                name.includes(
                    "attendance"
                )
            ) {

                generateReport(
                    "attendance"
                );

            }

            else if (
                name.includes(
                    "grade"
                )
            ) {

                generateReport(
                    "grades"
                );

            }

            else {

                generateReport(
                    "training"
                );

            }


            return;
        }

    }
);


/* =========================================================
   NOTIFICATIONS
   ========================================================= */

const notificationBtn =
    document.getElementById(
        "notificationBtn"
    );


if (notificationBtn) {

    notificationBtn.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();


            const old =
                document.getElementById(
                    "notificationPanel"
                );


            if (old) {

                old.remove();

                return;

            }


            const panel =
                document.createElement(
                    "div"
                );


            panel.id =
                "notificationPanel";


            panel.style.cssText = `

                position: fixed;

                top: 75px;

                right: 25px;

                width: 320px;

                max-width:
                    calc(100vw - 40px);

                background: white;

                border-radius: 12px;

                box-shadow:
                    0 12px 35px
                    rgba(0,0,0,.16);

                padding: 16px;

                z-index: 99999;

                border:
                    1px solid #e5e7eb;

            `;


            panel.innerHTML = `

                <strong
                    style="
                        display:block;
                        margin-bottom:12px;
                    ">
                    Notifications
                </strong>

                <div
                    style="
                        padding:10px 0;
                        border-bottom:1px solid #eee;
                    ">

                    <strong>
                        Upcoming Training
                    </strong>

                    <small
                        style="
                            display:block;
                            color:#777;
                        ">
                        ${trainings.length}
                        scheduled training
                        activities.
                    </small>

                </div>


                <div
                    style="
                        padding:10px 0;
                        border-bottom:1px solid #eee;
                    ">

                    <strong>
                        Attendance
                    </strong>

                    <small
                        style="
                            display:block;
                            color:#777;
                        ">
                        ${attendanceRecords.length}
                        attendance records.
                    </small>

                </div>


                <div
                    style="
                        padding:10px 0;
                    ">

                    <strong>
                        Cadet Records
                    </strong>

                    <small
                        style="
                            display:block;
                            color:#777;
                        ">
                        ${cadets.length}
                        registered cadets.
                    </small>

                </div>

            `;


            document.body.appendChild(
                panel
            );

        }
    );
}


/* =========================================================
   LOGOUT
   ========================================================= */

const logoutBtn =
    document.getElementById(
        "logoutBtn"
    );


if (logoutBtn) {

    logoutBtn.addEventListener(
        "click",
        function () {

            const confirmed =
                confirm(
                    "Are you sure you want to logout?"
                );


            if (!confirmed) {
                return;
            }


            sessionStorage.removeItem(
                "rotc_logged_in"
            );


            showToast(
                "You have been logged out.",
                "success"
            );

        }
    );
}


/* =========================================================
   MODAL BACKDROP CLOSE
   ========================================================= */

document.addEventListener(
    "click",
    function (event) {

        if (
            event.target.classList.contains(
                "modal-overlay"
            )
        ) {

            const id =
                event.target.id;


            if (id) {

                closeModal(id);

            }

        }

    }
);


/* =========================================================
   INITIALIZATION
   ========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        renderCadets();

        renderAttendance();

        renderTrainings();

        renderGrades();

        renderUsers();

        renderActivities();

        loadSettings();

        updateDashboardStats();

        updateAttendanceStats();

        updateDashboardAverage();

    }
);


/* =========================================================
   REALTIME LOCAL UPDATE
   Refresh displayed information every 2 seconds
   ========================================================= */

setInterval(
    function () {

        /*
         * Re-read localStorage so changes made
         * by another tab are reflected.
         */

        const storedCadets =
            storage.get(
                "rotc_cadets",
                cadets
            );


        const storedAttendance =
            storage.get(
                "rotc_attendance",
                attendanceRecords
            );


        const storedTrainings =
            storage.get(
                "rotc_trainings",
                trainings
            );


        const storedGrades =
            storage.get(
                "rotc_grades",
                grades
            );


        if (
            JSON.stringify(
                storedCadets
            ) !==
            JSON.stringify(
                cadets
            )
        ) {

            cadets =
                storedCadets;

            renderCadets();

        }


        if (
            JSON.stringify(
                storedAttendance
            ) !==
            JSON.stringify(
                attendanceRecords
            )
        ) {

            attendanceRecords =
                storedAttendance;

            renderAttendance();

        }


        if (
            JSON.stringify(
                storedTrainings
            ) !==
            JSON.stringify(
                trainings
            )
        ) {

            trainings =
                storedTrainings;

            renderTrainings();

        }


        if (
            JSON.stringify(
                storedGrades
            ) !==
            JSON.stringify(
                grades
            )
        ) {

            grades =
                storedGrades;

            renderGrades();

        }


        updateDashboardStats();

        updateAttendanceStats();

        updateDashboardAverage();

    },
    2000
);


/* =========================================================
   CROSS-TAB REALTIME UPDATE
   ========================================================= */

window.addEventListener(
    "storage",
    function (event) {

        if (
            event.key ===
            "rotc_cadets"
        ) {

            cadets =
                storage.get(
                    "rotc_cadets",
                    []
                );

            renderCadets();

            updateDashboardStats();

        }


        if (
            event.key ===
            "rotc_attendance"
        ) {

            attendanceRecords =
                storage.get(
                    "rotc_attendance",
                    []
                );

            renderAttendance();

            updateAttendanceStats();

        }


        if (
            event.key ===
            "rotc_trainings"
        ) {

            trainings =
                storage.get(
                    "rotc_trainings",
                    []
                );

            renderTrainings();

            updateDashboardStats();

        }


        if (
            event.key ===
            "rotc_grades"
        ) {

            grades =
                storage.get(
                    "rotc_grades",
                    []
                );

            renderGrades();

            updateDashboardAverage();

        }

    }
);


/* =========================================================
   TOAST ANIMATION
   ========================================================= */

const toastStyle =
    document.createElement(
        "style"
    );


toastStyle.textContent = `

    @keyframes rotcToastIn {

        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }

    }

`;


document.head.appendChild(
    toastStyle
);