<?php

namespace App\Http\Controllers;

use App\Models\utilisateur;
use Illuminate\Http\Request;
use App\Services\IUtilisateurService;
class UtilisateurController extends Controller
{
    private $utilisateurService;

    public function __construct(IUtilisateurService $utilisateurService)
    {
        $this->utilisateurService = $utilisateurService;
    }
    public function index()
    {
        $us = $this->utilisateurService->getAllUtilisateurs();
        return view('utilisateurs.index', [
            'utilisateurs' => $us
        ]);
    }

    public function create()
    {
        return view('utilisateurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'log' => 'required|string|max:255',
            'pass' => 'required|string|max:255',
        ]);
        $utilisateur = new utilisateur();
        $utilisateur->nom = $request->input('nom');
        $utilisateur->log = $request->input('log');
        $utilisateur->pass = $request->input('pass');
        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(utilisateur $utilisateur)
    {
        return view('utilisateurs.show', [
            'utilisateur' => $this->utilisateurService->getUtilisateurById($utilisateur->id)
        ]);
    }

    public function edit(utilisateur $utilisateur)
    {
        return view('utilisateurs.edit', [
            'utilisateur' => $this->utilisateurService->getUtilisateurById($utilisateur->id)
        ]);
    }

    public function update(Request $request, utilisateur $utilisateur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'log' => 'required|string|max:255',
            'pass' => 'required|string|max:255',
        ]);
       
        $data = [
            'nom' =>  $request->input('nom'),
            'log' => $request->input('log'),
            'pass' => $request->input('pass'),
        ];
        $this->utilisateurService->updateUtilisateur($utilisateur->id, $data);
        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(utilisateur $utilisateur)
    {
        //
    }
}
