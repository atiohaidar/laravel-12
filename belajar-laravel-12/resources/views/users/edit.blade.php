<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT') {{-- Penting untuk method update --}}
        <div>
            <label for="name">Nama:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div>
            <label for="password">Password Baru (kosongkan jika tidak ingin mengubah):</label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <label for="password_confirmation">Konfirmasi Password Baru:</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        <button type="submit">Update</button>
        <a href="{{ route('users.index') }}">Batal</a>
    </form>
</body>
</html>