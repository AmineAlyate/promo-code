<?php

namespace PromoCode\Transformers;

use League\Fractal\TransformerAbstract;
use PromoCode\Models\PromoCode;

class PromoCodeTransformer extends TransformerAbstract
{
    public function transform(PromoCode $promoCode): array
    {
        return [
            'id'          => $promoCode->getId(),
            'code'        => $promoCode->getCode(),
            'value'       => $promoCode->getValue(),
            'type'        => $promoCode->getTypeAsText(),
            'end_date'    => $promoCode->getEndDate()->toDateTimeString(),
            'max_usage'   => $promoCode->getMaxUsage(),
            'usage_count' => $promoCode->getUsageCount(),
            'users'       => $promoCode->getUsers(),
        ];
    }
}
