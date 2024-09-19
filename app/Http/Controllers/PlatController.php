<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlatRequest;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PlatController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'asc');
        $search = $request->get('search', '');

        $plats = Plat::with(['user', 'favoris'])->where(function ($query) use ($search) {
            if ($search) {
                $query->where('Titre', 'like', "%$search%")
//Si je ne trouve pas de plats avec ce mot ou cette phrase dans le titre, je veux aussi chercher dans les noms des utilisateurs qui ont créé ces plats.
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            }
        })
            ->when($sort === 'Likes', function ($query) use ($direction) {
                return $query->orderByRaw('CAST(Likes AS UNSIGNED) ' . $direction);
            }, function ($query) use ($sort, $direction) {
                return $query->orderBy($sort, $direction);
            })
            ->paginate(4);

        return view('index', compact('plats'));
    }

    public function show(Plat $plat)
    {
        return view('show', compact('plat'));
    }

    public function create(StorePlatRequest $request, Plat $plat)
    {

        if (!$request->user()->can('create plats')) {
            return redirect()->back()->with('admin', 'Vous n\'avez pas l\'autorisation de crée un plat');

        }
        return view('create', compact('plat'));
    }


    public function store(StorePlatRequest $request, Plat $plat)
    {

        $plat = new Plat();
        $plat->Titre = $request->get('titre');
        $plat->Recette = $request->get('recette');
        $plat->Likes = $request->get('likes');
        $plat->Image = fake()->imageUrl($width = 320, $height = 240, 'dish');
        $plat->user_id = Auth::id();
        $plat->save();

        return redirect()->route('plats.show', $plat);
    }

    public function edit(StorePlatRequest $request, Plat $plat)
    {

        return view('edit', compact('plat'));
    }

    public function update(StorePlatRequest $request, Plat $plat)
    {
        //Requête HTTP -> Instance de StorePlatRequest -> authorize() -> rules() -> Validation -> Méthode du contrôleur
        $plat->update($request->all());
        $plat->save();


        return redirect()->route('plats.show', $plat);
    }


    public function destroy(Request $request, Plat $plat)
    {
        if ($request->user()->can('destroy plats', $plat) && $plat->user_id == Auth::id()) {
            $plat->delete();
        } elseif ($plat->user_id !== Auth::id()) {
            return redirect()->back()->with('dest', 'Vous ne pouvez pas supprimer ce plat car vous n\'êtes pas le créateur de ce dernier');
        } else {
            return redirect()->back()->with('admin', 'Vous n\'avez pas l\'autorisation de supprimer un plat');
        }
        return redirect()->route('plats.index');
    }


    public function topCreators()
    {
        // Récupère les utilisateurs avec le nombre total de likes de leurs plats
        $users = User::withCount(['plats as total_likes' => function ($query) {
            $query->select(DB::raw('SUM(Likes)'));
        }])
            ->orderBy('total_likes', 'desc')
            ->get();

        return view('classement', compact('users'));
    }

}
