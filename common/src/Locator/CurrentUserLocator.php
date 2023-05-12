<?php

namespace PromoCode\Locator;

use PromoCode\Exceptions\UserNotFoundException;
use PromoCode\Models\User;

class CurrentUserLocator
{
    private ?User $user = null;

    /**
     * @return User|null
     * @throws UserNotFoundException
     */
    public function get(): ?User
    {
        if (!$this->user instanceof User) {
            throw new UserNotFoundException();
        }

        return $this->user;
    }

    public function set(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
