document.querySelector('.profile-btn').addEventListener('click', function() {
    this.querySelector('.dropdown-content').classList.toggle('show');
});

// Ferme le menu déroulant si l'utilisateur clique en dehors de celui-ci
window.onclick = function(event) {
  if (!event.target.matches('.profile-btn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
