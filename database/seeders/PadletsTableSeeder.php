<?php

namespace Database\Seeders;

use App\Models\Padlet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PadletsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $p1 = new Padlet();
        $p1->name = 'Mein erstes Padlet';
        $p1->created_at = date('Y-m-d H:i:s');
        $p1->updated_at = date('Y-m-d H:i:s');
        $user1 = User::first();
        $p1->user()->associate($user1);
        $p1->save();

        $p2 = new Padlet();
        $p2->name = 'Mein zweites Padlet';
        $p2->created_at = date('Y-m-d H:i:s');
        $p2->updated_at = date('Y-m-d H:i:s');
        $user2 = User::skip(1)->first();
        $p2->user()->associate($user2);
        $p2->save();
        $user4 = User::skip(3)->first();
        $user3 = User::skip(2)->first();

        //https://serversideup.net/managing-pivot-data-with-laravel-eloquent/ + Skript S.33
        $p2->sharedWith()->attach([
            $user1->id => [
                'role' => 1
                ],

            $user3->id => [
                'role' => 3
            ],

            $user4->id => [
                'role' => 4
            ]
        ]);

        $p1->sharedWith()->attach([
            $user2->id => [
                'role' => 2
            ],

            $user4->id => [
                'role' => 4
            ]

        ]);
    }
}
