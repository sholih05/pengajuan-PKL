<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#data-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-tools"></i><span>PKL</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="data-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav" style="">
                <li>
                    <a href="{{ route('penempatan') }}">
                        <i class="bi bi-circle"></i><span>Penempatan</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link collapsed" href="{{ route('presensi') }}">
                        <i class="bi bi-circle"></i> <span>Presensi</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link collapsed" href="{{ route('kendala-saran') }}">
                        <i class="bi bi-circle"></i> <span>Kendala & Saran</span>
                    </a>
                </li>

                <li>
                    <a class="nav-link collapsed" href="{{ route('nilai-quesioner') }}">
                        <i class="bi bi-circle"></i> <span>Nilai Quisioner</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link collapsed" href="{{ route('pengajuan.surat.index') }}">
                        <i class="bi bi-circle"></i> <span>Pengajuan Surat</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link collapsed" href="{{ route('penilaian.index') }}">
                        <i class="bi bi-circle"></i> <span>Penilaian</span>
                    </a>
                </li>
                
            </ul>
        </li><!-- End PKL Nav -->


        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('siswa') }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Siswa</span>
            </a>
        </li><!-- End Siswa Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('guru') }}">
                <i class="bi bi-person-video3"></i>
                <span>Guru</span>
            </a>
        </li><!-- End Siswa Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#dudi-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-globe"></i><span>DUDI</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="dudi-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav" style="">
                <li>
                    <a href="{{ route('ketersediaan') }}">
                        <i class="bi bi-circle"></i><span>Ketersediaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dudi') }}">
                        <i class="bi bi-circle"></i><span>Dudi</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link collapsed" href="{{ route('instruktur') }}">
                        <i class="bi bi-circle"></i> <span>Instruktur</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Data Nav -->


        @if (auth()->user()->role == 1)
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#master-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-database"></i><span>Master</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="master-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav" style="">
                    <li>
                        <a href="{{ route('master.jurusan') }}">
                            <i class="bi bi-circle"></i><span>Jurusan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('master.tahun_akademik') }}">
                            <i class="bi bi-circle"></i><span>Tahun Akademik</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link collapsed" href="{{ route('master.quesioner') }}">
                            <i class="bi bi-circle"></i> <span>Quisioner</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link collapsed" href="{{ route('template-penilaian.index') }}">
                            <i class="bi bi-circle"></i> <span>Template Penilaian</span>
                        </a>
                    </li>
                </ul>
            </li><!-- End Master Nav -->
        @endif
    </ul>

</aside><!-- End Sidebar-->