<?php

namespace DB;

class response_manager {
  private $empty = false;
  private $result = array();
  private $errno = NULL;
  private $error_message = NULL;

  public function __construct($res, $conn) {
    if (isset($res)) {
      $this->result = $res;
      $this->empty = false;
    }

    $this->errno = $conn->error;
  }

  public function set_message($mes): void {
    $this->error_message = $mes;
  }

  public function is_empty(): bool {
    return $this->empty;
  }

  public function get_result(): array {
    return $this->result;
  }

  public function get_error_message(): string {
    return $this->error_message;
  }

  public function get_errno(): int {
    return $this->errno;
  }
}
?>
