<?php
// Pengaturan Sesi Admin & Flag
$admin_cookie_name = "admin_session";
$admin_cookie_value = "adm1n_s3cr3t_778899_token";
$flag = "POLINES{XSS_TO_ADM1N_T4K30V3R_XX}";

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    // Kredensial admin (sebagai simulasi)
    if ($user === 'admin' && $pass === 'password123') {
        // Set cookie TANPA HttpOnly agar kerentanan XSS bisa mengeksploitasinya
        setcookie($admin_cookie_name, $admin_cookie_value, time() + 3600, "/");
        header("Location: /admin");
        exit;
    } else {
        $error = "Kredensial tidak valid.";
    }
}

// Check Authentication
$is_admin = false;
if (isset($_COOKIE[$admin_cookie_name]) && $_COOKIE[$admin_cookie_name] === $admin_cookie_value) {
    $is_admin = true;
}

// Handle Logout
if (isset($_GET['logout'])) {
    setcookie($admin_cookie_name, "", time() - 3600, "/");
    header("Location: /admin");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Abimanyu Enterprise</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                        },
                        slate: {
                            750: '#293548',
                            850: '#151e2e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-900 min-h-screen text-slate-200 flex flex-col">

<?php if ($is_admin): ?>
    <!-- Admin Dashboard (Hanya bisa diakses jika cookie admin valid) -->
    <div class="max-w-4xl mx-auto mt-12 w-full px-4 flex-grow">
        <div class="bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 overflow-hidden">
            <div class="bg-slate-850 px-8 py-6 border-b border-slate-700 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h2 class="text-xl font-bold text-white">Dashboard Admin</h2>
                </div>
                <a href="?logout=1" class="text-sm bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors shadow-sm font-semibold">Logout</a>
            </div>
            <div class="p-8 space-y-8">
                <div class="bg-green-900/40 border border-green-500/50 rounded-xl p-8 text-center shadow-inner">
                    <h3 class="text-green-400 font-bold text-2xl mb-2">Selamat Datang, Administrator!</h3>
                    <p class="text-slate-300 text-sm mb-6">Anda telah berhasil masuk ke halaman rahasia. Berikut adalah flag yang Anda cari:</p>
                    
                    <!-- THE FLAG -->
                    <div class="bg-black/60 rounded-xl p-5 inline-block border border-green-500/40 shadow-lg">
                        <span class="font-mono text-2xl text-green-300 font-bold tracking-wider select-all"><?php echo $flag; ?></span>
                    </div>
                </div>
                
                <div class="text-center pt-4 border-t border-slate-700">
                    <a href="/" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Portal Publik</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Login Form -->
    <div class="flex-grow flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 overflow-hidden">
            <div class="bg-slate-850 px-8 py-8 border-b border-slate-700 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-indigo-500/10"></div>
                <div class="relative z-10 flex justify-center mb-4">
                    <div class="bg-slate-900 p-3 rounded-full shadow-inner border border-slate-700">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white relative z-10">Admin Login</h2>
                <p class="mt-2 text-sm text-slate-400 relative z-10">Abimanyu Enterprise Secure Portal</p>
            </div>
            
            <form method="POST" class="p-8 space-y-6">
                <?php if (isset($error)): ?>
                    <div class="bg-red-900/40 border border-red-500/50 rounded-lg p-3 text-red-300 text-sm text-center font-medium">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-300">Username</label>
                    <input type="text" name="username" id="username" class="mt-1.5 block w-full rounded-lg bg-slate-900 border-slate-700 text-white placeholder-slate-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 border transition-colors" required placeholder="admin">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300">Password</label>
                    <input type="password" name="password" id="password" class="mt-1.5 block w-full rounded-lg bg-slate-900 border-slate-700 text-white placeholder-slate-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 border transition-colors" required placeholder="••••••••">
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-all duration-200 transform hover:-translate-y-0.5">
                        Masuk Ke Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
