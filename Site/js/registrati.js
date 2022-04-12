let dettagli_form = {
  "nome": [
    "Nome",
    /^[A-Za-zàèùìòé\s]\w{2,20}$/,
    "Inserire un nome di lunghezza compresa tra i 2 e 20 caratteri"
  ],
  "cognome": [
    "Cognome",
    /^[A-Za-zàèùìòé\s]\w{2,20}$/,
    "Inserire un cognome di lunghezza compresa tra i 2 e 20 caratteri"
  ],
  "telefono": [
    "Numero telefonico",
    /^[0-9]{10}$/,
    "Inserire un numero telefonico corretto"
  ],
  "username": [
    "Username",
    /^[A-Za-z\s]\w{2,10}$$/,
    "Inserire un username di lunghezza tra i 2 e 10 caratteri"
  ],
  "email": [
    "Indirizzo mail",
    /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
    "Inserire un indirizzo mail corretto"
  ],
  "password": [
    "Password",
    /^(?!.*\s)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()--+={}\[\]|\\:;"'<>,.?/_₹]).{10,16}$/,
    "Inserire una password di almeno 8 caratteri, di cui: uno minuscolo, uno maiuscolo, un numero ed un carattere speciale"
  ],
  "conferma": [
    "Password",
    /^(?!.*\s)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()--+={}\[\]|\\:;"'<>,.?/_₹]).{10,16}$/,
    "Le due password non coincidono"
  ]
};

function caricamento() {
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

  if (input.id == "conferma" && !confirmPass()) {
    mostraErrore(input);
    return false;
  }

  return true;
}

function confirmPass() {
  const password = document.querySelector('input[name=password]');
  const confirm = document.querySelector('input[name=conferma]');

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
    if (input.id == "conferma" && !confirmPass(input)) {
      mostraErrore(input);
      return false;
    }
  }
  return true;
}