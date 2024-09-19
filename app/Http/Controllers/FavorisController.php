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

        $user->favoris()->toggle($plat->id);

        // Vérifie si le plat est maintenant dans les favoris
        $favori = $user->favoris()->where('plat_id', $plat->id)->exists();

        if ($favori) {
            return redirect()->back()->with('isfav', 'Vous avez ajouté le plat à vos favoris');
        } else {
            return redirect()->back()->with('notfav', 'Vous avez supprimé le plat de vos favoris');
        }
    }


}

