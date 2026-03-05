<form method="POST" action="/upload-import" enctype="multipart/form-data">
    @csrf

    File:
    <input type="file" name="file">

    Run Time:
    <input type="datetime-local" name="run_at">

    <button type="submit">Upload</button>
</form>
