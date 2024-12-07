document.addEventListener("DOMContentLoaded", function() {
    // Get the current date
    const currentDate = new Date();

    // Array of month names for displaying the full month name
    const monthNames = [
        "January", "February", "March", "April", "May", "June", "July", "August",
        "September", "October", "November", "December"
    ];

    // Array of day names for displaying the full day of the week
    const dayNames = [
        "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ];

    // Get the day, month, and year
    const day = currentDate.getDate();
    const month = monthNames[currentDate.getMonth()];
    const year = currentDate.getFullYear();
    const dayOfWeek = dayNames[currentDate.getDay()]; // Get the day of the week (Sunday, Monday, etc.)

    // Format the date as "DayOfWeek, Month Day, Year" (e.g., "Monday, November 9, 2024")
    const formattedDate = `${dayOfWeek}, ${month} ${day}, ${year}`;

    // Display the formatted date in the h2 element
    document.getElementById("reservation-date").textContent = `Reservation Date: ${formattedDate}`;

    // Get all available cells
    const availableCells = document.querySelectorAll('.available');
    let totalSelectedCount = 0; // To keep track of total selected cells
    let selectedRow = null; // To track the row that has been selected from

    // Add click event listener to each cell
    availableCells.forEach(cell => {
        cell.addEventListener('click', function() {
            // Get the row of the clicked cell
            const row = cell.parentElement;

            // If the row is already selected, prevent selection from another row
            if (selectedRow && selectedRow !== row) {
                alert("You can only select from one room at a time.");
                return;
            }

            const cellsInRow = row.querySelectorAll('.available');
            
            // Check how many cells are currently selected in the row
            const selectedCellsInRow = row.querySelectorAll('.available.selected');
            const selectedCountInRow = selectedCellsInRow.length;

            // If the cell is already selected, unselect it
            if (cell.classList.contains('selected')) {
                cell.classList.remove('selected');
                totalSelectedCount--; // Decrease total selected count

                // If the row is unselected, reset the selectedRow variable
                if (selectedCountInRow - 1 === 0) {
                    selectedRow = null;
                }
            } else {
                // If two cells are already selected globally, do not allow more selections
                if (totalSelectedCount < 2) {
                    // Get the index of the clicked cell
                    const cellIndex = Array.from(cellsInRow).indexOf(cell);

                    // Check for continuous selection from left-to-right
                    const leftSelectionAllowed = cellIndex > 0 && cellsInRow[cellIndex - 1].classList.contains('selected');
                    const rightSelectionAllowed = cellIndex < cellsInRow.length - 1 && cellsInRow[cellIndex + 1].classList.contains('selected');

                    // Check for an empty row (no selected cells)
                    const isEmptyRow = selectedCellsInRow.length === 0;

                    // Allow selection if either the left or right cell is selected, or if it's an empty row
                    if (leftSelectionAllowed || rightSelectionAllowed || isEmptyRow) {
                        cell.classList.add('selected');
                        totalSelectedCount++; // Increase total selected count

                        // Set selectedRow if it's the first selection in the row
                        if (!selectedRow) {
                            selectedRow = row;
                        }
                    }
                }
            }

            // If total selected count is 2, disable further selections
            if (totalSelectedCount >= 2) {
                availableCells.forEach(c => {
                    if (!c.classList.contains('selected')) {
                        c.style.pointerEvents = 'none'; // Disable clicking on unselected cells
                        c.style.opacity = '0.75'; // Make unselected cells look disabled
                    }
                });
            } else {
                // Re-enable clicking on cells if total selected count is less than 2
                availableCells.forEach(c => {
                    c.style.pointerEvents = 'auto'; // Enable clicking on cells
                    c.style.opacity = '1'; // Reset opacity
                });
            }

        });
    });

    const timeslots = [
        "8:00AM", "9:00AM", "10:00AM", "11:00AM", 
        "12:00PM", "1:00PM", "2:00PM", "3:00PM", 
        "4:00PM", "5:00PM", "6:00PM", "7:00PM", "8:00PM"
    ];

    // Submit times function
    function submitTimes() {
        const selectedCells = Array.from(document.querySelectorAll('.selected'));
        const row_parent = selectedCells[0].parentNode;
        let times = [];
        let room = "";

        // Get the room number text from the row
        room = row_parent.querySelector('td:first-child').textContent;


        for (let i = 0; i < 14; i++) {

            if (i == 0) { continue; }
            let cell = row_parent.children.item(i);
            if (!selectedCells.includes(cell)) { continue; }

            // Get the time slot text from the column
            
            times.push(timeslots[i-1]);
        }

        if (times.length > 1) {
            alert("You have selected time slots " + times.join(' and ') + " for " + room + ".");
        } else {
            alert("You have selected time slot " + times[0] + " for " + room + ".");
        }
        // handle the submission

    }
});