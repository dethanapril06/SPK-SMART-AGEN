<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="row g-3">

        <div class="col-12">
            <label for="update_password_current_password" class="form-label fw-semibold">
                Password Saat Ini
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password" placeholder="Masukkan password saat ini">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <label for="update_password_password" class="form-label fw-semibold">
                Password Baru
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                <input id="update_password_password" name="password" type="password"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password" placeholder="Masukkan password baru">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Gunakan password yang panjang dan acak untuk keamanan akun.
            </div>
        </div>

        <div class="col-12">
            <label for="update_password_password_confirmation" class="form-label fw-semibold">
                Konfirmasi Password Baru
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password" placeholder="Ulangi password baru">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-12 d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-warning text-white">
                <i class="bi bi-arrow-repeat me-1"></i> Ubah Password
            </button>

            @if (session('status') === 'password-updated')
                <span class="badge bg-success fs-6 py-2 px-3">
                    <i class="bi bi-check-lg me-1"></i> Password diperbarui!
                </span>
            @endif
        </div>

    </div>
</form>
