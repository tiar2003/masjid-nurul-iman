<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Masjid Nurul Iman</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-gray-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-emerald-100">
        <!-- Header Banner -->
        <div class="bg-emerald-700 p-6 text-center text-white relative">
            <h2 class="text-2xl font-bold tracking-wide">MASJID NURUL IMAN</h2>
            <p class="text-emerald-100 text-xs mt-1 uppercase tracking-wider">Panel Administrasi & Manajemen Zakat</p>
        </div>

        <!-- Form Login Area -->
        <div class="p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 text-center">Silakan Masuk</h3>

            <!-- Session Status / Errors -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 text-xs text-red-700 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email Administrator</label>
                    <input type="email5" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@nuruliman.local" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm outline-none transition-all">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <span class="ml-2 text-xs">Ingat Saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 hover:underline">Lupa sandi?</a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition-colors duration-200 text-sm tracking-wide">
                    MASUK KE DASHBOARD
                </button>
            </form>
        </div>

        <!-- Footer Card -->
        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-500">&copy; 2026 Pengurus Masjid Nurul Iman. Sistem Informasi Lokal.</p>
        </div>
    </div>

</body>
</html>