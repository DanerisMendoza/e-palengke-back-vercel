<?php

namespace Database\Seeders;
use App\Models\Access as AccessModel;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Access extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin
        $Access = new AccessModel();
        $Access->user_role_details_id = 1;
        $Access->side_nav_id = 1;
        $Access->save();
        
        $Access = new AccessModel();
        $Access->user_role_details_id = 1;
        $Access->side_nav_id = 4;
        $Access->save();
   
        $Access = new AccessModel();
        $Access->user_role_details_id = 1;
        $Access->side_nav_id = 6;
        $Access->save();
     
        $Access = new AccessModel();
        $Access->user_role_details_id = 1;
        $Access->side_nav_id = 8;
        $Access->save();

        $Access = new AccessModel();
        $Access->user_role_details_id = 1;
        $Access->side_nav_id = 11;
        $Access->save();
      
        $Access = new AccessModel();
        $Access->user_role_details_id = 1;
        $Access->side_nav_id = 12;
        $Access->save();


        //customer
        $Access = new AccessModel();
        $Access->user_role_details_id = 2;
        $Access->side_nav_id = 3;
        $Access->save();

        $Access = new AccessModel();
        $Access->user_role_details_id = 2;
        $Access->side_nav_id = 2;
        $Access->save();
     
        $Access = new AccessModel();
        $Access->user_role_details_id = 2;
        $Access->side_nav_id = 10;
        $Access->save();
  
        $Access = new AccessModel();
        $Access->user_role_details_id = 2;
        $Access->side_nav_id = 5;
        $Access->save();
   
        $Access = new AccessModel();
        $Access->user_role_details_id = 2;
        $Access->side_nav_id = 13;
        $Access->save();

        $Access = new AccessModel();
        $Access->user_role_details_id = 2;
        $Access->side_nav_id = 14;
        $Access->save();
  
        //seller
        $Access = new AccessModel();
        $Access->user_role_details_id = 3;
        $Access->side_nav_id = 9;
        $Access->save();
 
        $Access = new AccessModel();
        $Access->user_role_details_id = 3;
        $Access->side_nav_id = 14;
        $Access->save();

        //delivery
        $Access = new AccessModel();
        $Access->user_role_details_id = 4;
        $Access->side_nav_id = 7;
        $Access->save();
      
        $Access = new AccessModel();
        $Access->user_role_details_id = 4;
        $Access->side_nav_id = 14;
        $Access->save();
    }
}
