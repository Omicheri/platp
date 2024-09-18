@extends('layouts.app')

@section('content')

    <h1>Top Créateur</h1>
    <table>
        <thead>
        <tr>
            <th>Position</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Nombre de Likes</th>
        </tr>
        </thead>
        <tbody>
        @if($users->isEmpty())
            <tr>
                <td colspan="4">Il n'y a pas de créateurs enregistrés</td>
            </tr>
        @else
            @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->total_likes }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>

    </table>
    <a href="{{ route('plats.index') }}">Retour</a>
@endsection
