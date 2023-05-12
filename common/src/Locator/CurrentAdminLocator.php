<?php

namespace PromoCode\Locator;

use PromoCode\Exceptions\AdminNotFoundException;
use PromoCode\Models\Admin;

class CurrentAdminLocator
{
    private ?Admin $admin = null;

    /**
     * @return Admin|null
     * @throws AdminNotFoundException
     */
    public function get(): ?Admin
    {
        if (!$this->admin instanceof Admin) {
            throw new AdminNotFoundException();
        }

        return $this->admin;
    }

    public function set(Admin $admin): self
    {
        $this->admin = $admin;

        return $this;
    }
}
