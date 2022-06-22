var dettagli_form = {
  "nomecognome": [
    "Nome e Cognome",
    /^[A-Za-zòàùçèéì' ]+$/,
    "Inserire un nominativo corretto"
  ],
  "indirizzo": [
    "Indirizzo",
    /^(?!\s*$).+$/,
    "Devi inserire un indirizzo, controlla di averne salvato uno nel tuo account"
  ],
  "numCarta": [
    "Numero Carta",
    /^[0-9]{16,16}$/,
    "Inserire un numero di carta valido di 16 cifre, senza spazi"
  ],
  "csv": [
    "CSV",
    /^[0-9]{3,3}$/,
    "Inserire un csv di 3 cifre"
  ]
};

var chooseFile;
var imgPreview;

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

function confirmPass() {
  const password = document.querySelector('input[name=vecchiaPassword]');
  const confirm = document.querySelector('input[name=nuovaPassword]');

  if (confirm.value === password.value) {
    return true
  }
  return false;
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