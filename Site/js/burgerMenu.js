var hamburger;
var navMenu;
var navLink;

function setUp() {
  hamburger = document.getElementsByClassName("hamburger")[0];
  navMenu = document.getElementsByClassName("nav-menu")[0];
  navLinks = document.getElementsByClassName("nav-link");

  hamburger.addEventListener("click", mobileMenu);

  [].forEach.call(navLinks, function (link) {
    link.addEventListener("click", closeMenu);
  })

}

function mobileMenu() {
  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
}

function closeMenu() {
  hamburger.classList.remove("active");
  navMenu.classList.remove("active");
}

window.addEventListener('load', function () {
  setUp();
});