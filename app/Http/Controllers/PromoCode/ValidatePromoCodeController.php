<?php

namespace App\Http\Controllers\PromoCode;

use Illuminate\Http\Request;
use PromoCode\Exceptions\PromoCode\InvalidPromoCodeException;
use PromoCode\Exceptions\UserNotFoundException;
use PromoCode\Locator\CurrentUserLocator;
use PromoCode\Models\PromoCode;
use PromoCode\Services\PromoCode\PromoCodeService;
use Throwable;

class ValidatePromoCodeController
{
    public function __construct(
        private readonly PromoCodeService $promoCodeService,
        private readonly CurrentUserLocator $currentUserLocator
    ) {
    }

    public function __invoke(Request $request, string $code, int $price)
    {
        try {
            $promoCode = $this->promoCodeService->findByCode($code);

            try {
                $user = $this->currentUserLocator->get();
            } catch (UserNotFoundException $e) {
                $user = null;
            }

            $this->promoCodeService->validatePromoCode($code, $user);
            $this->promoCodeService->promoCodeUsed($promoCode, $user);

            $discountedAmount = $promoCode->getType() === PromoCode::TYPE_VALUE
                ? $promoCode->getValue()
                : $price * $promoCode->getValue() / 100;

            return response()->json(
                [
                    'price'                        => $price,
                    'promo_code_discounted_amount' => $discountedAmount,
                    'final_price'                  => bcsub($price, $discountedAmount, 2),
                ],
                200
            );
        } catch (InvalidPromoCodeException $e) {
            return response()->json(
                [
                    'body' => 'Promo code not valid',
                ],
                404
            );
        } catch (Throwable $e) {
            // TODO log error

            return response()->json(['error' => 'Error occurred'], 500);
        }
    }
}
