<?php

session_start();

class carrello {
  private $total = 0.0;
  private $element = array();

  public function add($isbn, $quant, $prezzo): void {
    $this->element[$isbn] = $quant == 0 ? 1 : $quant;

    $this->total += ($prezzo == 0 ? 1 : $prezzo) * ($quant == 0 ? 1 : $quant);
  }

  public function remove($isbn, $quant, $prezzo): void {
    if ($this->element[$isbn] <= $quant) {
      $this->total -= $this->element[$isbn] * ($prezzo == 0 ? 1 : $prezzo);
      unset($this->element[$isbn]);
    } else {
      $this->total -= ($quant == 0 ? 1 : $quant) * ($prezzo == 0 ? 1 : $prezzo);
      $this->element[$isbn] -= $quant;
    }
  }

  public function get_total(): float {
    return $this->total;
  }

  public function get_cart(): array {
    return $this->element;
  }
}

$isbn = isset($_GET["isbn"]) ? $_GET["isbn"] : NULL;
$quant = isset($_GET["quantita"]) ? $_GET["quantita"] : NULL;
$prezzo = isset($_GET["prezzo"]) ? $_GET["prezzo"] : NULL;

if (isset($isbn) && isset($quant) && isset($prezzo)) {
  if ($_GET["action"] == "add") {
    if (!isset($_SESSION["cart"])) {
      $_SESSION["cart"] = new carrello();
    }
    $_SESSION["cart"]->add($isbn, $quant, $prezzo);
  } else if ($_GET["action"] == "remove") {
    $_SESSION["cart"]->remove($isbn, $quant, $prezzo);
  }
} else {
  header("Location: error.php");
}
