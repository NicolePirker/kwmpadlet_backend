<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Padlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class PadletController extends Controller
{
    public function index() : JsonResponse {
        $userId = $this->getLoggedInUserId();
        // Alle Padlets laden + Benutzer dazuladen mit denen Padlets geteilt wurden (with('sharedWith')->
        // sharedWith nur laden (whereHas), wenn es auch eine id in sharedWith gibt (sprich wenn es geteilt wurde),
        // UND diese mit der User id des aktuell eingeloggten Users übereinstimmt
        $padletsDb = Padlet::with('sharedWith')->whereHas('sharedWith', function($query) use ($userId){
            $query->where('id', $userId);
        })->orWhere('user_id', $userId)->get(); // Und zusätzlich alle Padlets laden die vom User selbst erstellt wurden

        $padlets = []; // leeres Array erstellen
        foreach ($padletsDb as $padletDb){
            $padletId = $padletDb->id;
            $padlet = array('id' => $padletId, 'name' => $padletDb->name, 'role' => $this->getRole($padletId),
                'user_id' => $padletDb->user_id);
            $padlets[] = $padlet; //$padlet in Array reinspeichern
        }

        return response()->json($padlets, 200);
    }

    public function findByPadletId($id) : JsonResponse {
        $padlet = Padlet::where('id', $id)->with(['user', 'entries','entries.user','entries.comments','entries.comments.user', 'entries.ratings'])->first();
        if($padlet == null){
            return response()->json(null, 404);
        } // Prüfen, ob er eine Berechtigung hat
        $role = $this->getRole($id);
        if($role == -1){
            return response()->json(null, 401);
        }
        $padlet->role = $role;
        return response()->json($padlet, 200);
    }

    // Padlet erstellen
    public function create(Request $request) : JsonResponse {
        $userId = $this->getLoggedInUserId();
        DB::beginTransaction();
        try {
            $data = $request->all(); // Liefert alle Inhalte des request als Array
            $data["user_id"] = $userId; // autor einfügen
            $padlet = Padlet::create($data);
            $padlet->save();
            DB::commit();
            return response()->json($padlet, 200);
        }
        catch (\Exception $e) {

            DB::rollBack();
            return response()->json("saving padlet failed: " . $e->getMessage(), 420);
        }
    }

    // Padlet verändern (= Umbenennen)
    public function update(Request $request, string $id) : JsonResponse {
        $userId = $this->getLoggedInUserId();

        DB::beginTransaction();
        try {
            $padlet = Padlet::where('id', $id)->first();
            if($padlet == null){
                return response()->json(null, 404);
            }
            if($this->getRole($id) <= 2){
                return response()->json(null, 401);
            }
            $padlet->name = $request->get("name");
            $padlet->update();

            DB::commit();
            return response()->json($padlet, 200);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json("updating padlet failed: " . $e->getMessage(), 420);
        }

    }

// Padlet löschen
    public function delete(string $id) : JsonResponse {
        $padlet = Padlet::where('id', $id)->first();
        if($padlet == null){
            return response()->json('padlet could not be deleted - it does not exist', 422);
        }
        if($this->getRole($id) <= 3){
            return response()->json(null, 401);
        }
        $padlet->delete();
        return response()->json('padlet (' . $id . ') successfully deleted', 200);
    }
}
