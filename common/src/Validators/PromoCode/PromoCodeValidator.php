<?php

namespace PromoCode\Validators\PromoCode;

use PromoCode\Models\PromoCode;

class PromoCodeValidator
{
    public function rules(): array
    {
        // TODO - move the unique and exists checks to the PromoCodeService to avoid consuming the database [To have a single introduction point for the database]
        return [
            'value'             => 'required|numeric',
            'type'              => 'required|in:' . implode(',', array_keys(PromoCode::getAvailableTypes())),
            'code'              => 'nullable|string|unique:promo_codes,code',
            'end_date'          => 'nullable|date',
            'max_usage'         => 'nullable|integer',
            'users'             => 'nullable|array',
            'users.*.user_id'   => 'nullable|integer|exists:users,id',
            'users.*.max_usage' => 'nullable|integer',
        ];
    }
}
