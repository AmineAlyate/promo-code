<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PromoCode\Models\Admin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::query()->create(
            [
                Admin::COL_EMAIL    => 'amine@promocode.test',
                Admin::COL_PASSWORD => bcrypt('password'),
            ]
        );
    }
}
