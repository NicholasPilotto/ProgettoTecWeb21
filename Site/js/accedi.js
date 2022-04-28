var dettagli_form = {
  "email": [
    "Indirizzo mail",
    /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
    "Inserire un indirizzo mail corretto"
  ],
  "password": [
    "Password",
    /^[A-Za-zàèùìòé\s]\w{2,20}$/,
    "I valori inseriti non sembrano corrretti"
  ]
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