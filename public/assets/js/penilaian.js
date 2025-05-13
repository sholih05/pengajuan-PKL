/**
 * JavaScript untuk form penilaian
 */
$(function() {
    // Fungsi untuk mendapatkan keterangan nilai
    function getNilaiKeterangan(nilai) {
        if (nilai >= 90) {
            return 'Sangat Baik';
        } else if (nilai >= 80) {
            return 'Baik';
        } else if (nilai >= 70) {
            return 'Cukup';
        } else if (nilai >= 60) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }
    
    // Event handler untuk input nilai
    $('.nilai-input').on('input', function() {
        var nilai = $(this).val();
        var indikatorId = $(this).data('indikator');
        
        // Validasi nilai
        if (nilai < 0) {
            $(this).val(0);
            nilai = 0;
        } else if (nilai > 100) {
            $(this).val(100);
            nilai = 100;
        }
        
        // Update keterangan
        if (nilai) {
            $('#ket-' + indikatorId).text(getNilaiKeterangan(nilai));
        } else {
            $('#ket-' + indikatorId).text('-');
        }
    });
    
    // Trigger input event untuk nilai yang sudah ada
    $('.nilai-input').trigger('input');
    
    // Validasi form sebelum submit
    $('#penilaianForm').on('submit', function(e) {
        var valid = true;
        
        // Cek semua input nilai
        $('.nilai-input').each(function() {
            var nilai = $(this).val();
            
            if (!nilai) {
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