@extends('layouts.default')
@section('content')

<h1>Liste des Utilisateurs</h1>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Liste des Utilisateurs</h3>
        <div class="card-tools">
            <ul class="pagination pagination-sm float-right">
                <li class="page-item"><a class="page-link" href="#">«</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">»</a></li>
            </ul>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Nom</th>
                    <th>Login</th>
                    <th>Create at</th>
                    <th style="width: 40px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($utilisateurs as $utilisateur)
                <tr>
                    <td>{{$utilisateur->id}}</td>
                    <td>{{$utilisateur->nom}}</td>
                    <td>{{$utilisateur->log}}</td>          
                    <td>{{$utilisateur->create_at}}</td>
                    <td>---------------</td>
                    <td>
                        <!-- Add your action buttons here -->
                        <form action="{{ route('utilisateurs.destroy', $utilisateur->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@stop
