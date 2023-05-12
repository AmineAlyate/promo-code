<?php

namespace PromoCode\Services\PromoCode;

use PromoCode\Models\PromoCode;

/*
 * Custom service dedicated to generate promo code
 * to keep the logic flexible to any changes
 */

class PromoCodeGeneratorService
{
    public function generateCode(): string
    {
        return strtr(
            config('promo_code.signature'),
            [
                '[year]' => now()->year,
                '[count]' => PromoCode::query()->count() + 1,
            ]
        );
    }
}
