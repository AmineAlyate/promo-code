<?php

namespace PromoCode\Exceptions\Auth;

class InvalidCredentialsException extends \Exception
{
    public function __construct($message = "Invalid credentials")
    {
        parent::__construct($message);
    }
}
