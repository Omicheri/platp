@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <h1>Créer un nouveau plat</h1>
        <form action="{{ route('plats.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" name="titre" id="titre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="recette">Recette</label>
                <textarea name="recette" id="recette" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="likes">Likes</label>
                <input type="number" name="likes" id="likes" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="{{ route('plats.index', $plat) }}">Retour</a>
        </form>
    </div>
@endsection
