<div class="alert alert-danger d-flex align-items-start mt-3" role="alert">
    <i class="bi bi-exclamation-octagon-fill fs-5 me-3 mt-1 flex-shrink-0"></i>
    <div>
        <strong>Perhatian!</strong> Setelah akun dihapus, semua data dan informasi akan hilang secara permanen.
        Pastikan Anda sudah mengunduh data yang diperlukan sebelum melanjutkan.
    </div>
</div>

<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
    <i class="bi bi-trash3-fill me-1"></i> Hapus Akun Saya
</button>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true" @if ($errors->userDeletion->isNotEmpty()) data-bs-show="true" @endif>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">
                    <i class="bi bi-shield-exclamation me-2"></i>
                    Konfirmasi Hapus Akun
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Tindakan ini <strong class="text-danger">tidak dapat dibatalkan</strong>.
                        Semua data Anda akan dihapus secara permanen. Masukkan password Anda untuk melanjutkan.
                    </p>

                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-semibold">
                            Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill text-danger"></i></span>
                            <input id="delete_password" name="password" type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Masukkan password Anda" autofocus>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->userDeletion->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                modal.show();
            });
        </script>
    @endpush
@endif
