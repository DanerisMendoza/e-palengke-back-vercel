<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole as UserRoleModel;
use Illuminate\Support\Facades\Hash;

class UserRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin
        $userRole = new UserRoleModel();
        $userRole->user_id = 1;
        $userRole->user_role_details_id = 1;
        $userRole->status = 'active';
        $userRole->save();
        //customer1 & seller
        $userRole = new UserRoleModel();
        $userRole->user_id = 2;
        $userRole->user_role_details_id = 2;
        $userRole->status = 'active';

        $userRole->save();
        $userRole = new UserRoleModel();
        $userRole->user_id = 2;
        $userRole->user_role_details_id = 3;
        $userRole->status = 'active';
        $userRole->save();

        //customer2 & seller
        $userRole = new UserRoleModel();
        $userRole->user_id = 3;
        $userRole->user_role_details_id = 2;
        $userRole->status = 'active';
        $userRole->save();
        $userRole = new UserRoleModel();

        $userRole->user_id = 3;
        $userRole->user_role_details_id = 3;
        $userRole->status = 'active';
        $userRole->save();

        //patrick
        $userRole = new UserRoleModel();
        $userRole->user_id = 4;
        $userRole->user_role_details_id = 2;
        $userRole->status = 'active';
        $userRole->save();

        //delivery1
        $userRole = new UserRoleModel();
        $userRole->user_id = 5;
        $userRole->user_role_details_id = 2;
        $userRole->status = 'active';
        $userRole->save();

        $userRole = new UserRoleModel();
        $userRole->user_id = 5;
        $userRole->user_role_details_id = 4;
        $userRole->status = 'active';
        $userRole->save();

        //john
        $userRole = new UserRoleModel();
        $userRole->user_id = 6;
        $userRole->user_role_details_id = 2;
        $userRole->status = 'active';
        $userRole->save();
    }
}
