<?php

namespace App\Http\Controllers\PromoCode;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use PromoCode\Services\PromoCode\PromoCodeService;
use PromoCode\Transformers\PromoCodeTransformer;
use Throwable;

class StorePromoCodeController
{
    public function __construct(
        private readonly PromoCodeService $promoCodeService,
        private readonly PromoCodeTransformer $promoCodeTransformer
    ) {
    }

    public function __invoke(Request $request)
    {
        try {
            $promoCode = $this->promoCodeService->create($request->all());

            return response()->json($this->promoCodeTransformer->transform($promoCode), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            // TODO log error

            return response()->json(['error' => 'Error occurred'], 500);
        }
    }
}
