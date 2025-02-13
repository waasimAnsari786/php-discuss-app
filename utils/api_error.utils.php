<?php
class ApiError extends Exception
{
  public $statusCode;
  public $data;
  public $success;
  public $errors;

  public function __construct(
    $statusCode,
    $message = "Something went wrong",
    $errors = [],
    $code = 0,
    Exception $previous = null
  ) {
    parent::__construct($message, $code, $previous);

    $this->statusCode = $statusCode;
    $this->data = null;
    $this->message = $message;
    $this->success = false;
    $this->errors = $errors;
  }

  public function toArray()
  {
    return [
      'statusCode' => $this->statusCode,
      'data' => $this->data,
      'message' => $this->message,
      'success' => $this->success,
      'errors' => $this->errors
    ];
  }
}
