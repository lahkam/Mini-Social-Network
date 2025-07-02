@extends('layouts.default')
@section('content')
<h>Liste de Produits</h>

<div class="card">
              <div class="card-header">
                <h3 class="card-title">Simple Full Width Table</h3>

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
                      <th>Task</th>
                      <th>Progress</th>
                      <th style="width: 40px">Label</th>
                    </tr>
                  </thead>
                  <tbody>
                     @foreach($prds as $p)
    <tr>
        <td>{{$p->id}}</td>
        <td>{{$p->desg}}</td>
        <td>{{$p->prix}}</td>
        <td>{{$p->qte}}</td>
        
        <td>
            <form action="{{ route('produits.destroy', $p->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?')">Supprimer</button>
            </form>
        </td>
    </tr>

    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>

@stop
