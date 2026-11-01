// IBATP — menu mobile, message d'erreur du formulaire, année du pied de page
(function () {
  var bouton = document.querySelector('.menu-bouton');
  var nav = document.getElementById('nav-principale');
  if (bouton && nav) {
    bouton.addEventListener('click', function () {
      var ouvert = nav.classList.toggle('ouvert');
      bouton.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
      bouton.setAttribute('aria-label', ouvert ? 'Fermer le menu' : 'Ouvrir le menu');
    });
    nav.querySelectorAll('a').forEach(function (lien) {
      lien.addEventListener('click', function () {
        nav.classList.remove('ouvert');
        bouton.setAttribute('aria-expanded', 'false');
        bouton.setAttribute('aria-label', 'Ouvrir le menu');
      });
    });
  }

  if (/[?&]envoi=erreur/.test(window.location.search)) {
    var erreur = document.getElementById('erreur-form');
    if (erreur) {
      erreur.hidden = false;
      var contact = document.getElementById('contact');
      if (contact) contact.scrollIntoView();
    }
  }

  document.querySelectorAll('.annee').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();
