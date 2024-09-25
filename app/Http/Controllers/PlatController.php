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
        $userId = Auth::id();

        $plats = Plat::with(['user', 'favoris'])
            //Sous-requête qui compte le nombre de lignes dans la table favoris où plat_id correspond à l’ID du plat actuel et user_id correspond à l’utilisateur actuel et l'assigne à is_favoris.
            ->select('plats.*')
            ->withCount(['favoris as is_favori' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])

            ->where(function ($query) use ($search) {

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

    public function create(Request $request, Plat $plat)
    {

        if (!$request->user()->can('create plats')) {
            return redirect()->back()->with('admin', 'Vous n\'avez pas l\'autorisation de crée un plat car vous n\' êtes pas admin');

        }
        return view('create', compact('plat'));
    }


    public function store(StorePlatRequest $request, Plat $plat)
    {

        $data = $request->all();

        // Ajouter 'user_id' et 'Image' aux données

        $data['user_id'] = Auth::id();
        $data['Image'] = fake()->imageUrl(320, 240, 'dish');
        $data['Likes'] = fake()->numberBetween(1, 100);

        // Créer une nouvelle instance de Plat

        $plat = new Plat($data);
        // Sauvegarder le plat dans la base de données
        $plat->save();

        return redirect()->route('plats.show', $plat);
    }

    public function edit(Request $request, Plat $plat)
    {
        if ($request->user()->can('create plats')) {
            // L'admin peut modifier n'importe quel plat
            return view('edit', compact('plat'));
        } else {
            // Vérifie si l'utilisateur est le propriétaire du plat
            if ($request->user()->id === $plat->user_id) {
                return view('edit', compact('plat'));
            } else {
                // Redirige avec un message d'erreur si l'utilisateur n'est pas autorisé
                return redirect()->back()->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce plat .');
            }
        }
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
        if ($request->user()->hasRole('administrator')) {
            $plat->delete();
            // L'admin peut modifier n'importe quel plat
            return redirect()->route('plats.index');
        } else {
            // Vérifie si l'utilisateur est le propriétaire du plat
            if ($request->user()->id === $plat->user_id) {
                return redirect()->route('plats.index');
            } else {
                // Redirige avec un message d'erreur si l'utilisateur n'est pas autorisé
                return redirect()->back()->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce plat');
            }
        }
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
