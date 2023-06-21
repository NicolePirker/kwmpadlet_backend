<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Padlet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function findEntryById($id) : JsonResponse {
        $entry = Entry::where('id', $id)->with(['user', 'comments','comments.user', 'ratings'])->first();
        if($entry == null){
            return response()->json(null, 404);
        }
        $padletId = $entry->padlet_id;
        $role = $this->getRole($padletId);
        if($role == -1){
            return response()->json(null, 401);
        }
        return response()->json($entry, 200);
    }

    public function create(Request $request, $padletId) : JsonResponse {
        $loggedInUser = $this->getLoggedInUser(); // User laden
        $padlet = Padlet::where('id', $padletId)->first(); // Aktuelles Padlet laden

        if($loggedInUser == null || $padlet == null){
            return response()->json(null, 404);
        }
        if(!$this->hasCreatePermission($padletId)){ // Rechte überprüfen
            return response()->json(null, 401);
        }
        DB::beginTransaction();
        try {
            $data = $request->all(); // Alle Eingabedaten vom http-request holen, all speichert diese als assoziatives Array ab
            $entry = new Entry($data); // Neuen Entry erstellen, mit den in $data gespeicherten Daten
            $entry->user()->associate($loggedInUser); // Eintrag mit User verknüpfen
            $entry->padlet()->associate($padlet); // Eintrag mit Padlet verknüpfen
            $entry->save();
            DB::commit();
            return response()->json($entry, 200);
        }
        catch (\Exception $e) {

            DB::rollBack();
            return response()->json("saving entry failed: " . $e->getMessage(), 420);
        }
    }

    public function update(Request $request, string $entryId) : JsonResponse {
        $entry = Entry::where('id', $entryId)->first(); // Aktuellen Eintrag laden

        if($entry == null){
            return response()->json(null, 404);
        }
        if(!$this->hasEditPermission($entry->padlet_id)){ // Auf Padlet id in entry zurückgreifen
            return response()->json(null, 401);
        }

        DB::beginTransaction();
        try {
            $entry->text = $request->get("text"); // Auf text zugreifen und neuen Wert aus http-request übergeben
            $entry->update(); // Änderungen werden gespeichert (persistiert)

            // Doku in Laravel zu update: https://laravel.com/docs/10.x/queries

            DB::commit();
            return response()->json($entry, 200);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json("updating padlet failed: " . $e->getMessage(), 420);
        }

    }

//DELETE
    public function delete(string $entryId) : JsonResponse {
        $entry = Entry::where('id', $entryId)->first();

        if($entry == null){
            return response()->json(null, 404);
        }
        if(!$this->hasDeletePermission($entry->padlet_id)){
            return response()->json(null, 401);
        }

        $entry->delete();
        return response()->json('entry successfully deleted', 200);
    }
}
