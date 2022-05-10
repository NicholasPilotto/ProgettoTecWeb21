<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class cart_item {
  public $total = 0.0;
  public $quant = 0;

  public function __set($t, $q) {
    $this->total += ($t == 0 ? 1 : $t) * ($q == 0 ? 1 : $q);
    $this->quant = $q == 0 ? 1 : $this->quant + $q;
  }
}

class cart {
  private $items = array();

  public function add($isbn, $quant, $prezzo): void {
    if (!isset($this->items[$isbn])) {
      $this->items += [$isbn => new cart_item()];
    }

    $this->items[$isbn]->__set($prezzo, $quant);
  }

  public function remove($isbn, $quant): void {
    if ($this->items[$isbn]->quant <= $quant) {
      unset($this->items[$isbn]);
    } else {
      $this->items[$isbn]->total -= ($quant == 0 ? 1 : $quant) * ($this->items[$isbn]->total / $this->items[$isbn]->quant);
      $this->items[$isbn]->quant -= $quant;
    }
  }

  public function get_total(): float {
    $tot = 0;

    foreach ($this->items as $item) {
      $tot += $item->total;
    }

    return $tot;
  }

  public function get_cart(): array {
    return $this->items;
  }
}

// $isbn = isset($_GET["isbn"]) ? $_GET["isbn"] : NULL;
// $quant = isset($_GET["quantita"]) ? $_GET["quantita"] : NULL;
// $prezzo = isset($_GET["prezzo"]) ? $_GET["prezzo"] : NULL;

// if (isset($isbn) && isset($quant) && isset($prezzo)) {
//   if ($_GET["action"] == "add") {
//     if (!isset($_SESSION["cart"])) {
//       $_SESSION["cart"] = new carrello();
//     }
//     $_SESSION["cart"]->add($isbn, $quant, $prezzo);
//   } else if ($_GET["action"] == "remove") {
//     $_SESSION["cart"]->remove($isbn, $quant, $prezzo);
//   }
// } else {
//   header("Location: error.php");
// }
