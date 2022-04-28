var dettagli_form = {
  "commento": [
    "Comento",
    /^[A-Za-z\s]\w{10,500}$/,
    "Lascia un commento tra i 10 e 500 caratteri"
  ]
};

function caricamento() {

  let form = document.getElementById("formRecensione");

  let text = document.getElementById("commento");
  let rem = document.getElementById("remaining");

  text.addEventListener("keyup", function () {
    rem.innerHTML = 'Caratteri rimanenti: ' + (500 - this.value.length);
  })

  form.addEventListener("submit", function (event) {
    if (!validazioneForm()) {
      event.preventDefault();
    }
  });

  for (var key in dettagli_form) {
    var input = document.getElementById(key);
    campoDefault(input);
    input.onfocus = function () {
      campoPerInput(this);
    };
    input.onblur = function () {
      validazioneCampo(this);
    };
  }
}

function campoDefault(input) {
  if (input.value == "") {
    input.value = dettagli_form[input.id][0];
  }
}

function campoPerInput(input) {
  if (input.value == dettagli_form[input.id][0]) {
    input.value = "";
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

function mostraErrore(input, increment = 0) {
  var padre = input.parentNode;
  var errore = document.createElement("strong");
  errore.className = "errorSuggestion";
  errore.appendChild(document.createTextNode(dettagli_form[input.id][2 + increment]));
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