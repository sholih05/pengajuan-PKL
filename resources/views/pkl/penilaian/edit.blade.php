@extends('layouts.main')
@section('title')
    Edit Penilaian PKL
@endsection
@section('pagetitle')
<div class="pagetitle">
    <h1>Edit Penilaian PKL</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">PKL</li>
            <li class="breadcrumb-item"><a href="{{ route('penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active">Edit Penilaian</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Data Siswa</h5>
        <div class="row mb-4">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">NIS</th>
                        <td>{{ $siswa->nis }}</td>
                    </tr>
                    <tr>
                        <th>Nama Siswa</th>
                        <td>{{ $siswa->nama }}</td>
                    </tr>
                    <tr>
                        <th width="30%">Jurusan</th>
                        <td>{{ $siswa->jurusan->jurusan ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <form action="{{ route('penilaian.update', $siswa->nis) }}" method="POST" id="penilaianEditForm">
            @csrf
            @method('PUT')

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <label for="projectpkl" class="form-label">Masukkan Judul Project PKL</label>
                    <input class="form-control" id="projectpkl" name="projectpkl"
                              value="{{ $projectpkl->projectpkl ?? '' }}"></input>
                </div>
            </div>

            <h5 class="card-title">Edit Form Penilaian</h5>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Petunjuk Edit Penilaian:</strong><br>
                • Pilih Ya/Tidak untuk setiap indikator penilaian<br>
                • Nilai yang sudah ada akan ditampilkan sebagai nilai default
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="60%">Indikator Penilaian</th>
                            <th width="20%">Ketercapaian (Ya/Tidak)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($templates as $template)
                            @foreach ($template->mainItems as $mainItem)
                                {{-- Level 1 - Main Indicator --}}
                                <tr>
                                    <td><strong>{{ $no++ }}</strong></td>
                                    <td>
                                        <strong>{{ $mainItem->indikator }}</strong>
                                    </td>
                                    <td>
                                        <input type="number"
                                            name="nilai[{{ $mainItem->id }}]"
                                            class="form-control main-indicator-value"
                                            readonly
                                            hidden
                                            data-main-id="{{ $mainItem->id }}"
                                            placeholder="Dihitung otomatis">
                                    </td>

                                </tr>

                                {{-- Level 2 - Sub Indicators --}}
                                @if ($mainItem->children->isNotEmpty())
                                    @foreach ($mainItem->children as $subItem)
                                        <tr>
                                            <td></td>
                                            <td class="ps-3">
                                                {{ $subItem->indikator }}
                                            </td>
                                            <td>
                                                @if ($subItem->level3Children->isNotEmpty())
                                                    {{-- Has Level 3, so this is calculated --}}
                                                    <input type="number"
                                                        class="form-control sub-indicator-value"
                                                        readonly
                                                        hidden
                                                        data-sub-id="{{ $subItem->id }}"
                                                        data-main-id="{{ $mainItem->id }}"
                                                        placeholder="Dihitung dari Level 3">
                                                @else
                                                    {{-- No Level 3, so this is assessed directly --}}
                                                    @php
                                                        $currentValue = $existingValues[$subItem->id] ?? null;
                                                    @endphp
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input nilai-radio sub-direct-assessment"
                                                               type="radio"
                                                               name="nilai-sub[{{ $subItem->id }}]"
                                                               value="1"
                                                               data-sub-id="{{ $subItem->id }}"
                                                               data-main-id="{{ $mainItem->id }}"
                                                               {{ $currentValue == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label">Ya</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input nilai-radio sub-direct-assessment"
                                                               type="radio"
                                                               name="nilai-sub[{{ $subItem->id }}]"
                                                               value="0"
                                                               data-sub-id="{{ $subItem->id }}"
                                                               data-main-id="{{ $mainItem->id }}"
                                                               {{ $currentValue == 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label">Tidak</label>
                                                    </div>
                                                @endif
                                            </td>

                                        </tr>

                                        {{-- Level 3 - Sub-Sub Indicators --}}
                                        @if ($subItem->level3Children->isNotEmpty())
                                            @foreach ($subItem->level3Children as $subSubItem)
                                                @php
                                                    $currentValue = $existingValues[$subSubItem->id] ?? null;
                                                @endphp
                                                <tr>
                                                    <td></td>
                                                    <td class="ps-5">
                                                        {{ $subSubItem->indikator }}
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input nilai-radio level3-assessment"
                                                                   type="radio"
                                                                   name="nilai-sub[{{ $subSubItem->id }}]"
                                                                   value="1"
                                                                   data-subsub-id="{{ $subSubItem->id }}"
                                                                   data-sub-id="{{ $subItem->id }}"
                                                                   data-main-id="{{ $mainItem->id }}"
                                                                   {{ $currentValue == 1 ? 'checked' : '' }}>
                                                            <label class="form-check-label">Ya</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input nilai-radio level3-assessment"
                                                                   type="radio"
                                                                   name="nilai-sub[{{ $subSubItem->id }}]"
                                                                   value="0"
                                                                   data-subsub-id="{{ $subSubItem->id }}"
                                                                   data-sub-id="{{ $subItem->id }}"
                                                                   data-main-id="{{ $mainItem->id }}"
                                                                   {{ $currentValue == 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label">Tidak</label>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <label for="catatan" class="form-label">Catatan Penilaian</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="4"
                              placeholder="Masukkan catatan atau komentar tambahan untuk penilaian ini...">{{ old('catatan', $catatanText) }}</textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-end">
                    <a href="{{ route('penilaian.show', $siswa->nis) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning" id="updateBtn">
                        <i class="bi bi-pencil-square"></i> Update Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Perubahan --}}
<div class="modal fade" id="confirmChangeModal" tabindex="-1" aria-labelledby="confirmChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmChangeModalLabel">Konfirmasi Perubahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan mengubah penilaian untuk siswa <strong>{{ $siswa->nama }}</strong>.</p>
                <p>Apakah Anda yakin ingin menyimpan perubahan ini?</p>
                <div id="changesList" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmUpdateBtn">Ya, Update Penilaian</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Store original values for comparison
    const originalValues = {};

    // Initialize original values and calculations
    $('.nilai-radio:checked').each(function() {
        const name = $(this).attr('name');
        originalValues[name] = $(this).val();
    });

    // Initial calculations
    calculateAllIndicators();

    // Function to update keterangan based on value
    function updateKeterangan(elementId, value) {
        const ketElement = $('#' + elementId);
        if (value === 1 || value === '1') {
            ketElement.removeClass('bg-secondary bg-danger').addClass('bg-success').text('Tercapai');
        } else if (value === 0 || value === '0') {
            ketElement.removeClass('bg-secondary bg-success').addClass('bg-danger').text('Tidak Tercapai');
        } else {
            ketElement.removeClass('bg-success bg-danger').addClass('bg-secondary').text('Menunggu');
        }
    }

    // Function to calculate Level 2 from Level 3
    function calculateLevel2(subId) {
        let total = 0;
        let count = 0;

        $(`input[data-sub-id="${subId}"].level3-assessment:checked`).each(function() {
            total += parseInt($(this).val());
            count++;
        });

        // Check if all level 3 items for this sub are assessed
        const totalLevel3Items = $(`input[data-sub-id="${subId}"].level3-assessment`).length / 2;

        if (count === totalLevel3Items) {
            const level2Value = (total === count) ? 1 : 0;
            $(`input[data-sub-id="${subId}"].sub-indicator-value`).val(level2Value);
            updateKeterangan(`ket-sub-${subId}`, level2Value);

            const mainId = $(`input[data-sub-id="${subId}"]`).first().data('main-id');
            calculateMainIndicator(mainId);
        } else {
            $(`input[data-sub-id="${subId}"].sub-indicator-value`).val('');
            updateKeterangan(`ket-sub-${subId}`, null);
        }
    }

    // Function to calculate Main Indicator from Sub Indicators
    function calculateMainIndicator(mainId) {
        let total = 0;
        let count = 0;

        // Check direct sub assessments
        $(`input[data-main-id="${mainId}"].sub-direct-assessment:checked`).each(function() {
            total += parseInt($(this).val());
            count++;
        });

        // Check calculated sub values
        $(`input[data-main-id="${mainId}"].sub-indicator-value`).each(function() {
            const val = $(this).val();
            if (val !== '') {
                total += parseInt(val);
                count++;
            }
        });

        // Check total sub indicators for this main
        const totalSubItems = $(`input[data-main-id="${mainId}"].sub-direct-assessment`).length / 2 +
                             $(`input[data-main-id="${mainId}"].sub-indicator-value`).length;

        if (count === totalSubItems) {
            const percentage = (count > 0) ? (total / count) * 100 : 0;
            $(`input[data-main-id="${mainId}"].main-indicator-value`).val(percentage.toFixed(0));

            if (percentage >= 80) {
                updateKeterangan(`ket-main-${mainId}`, 1);
            } else {
                updateKeterangan(`ket-main-${mainId}`, 0);
            }
        } else {
            $(`input[data-main-id="${mainId}"].main-indicator-value`).val('');
            updateKeterangan(`ket-main-${mainId}`, null);
        }
    }

    // Function to calculate all indicators
    function calculateAllIndicators() {
        // Calculate all Level 2 from Level 3
        const processedSubs = new Set();
        $('.level3-assessment:checked').each(function() {
            const subId = $(this).data('sub-id');
            if (!processedSubs.has(subId)) {
                calculateLevel2(subId);
                processedSubs.add(subId);
            }
        });

        // Calculate all Main indicators
        const processedMains = new Set();
        $('.sub-direct-assessment:checked, .sub-indicator-value').each(function() {
            const mainId = $(this).data('main-id');
            if (mainId && !processedMains.has(mainId)) {
                calculateMainIndicator(mainId);
                processedMains.add(mainId);
            }
        });
    }

    // Event handlers
    $('.level3-assessment').change(function() {
        const subSubId = $(this).data('subsub-id');
        const subId = $(this).data('sub-id');
        const value = $(this).val();

        updateKeterangan(`ket-subsub-${subSubId}`, value);
        calculateLevel2(subId);
    });

    $('.sub-direct-assessment').change(function() {
        const subId = $(this).data('sub-id');
        const mainId = $(this).data('main-id');
        const value = $(this).val();

        updateKeterangan(`ket-sub-${subId}`, value);
        calculateMainIndicator(mainId);
    });

    // Form submission with confirmation
    $('#penilaianEditForm').on('submit', function(e) {
        e.preventDefault();

        // Check for changes
        const changes = [];
        $('.nilai-radio:checked').each(function() {
            const name = $(this).attr('name');
            const currentValue = $(this).val();
            const originalValue = originalValues[name];

            if (originalValue !== currentValue) {
                const indicator = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                changes.push({
                    indicator: indicator,
                    from: originalValue == 1 ? 'Ya' : 'Tidak',
                    to: currentValue == 1 ? 'Ya' : 'Tidak'
                });
            }
        });

        if (changes.length > 0) {
            // Show confirmation modal with changes
            let changesList = '<div class="alert alert-warning"><strong>Perubahan yang akan disimpan:</strong><ul>';
            changes.forEach(function(change) {
                changesList += `<li>${change.indicator}: ${change.from} → ${change.to}</li>`;
            });
            changesList += '</ul></div>';

            $('#changesList').html(changesList);
            $('#confirmChangeModal').modal('show');
        } else {
            // No changes, submit directly
            this.submit();
        }
    });

    // Confirm update button
    $('#confirmUpdateBtn').click(function() {
        $('#confirmChangeModal').modal('hide');
        $('#penilaianEditForm')[0].submit();
    });

    // Initial calculation on page load
    calculateAllIndicators();
});
</script>
@endsection