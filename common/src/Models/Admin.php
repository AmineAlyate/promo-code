<?php

namespace PromoCode\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends User implements JWTSubject
{
    public const COL_ID = 'id';
    public const COL_EMAIL = 'email';
    public const COL_PASSWORD = 'password';

    protected $table = 'admins';

    protected $guarded = [];

    public function getJWTIdentifier(): string
    {
        return $this->getId();
    }

    public function getId(): string
    {
        return $this->getAttribute(self::COL_ID);
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::COL_EMAIL);
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
