function test() {
  document.getElementById("inputNameShow").value = document.getElementById("inputNameHide").textContent;
  document.getElementById("inputNameShow").style.display = "block";
  document.getElementById("inputNameHide").style.display = "none";
  document.getElementById("inputNameButton").style.display = "none";
  document.getElementById("outputNameButton").style.display = "block";
}

function reverse() {
  document.getElementById("inputNameHide").textContent = document.getElementById("inputNameShow").value;
  document.getElementById("inputNameShow").style.display = "none";
  document.getElementById("inputNameHide").style.display = "block";
  document.getElementById("inputNameButton").style.display = "block";
  document.getElementById("outputNameButton").style.display = "none";
}

var dettagli_form = {
  "username": [
    "Username",
    /^[A-Za-z\s]\w{2,10}$/,
    "Inserire un indirizzo username corretto"
  ],
  "email": [
    "Indirizzo mail",
    /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
    "Inserire un indirizzo mail corretto"
  ],
};

function caricamento() {

  let form = document.getElementById("form");

  form.addEventListener("submit", function (event) {
    if (!validazioneForm()) {
      event.preventDefault();
    }
  });

  for (var key in dettagli_form) {
    var input = document.getElementById(key);
    input.onblur = function () {
      validazioneCampo(this);
    };
  }
}

function validazioneCampo(input) {
  var padre = input.parentNode;

  if (padre.children.length == 2) {
    padre.removeChild(padre.children[1]);
  }

  if (input.value.search(dettagli_form[input.id][1]) != 0 || input.value == dettagli_form[input.id][0]) {
    mostraErrore(input);
    return false;
  }
  return true;
}

function mostraErrore(input) {
  var padre = input.parentNode;
  var errore = document.createElement("strong");
  errore.className = "errorSuggestion";
  errore.appendChild(document.createTextNode(dettagli_form[input.id][2]));
  padre.appendChild(errore);
}

function validazioneForm() {
  for (var key in dettagli_form) {
    var input = document.getElementById(key);
    if (!validazioneCampo(input)) {
      return false;
    }
  }
  return true;
}

window.addEventListener('load', function () {
  caricamento();
})