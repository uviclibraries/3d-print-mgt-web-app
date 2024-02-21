window.onload = function() {
    // Function to open a popup
    function openPopup(popupId) {
        var popup = document.getElementById(popupId);
        if (popup) {
            popup.style.display = "block";
        }
    }

    // Function to close a popup
    function closePopup(popupId) {
        var popup = document.getElementById(popupId);
        if (popup) {
            popup.style.display = "none";
        }
    }

    // Attach event listeners to buttons
    var duplicateButton = document.getElementById("duplicate-button");

    if (duplicateButton) {
      duplicateButton.onclick = function() { openPopup("DuplicateJobPopup"); }
    }

    // Attach event listeners to close buttons
      var closeButtons = document.getElementsByClassName("close");
      for (var i = 0; i < closeButtons.length; i++) {
          closeButtons[i].onclick = function() {
              var popupId = this.getAttribute("data-popup");
              closePopup(popupId);
          }
      }

      // Close popup when clicking outside of it
      window.onclick = function(event) {
          if (event.target.classList.contains("popup")) {
              event.target.style.display = "none";
          }
      }
    }