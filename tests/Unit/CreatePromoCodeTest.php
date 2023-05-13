<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;
use PromoCode\Models\PromoCode;
use PromoCode\Services\PromoCode\PromoCodeService;
use Tests\CreatesApplication;

class CreatePromoCodeTest extends BaseTestCase
{
    use CreatesApplication;

    public function test_create_promo_code()
    {
        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);

        $attributes = [
            PromoCode::COL_CODE     => 'TEST',
            PromoCode::COL_VALUE    => 10,
            PromoCode::COL_TYPE     => PromoCode::TYPE_PERCENTAGE,
            PromoCode::COL_END_DATE => '2023-01-01',
        ];

        $promoCode = $promoCodeService->create($attributes);

        $this->assertInstanceOf(PromoCode::class, $promoCode);
    }

    public function test_create_promo_code_with_invalid_type()
    {
        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);

        $attributes = [
            PromoCode::COL_CODE     => 'TEST',
            PromoCode::COL_VALUE    => 10,
            PromoCode::COL_TYPE     => 3,
            PromoCode::COL_END_DATE => '2023-01-01',
        ];

        $this->expectException(ValidationException::class);

        $promoCode = $promoCodeService->create($attributes);
    }

    public function test_create_promo_code_without_providing_code()
    {
        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);

        $attributes = [
            PromoCode::COL_VALUE    => 10,
            PromoCode::COL_TYPE     => PromoCode::TYPE_PERCENTAGE,
            PromoCode::COL_END_DATE => '2023-01-01',
        ];

        $promoCode = $promoCodeService->create($attributes);

        $this->assertInstanceOf(PromoCode::class, $promoCode);
    }
}
