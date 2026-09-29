<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#123d34">
    <title>Masuk Pengelola | Masjid Nurul Iman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-visual" aria-label="Masjid Nurul Iman">
            <img src="{{ asset('storage/galleries/0vr9RUWOFKF8Ycoo0ID34lXFWeydYTNJ6Hqha7Iy.jpg') }}" alt="Suasana kegiatan di Masjid Nurul Iman">
            <div class="login-visual__shade"></div>
            <a class="login-brand" href="{{ url('/') }}">
                <img src="{{ asset('logo-masjid.png') }}" alt="">
                <span>Masjid <strong>Nurul Iman</strong><small>Krapyak, Semarang</small></span>
            </a>
            <div class="login-visual__message">
                <p class="eyebrow">Ruang pengelola</p>
                <h1>Melayani<br><em>dengan amanah.</em></h1>
                <p>Kelola informasi dan kegiatan masjid dalam satu tempat.</p>
            </div>
            <span class="login-visual__location">RW IX, Kelurahan Krapyak, Semarang</span>
        </section>

        <section class="login-panel">
            <div class="login-panel__inner">
                <a class="login-back" href="{{ url('/') }}"><span aria-hidden="true">&larr;</span> Kembali ke website</a>
                <p class="eyebrow eyebrow--green">Panel pengelola</p>
                <h2>Selamat datang.</h2>
                <p class="login-panel__intro">Masuk untuk mengelola jadwal dan informasi Masjid Nurul Iman.</p>

                @if ($errors->any())
                    <div class="login-alert" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf
                    <div class="login-field">
                        <label for="email">Email administrator</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@masjid.id" aria-describedby="email-error">
                        @error('email')<span id="email-error" class="login-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="login-field">
                        <div class="login-field__label-row">
                            <label for="password">Kata sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Lupa sandi?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" aria-describedby="password-error">
                        @error('password')<span id="password-error" class="login-field__error">{{ $message }}</span>@enderror
                    </div>
                    <label class="login-remember">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                    <button type="submit" class="login-submit">Masuk ke panel <span aria-hidden="true">&rarr;</span></button>
                </form>

                <p class="login-panel__foot">Akses khusus pengurus Masjid Nurul Iman.</p>
            </div>
        </section>
    </main>
</body>
</html>