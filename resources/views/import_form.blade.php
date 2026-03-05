<form method="POST" action="{{ route('import.upload') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Upload CSV</button>
</form>
