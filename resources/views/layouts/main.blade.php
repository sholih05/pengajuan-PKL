<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Monitoring | @yield('title')</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="{{ asset('assets') }}/img/SMK_KARBAK.png" rel="icon">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/SMK_KARBAK.png') }}">



    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets') }}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/remixicon/remixicon.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('assets/') }}/vendor/sweetalert2/sweetalert2.css">


    <!-- Template Main CSS File -->
    <link href="{{ asset('assets') }}/css/style.css" rel="stylesheet">

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
    @yield('css')
</head>

<body @if (auth()->check()) @if (in_array(auth()->user()->role, [3, 4, 5]))
        class="toggle-sidebar" @endif
    @endif>
    @auth

        <!-- ======= Header ======= -->
        <header id="header" class="header fixed-top d-flex align-items-center">

            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                    <!-- <img src="{{ asset('assets') }}/img/bgputih.png" alt=""> -->
                    <div style="
                        display: inline-block; 
                        position: relative; 
                        background-color: white; 
                        clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
                        padding: 1,5px;
                    ">
                        <img src="{{ asset('assets') }}/img/SMK_KARBAK.png" alt="Logo" style="display: block; width: 100%; height: auto;">
                    </div>

                    <span class="d-none d-lg-block">Monitoring</span>
                </a>
                @if (auth()->user()->role == 1 || auth()->user()->role == 2)
                    <i class="bi bi-list toggle-sidebar-btn"></i>
                @endif
            </div><!-- End Logo -->

            {{-- <div class="search-bar">
                <form class="search-form d-flex align-items-center" method="POST" action="#">
                    <input type="text" name="query" placeholder="Search" title="Enter search keyword">
                    <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                </form>
            </div><!-- End Search Bar --> --}}

            @include('layouts.include.head-nav')
        </header><!-- End Header -->
        @if (auth()->user()->role == 1 || auth()->user()->role == 2)
            <!-- ======= Sidebar ======= -->
            @include('layouts.include.sidebar')
        @endif
        <main id="main" class="main">

            @yield('pagetitle')
            <section class="section dashboard">
                @yield('content')
            </section>

        </main><!-- End #main -->

        <!-- Modal Upload Excel -->
        <div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadExcelModalLabel">Upload Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadExcelForm" enctype="multipart/form-data">
                            @csrf
                            @isset($tahunAkademikExcel)
                                <div class="mb-3">
                                    <label for="id_ta" class="form-label">Tahun Akademik</label>
                                    <select class="form-select" id="id_ta_excel" name="id_ta_excel" required>
                                        <option value="">Pilih</option>
                                        @foreach ($tahunAkademikExcel as $item)
                                            <option value="{{ $item->id_ta }}">{{ $item->tahun_akademik }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"> Tahun Akademik wajib dipilih. </div>
                                </div>
                            @endisset
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Pilih File Excel
                                    @isset($masterExcel)
                                        ( <a href="{{ $masterExcel }}">contoh</a> )
                                    @endisset
                                </label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file"
                                    accept=".xlsx,.xls,.csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                        <div id="uploadingMessage" class="mt-3" style="display:none;">
                            <p>Uploading...</p>
                        </div>
                        <div id="errorMessage" class="mt-3 text-danger" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Upload Excel -->

        <!-- ======= Footer ======= -->
        <footer id="footer" class="footer">
            <div class="copyright">
                &copy; Copyright <strong><span>Sistem Monitoring PKL</span></strong>. All Rights Reserved
            </div>
            <div class="credits" style="font-size: x-small;">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
        </footer><!-- End Footer -->

        <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>
    @else
        @yield('content')
    @endauth

    <script src="{{ asset('assets') }}/vendor/jquery/jquery-3.7.1.min.js"></script>
    <!-- Vendor JS Files -->
    <script src="{{ asset('assets') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/tinymce/tinymce.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/php-email-form/validate.js"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/') }}/vendor/sweetalert2/sweetalert2.min.js"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets') }}/js/main.js"></script>

    <script>
        $(document).ready(function() {
            // Set CSRF token in the AJAX request header
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}",
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: "error",
                title: "{{ session('error') }}",
            });
        @endif

        @if (session('swal_success'))
            Swal.fire(
                'Yeayy!',
                "{{ session('swal_success') }}",
                'success'
            );
        @endif

        @if (session('swal_error'))
            Swal.fire(
                'Aduhh!',
                "{{ session('swal_error') }}",
                'error'
            );
        @endif
    </script>

    <script>
        // Event click untuk tombol hapus notif
        function deleteNotif(id) {
            $.ajax({
                url: "{{ url('/deleteNotif') }}" + "/" + id,
                method: "GET",
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        var txt = $('#text-count-notif').text();
                        var txt = parseInt(txt) - 1;
                        $('#text-count-notif').text(txt);
                        $('#text-count-notif2').text(txt);
                        $('#lin' + id).hide();

                    } else {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                },
                error: function(response) {
                    // Handle error
                    Toast.fire({
                        icon: "error",
                        title: 'Woops! Fatal Error.'
                    });
                }
            });
        };
    </script>

    @yield('js')

</body>

</html>
