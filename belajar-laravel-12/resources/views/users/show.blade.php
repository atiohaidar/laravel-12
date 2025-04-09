<!DOCTYPE html>
<html>
<head>
    <title>Detail User</title>
</head>
<body>
    <h2>Detail User</h2>

    <div>
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Dibuat pada:</strong> {{ $user->created_at }}</p>
        <p><strong>Diupdate pada:</strong> {{ $user->updated_at }}</p>
    </div>

    <a href="{{ route('users.index') }}">Kembali ke Daftar User</a>
</body>
</html>