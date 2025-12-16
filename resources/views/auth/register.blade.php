@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Name --}}
                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" required
                                        autofocus>
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="row mb-3">
                                <label for="role" class="col-md-4 col-form-label text-md-end">Role</label>
                                <div class="col-md-6">
                                    <!-- Fixed: Changed name from 'roles' to 'role' -->
                                    <select id="role" name="role" class="form-select" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="siswa">Siswa</option>
                                        <option value="guru">Guru</option>
                                    </select>
                                </div>
                            </div>

                            {{-- NIS --}}
                            <div id="nis-field" class="row mb-3" style="display: none;">
                                <label for="nis" class="col-md-4 col-form-label text-md-end">NIS</label>
                                <div class="col-md-6">
                                    <input id="nis" type="text" class="form-control" name="nis">
                                </div>
                            </div>

                            {{-- NIP --}}
                            <div id="nip-field" class="row mb-3" style="display: none;">
                                <label for="nip" class="col-md-4 col-form-label text-md-end">NIP</label>
                                <div class="col-md-6">
                                    <input id="nip" type="text" class="form-control" name="nip">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" required>
                                </div>
                            </div>

                            {{-- No Telp --}}
                            <div class="row mb-3">
                                <label for="no_telp" class="col-md-4 col-form-label text-md-end">No Telepon</label>
                                <div class="col-md-6">
                                    <input id="no_telp" type="text" class="form-control" name="no_telp" required>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="row mb-3">
                                <label for="alamat" class="col-md-4 col-form-label text-md-end">Alamat</label>
                                <div class="col-md-6">
                                    <textarea id="alamat" class="form-control" name="alamat" required></textarea>
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="row mb-3">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirm
                                    Password</label>
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fixed: Changed 'roles' to 'role' in JavaScript
        const rolesSelect = document.getElementById('role');
        const nisField = document.getElementById('nis-field');
        const nipField = document.getElementById('nip-field');

        function toggleFields() {
            // Fixed: Changed 'selectedRoles' to 'selectedRole'
            const selectedRole = rolesSelect.value;
            nisField.style.display = (selectedRole === 'siswa') ? 'flex' : 'none';
            nipField.style.display = (selectedRole === 'guru') ? 'flex' : 'none';
        }

        rolesSelect.addEventListener('change', toggleFields);
        toggleFields(); // inisialisasi saat halaman dibuka
    });
</script>