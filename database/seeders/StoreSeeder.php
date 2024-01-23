<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Store = new Store();
        $Store->user_role_id = 3;
        $Store->name = 'James Grilled Barbecue';
        $Store->status = 'active';
        $Store->latitude = '14.653495';
        $Store->longitude = '120.965970';
        $Store->address = 'Sabalo Street Caloocan City';
        $Store->monday = '10:00 AM - 10:30 PM';
        $Store->tuesday = '10:00 AM - 10:30 PM';
        $Store->wednesday = '10:00 AM - 10:30 PM';
        $Store->thursday = '10:00 AM - 10:30 PM';
        $Store->friday = '10:00 AM - 10:30 PM';
        $Store->saturday = '10:00 AM - 8:00 PM';
        $Store->sunday = '10:00 AM - 8:00 PM';
        $Store->save();

        $Store = new Store();
        $Store->user_role_id = 5;
        $Store->name = 'Jennie Tea Store';
        $Store->status = 'active';
        $Store->latitude = '14.653318';
        $Store->longitude = '120.966201';
        $Store->address = 'Sabalo Street Caloocan City';
        $Store->monday = '9:00 AM - 9:30 PM';
        $Store->tuesday = '9:00 AM - 9:30 PM';
        $Store->wednesday = '9:00 AM - 9:30 PM';
        $Store->thursday = '9:00 AM - 9:30 PM';
        $Store->friday = '9:00 AM - 9:30 PM';
        $Store->saturday = '9:00 AM - 9:00 PM';
        $Store->sunday = '9:00 AM - 9:00 PM';
        $Store->save();
    }
}
