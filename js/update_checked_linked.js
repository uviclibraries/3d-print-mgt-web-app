/*
Listeners for 
    - Set statuses to match in all selected jobs (set-statuses-checkbox)
    - Set selected jobs as children ('set-children-checkbox')
    - Unlink slected jobs as children ('unlink-children-checkbox')
*/
//Get checkbox elements
var setChildrenCheckbox = document.getElementById('set-children-checkbox');
var matchStatusCheckbox = document.getElementById('set-statuses-checkbox');
var unlinkChildrenCheckbox = document.getElementById('unlink-children-checkbox');

//uncheck "unlink children" checkbox if "set children" has been selected
if (setChildrenCheckbox && setChildrenCheckbox.offsetParent !== null) {
    setChildrenCheckbox.addEventListener('change', function() {
        if (this.checked) {
            console.log('Checkbox is checked');
            unlinkChildrenCheckbox.checked = false;
        } 
    });
}

//uncheck "unlink children" checkbox if "update status to match" has been selected
matchStatusCheckbox.addEventListener('change', function() {
    if (this.checked) {
        unlinkChildrenCheckbox.checked = false;
    }
});


//uncheck "update status to match" and "set-children-checkbox" checkboxes 
    //if "unlink children" has been selected
unlinkChildrenCheckbox.addEventListener('change', function() {
    if (this.checked) {
        console.log("checkbox checked");
        if (setChildrenCheckbox && setChildrenCheckbox.offsetParent !== null) {
            setChildrenCheckbox.checked = false;
        }
        matchStatusCheckbox.checked = false;
    }
});
