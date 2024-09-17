<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PlatController extends Controller
{
    public function index()
    {
        $plats = Plat::with(['user', 'favoris'])->paginate(4);
        return view('index', compact('plats'));
    }

    public function show(Plat $plat)
    {
        return view('show', compact('plat'));
    }

    public function create(Request $request, Plat $plat)
    {

        if (!$request->user()->can('create plats')) {
            return redirect()->back()->with('admin', 'Vous n\'avez pas l\'autorisation de crée un plat');
        }
        return view('create', compact('plat'));
    }


    public function store(Request $request, Plat $plat)
    {

        $faker = \Faker\Factory::create();

        $faker->addProvider(new \Xvladqt\Faker\LoremFlickrProvider($faker));

        $this->validated($request, $plat);
        $plat = new Plat();
        $plat->Titre = $request->get('titre');
        $plat->Recette = $request->get('recette');
        $plat->Likes = $request->get('likes');
        $plat->Image = $faker->imageUrl($width = 320, $height = 240, ['dish']);
        $plat->user_id = Auth::id();
        $plat->save();

        return redirect()->route('plats.show', $plat);

    }

    public function edit(Request $request, Plat $plat)
    {
        if ($plat->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier ce plat car vous n\'êtes pas le créateur de ce dernier');
        }
        return view('edit', compact('plat'));
    }

    public function update(Request $request, Plat $plat)
    {
        $this->validated($request, $plat);
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
    private function validated(Request $request, Plat $plat)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'recette' => 'required|string|max:2048',
            'likes' => 'required|integer',]);
    }

}
