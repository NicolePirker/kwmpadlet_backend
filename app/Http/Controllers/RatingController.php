<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Entry;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    private function isValidRatingNumber($rating){
        return $rating >= 1 && $rating <= 5;
    }
    public function create(Request $request, $entryId) : JsonResponse {
        $loggedInUser = $this->getLoggedInUser();
        $entry = Entry::where('id', $entryId)->first();
        $rating = $request->get('rating');
        if(!$this->isValidRatingNumber($rating)){
            return response()->json('invalid rating number (1-5)', 500);
        }
        if($loggedInUser == null || $entry == null){
            return response()->json(null, 404);
        }
        $padletId = $entry->padlet_id;
        if(!$this->hasCreatePermission($padletId)){
            return response()->json(null, 401);
        }
        DB::beginTransaction();
        try {
            $data = $request->all();
            $rating = new Rating($data);
            $rating->user()->associate($loggedInUser);
            $rating->entry()->associate($entry);
            $rating->save();

            DB::commit();
            return response()->json($rating, 200);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving rating failed: " . $e->getMessage(), 420);
        }
    }

    public function update(Request $request, string $ratingId) : JsonResponse {
        $rating = Rating::where('id', $ratingId)->first();
        $newRating = $request->get('rating');
        if(!$this->isValidRatingNumber($newRating)){
            return response()->json('invalid rating number (1-5)', 500);
        }
        if($rating == null){
            return response()->json(null, 404);
        }
        $authorId = $rating->user_id;
        if($authorId != $this->getLoggedInUserId()){
            return response()->json(null, 401);
        }

        DB::beginTransaction();
        try {
            $rating->rating = $request->get("rating");
            $rating->update();

            DB::commit();
            return response()->json($rating, 200);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json("updating rating failed: " . $e->getMessage(), 420);
        }

    }

    public function delete(string $ratingId) : JsonResponse {
        $rating = Rating::where('id', $ratingId)->first();
        if($rating == null){
            return response()->json(null, 404);
        }
        $authorId = $rating->user_id;
        if($authorId != $this->getLoggedInUserId()){
            return response()->json(null, 401);
        }

        $rating->delete();
        return response()->json('rating successfully deleted', 200);
    }
}
