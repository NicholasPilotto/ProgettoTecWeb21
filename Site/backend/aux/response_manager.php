<?php

namespace DB;

class response_manager {

  private $empty = false;
  private $result = array();
  private $errno = 0;
  private $error_message_mysqli = "";
  private $error_message = "";

  public function __construct($res, $conn, $mess) {
    $this->result = $res;

    $this->error_message = $mess;
    $this->empty = count($res) != 0 ? false : true;

    if ($conn) {
      $this->error_message_mysqli = $conn->error;

      $this->errno = $conn->errno;
    }
  }

  public function ok(): bool {
    return !$this->empty && !$this->errno;
  }

  public function is_empty(): bool {
    return $this->empty;
  }

  public function get_element_count(): int {
    return count($this->result);
  }

  public function get_result(): array {
    return $this->result;
  }

  public function get_error_message(): string {
    return $this->error_message;
  }

  public function get_error_message_mysqli(): string {
    return $this->error_message_mysqli;
  }

  public function get_errno(): int {
    return $this->errno;
  }

  public function set_error_message($mes): void {
    $this->error_message = $mes;
  }
}
