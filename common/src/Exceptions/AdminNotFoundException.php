<?php

namespace PromoCode\Exceptions;

use Exception;

class AdminNotFoundException extends Exception
{
    public function __construct($message = 'Admin not found')
    {
        parent::__construct($message);
    }
}
