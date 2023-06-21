<?php

namespace App\Http\Controllers;

use App\Models\Padlet;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Welcher User hat sich angemeldet --> user id bekommen
    // JWTAuth - analysiert den JWT und gibt das Benutzerobjekt zurück
    // -> authenticate - gibt das Benutzerobjekt nur zurück, wenn der JWT gültig ist,
    // sprich der User muss authentifiziert sein
    public function getLoggedInUserId(){
        $user = JWTAuth::parseToken()->authenticate();
        return $user->id;
    }

    // Eingeloggten user zurückgeben
    // Benutzer in der DB suchen bei dem die ID mit der ID des eingeloggten User übereinstimmt
    public function getLoggedInUser(){
        return User::where('id', $this->getLoggedInUserId())->first();
    }

    function isAuthor($padletId, $userId){
        $authorId = Padlet::where('id', $padletId)->first()->user_id;
        if($authorId == $userId){
            // eingeloggter user ist autor des padlets
            return true;
        }
        return false;
    }

    public function hasCreatePermission($padletId){
        $role = $this->getRole($padletId); // Rolle abrufen
        return in_array($role, [2,3,4]);
    }

    public function hasEditPermission($padletId){
        $role = $this->getRole($padletId);
        return in_array($role, [3,4]);
    }

    public function hasDeletePermission($padletId){
        $role = $this->getRole($padletId);
        return in_array($role, [4]);
    }

    function getRole($padletId){
        // User id speichern
        $userId = $this->getLoggedInUserId();
        // Prüfen, ob der User auch der Autor/Ersteller des Padlets ist
        if($this->isAuthor($padletId,$userId)){
            // eingeloggter user ist autor des padlets
            return 4;

        } // Fall 2: Eingeloggter User ist nicht Ersteller des Padlets:
        // Alle User holen inklusive (with) Padlets die mit ihnen geteilt wurden
        $loggedInUser = User::with('sharedPadlets')->where('id', $userId)->first();
        // Auf die geteilten Padlets zugreifen und davon dann auf das aktuelle Padlet zugreifen anhand der padlet id
        $padlet = $loggedInUser->sharedPadlets()->where('padlet_id', $padletId)->first();
        // Wenn ein geteiltes padlet gefunden wird, dann in der pivot-Tabelle auf die Rolle zugreifen
        if($padlet != null){
            return $padlet->pivot->role;
        }
        return -1;
    }
}
