<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create a page for each user with tenant role
        $users = User::role('tenant')->get();
        foreach ($users as $user) {
            Page::factory()->create([
                'user_id' => $user->id,
                'company_id' => $user->company_id ?? 1, // Adjust based on your auth logic
                'tenant_id' => $user->tenant_id,
                'slug' => Str::slug($user->first_name . ' ' . $user->last_name . ' page'),
                'title' => $user->first_name . ' ' . $user->last_name . ' Page',
                'description' => 'This is a page for ' . $user->first_name . ' ' . $user->last_name,
                'is_active' => true
            ]);
        }
    }
}
