<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Entry;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 10; $i++){
            $p1 = new Comment();
            $p1->text = "Comment $i";
            $p1->created_at = date('Y-m-d H:i:s');
            $p1->updated_at = date('Y-m-d H:i:s');
            $user = User::first();
            $entry = Entry::first();
            $p1->user()->associate($user);
            $p1->entry()->associate($entry);
            $p1->save();
        }

        for ($i = 0; $i <= 10; $i++){
            $p1 = new Comment();
            $p1->text = "Comment $i";
            $p1->created_at = date('Y-m-d H:i:s');
            $p1->updated_at = date('Y-m-d H:i:s');
            $user = User::skip(1)->first();
            $entry = Entry::skip(1)->first();
            $p1->user()->associate($user);
            $p1->entry()->associate($entry);
            $p1->save();
        }
    }
}
