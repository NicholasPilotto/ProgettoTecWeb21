var mybutton;

window.onload = function () {
  mybutton = document.getElementById("scrollTopBtn");
}

window.onscroll = function () { scrollFunction() };

function scrollFunction() {
  if (mybutton) {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }
}

function topFunction() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}