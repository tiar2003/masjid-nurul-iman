<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Informasi kegiatan, jadwal khutbah, dan layanan Masjid Nurul Iman Krapyak, Semarang.">
    <meta name="theme-color" content="#123d34">
    <title>Masjid Nurul Iman | Krapyak, Semarang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .management-public-section { background: #f5f2e9; }
        .management-public-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        .management-public-group { padding: 22px; border: 1px solid rgba(18, 61, 52, .14); background: rgba(255, 255, 255, .72); }
        .management-public-group h3 { margin: 0 0 12px; color: #123d34; font-size: .76rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .management-public-group ul { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; color: #263a34; font-size: .92rem; line-height: 1.45; }
        @media (max-width: 800px) { .management-public-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 520px) { .management-public-grid { grid-template-columns: 1fr; } .management-public-group { padding: 18px; } }
    </style>
</head>
<body class="public-site font-sans antialiased">
    @php
        $heroImagePath = $galleries->first()?->image_path ?? 'galleries/0vr9RUWOFKF8Ycoo0ID34lXFWeydYTNJ6Hqha7Iy.jpg';
        $nextKhutbah = $jadwalKhutbah->first();
    @endphp

    <header class="site-header" x-data="{ menuOpen: false }" @keydown.escape.window="menuOpen = false">
        <nav class="site-nav" aria-label="Navigasi utama">
            <a class="site-brand" href="#beranda" aria-label="Masjid Nurul Iman, beranda">
                <img src="{{ asset('logo-masjid.png') }}" alt="" class="site-brand__logo">
                <span class="site-brand__name">Masjid <strong>Nurul Iman</strong><small>Krapyak, Semarang</small></span>
            </a>

            <button class="site-menu-toggle" type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" :aria-label="menuOpen ? 'Tutup menu' : 'Buka menu'" aria-controls="site-navigation-links">
                <span></span><span></span>
            </button>

            <div id="site-navigation-links" class="site-nav__links" :class="{ 'is-open': menuOpen }">
                <a href="#jadwal" @click="menuOpen = false">Jadwal</a>
                @if ($ramadanSchedules->isNotEmpty())
                    <a href="#agenda" @click="menuOpen = false">Agenda</a>
                @endif
                <a href="#kegiatan" @click="menuOpen = false">Kegiatan</a>
                @if ($managementMembers->isNotEmpty())
                    <a href="#pengurus" @click="menuOpen = false">Pengurus</a>
                @endif
                <a href="#keuangan" @click="menuOpen = false">Kotak amal</a>
                <a class="site-nav__admin" href="{{ url('/login') }}">Masuk admin <span aria-hidden="true">&rarr;</span></a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero" id="beranda">
            <img class="hero__image" src="{{ asset('storage/' . $heroImagePath) }}" alt="Kegiatan di Masjid Nurul Iman" fetchpriority="high">
            <div class="hero__shade"></div>
            <div class="hero__content page-width">
                <p class="eyebrow hero__eyebrow"><span></span> Rumah ibadah dan kebersamaan warga</p>
                <h1>Masjid<br><em>Nurul Iman</em></h1>
                <p class="hero__intro">Tempat kita pulang untuk beribadah, belajar, dan menguatkan kebersamaan di Krapyak.</p>
                <a class="button button--sunrise" href="#jadwal">Lihat jadwal Jumat <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="hero__location"><span class="hero__location-mark"></span> RW IX, Kelurahan Krapyak, Semarang</div>
            @if ($nextKhutbah)
                <a href="#jadwal" class="next-friday" aria-label="Jadwal khutbah Jumat berikutnya">
                    <span class="next-friday__label">Jumat mendatang</span>
                    <strong>{{ \Carbon\Carbon::parse($nextKhutbah->date)->locale('id')->translatedFormat('d F Y') }}</strong>
                    <span class="next-friday__speaker">{{ $nextKhutbah->speaker_name ?? ($nextKhutbah->speaker->name ?? 'Khatib') }}</span>
                </a>
            @endif
            <a class="hero__scroll" href="#jadwal" aria-label="Gulir ke jadwal"><span></span></a>
        </section>

        <section class="welcome-strip" aria-label="Informasi masjid">
            <div class="page-width welcome-strip__inner">
                <span class="welcome-strip__caption">Assalamu'alaikum warahmatullahi wabarakatuh</span>
                <span class="welcome-strip__rule"></span>
                <span class="welcome-strip__caption">Mari makmurkan masjid bersama</span>
            </div>
        </section>

        <section class="schedule-section section-pad" id="jadwal">
            <div class="page-width">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow eyebrow--green">Waktu untuk berkumpul</p>
                        <h2>Jadwal <em>masjid</em></h2>
                    </div>
                    <p class="section-heading__copy">Catat waktunya, ajak keluarga dan tetangga untuk hadir bersama.</p>
                </div>

                <div class="schedule-grid">
                    <article class="schedule-panel schedule-panel--official">
                        <div class="schedule-panel__heading">
                            <div class="schedule-panel__icon" aria-hidden="true">J</div>
                            <div>
                                <span class="schedule-panel__kicker">Setiap Jumat</span>
                                <h3>Khutbah Jumat</h3>
                            </div>
                            @if ($hasOfficialKhutbah)
                                <span class="official-label">Jadwal resmi</span>
                            @endif
                        </div>
                        <div class="schedule-list">
                            @forelse ($jadwalKhutbah as $khutbah)
                                <div class="schedule-row">
                                    <time datetime="{{ $khutbah->date }}" class="schedule-row__date">
                                        <strong>{{ \Carbon\Carbon::parse($khutbah->date)->locale('id')->translatedFormat('d') }}</strong>
                                        <span>{{ \Carbon\Carbon::parse($khutbah->date)->locale('id')->translatedFormat('M Y') }}</span>
                                    </time>
                                    <span class="schedule-row__name">{{ $khutbah->speaker_name ?? ($khutbah->speaker->name ?? '-') }}</span>
                                </div>
                            @empty
                                <div class="empty-schedule">Jadwal khutbah akan segera diperbarui.</div>
                            @endforelse
                        </div>
                    </article>

                    <article class="schedule-panel schedule-panel--kultum">
                        <div class="schedule-panel__heading">
                            <div class="schedule-panel__icon" aria-hidden="true">K</div>
                            <div>
                                <span class="schedule-panel__kicker">Belajar bersama</span>
                                <h3>Kultum</h3>
                            </div>
                        </div>
                        <div class="schedule-list">
                            @forelse ($jadwalKultum as $kultum)
                                <div class="schedule-row">
                                    <time datetime="{{ $kultum->date }}" class="schedule-row__date">
                                        <strong>{{ \Carbon\Carbon::parse($kultum->date)->locale('id')->translatedFormat('d') }}</strong>
                                        <span>{{ \Carbon\Carbon::parse($kultum->date)->locale('id')->translatedFormat('M Y') }}</span>
                                    </time>
                                    <span class="schedule-row__name">{{ $kultum->speaker->name ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="empty-schedule">Jadwal kultum belum tersedia.</div>
                            @endforelse
                        </div>
                    </article>
                </div>
            </div>
        </section>

        @if ($ramadanSchedules->isNotEmpty())
            <section class="schedule-section section-pad" id="agenda">
                <div class="page-width">
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow eyebrow--green">Agenda pilihan</p>
                            <h2>Ramadan <em>bersama</em></h2>
                        </div>
                        <p class="section-heading__copy">Jadwal takjil dan kultum yang dibagikan pengelola untuk jamaah.</p>
                    </div>
                    <div class="schedule-grid">
                        @foreach ($ramadanSchedules as $agenda)
                            <article class="schedule-panel {{ $agenda->kind === 'Takjil' ? 'schedule-panel--kultum' : '' }}">
                                <div class="schedule-row schedule-list">
                                    <time datetime="{{ $agenda->date->format('Y-m-d') }}" class="schedule-row__date">
                                        <strong>{{ $agenda->date->locale('id')->translatedFormat('d') }}</strong>
                                        <span>{{ $agenda->date->locale('id')->translatedFormat('M Y') }}</span>
                                    </time>
                                    <div class="schedule-row__name">
                                        <span class="schedule-panel__kicker">{{ $agenda->kind }}</span>
                                        {{ $agenda->title ?: ($agenda->person_name ?: $agenda->kind) }}
                                        @if ($agenda->kind === 'Takjil' && $agenda->quantity)
                                            <small class="block font-normal text-gray-500">{{ $agenda->quantity }} porsi</small>
                                        @elseif ($agenda->person_name)
                                            <small class="block font-normal text-gray-500">{{ $agenda->person_name }}</small>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($managementMembers->isNotEmpty())
            <section class="schedule-section section-pad management-public-section" id="pengurus">
                <div class="page-width">
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow eyebrow--green">Takmir masjid</p>
                            <h2>Susunan <em>pengurus</em></h2>
                        </div>
                        <p class="section-heading__copy">Periode aktif: {{ $managementPeriod->name }}</p>
                    </div>
                    <div class="management-public-grid">
                        @foreach ($managementMembers->groupBy('position_name') as $position => $members)
                            <article class="management-public-group">
                                <h3>{{ $position ?: 'Pengurus' }}</h3>
                                <ul>
                                    @foreach ($members as $member)
                                        <li>{{ $member->name }}{{ $member->title ? ', ' . $member->title : '' }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="community-section" id="kegiatan">
            <div class="community-section__copy">
                <p class="eyebrow eyebrow--light">Tumbuh dalam kebaikan</p>
                <h2>Ruang untuk<br><em>semua generasi.</em></h2>
                <p>Dari belajar mengaji hingga kajian rutin, setiap langkah kebaikan dimulai dari kebersamaan.</p>
                <a href="#galeri" class="text-link text-link--light">Lihat cerita kegiatan <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="community-section__visual" role="img" aria-label="Suasana kegiatan di Masjid Nurul Iman">
                <img src="{{ asset('storage/' . $heroImagePath) }}" alt="">
                <span class="community-section__note">Masjid yang hidup<br>adalah masjid kita bersama.</span>
            </div>
        </section>

        <section class="gallery-section section-pad" id="galeri">
            <div class="page-width">
                <div class="section-heading section-heading--gallery">
                    <div>
                        <p class="eyebrow eyebrow--green">Dari halaman masjid</p>
                        <h2>Cerita <em>kita</em></h2>
                    </div>
                    <p class="section-heading__copy">Potongan kebersamaan dari kegiatan warga dan jamaah.</p>
                </div>

                @if ($galleries->isNotEmpty())
                    <div class="gallery-grid">
                        @foreach ($galleries as $galeri)
                            <article class="gallery-item">
                                <img src="{{ asset('storage/' . $galeri->image_path) }}" alt="{{ $galeri->caption ?: $galeri->category }}" loading="lazy">
                                <div class="gallery-item__caption">
                                    <span>{{ $galeri->category }}</span>
                                    @if ($galeri->caption)<p>{{ $galeri->caption }}</p>@endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="gallery-empty">
                        <img src="{{ asset('logo-masjid.png') }}" alt="" loading="lazy">
                        <p>Ruang cerita kegiatan warga akan hadir di sini.</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="amal-section section-pad" id="keuangan">
            <div class="page-width">
                <div class="amal-heading">
                    <div>
                        <p class="eyebrow eyebrow--green">Amanah bersama</p>
                        <h2>Kotak amal <em>Tarawih</em></h2>
                    </div>
                    <p class="section-heading__copy">Laporan penerimaan yang tercatat untuk jamaah.</p>
                </div>
                <div class="amal-total">
                    <div>
                        <span>Total penerimaan tercatat</span>
                        <strong id="total-amal-display" aria-live="polite">Memuat...</strong>
                    </div>
                    <span class="amal-total__mark" aria-hidden="true">NI</span>
                </div>
                <div class="amal-table-wrap">
                    <table class="amal-table">
                        <thead>
                            <tr><th>No.</th><th>Hari</th><th>Tanggal</th><th>Penerimaan</th><th>Akumulasi</th></tr>
                        </thead>
                        <tbody id="tabel-amal-body" aria-live="polite">
                            <tr><td colspan="5" class="amal-table__empty">Memuat laporan kotak amal...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="page-width site-footer__main">
            <a class="site-brand site-brand--footer" href="#beranda">
                <img src="{{ asset('logo-masjid.png') }}" alt="" class="site-brand__logo">
                <span class="site-brand__name">Masjid <strong>Nurul Iman</strong><small>Krapyak, Semarang</small></span>
            </a>
            <p>Rumah ibadah, ruang belajar,<br>dan tempat kita saling menguatkan.</p>
            <a href="#beranda" class="back-to-top">Kembali ke atas <span aria-hidden="true">&uarr;</span></a>
        </div>
        <div class="page-width site-footer__bottom">
            <span>&copy; {{ now()->year }} Masjid Nurul Iman</span>
            <a href="{{ url('/login') }}">Akses pengelola</a>
        </div>
    </footer>

    <script>
        function muatDataKotakAmal() {
            const tbody = document.getElementById('tabel-amal-body');
            const totalDisplay = document.getElementById('total-amal-display');

            fetch('/Nurul_Iman/api.php')
                .then(response => {
                    if (!response.ok) throw new Error('Laporan belum dapat dimuat.');
                    return response.json();
                })
                .then(data => {
                    const rows = Array.isArray(data) ? data : (data.data || []);
                    let cumulative = 0;

                    if (rows.length === 0) {
                        totalDisplay.textContent = 'Rp 0';
                        tbody.innerHTML = '<tr><td colspan="5" class="amal-table__empty">Belum ada penerimaan yang tercatat.</td></tr>';
                        return;
                    }

                    tbody.innerHTML = rows.map((item, index) => {
                        const amount = Number.parseInt(item.hasil || item.jumlah || item.nominal || item.Hasil || 0, 10) || 0;
                        cumulative += amount;
                        const date = item.tanggal || '-';
                        const parts = date.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                        const dayNames = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                        const day = parts ? dayNames[new Date(Number(parts[1]), Number(parts[2]) - 1, Number(parts[3])).getDay()] : '-';

                        return `<tr><td>${index + 1}</td><td>${item.hari || day}</td><td>${date}</td><td>Rp ${amount.toLocaleString('id-ID')}</td><td>Rp ${cumulative.toLocaleString('id-ID')}</td></tr>`;
                    }).join('');

                    totalDisplay.textContent = 'Rp ' + cumulative.toLocaleString('id-ID');
                })
                .catch(() => {
                    totalDisplay.textContent = 'Belum tersedia';
                    tbody.innerHTML = '<tr><td colspan="5" class="amal-table__empty">Laporan belum dapat dimuat. Silakan coba kembali nanti.</td></tr>';
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            muatDataKotakAmal();
            window.setInterval(muatDataKotakAmal, 30000);
        });
    </script>
</body>
</html>