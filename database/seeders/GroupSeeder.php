<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::create([
            "name"          => "Super Admin",
            "description"   => "All Permission For Super Admin",
            "is_admin"      => 1
        ]);

        GroupAccess::create([
            "group_id"      => 1,
            "group_access"  => ["admin_list","admin_create","admin_delete","admin_restore",]
        ]);
    }
}
