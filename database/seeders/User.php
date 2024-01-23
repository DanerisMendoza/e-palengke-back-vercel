<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new UserModel();
        $user->username = 'admin';
        $user->password = Hash::make('admin');
        $user->save();
        
        $user = new UserModel();
        $user->username = 'Thomas';
        $user->password = Hash::make('123');
        $user->save();
        
        $user = new UserModel();
        $user->username = 'Stephanie';
        $user->password = Hash::make('123');
        $user->save();
        
        $user = new UserModel();
        $user->username = 'patrick';
        $user->password = Hash::make('123');
        $user->save();
        
        $user = new UserModel();
        $user->username = 'Antonio';
        $user->password = Hash::make('123');
        $user->save();

        $user = new UserModel();
        $user->username = 'john';
        $user->password = Hash::make('123');
        $user->save();
    }
}
