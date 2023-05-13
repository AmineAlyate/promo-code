<?php

namespace PromoCode\Services\PromoCode;

use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use PromoCode\DTO\Collections\PromoCodeUsers;
use PromoCode\DTO\PromoCodeUser;
use PromoCode\Exceptions\PromoCode\InvalidPromoCodeException;
use PromoCode\Models\PromoCode;
use PromoCode\Models\User;
use PromoCode\Repositories\PromoCode\PromoCodeRepository;
use PromoCode\Validators\PromoCode\PromoCodeValidator;

class PromoCodeService
{
    public function __construct(
        private readonly PromoCodeRepository $promoCodeRepository,
        private readonly Factory $validatorFactory,
        private readonly PromoCodeValidator $promoCodeValidator,
        private readonly PromoCodeGeneratorService $promoCodeGeneratorService
    ) {
    }

    /**
     * @param array $data
     *
     * @return PromoCode
     * @throws ValidationException
     */
    public function create(array $data): PromoCode
    {
        $attributes = $this->validatorFactory->validate($data, $this->promoCodeValidator->rules());
        if (!isset($attributes['code'])) {
            $attributes['code'] = $this->promoCodeGeneratorService->generateCode();
        }

        $this->validateUsers($attributes);

        return $this->promoCodeRepository->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return void
     */
    private function validateUsers(array $attributes): void
    {
        if (!isset($attributes['users'])) {
            return;
        }

        PromoCodeUsers::createFromArray($attributes['users']);
    }

    /**
     * @param string $code
     * @param User|null $user
     *
     * @return PromoCode
     * @throws InvalidPromoCodeException
     */
    public function validatePromoCode(string $code, ?User $user = null): PromoCode
    {
        $promoCode = $this->findByCode($code);
        if (!$promoCode instanceof PromoCode) {
            throw new InvalidPromoCodeException($code, 'Promo code not found');
        }

        if (!is_null($promoCode->getEndDate()) && $promoCode->getEndDate()->isPast()) {
            throw new InvalidPromoCodeException($code, 'Promo code expired');
        }

        if ($promoCode->getMaxUsage() !== null && $promoCode->getMaxUsage() <= $promoCode->getUsageCount()) {
            throw new InvalidPromoCodeException($code, 'Promo code usage limit reached');
        }

        if (count($promoCode->getUsers()) === 0) {
            return $promoCode;
        }

        if (!$user instanceof User) {
            throw new InvalidPromoCodeException($code, 'Promo code is not available for this user');
        }

        $promoCodeUsers = PromoCodeUsers::createFromArray($promoCode->getUsers());

        if (!$promoCodeUsers->hasUser($user->getId())) {
            throw new InvalidPromoCodeException($code, 'Promo code is not available for this user');
        }

        if ($promoCodeUsers->getMaxUsagePerUser($user->getId()) <= $promoCodeUsers->getUsageCountForUser($user->getId())
        ) {
            throw new InvalidPromoCodeException($code, 'Promo code usage limit reached for this user');
        }

        return $promoCode;
    }

    public function findByCode(string $code): ?PromoCode
    {
        return $this->promoCodeRepository->findByCode($code);
    }

    public function promoCodeUsed(PromoCode $promoCode, ?User $user = null): bool
    {
        if ($user instanceof User) {
            $this->incrementUsageCountForUser($promoCode, $user->getId());
        }

        return $this->incrementUsageCount($promoCode);
    }

    private function incrementUsageCountForUser(PromoCode $promoCode, int $userId): void
    {
        $promoCodeUsers = PromoCodeUsers::createFromArray($promoCode->getUsers());
        $codeUser = $promoCodeUsers->findUser($userId);
        if (!$codeUser instanceof PromoCodeUser) {
            return;
        }

        $codeUser->incrementUsage();

        $this->promoCodeRepository->update(
            $promoCode->getId(),
            [
                PromoCode::COL_USERS => $promoCodeUsers->toArray(),
            ]
        );
    }

    private function incrementUsageCount(PromoCode $promoCode): bool
    {
        return $this->promoCodeRepository->update(
            $promoCode->getId(),
            [
                PromoCode::COL_USAGE_COUNT => $promoCode->getUsageCount() + 1,
            ]
        );
    }
}
