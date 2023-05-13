<?php

namespace PromoCode\Services\PromoCode;

use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
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

        foreach ($attributes['users'] as $userData) {
            PromoCodeUser::createFromArray($userData);
        }
    }

    /**
     * @param string $code
     * @param User|null $user
     *
     * @return void
     * @throws InvalidPromoCodeException
     */
    public function validatePromoCode(string $code, ?User $user = null): void
    {
        $promoCode = $this->findByCode($code);
        if (!$promoCode instanceof PromoCode) {
            throw new InvalidPromoCodeException($code);
        }

        if (!is_null($promoCode->getEndDate()) && $promoCode->getEndDate()->isPast()) {
            throw new InvalidPromoCodeException($code);
        }

        if ($promoCode->getMaxUsage() !== null && $promoCode->getMaxUsage() <= $promoCode->getUsageCount()) {
            throw new InvalidPromoCodeException($code);
        }

        if ($promoCode->getUsers()->count() === 0) {
            return;
        }

        if (!$user instanceof User) {
            throw new InvalidPromoCodeException($code);
        }

        if (!$promoCode->hasUser($user->getId())) {
            throw new InvalidPromoCodeException($code);
        }

        if ($promoCode->getMaxUsagePerUser($user->getId()) <= $promoCode->getUsageCountForUser($user->getId())
        ) {
            throw new InvalidPromoCodeException($code);
        }
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
        $promoCodeUsers = $promoCode->getUsers();
        $codeUser = $promoCodeUsers->first(fn(PromoCodeUser $user) => $user->getUserId() === $userId);
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
