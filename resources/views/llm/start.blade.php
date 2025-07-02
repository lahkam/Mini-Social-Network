<form action="{{ route('llm.index') }}" method="get">
    @csrf
    <div>
        <label for="inputText">Votre texte :</label><br>
        <textarea id="inputText" name="prompt" rows="6" cols="50"></textarea>
    </div>
    <div>
        <button type="submit">Envoyer</button>
    </div>
</form>