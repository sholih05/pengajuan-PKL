@extends('layouts.main')
@section('title')
    Edit Dudi
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Edit DUDI</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Dudi</li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Edit Data DUDI</h5>
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

                <form action="{{ route('dudi.update', $dudi->id_dudi) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama DUDI</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $dudi->nama) }}" required maxlength="30">
                        <div class="invalid-feedback">
                            Nama wajib diisi dan maksimal 30 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nama_pimpinan" class="form-label">Nama Pimpinan</label>
                        <input type="text" class="form-control @error('nama_pimpinan') is-invalid @enderror" id="nama_pimpinan" name="nama_pimpinan" value="{{ old('nama_pimpinan', $dudi->nama_pimpinan) }}" required maxlength="50">
                        <div class="invalid-feedback">
                            Nama pimpinan wajib diisi dan maksimal 50 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="no_kontak" class="form-label">Nomor Kontak</label>
                        <input type="number" class="form-control @error('no_kontak') is-invalid @enderror" id="no_kontak" name="no_kontak" value="{{ old('no_kontak', $dudi->no_kontak) }}" required maxlength="14">
                        <div class="invalid-feedback">
                            Nomor kontak wajib diisi dan maksimal 14 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" maxlength="100" required>{{ old('alamat', $dudi->alamat) }}</textarea>
                        <div class="invalid-feedback">
                            Alamat maksimal 100 karakter.
                        </div>
                    </div>

                    <!-- Map to select coordinates -->
                    <div id="map" style="height: 400px;"></div>

                    <div class="mb-3 mt-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitude1" value="{{ old('latitude', $dudi->latitude) }}" readonly required>
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $dudi->latitude) }}">
                    </div>

                    <div class="mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitude1" value="{{ old('longitude', $dudi->longitude) }}" readonly required>
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $dudi->longitude) }}">
                    </div>

                    <div class="mb-3">
                        <label for="radius" class="form-label">Radius (meter)</label>
                        <input type="number" class="form-control" id="radius" name="radius" placeholder="Masukkan radius dalam meter" required value="{{ old('radius', $dudi->radius) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script src="{{ asset('assets') }}/vendor/leaflet/leaflet.js"></script>
<script>
    // Inisialisasi peta dengan koordinat yang ada
    var map = L.map('map').setView([{{ $dudi->latitude }}, {{ $dudi->longitude }}], 13);

    // Tile Layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker = L.marker([{{ $dudi->latitude }}, {{ $dudi->longitude }}]).addTo(map);
    var radiusCircle;

    // Fungsi untuk menggambar ulang lingkaran radius
    function updateRadius(lat, lng, radius) {
        if (radiusCircle) {
            map.removeLayer(radiusCircle);
        }
        radiusCircle = L.circle([lat, lng], {
            color: 'blue',
            fillColor: '#cce5ff',
            fillOpacity: 0.4,
            radius: radius // Radius dalam meter
        }).addTo(map);
    }

    // Inisialisasi lingkaran radius awal
    updateRadius({{ $dudi->latitude }}, {{ $dudi->longitude }}, {{ $dudi->radius }}); // Default 100 meter

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

        // Update lingkaran radius berdasarkan lokasi baru
        var radius = parseFloat(document.getElementById('radius').value || 8);
        updateRadius(lat, lng, radius);
    });

    // Event perubahan pada input radius
    document.getElementById('radius').addEventListener('input', function() {
        var radius = parseFloat(this.value || 8); // Default radius 100 meter
        var lat = parseFloat(document.getElementById('latitude').value || {{ $dudi->latitude }});
        var lng = parseFloat(document.getElementById('longitude').value || {{ $dudi->longitude }});
        updateRadius(lat, lng, radius);
    });
</script>

@endsection

@section('css')
    <link href="{{ asset('assets') }}/vendor/leaflet/leaflet.css" rel="stylesheet">
@endsection
