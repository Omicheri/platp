<?php

namespace Database\Seeders;



use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plat;

class PlatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        Plat::factory()->count(30)->create();
        foreach (User::all() as $user) {
            $platIds = Plat::inRandomOrder()->take(rand(1,5))->pluck('id');

            $user->favoris()->attach($platIds);
            if (!$user->hasRole('administrator')){
            $user->assignRole('user');}
        }
    }
}
