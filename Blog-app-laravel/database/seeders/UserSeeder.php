<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = [['ata@gmail.com', 'admin', 'admin123'],
                 ['at3a@gmail.com', 'user', 'user123']];

            foreach ($users as $user) {
                $obj = new User();
                $obj->email = $user[0];
                $obj->name = $user[1];
                $obj->password = bcrypt($user[2]);
                $obj->save();
            }
    }
}
