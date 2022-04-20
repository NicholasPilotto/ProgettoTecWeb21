function resetFilters() {
  var checkElement = document.querySelectorAll('input[type=checkbox]');
  checkElement.forEach(check => {
    check.checked = false;
  });

  document.getElementById("prezzoMin").value = 1;
  document.getElementById("prezzoMax").value = 20;

  return false;
}

function controlNumberMin() {
  var min = document.getElementById("prezzoMin");
  var max = document.getElementById("prezzoMax");

  if (min.value == max.value - 1) {
    min.max = min.value;
  }
}

function controlNumberMax() {
  var min = document.getElementById("prezzoMin");
  var max = document.getElementById("prezzoMax");
  min.max = max.value - 1;

  if (min.value == max.value) {
    min.value = max.value - 1;
  }
}