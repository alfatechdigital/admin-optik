@extends('layouts.admin')
@section('title', 'Tambah Pasien')
@section('page-title', 'Tambah Pasien Baru')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-3">
                    <i class="bi bi-person-plus text-primary me-2"></i>Form Data Pasien
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('patients.store') }}" id="patientForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir"
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ old('nik') }}"
                                    placeholder="NIK">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}"
                                    placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. BPJS</label>
                                <input type="text" name="no_bpjs" class="form-control" value="{{ old('no_bpjs') }}"
                                    placeholder="Nomor BPJS">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe BPJS</label>
                                <select name="tipe_bpjs" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ old('tipe_bpjs') == '1' ? 'selected' : '' }}>Kelas 1</option>
                                    <option value="2" {{ old('tipe_bpjs') == '2' ? 'selected' : '' }}>Kelas 2</option>
                                    <option value="3" {{ old('tipe_bpjs') == '3' ? 'selected' : '' }}>Kelas 3</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Riwayat Penyakit</label>
                                <textarea name="riwayat_penyakit" class="form-control" rows="2"
                                    placeholder="Diabetes, hipertensi, alergi obat, dll...">{{ old('riwayat_penyakit') }}</textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i>Simpan Pasien
                            </button>
                            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('patientForm').addEventListener('submit', function (e) {
    const nikEl = document.querySelector('input[name="nik"]');
    const nik = nikEl ? nikEl.value.trim() : '';
    if (nik && !/^\d{16}$/.test(nik)) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'NIK Tidak Valid',
            text: 'Nomor Induk Kependudukan (NIK) harus terdiri dari 16 digit angka.',
            confirmButtonText: 'Baik, saya perbaiki'
        });
        nikEl.classList.add('is-invalid');
        nikEl.focus();
        return false;
    }
    if (nikEl) nikEl.classList.remove('is-invalid');
    
    const noBpjsEl = document.querySelector('input[name="no_bpjs"]');
    const no_bpjs = noBpjsEl ? noBpjsEl.value.trim() : '';
    if (no_bpjs && !/^\d{13}$/.test(no_bpjs)) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'No BPJS Tidak Valid',
            text: 'Nomor BPJS harus terdiri dari 13 digit angka.',
            confirmButtonText: 'Baik, saya perbaiki'
        });
        noBpjsEl.classList.add('is-invalid');
        noBpjsEl.focus();
        return false;
    }
    if (noBpjsEl) noBpjsEl.classList.remove('is-invalid');
    
    const telpEl = document.querySelector('input[name="no_hp"]');
    const telp = telpEl ? telpEl.value.trim() : '';
    if (telp && !/^\d{9,15}$/.test(telp)) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'No Telepon Tidak Valid',
            text: 'Nomor telepon harus berupa angka antara 9 hingga 15 digit.',
            confirmButtonText: 'Baik, saya perbaiki'
        });
        telpEl.classList.add('is-invalid');
        telpEl.focus();
        return false;
    }
    if (telpEl) telpEl.classList.remove('is-invalid');
});
</script>
@endpush