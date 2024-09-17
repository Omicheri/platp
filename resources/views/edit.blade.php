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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Modifier
                        le plat : {{ $plat->Titre }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('plats.update', $plat) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="titre">Titre</label>
                                <input type="text" class="form-control" id="titre" name="titre" value="{{$plat->Titre }}">
                            </div>

                            <div class="form-group">
                                <label for="recette">Recette</label>
                                <textarea class="form-control" id="recette" name="recette">{{ $plat->Recette }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="likes">Likes</label>
                                <input type="number" class="form-control" id="likes" name="likes" value="{{ $plat->Likes }}">
                            </div>
                            <a href="{{ route('plats.index', $plat) }}">Retour</a>
                            <button type="submit" class="btn btn-primary">Sauvegarder</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
