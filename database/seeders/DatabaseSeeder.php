<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PromoCode\Models\Admin;
use PromoCode\Models\User;

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
                Admin::COL_EMAIL    => 'admin@promocode.test',
                Admin::COL_PASSWORD => bcrypt('password'),
            ]
        );

        User::query()->create(
            [
                User::COL_NAME     => 'user1',
                User::COL_EMAIL    => 'user1@promocode.test',
                User::COL_PASSWORD => bcrypt('password'),
            ]
        );

        User::query()->create(
            [
                User::COL_NAME     => 'user2',
                User::COL_EMAIL    => 'user2@promocode.test',
                User::COL_PASSWORD => bcrypt('password'),
            ]
        );
    }
}
