<form action="{{ route('devis.import') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Fichier devis (Excel)</label>
    <input type="file" name="fichier" accept=".xlsx,.xls" required>

    <button type="submit">Importer le devis</button>
</form>
