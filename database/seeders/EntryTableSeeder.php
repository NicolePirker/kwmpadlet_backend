<?php

namespace Database\Seeders;

use App\Models\Entry;
use App\Models\Padlet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    // Entries erstellen
    public function run()
    {
        for ($i = 1; $i <= 3; $i++){
            $p1 = new Entry();
            $p1->text = "Post $i";
            $p1->created_at = date('Y-m-d H:i:s');
            $p1->updated_at = date('Y-m-d H:i:s');
            $user = User::first();
            $padlet = Padlet::first();
            $p1->user()->associate($user);
            $p1->padlet()->associate($padlet);
            $p1->save();
        }

        for ($i = 1; $i <= 5; $i++){
            $p1 = new Entry();
            $p1->text = "Post $i";
            $p1->created_at = date('Y-m-d H:i:s');
            $p1->updated_at = date('Y-m-d H:i:s');
            $user = User::skip(1)->first();
            $padlet = Padlet::skip(1)->first();
            $p1->user()->associate($user);
            $p1->padlet()->associate($padlet);
            $p1->save();
        }
    }
}
