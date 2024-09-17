<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plat;
use Illuminate\Support\Facades\Auth;

class FavorisController extends Controller
{
    public function toggleFavori(Plat $plat)
    {
        $user = Auth::user();

        $favori = $user->favoris()->where('plat_id', $plat->id)->exists();

        if ($favori) {
            $user->favoris()->detach($plat->id);
            redirect()->back()->with('notfav','Vous avez supprimer le plat de vos favoris');
        } else {
            $user->favoris()->attach($plat->id);
            redirect()->back()->with('isfav','Vous avez ajouté le plat à vos favoris');
        }

        return back();
    }
}

