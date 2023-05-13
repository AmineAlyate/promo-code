<?php

namespace PromoCode\DTO\Collections;

use Illuminate\Support\Collection;
use PromoCode\DTO\PromoCodeUser;

class PromoCodeUsers extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    public static function createFromArray(array $data): self
    {
        $promoCodeUsers = new self();

        foreach ($data as $promoCodeUser) {
            $promoCodeUsers->push(PromoCodeUser::createFromArray($promoCodeUser));
        }

        return $promoCodeUsers;
    }

    public function hasUser(int $id): bool
    {
        return $this->findUser($id) !== null;
    }

    public function findUser(int $id): ?PromoCodeUser
    {
        return $this->first(fn(PromoCodeUser $user) => $user->getUserId() === $id);
    }

    public function getMaxUsagePerUser(int $id): int
    {
        /** @var PromoCodeUser|null $user */
        $user = $this->findUser($id);

        return $user ? $user->getMaxUsage() : 0;
    }


    public function getUsageCountForUser(int $id): int
    {
        /** @var PromoCodeUser|null $user */
        $user = $this->findUser($id);

        return $user ? $user->getUsage() : 0;
    }
}
