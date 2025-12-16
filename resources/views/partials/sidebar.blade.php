<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mt-3">
            <img src="{{ URL::asset($pengaturan->logo ?? 'assets/img/logo.png') ?? 'https://akupintar.id/documents/20143/0/default_logo_sekolah_pintar.png/9e3fd3b1-ee82-c891-4cd7-1e494ff374b8?version=2.0&t=1591343449773&imagePreview=1' }}"
                alt="" style="width: 50px">
            <a href="">{{ $pengaturan->name ?? config('app.name') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">{{ strtoupper(substr(config('app.name'), 0, 2)) }}</a>
        </div>

        {{-- <--ADMIN--> --}}
        <ul class="sidebar-menu">
            @if (Auth::check() && Auth::user()->role == 'admin')
                <li class="{{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('admin.dashboard') }}"><i class="fas fa-columns"></i> <span>Dashboard</span></a>
                </li>
                <li class="menu-header">Master Data</li>

                <li class="{{ request()->routeIs('jurusan.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('jurusan.index') }}"><i class="fas fa-book"></i> <span>Jurusan</span></a></li>

                <li class="{{ request()->routeIs('mapel.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('mapel.index') }}"><i class="fas fa-book"></i> <span>Mata Pelajaran</span></a>
                </li>
                <li class="{{ request()->routeIs('guru.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('guru.index') }}"><i class="fas fa-user"></i> <span>Guru</span></a></li>

                <li class="{{ request()->routeIs('kelas.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('kelas.index') }}"><i class="far fa-building"></i> <span>Kelas</span></a></li>

                <li class="{{ request()->routeIs('siswa.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('siswa.index') }}"><i class="fas fa-users"></i> <span>Siswa</span></a></li>

                <li class="{{ request()->routeIs('jadwal.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('jadwal.index') }}"><i class="fas fa-calendar"></i> <span>Jadwal</span></a></li>

                <li class="{{ request()->routeIs('user.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('user.index') }}"><i class="fas fa-user"></i> <span>User</span></a></li>

                <li class="{{ request()->routeIs('admin.grades.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('admin.grades.index') }}"><i class="fas fa-chart-bar"></i> <span>Manajemen Nilai</span></a>
                </li>

                <li class="{{ request()->routeIs('pengumuman-sekolah.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('pengumuman-sekolah.index') }}"><i class="fas fa-bullhorn"></i>
                        <span>Pengumuman</span></a></li>

                <li class="{{ request()->routeIs('pengaturan.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('pengaturan.index') }}"><i class="fas fa-cog"></i> <span>Pengaturan</span></a>
                </li>

                {{-- <--GURU--> --}}
            @elseif (Auth::check() && Auth::user()->role == 'guru')
                <li class="{{ request()->routeIs('guru.dashboard.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('guru.dashboard') }}"><i class="fas fa-columns"></i> <span>Dashboard</span></a>
                </li>
                <li class="menu-header">Master Data</li>
                <li class="{{ request()->routeIs('materi.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('materi.index') }}"><i class="fas fa-book"></i> <span>Materi</span></a></li>
                <li class="{{ request()->routeIs('tugas.*') ? 'active' : '' }}"><a class="nav-link"
                        href="{{ route('tugas.index') }}"><i class="fas fa-list"></i> <span>Tugas</span></a></li>
                {{-- <li class="{{ request()->is('guru/siswa') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guru.siswa') }}">
                        <i class="fas fa-users"></i><span>Siswa Saya</span>
                    </a>
                </li> --}}
                <li class="{{ request()->is('absensi*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('absensi.index') }}">
                        <i class="fas fa-calendar-check"></i><span>Absensi</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('banksoal.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('banksoal.index') }}">
                        <i class="fas fa-question-circle"></i><span>Bank Soal</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('kuis_ujian.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('kuis_ujian.index') }}">
                        <i class="fas fa-laptop-code"></i><span>Kuis & Ujian</span>
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('grades.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('grades.index') }}">
                        <i class="fas fa-chart-bar"></i><span>Manajemen Nilai</span>
                    </a>
                </li>
            @elseif (Auth::check() && Auth::user()->role == 'guru_super')
                <li class="{{ request()->routeIs('guru_super.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guru_super.dashboard') }}">
                        <i class="fas fa-columns"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('materi.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('materi.index') }}">
                        <i class="fas fa-book"></i> <span>Kelola Materi</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('tugas.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('tugas.index') }}">
                        <i class="fas fa-list"></i> <span>Kelola Tugas</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.index') }}">
                        <i class="fas fa-user"></i> <span>Lihat Siswa</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('guru.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guru.index') }}">
                        <i class="fas fa-user-tie"></i> <span>Lihat Guru</span>
                    </a>
                </li>

                {{-- <--SISWA--> --}}
            @elseif (Auth::check() && Auth::user()->role == 'siswa')
                <li class="{{ request()->routeIs('siswa.dashboard.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.dashboard') }}">
                        <i class="fas fa-columns"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('siswa.materi') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.materi') }}">
                        <i class="fas fa-book"></i> <span>Materi</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('siswa.tugas') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.tugas') }}">
                        <i class="fas fa-list"></i> <span>Tugas</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('absensi.siswa.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('absensi.siswa.index') }}">
                        <i class="fas fa-calendar-check"></i> <span>Absensi</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('siswa.kuis.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.kuis.index') }}">
                        <i class="fas fa-laptop-code"></i><span>Kuis</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('siswa.ujian.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.ujian.index') }}">
                        <i class="fas fa-pen-square"></i><span>Ujian</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('siswa.grades.report') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('siswa.grades.report') }}">
                        <i class="fas fa-chart-bar"></i><span>Nilai Saya</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>