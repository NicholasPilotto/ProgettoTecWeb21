window.onload = function ()
{
  var divTornaSu = document.getElementById("divTornaSu");

  let hasScrollbar = window.innerWidth > document.documentElement.clientWidth;
  if(hasScrollbar)
  {
    divTornaSu.style.display = "block";
  }
  else
  {
    divTornaSu.style.display = "none";
  }
}

function topFunction()
{
  window.scrollTo({ top: 0, behavior: 'smooth' });
}