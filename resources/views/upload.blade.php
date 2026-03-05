<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
</head>
<body>

<h2>Laravel Redis Queue Upload</h2>

<form action="/upload-file" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file">
    <button type="submit">Upload</button>
</form>

</body>
</html>
