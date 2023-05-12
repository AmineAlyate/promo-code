<?php

namespace PromoCode\Repositories\PromoCode;

use PromoCode\Models\PromoCode;

class PromoCodeRepository
{
    public function create(array $data): PromoCode
    {
        /** @var PromoCode] */
        return PromoCode::query()->create($data);
    }

    public function findByCode(string $code): ?PromoCode
    {
        /** @var PromoCode|null */
        return PromoCode::query()->where(PromoCode::COL_CODE, $code)->first();
    }

    public function update(int $id, array $attributes): bool
    {
        return PromoCode::query()->where(PromoCode::ID_COLUMN, $id)->update($attributes) > 0;
    }
}
