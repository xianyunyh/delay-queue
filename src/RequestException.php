<?php
namespace DelayQueue;


use Throwable;

class RequestException extends \Exception
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


    public function formatJson()
    {
        return json_encode([
            'code' =>1 ,
            'message' => $this->message
        ]);
    }

}