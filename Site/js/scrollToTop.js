function insertButton() {
  var divTornaSu = document.getElementById("divTornaSu");

  if (divTornaSu) {
    divTornaSu.style.display = "none";

    let hasScrollbar = window.innerHeight < document.body.clientHeight;
    if (hasScrollbar) {
      divTornaSu.style.display = "block";
    }
  }
}

function topFunction() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.addEventListener('load', function () {
  insertButton();
});

window.addEventListener('onresize', function () {
  insertButton();
});