<html >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
        </style>
    </head>
    <body>
<h1>Convertisseur</h1>
<form action="conv/argent">
<table>
    <tr>
        <td>DH</td><td><input type='number' name="mnt" /></td>
        <td>vers</td><td><select name="sy" >
            <option>dollar</option>
            <option>euro</option>
        </select></td>
    </tr>
    <tr>
    <td>Celcieuse</td><td><input type='number' /></td>
       
    </tr>
    <tr>
    <td><input type='submit' value="convertir" /></td>
    <td><input type='reset' value="Annuler" /></td>
    </tr>
</table>
</form>
    </body>
</html>