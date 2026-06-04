<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="row g-3">

        <div class="col-12">
            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input id="name" name="name" type="text"
                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}"
                    required autofocus autocomplete="name" placeholder="Masukkan nama lengkap">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <label for="email" class="form-label fw-semibold">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input id="email" name="email" type="email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}"
                    required autocomplete="username" placeholder="Masukkan alamat email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12 d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save-fill me-1"></i> Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <span class="badge bg-success fs-6 py-2 px-3">
                    <i class="bi bi-check-lg me-1"></i> Tersimpan!
                </span>
            @endif
        </div>

    </div>
</form>
