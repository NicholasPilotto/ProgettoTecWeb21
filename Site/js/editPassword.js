var dettagli_form = {
  "vecchiaPassword": [
    "Password",
    /^(?!.*\s)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~!@#$%^&*--+={}\[\]|\\:;<>,.?/_₹]).{10,16}$/,
    "Inserire una password di almeno 8 caratteri, di cui: uno minuscolo, uno maiuscolo, un numero ed un carattere speciale"
  ],
  "nuovaPassword": [
    "Password",
    /^(?!.*\s)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~!@#$%^&*--+={}\[\]|\\:;<>,.?/_₹]).{10,16}$/,
    "Inserire una password di almeno 8 caratteri, di cui: uno minuscolo, uno maiuscolo, un numero ed un carattere speciale"
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