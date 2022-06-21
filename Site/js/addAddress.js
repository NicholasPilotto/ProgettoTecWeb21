function caricamento() {
  let form = document.getElementById("formAggiungiIndirizzo");

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

let dettagli_form = {
  "citta": [
    "Città",
    /^[A-Za-zàèùìòé\s ]{2,20}$/,
    "Inserire un nome di città di lunghezza compreso tra i 2 e 20 caratteri"
  ],
  "cap": [
    "Cap",
    /^[0-9]{5}$/,
    "Inserire un CAP di 5 caratteri numerici"
  ],
  "via": [
    "Via",
    /^[A-Za-zàèùìòé\s]{2,20}$/,
    "Inserire una via corretto"
  ],
  "num_civico": [
    "Numero civico",
    /^([1-9][0-9]*)(\/[a-zA-Z])*?/,
    "Inserire un numero civico tra 1 e 4 caratteri"
  ]
};

function campoDefault(input) {
  if (input.value == "") {
    input.classname = "";
    input.value = dettagli_form[input.id][0];
  }
}

function campoPerInput(input) {
  if (input.value == dettagli_form[input.id][0]) {
    input.classname = "";
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
    if (input.id == "conferma" && !confirmPass(input)) {
      mostraErrore(input);
      return false;
    }
  }
  return true;
}

window.addEventListener('load', function () {
  caricamento();
});