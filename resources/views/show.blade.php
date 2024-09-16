@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{$plat->Titre }}</div>

                    <div class="card-body">
                        <p><strong>Recette :</strong> {{ $plat->Recette }}</p>
                        <p><strong>Likes :</strong> {{ $plat->Likes }}</p>
                        <p><strong>Créateur :</strong> {{ $plat->user->name }}</p>
                    </div>
                    <button><a href="{{ route('plats.index', $plat) }}">Retour</a></button>
                </div>
            </div>
        </div>
    </div>
@endsection
