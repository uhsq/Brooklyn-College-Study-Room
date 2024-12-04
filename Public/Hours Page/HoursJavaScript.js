const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
const times = {
    "Mon": "8am - 8:45pm",
    "Tue": "8am - 8:45pm",
    "Wed": "8am - 8:45pm",
    "Thu": "8am - 8:45pm",
    "Fri": "10am - 4:45pm",
    "Sat": "12pm - 4:45pm",
    "Sun": "12pm - 4:45pm"
};

const calendar = document.getElementById("calendar");

let currentDate = new Date();

// Ensure the current date starts at the beginning of the week (Monday)
function getStartOfWeek(date) {
    const day = date.getDay();
    const diff = date.getDate() - (day === 0 ? 6 : day - 1); // Adjust when day is Sunday (0)
    return new Date(date.setDate(diff));
}

// Format the date as MM/DD
function formatDate(date) {
    const month = date.getMonth() + 1; // Months are 0-indexed
    const day = date.getDate();
    return `${month}/${day}`;
}

// Function to render the week
function renderWeek(startDate) {
    calendar.innerHTML = ""; // Clear previous week
    let date = new Date(startDate); // Start from the provided start date

    for (let i = 0; i < 7; i++) {
        const dayDiv = document.createElement('div');
        dayDiv.classList.add('day');
        
        // Add day of the week, date, and time
        const dayHTML = `
            <div>${daysOfWeek[i]}</div>
            <div class="date">${formatDate(date)}</div>
            <div class="time">${times[daysOfWeek[i]]}</div>
        `;

        dayDiv.innerHTML = dayHTML;

        // If today's date, highlight it
        const today = new Date();
        if (today.toDateString() === date.toDateString()) {
            dayDiv.classList.add("today");
        }

        calendar.appendChild(dayDiv); // Add to calendar
        date.setDate(date.getDate() + 1); // Move to next day
    }
}

// Load the current week on page load
let startOfWeek = getStartOfWeek(new Date());
renderWeek(startOfWeek);

// Navigate to previous week
document.getElementById("prevWeekBtn").addEventListener("click", () => {
    startOfWeek.setDate(startOfWeek.getDate() - 7);
    renderWeek(startOfWeek);
});

// Navigate to next week
document.getElementById("nextWeekBtn").addEventListener("click", () => {
    startOfWeek.setDate(startOfWeek.getDate() + 7);
    renderWeek(startOfWeek);
});
