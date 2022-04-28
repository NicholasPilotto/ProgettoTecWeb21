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

window.onload = function () { insertButton() };
window.onresize = function () { insertButton() };

function topFunction() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}