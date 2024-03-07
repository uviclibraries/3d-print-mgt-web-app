//Tab table of active jobs for the current customer-->
function openStatus(evt, status) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(status).style.display = "block";
  evt.currentTarget.className += " active";
}

function removeStatus(tab, div) {
  var tabElement = document.getElementById(tab);
  var divElement = document.getElementById(div);

  if (tabElement) {
    tabElement.style.display = 'none';
  } 
  if (divElement) {
    divElement.style.display = 'none';
  } 
}
