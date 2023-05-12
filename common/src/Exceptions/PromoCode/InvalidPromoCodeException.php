<?php

namespace PromoCode\Exceptions\PromoCode;

use Exception;

class InvalidPromoCodeException extends Exception
{
    private string $promoCode;

    public function __construct(string $promoCode, $message = 'Invalid promo code')
    {
        parent::__construct($message);

        $this->promoCode = $promoCode;
    }

    public function getPromoCode(): string
    {
        return $this->promoCode;
    }
}
