let isClockIn = false; // Variable to track clock in/out state
let loginTime = document.getElementById("login_time").value;
console.log(loginTime);
let timer = document.getElementById("timer");
let timerInterval; // To store the timer interval ID

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

// Event listener for the Clock-In/Clock-Out button
document.getElementById("clock_in_btn").addEventListener("click", function () {
    const clockInBtn = this;

    if (!isClockIn) {
    
      const currentDate = new Date();
      const currentTime = currentDate.toISOString().slice(0, 10) + " 00:00:00";
      
      
        loginTime = currentTime;
        document.getElementById("login_time").value = currentTime;

        // Start the timer
        calculateTimeDifference(loginTime);
    } else {
        // Clock Out
        const confirmText = prompt("Please type 'confirm' to clock out:");
        if (confirmText !== "confirm") {
            alert("Clock out cancelled.");
         
               // Stop the timer
        clearInterval(timerInterval);
        timer.innerHTML = "00:00:00";
        return;
        }

     
    }

    // Get location and send via AJAX
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                $.ajax({
                    url: "user_attendance.php",
                    type: "POST",
                    data: {
                        latitude: latitude,
                        longitude: longitude,
                        status: isClockIn ? "logout" : "login"
                    },
                    success: function (response) {
                        let data = JSON.parse(response);
                        if (data.success) {
                            isClockIn = !isClockIn; // Toggle clock in state
                            alert(data.message);

                            // Update button text based on state
                            clockInBtn.innerHTML = isClockIn
                                ? '<img src="assets/img/icons/clock-in.svg" alt="Icon"> Clock-Out'
                                : '<img src="assets/img/icons/clock-in.svg" alt="Icon"> Clock-In';
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error sending location:", error);
                        alert("Error sending location data.");
                    },
                });
            },
            function (error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        alert("Please enable location access to clock in/out.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Location information unavailable.");
                        break;
                    case error.TIMEOUT:
                        alert("Location request timed out.");
                        break;
                    default:
                        alert("An unknown error occurred.");
                        break;
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
   
});

// Initialize timer if loginTime is already set
if (loginTime) {
    calculateTimeDifference(loginTime);
}
