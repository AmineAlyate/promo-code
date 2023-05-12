<?php

namespace PromoCode\Services\PromoCode;

use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use PromoCode\DTO\PromoCodeUser;
use PromoCode\Models\PromoCode;
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
}
