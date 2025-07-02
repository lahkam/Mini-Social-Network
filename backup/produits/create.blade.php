<form action="{{ route('produits.store') }}" method="POST">
    @csrf
    <div>
        <label for="desg">Désignation :</label>
        <input type="text" id="desg" name="desg" required>
    </div>
    <div>
        <label for="prix">Prix :</label>
        <input type="number" id="prix" name="prix" step="0.01" required>
    </div>
    <div>
        <label for="qte">Quantité :</label>
        <input type="number" id="qte" name="qte" required>
    </div>
    <button type="submit">Ajouter</button>
</form>