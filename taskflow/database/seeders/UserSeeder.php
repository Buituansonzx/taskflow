<?php

namespace Database\Seeders;

use App\Containers\AppSection\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Bùi Tuấn Sơn',
                'email' => 'bson55444@gmail.com',
                'password' => Hash::make('Sonheozx1@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Phạm Thị Vân Anh',
                'email' => 'phamthivananh@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Trần Thị Mai',
                'email' => 'tranthimai@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lê Minh Đức',
                'email' => 'leminhduc@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Phạm Quốc Huy',
                'email' => 'phamquochuy@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Đặng Thu Trang',
                'email' => 'dangthutrang@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hoàng Gia Bảo',
                'email' => 'hoanggiabao@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Võ Thanh Tùng',
                'email' => 'vothanhtung@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ngô Khánh Linh',
                'email' => 'ngokhanhlinh@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nguyễn Bùi Phương Anh',
                'email' => 'nguyenbuiphuonganh@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nguyễn Thúy Nga',
                'email' => 'nguyenthuynga@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Vương Văn Tuấn',
                'email' => 'vuongvantuan@gmail.com',
                'password' => Hash::make('Password123@'),
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}