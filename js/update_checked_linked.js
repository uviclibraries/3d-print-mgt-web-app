// Get the checkbox element
var checkbox = document.getElementById('set-children-checkbox');

// Add an event listener for the 'change' event
checkbox.addEventListener('change', function() {
    if (this.checked) {
        console.log('Checkbox is checked');
        // Perform action when checkbox is checked
    } else {
        console.log('Checkbox is unchecked');
        // Perform action when checkbox is unchecked
    }
});
