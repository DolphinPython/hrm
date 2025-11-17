// Variable to track clock in/out state
let isClockIn = false;
let loginTime = document.getElementById("login_time").value;

let timer = document.getElementById("timer");
let timerInterval; // To store the timer interval ID
let dbstatus = document.getElementById("status").value;
console.log(dbstatus);
// Function to reset the timer to 00:00:00
function resetTimer() {
    clearInterval(timerInterval);
    timer.innerHTML = "00:00:00";
}

// Function to calculate real-time difference
function calculateTimeDifference(startTime) {
    const loginDate = new Date(startTime.replace(" ", "T")); // Convert to valid Date format

    // Clear any existing interval
    clearInterval(timerInterval);

    timerInterval = setInterval(() => {
        const currentDate = new Date(); // Get current time
        const differenceMs = currentDate - loginDate; // Milliseconds difference

        if (isNaN(differenceMs)) {
            console.error("Invalid login time format.");
            clearInterval(timerInterval);
            return;
        }

        // Calculate hours, minutes, and seconds
        const hours = String(Math.floor(differenceMs / (1000 * 60 * 60))).padStart(2, '0');
        const minutes = String(Math.floor((differenceMs % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
        const seconds = String(Math.floor((differenceMs % (1000 * 60)) / 1000)).padStart(2, '0');

        timer.innerHTML = `${hours}:${minutes}:${seconds}`;
    }, 1000); // Update every second
}

// Function to start the timer from 00:00:01
function startTimer() {
    let secondsElapsed = 0;

    // Clear any existing interval
    clearInterval(timerInterval);

    timerInterval = setInterval(() => {
        secondsElapsed++;

        // Calculate hours, minutes, and seconds
        const hours = String(Math.floor(secondsElapsed / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((secondsElapsed % 3600) / 60)).padStart(2, '0');
        const seconds = String(secondsElapsed % 60).padStart(2, '0');

        timer.innerHTML = `${hours}:${minutes}:${seconds}`;
    }, 1000); // Update every second
}

// Event listener for the Clock-In/Clock-Out button
document.getElementById("clock_in_btn").addEventListener("click", function () {
    const clockInBtn = this;
    const status = clockInBtn.getAttribute("data-status");

    isClockIn = status === "login" ? true : false;
    if (!isClockIn) {
        // Clock-In
        resetTimer();
        startTimer();
    } else {
        // Clock-Out
        const confirmText = prompt("Please type 'confirm' to clock out:");
        if (confirmText !== "confirm") {
            alert("Clock out cancelled.");
            return;
        }

        // Stop and reset the timer
        resetTimer();
    }

    // Send clock-in/out data to the server
    $.ajax({
        url: "newuser_attendance.php",
        type: "POST",
        data: {},
        success: function (response) {
            let data = JSON.parse(response);
            if (data.success) {
                isClockIn = !isClockIn; // Toggle clock in state
                alert(data.message);
                window.location.reload();
                // Update button text based on state
                clockInBtn.innerHTML = isClockIn
                    ? '<img src="assets/img/icons/clock-in.svg" alt="Icon"> Clock-Out'
                    : '<img src="assets/img/icons/clock-in.svg" alt="Icon"> Clock-In';
            } else {
                alert(data.message);
                window.location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error sending clock-in/out data:", error);
            alert("Error sending clock-in/out data");
        },
    });
});

// Initialize timer if loginTime is already set
if (dbstatus==='login') {
    calculateTimeDifference(loginTime);
} else {
    resetTimer();
}
