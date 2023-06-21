<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Entry;
use App\Models\Padlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function create(Request $request, $entryId) : JsonResponse {
        $loggedInUser = $this->getLoggedInUser();
        $entry = Entry::where('id', $entryId)->first();

        if($loggedInUser == null || $entry == null){
            return response()->json(null, 404);
        }
        $padletId = $entry->padlet_id;
        if(!$this->hasCreatePermission($padletId)){
            return response()->json(null, 401);
        }
        DB::beginTransaction();
        try {
            $data = $request->all(); // Alle Daten vom http-request holen
            $comment = new Comment($data); // Ein neues Kommentar erstellen und Daten (Text) reinspeichern
            $comment->user()->associate($loggedInUser); // Kommentar mit aktuell eingeloggten User assoziieren
            $comment->entry()->associate($entry);   // Kommentar mit aktuellen Entry assoziieren
            $comment->save();   // In DB speichern

            DB::commit();   // Transaktion abschließen (Daten dauerhaft speichern)
            return response()->json($comment, 200);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving comment failed: " . $e->getMessage(), 420); // Transaktion wird nicht durchgeführt
        }
    }

    public function update(Request $request, string $commentId) : JsonResponse {
        $comment = Comment::where('id', $commentId)->first();
        if($comment == null){
            return response()->json(null, 404);
        }
        $authorId = $comment->user_id;
        if($authorId != $this->getLoggedInUserId()){
            return response()->json(null, 401);
        }

        DB::beginTransaction();
        try {
            $comment->text = $request->get("text");
            $comment->update();

            DB::commit();
            return response()->json($comment, 200);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json("updating comment failed: " . $e->getMessage(), 420);
        }

    }
    public function delete(string $commentId) : JsonResponse {
        $comment = Comment::where('id', $commentId)->first();
        if($comment == null){
            return response()->json(null, 404);
        }
        $authorId = $comment->user_id; // User kann nur eigenen Kommentare löschen
        if($authorId != $this->getLoggedInUserId()){
            return response()->json(null, 401);
        }

        $comment->delete();
        return response()->json('comment successfully deleted', 200);
    }
}
