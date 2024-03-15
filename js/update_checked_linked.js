/*
Listeners for 
    - Set statuses to match in all selected jobs (set-statuses-checkbox)
    - Set selected jobs as children ('set-children-checkbox')
    - Unlink slected jobs as children ('unlink-children-checkbox')
*/
//Get checkbox elements
document.addEventListener('DOMContentLoaded', function() {

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
    if(matchStatusCheckbox && matchStatusCheckbox.offsetParent !== null){
        matchStatusCheckbox.addEventListener('change', function() {
            if (this.checked) {
                unlinkChildrenCheckbox.checked = false;
            }
        });
    }

    //uncheck "update status to match" and "set-children-checkbox" checkboxes 
        //if "unlink children" has been selected
    if(unlinkChildrenCheckbox && unlinkChildrenCheckbox.offsetParent !== null){
        unlinkChildrenCheckbox.addEventListener('change', function() {
            if (this.checked) {
                console.log("checkbox checked");
                if (setChildrenCheckbox && setChildrenCheckbox.offsetParent !== null) {
                    setChildrenCheckbox.checked = false;
                }
                matchStatusCheckbox.checked = false;
            }
        });
    }


    // document.addEventListener('DOMContentLoaded', function() {
    // Function to check the state of checkboxes descending within the div id=Linked (div of linked jobs) and toggle link-children-div and unlink-children-div 
    function toggleLinkingDivs(checkboxes_class) {
        var link_div = document.getElementById("link-children-div");
        
        // Use querySelectorAll to select all checkboxes within linked jobs divs
        // const good_jobs_checkboxes = document.querySelectorAll('.allow-linking'); //obsolete?
        const bad_jobs_checkboxes= document.querySelectorAll('.prohibit-linking');

        let anyFail = false;
        for (let i = 0; i < bad_jobs_checkboxes.length; i++) {
            if (bad_jobs_checkboxes[i].checked) {
                anyFail = true;
                break;
            }
        }

        if(anyFail){
            document.getElementById("set-children-checkbox").checked = false;
            link_div.style.display = 'none'
        }
        else{
            link_div.style.display = 'block'
        }
    }

    function toggleUnlinkingDivs() {
        var unlink_div = document.getElementById("unlink-children-div");
        if(!document.getElementById("Linked")){
            return;
        }

        // Use querySelectorAll to select all checkboxes within linked jobs divs
        // const good_jobs_checkboxes = document.querySelectorAll('.allow-unlinking'); //obsolete?
        const bad_jobs_checkboxes= document.querySelectorAll('.prohibit-unlinking');

        let anyFail = false;
        for (let i = 0; i < bad_jobs_checkboxes.length; i++) {
            if (bad_jobs_checkboxes[i].checked) {
                anyFail = true;
                break;
            }
        }

        if(anyFail){
            document.getElementById("unlink-children-checkbox").checked = false;
            unlink_div.style.display = 'none'
        }
        else{
            unlink_div.style.display = 'block'
        }
    }

    const can_link_checkboxes = document.querySelectorAll('.allow-linking');
    for (let i = 0; i < can_link_checkboxes.length; i++) {
        can_link_checkboxes[i].addEventListener('click', () => toggleLinkingDivs());
    }
    
    const cant_link_checkboxes = document.querySelectorAll('.prohibit-linking');
    for (let i = 0; i < cant_link_checkboxes.length; i++) {
        cant_link_checkboxes[i].addEventListener('click', () => toggleLinkingDivs());
    }

    const can_unlink_checkboxes = document.querySelectorAll('.allow-unlinking');
    for (let i = 0; i < can_unlink_checkboxes.length; i++) {
        can_unlink_checkboxes[i].addEventListener('click', () => toggleUnlinkingDivs());
    }

    const cant_unlink_checkboxes = document.querySelectorAll('.prohibit-unlinking');
    for (let i = 0; i < cant_unlink_checkboxes.length; i++) {
        cant_unlink_checkboxes[i].addEventListener('click', () => toggleUnlinkingDivs());
    }


});
