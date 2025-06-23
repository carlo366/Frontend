@extends('frontend.profile.layout.template')

@section('contentprofile')
    <!-- Right Side -->
    <div class="col-md-12">

        <div class="profile-card p-4 card shadow-sm rounded-4">
            <h5 class="mb-4 fw-semibold text-muted">Ganti Password</h5>

            {{-- Alert sukses --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Alert error --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


            <form method="POST" action="{{ route('users.changePassword') }}">
                @csrf
                <div class="row g-4">
                    <!-- Password Lama -->
                    <div class="col-12">
                        <label for="current_password" class="form-label text-muted">
                            <i class="bi bi-lock-fill me-2"></i> Password Lama
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                placeholder="Masukkan password lama" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="current_password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Baru -->
                    <div class="col-12">
                        <label for="new_password" class="form-label text-muted">
                            <i class="bi bi-lock-fill me-2"></i> Password Baru
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                placeholder="Masukkan password baru" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="new_password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="col-12">
                        <label for="new_password_confirmation" class="form-label text-muted">
                            <i class="bi bi-lock-fill me-2"></i> Konfirmasi Password Baru
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" placeholder="Konfirmasi password baru" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="new_password_confirmation">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
