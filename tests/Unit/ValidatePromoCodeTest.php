<?php

namespace Tests\Unit;

use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PromoCode\DTO\PromoCodeUser;
use PromoCode\Exceptions\PromoCode\InvalidPromoCodeException;
use PromoCode\Models\PromoCode;
use PromoCode\Models\User;
use PromoCode\Services\PromoCode\PromoCodeService;
use Tests\CreatesApplication;

class ValidatePromoCodeTest extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    public function test_promo_code_not_exist_on_db_validity()
    {
        $this->expectException(InvalidPromoCodeException::class);

        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);
        $promoCodeService->validatePromoCode('NOT-EXIST-TEST');
    }

    public function test_expired_promo_code_validity()
    {
        $this->expectException(InvalidPromoCodeException::class);

        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);
        $promoCode = $promoCodeService->create(
            [
                PromoCode::COL_CODE     => 'TEST-EXPIRED',
                PromoCode::COL_VALUE    => 10,
                PromoCode::COL_TYPE     => PromoCode::TYPE_PERCENTAGE,
                PromoCode::COL_END_DATE => '2020-01-01',
            ]
        );

        $promoCodeService->validatePromoCode('TEST-EXPIRED');
    }

    public function test_promo_code_valid_for_user()
    {
        $user = $this->createUser();

        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);
        $promoCode = $promoCodeService->create(
            [
                PromoCode::COL_CODE     => 'PROMO-CODE-FOR-USER',
                PromoCode::COL_VALUE    => 10,
                PromoCode::COL_TYPE     => PromoCode::TYPE_PERCENTAGE,
                PromoCode::COL_END_DATE => '2024-01-01',
                PromoCode::COL_USERS    => [
                    PromoCodeUser::createFromArray(
                        [
                            'user_id'   => $user->getId(),
                            'max_usage' => 2,
                        ]
                    )->toArray(),
                ],
            ]
        );

        $promoCodeService->validatePromoCode('PROMO-CODE-FOR-USER', $user);

        $this->assertTrue(true);
    }

    private function createUser(): User
    {
        /** @var User */
        return User::query()->create(
            [
                User::COL_NAME     => 'Test User',
                User::COL_EMAIL    => $this->app->make(Generator::class)->email(),
                User::COL_PASSWORD => bcrypt('password'),
            ]
        );
    }

    public function test_promo_code_max_usage_not_exceeded()
    {
        $this->expectException(InvalidPromoCodeException::class);

        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);
        $promoCode = $promoCodeService->create(
            [
                PromoCode::COL_CODE      => 'PROMO-CODE-MAX-USAGE',
                PromoCode::COL_VALUE     => 10,
                PromoCode::COL_MAX_USAGE => 2,
                PromoCode::COL_TYPE      => PromoCode::TYPE_PERCENTAGE,
                PromoCode::COL_END_DATE  => '2024-01-01',
                PromoCode::COL_USERS     => [],
            ]
        );

        $promoCodeService->validatePromoCode('PROMO-CODE-MAX-USAGE');
        $promoCodeService->promoCodeUsed($promoCode);
        $promoCode = $promoCodeService->findByCode($promoCode->getCode());
        $promoCodeService->promoCodeUsed($promoCode);
        $promoCodeService->validatePromoCode('PROMO-CODE-MAX-USAGE');

        $this->assertTrue(true);
    }

    public function test_promo_code_max_usage_not_exceeded_for_user()
    {
        $user = $this->createUser();
        $this->expectException(InvalidPromoCodeException::class);

        /** @var PromoCodeService $promoCodeService */
        $promoCodeService = app(PromoCodeService::class);
        $promoCode = $promoCodeService->create(
            [
                PromoCode::COL_CODE      => 'PROMO-CODE-MAX-USAGE-FOR-USER',
                PromoCode::COL_VALUE     => 10,
                PromoCode::COL_MAX_USAGE => 2,
                PromoCode::COL_TYPE      => PromoCode::TYPE_PERCENTAGE,
                PromoCode::COL_END_DATE  => '2024-01-01',
                PromoCode::COL_USERS    => [
                    PromoCodeUser::createFromArray(
                        [
                            'user_id'   => $user->getId(),
                            'max_usage' => 2,
                        ]
                    )->toArray(),
                ],
            ]
        );

        $promoCodeService->validatePromoCode('PROMO-CODE-MAX-USAGE-FOR-USER', $user);
        $promoCodeService->promoCodeUsed($promoCode, $user);
        $promoCode = $promoCodeService->findByCode($promoCode->getCode());
        $promoCodeService->promoCodeUsed($promoCode, $user);
        $promoCodeService->validatePromoCode('PROMO-CODE-MAX-USAGE-FOR-USER', $user);

        $this->assertTrue(true);
    }
}
