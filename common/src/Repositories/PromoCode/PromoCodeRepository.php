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
}
