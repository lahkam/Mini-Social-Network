<?php

namespace App\Http\Controllers;

use App\Models\produit;
use Illuminate\Http\Request;

class ControllerProduct extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ps=produit::all();
        return view('produits/index',['prds'=>$ps]);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('produits/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'desg' => 'required|string|max:255',
            'prix' => 'required|numeric',
            'qte' => 'required|numeric',
        ]);

        // Create and save the new product
        $produit = new produit();
        $produit->desg = $validated['desg'];
        $produit->prix = $validated['prix'];
        $produit->qte = $validated['qte'] ;
        $produit->save();

        // Redirect to the product index page with a success message
        return redirect()->route('produits.index')->with('success', 'Produit ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(produit $produit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(produit $produit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, produit $produit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(produit $produit)
    {
        //
        $produit->delete();
        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }
}
