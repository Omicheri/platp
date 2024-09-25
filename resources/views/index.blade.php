@extends('layouts.app')

@section('content')
    @if (session('admin'))
        <div class="alert alert-danger">
            {{ session('admin') }}
        </div>
    @endif
    @if (session('isfav'))
        <div class="alert alert-success">
            {{ session('isfav') }}
        </div>
    @endif
    @if (session('notfav'))
        <div class="alert alert-danger">
            {{ session('notfav') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('dest'))
        <div class="alert alert-danger">
            {{ session('dest') }}
        </div>
    @endif

    <!-- Formulaire de recherche -->
    <form action="{{ route('plats.index') }}" method="GET">
        <input type="text" name="search" placeholder="Rechercher un plat ou un créateur"
               value="{{ request('search') }}">
        <button type="submit">Rechercher</button>
    </form>

    <table>
        <thead>
        <tr>
            <!--fusion de search sort et direction dans un tableau avec arraymerge  recuperant les parametre actuel de l'ulr avec request -->
            <th>Id_Plat
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => 'asc'])) }}">🔼</a>
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => 'desc'])) }}">🔽</a>
            </th>
            <th>Titre
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'Titre', 'direction' => 'asc'])) }}">🔼</a>
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'Titre', 'direction' => 'desc'])) }}">🔽</a>
            </th>
            <th>Likes
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'Likes', 'direction' => 'desc'])) }}">🔼</a>
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'Likes', 'direction' => 'asc'])) }}">🔽</a>
            </th>
            <th>Image</th>
            <th>Créateur
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'user_id', 'direction' => 'asc'])) }}">🔼</a>
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'user_id', 'direction' => 'desc'])) }}">🔽</a>
            </th>
            <th>Favori
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'is_favori', 'direction' => 'desc'])) }}">🔼</a>
                <a href="{{ route('plats.index', array_merge(request()->query(), ['sort' => 'is_favori', 'direction' => 'asc'])) }}">🔽</a>
            </th>
            <th></th>
            <th><a href="{{route('plats.create')}}">Create</a></th>
            <th></th>
            <th><a href="{{route('topcrea')}}">Classement</a></th>
        </tr>
        </thead>
        <tbody>
        @if($plats->isEmpty())
            <tr>Il n'y a pas de plat enregistré</tr>
        @endif
        @foreach($plats as $plat)
            <tr>
                <td>{{$plat->id}}</td>
                <td>{{ $plat->Titre }}</td>

                <td>{{ $plat->Likes }}</td>
                <td>
                    @if($plat->Image)
                        <img src="{{ $plat->Image }}" alt="{{ $plat->Titre }}">
                    @else
                        Pas d'image

                    @endif
                </td>
                <td>{{ $plat->user->name }}</td>
                <td>

                    @if(auth()->user()->favoris()->where('plat_id', $plat->id)->exists())
                        Oui
                    @else
                        Non
                    @endif
                </td>
                <td>
                    <a href="{{ route('plats.show', $plat) }}">Voir</a>
                    <a href="{{ route('plats.edit', $plat) }}">Modifier</a>

                    <form action="{{ route('plats.destroy', $plat) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('toggle.favori', $plat) }}" method="POST">
                        @csrf
                        <button type="submit">
                            @if(auth()->user()->favoris()->where('plat_id', $plat->id)->exists())
                                Retirer des favoris

                            @else
                                Ajouter aux favoris

                            @endif

                        </button>
                    </form>
                </td>

            </tr>
        @endforeach

        </tbody>


    </table>
    <!-- ajout les parametres actuelle à chaque lien de pagination généré -->
    <nav aria-label="Page navigation">{{ $plats->appends(request()->query())->links() }}</nav>
@endsection

