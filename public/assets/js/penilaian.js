$(function () {
    function getNilaiKeterangan(nilai) {
        if (nilai >= 0 && nilai <= 55) {
            return 'D (Perlu Perbaikan)';
        } else if (nilai >= 56 && nilai <= 70) {
            return 'C (Cukup Baik)';
        } else if (nilai >= 71 && nilai <= 85) {
            return 'B (Baik)';
        } else if (nilai >= 86 && nilai <= 100) {
            return 'A (Sangat Baik)';
        }
    }

    function hitungIndikatorUtama() {
        // Untuk setiap input indikator utama (readonly)
        $('input[type="number"][data-indikator]').each(function () {
            var parentInput = $(this);
            var indikatorId = parentInput.data('indikator');

            // Ambil semua radio yang punya data-parent = indikatorId
            var $children = $(`input[type="radio"][data-parent="${indikatorId}"]`);
            var grouped = {};

            // Group radio berdasarkan name
            $children.each(function () {
                var name = $(this).attr('name');
                if (!grouped[name]) grouped[name] = [];
                grouped[name].push($(this));
            });

            var total = 0;
            var yaCount = 0;

            // Hitung yang dipilih
            for (var group in grouped) {
                total++;
                var checked = grouped[group].find(r => r.is(':checked'));
                if (checked && checked.val() === '1') {
                    yaCount++;
                }
            }

            var nilai = total > 0 ? Math.round((yaCount / total) * 100) : 0;
            parentInput.val(nilai);
            $('#ket-' + indikatorId).text(getNilaiKeterangan(nilai));
        });
    }

    // Event: Ketika radio button diubah
    $(document).on('change', 'input[type="radio"][data-parent]', function () {
        hitungIndikatorUtama();
    });

    // Event: Ketika nilai indikator utama diubah manual
    $('input[type="number"][data-indikator]').on('input change', function () {
        var nilai = $(this).val();
        var indikatorId = $(this).data('indikator');

        if ($(this).is('input[type="number"]')) {
            // Validasi nilai 0 - 100
            if (nilai < 0) {
                $(this).val(0);
                nilai = 0;
            } else if (nilai > 100) {
                $(this).val(100);
                nilai = 100;
            }
        }

        if (nilai !== '') {
            $('#ket-' + indikatorId).text(getNilaiKeterangan(nilai));
        } else {
            $('#ket-' + indikatorId).text('-');
        }
    });

    // Trigger awal untuk menghitung jika ada isian sebelumnya
    hitungIndikatorUtama();

    // Validasi sebelum submit
    $('#penilaianForm').on('submit', function (e) {
        var valid = true;

        $('input[type="number"][data-indikator]').each(function () {
            var nilai = $(this).val();
            if (nilai === '' || isNaN(nilai)) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Silakan isi semua nilai indikator penilaian.');
        }
    });
});