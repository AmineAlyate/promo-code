<?php

namespace PromoCode\Exceptions\PromoCode;

use Exception;

class InvalidPromoCodeException extends Exception
{
    private string $promoCode;
    private string $reason;

    public function __construct(string $promoCode, string $reason, $message = 'Invalid promo code')
    {
        parent::__construct($message);

        $this->promoCode = $promoCode;
        $this->reason = $reason;
    }

    public function getPromoCode(): string
    {
        return $this->promoCode;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
