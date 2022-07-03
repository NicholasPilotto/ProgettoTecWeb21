var dettagli_form = {
  "isbn": [
    "ISBN",
    /^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/,
    "Inserire un ISBN di 13 cifre"
  ],
  "titolo": [
    "Titolo",
    /^[a-zA-Z0-9 <>"=/òàùèéÈÉÀÁÒÓÙÚ()'?.,!-]{10,500}$/,
    "Inserire un titolo corretto, non vuoto"
  ],
  "pagine": [
    "Pagine",
    /^[1-9][0-9]*$/,
    "Inserire un numero di pagine valido"
  ],
  "prezzo": [
    "Prezzo",
    /^([1-9][0-9]*)([.]([0-9]+))*$/,
    "Inserire un prezzo corretto, non vuoto"
  ],
  "quantita": [
    "Quantità",
    /^[1-9][0-9]*$/,
    "Inserire una quantità di libri valido"
  ],
  "trama": [
    "Trama",
    /^[a-zA-Z0-9 =«»åòàùèéÈÉÀÁÒÓÙÚìÌÍ()'’´?.,!-<>{}[\]]{10,2500}$/,
    "Inserire una trama valida, di almento 11 caratteri e meno di 1000."
  ],
};

var chooseFile;
var imgPreview;

function caricamento() {

  let form = document.getElementById("form");

  chooseFile = document.getElementById("copertina");
  imgPreview = document.getElementById("img-preview");
  chooseFile.addEventListener("change", function () {
    getImgData();
  });

  if (document.getElementById("pagine").value === "") {
    document.getElementById("pagine").value = 0;
  }
  if (document.getElementById("prezzo").value === "") {
    document.getElementById("prezzo").value = 0;
  }
  if (document.getElementById("quantita").value === "") {
    document.getElementById("quantita").value = 0;
  }

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

function getImgData() {
  const files = chooseFile.files[0];
  if (files) {
    const fileReader = new FileReader();
    fileReader.readAsDataURL(files);
    fileReader.addEventListener("load", function () {
      imgPreview.style.display = "block";
      imgPreview.src = this.result;
    });
  }
}

function checkModificaLibro() {
  var src = imgPreview.getAttribute('src');
  if (src != "") {
    imgPreview.src = src;
    imgPreview.style.display = "block";
  }
}

window.addEventListener('load', function () {
  caricamento();
  checkModificaLibro();
})