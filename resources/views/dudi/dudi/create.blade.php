@extends('layouts.main')
@section('title')
    Dudi
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Dudi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Dudi</li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Tambah Data Dudi</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="container mt-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dudi.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama DUDI</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="30">
                        <div class="invalid-feedback">
                            Nama wajib diisi dan maksimal 30 karakter.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nama_pimpinan" class="form-label">Nama Pimpinan</label>
                        <input type="text" class="form-control @error('nama_pimpinan') is-invalid @enderror" id="nama_pimpinan" name="nama_pimpinan" value="{{ old('nama_pimpinan') }}" required maxlength="50">
                        <div class="invalid-feedback">
                            Nama wajib diisi dan maksimal 50 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="no_kontak" class="form-label">Nomor Kontak</label>
                        <input type="number" class="form-control @error('no_kontak') is-invalid @enderror" id="no_kontak" name="no_kontak" value="{{ old('no_kontak') }}" required maxlength="14">
                        <div class="invalid-feedback">
                            Nomor kontak wajib diisi dan maksimal 14 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" maxlength="100" required>{{ old('alamat') }}</textarea>
                        <div class="invalid-feedback">
                            Alamat maksimal 100 karakter.
                        </div>
                    </div>

                    <div id="map" style="height: 400px;"></div>

                    <div class="mb-3 mt-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitude1" readonly required>
                        <input type="hidden" id="latitude" name="latitude">
                    </div>

                    <div class="mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitude1" readonly required>
                        <input type="hidden" id="longitude" name="longitude">
                    </div>

                    <div class="mb-3">
                        <label for="radius" class="form-label">Radius (meter)</label>
                        <input type="number" class="form-control" id="radius" name="radius" placeholder="Masukkan radius dalam meter" required value="8">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script src="{{ asset('assets') }}/vendor/leaflet/leaflet.js"></script>
<script>
    // Inisialisasi peta
    var map = L.map('map').setView([-6.982814303476982, 109.13654360065006], 13); // Koordinat awal (Surabaya)

    // Tile Layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker;
    var radiusCircle;

    // Event klik di peta untuk mendapatkan latitude dan longitude
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Tampilkan marker di lokasi yang diklik
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);

        // Masukkan koordinat ke input field
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('latitude1').value = lat;
        document.getElementById('longitude1').value = lng;

        // Tambahkan lingkaran radius jika radius diinput
        var radius = document.getElementById('radius').value;
        if (radius) {
            // Hapus lingkaran radius sebelumnya jika ada
            if (radiusCircle) {
                map.removeLayer(radiusCircle);
            }
            radiusCircle = L.circle([lat, lng], {
                color: 'blue',
                fillColor: '#cce5ff',
                fillOpacity: 0.4,
                radius: parseFloat(radius) // Radius dalam meter
            }).addTo(map);
        }
    });

    // Event perubahan pada input radius
    document.getElementById('radius').addEventListener('input', function() {
        var radius = this.value;
        if (marker) {
            // Hapus lingkaran radius sebelumnya jika ada
            if (radiusCircle) {
                map.removeLayer(radiusCircle);
            }
            radiusCircle = L.circle([marker.getLatLng().lat, marker.getLatLng().lng], {
                color: 'blue',
                fillColor: '#cce5ff',
                fillOpacity: 0.4,
                radius: parseFloat(radius) // Radius dalam meter
            }).addTo(map);
        }
    });
</script>

@endsection
@section('css')
    <link href="{{ asset('assets') }}/vendor/leaflet/leaflet.css" rel="stylesheet">
@endsection
