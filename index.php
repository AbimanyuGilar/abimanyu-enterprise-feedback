<?php
// MySQL Configuration
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_pass = '';
$db_name = 'abimanyu_feedbacks';

// Connect to MySQL server
$conn = new mysqli($db_host, $db_user, $db_pass, '', $db_port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database and table if they do not exist
$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
$conn->select_db($db_name);

$conn->query("CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    company VARCHAR(255),
    rating INT,
    message TEXT,
    date VARCHAR(50)
)");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $company = $_POST['company'] ?? '';
    $rating = (int)($_POST['rating'] ?? 5);
    $message = $_POST['message'] ?? '';
    $date = date('d M Y, H:i');

    if (!empty($name) && !empty($message)) {
        // Using prepared statements to safely insert data into DB (preventing SQLi, but keeping XSS intentionally later)
        $stmt = $conn->prepare("INSERT INTO feedbacks (name, company, rating, message, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $company, $rating, $message, $date);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to avoid form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Read feedbacks (ORDER BY id DESC to show the latest first)
$feedbacks = [];
$result = $conn->query("SELECT * FROM feedbacks ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abimanyu Enterprise - Portal Masukan Publik</title>
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
<body class="bg-slate-900 min-h-screen text-slate-200">

    <!-- Header Section -->
    <header class="bg-slate-850 shadow-lg border-b border-slate-750">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-600 rounded-lg p-2 shadow-md shadow-indigo-900/50">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-white tracking-tight">Abimanyu Enterprise</h1>
                        <p class="text-indigo-300 text-sm font-medium mt-1">Portal Masukan Publik</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-800 text-indigo-300 border border-slate-700">
                        Portal v2.1.0
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Column: Submission Form -->
            <div class="lg:col-span-5">
                <div class="bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 overflow-hidden sticky top-8">
                    <!-- Form Header -->
                    <div class="bg-slate-850 px-8 py-6 border-b border-slate-700">
                        <h2 class="text-xl font-bold text-white">Berikan Masukan</h2>
                        <p class="mt-2 text-sm text-slate-400">Kami terus meningkatkan solusi enterprise kami berdasarkan masukan berharga Anda.</p>
                    </div>
                    
                    <form method="POST" class="px-8 py-6 space-y-6">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-300">Nama Lengkap <span class="text-red-400">*</span></label>
                            <input type="text" name="name" id="name" required class="mt-1.5 block w-full rounded-lg bg-slate-900 border-slate-700 text-white placeholder-slate-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 border transition-colors duration-200" placeholder="John Doe">
                        </div>

                        <!-- Company Name -->
                        <div>
                            <label for="company" class="block text-sm font-semibold text-slate-300">Nama Perusahaan</label>
                            <input type="text" name="company" id="company" class="mt-1.5 block w-full rounded-lg bg-slate-900 border-slate-700 text-white placeholder-slate-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 border transition-colors duration-200" placeholder="PT Sukses Makmur">
                        </div>

                        <!-- Experience Rating -->
                        <div>
                            <label for="rating" class="block text-sm font-semibold text-slate-300">Penilaian Pengalaman</label>
                            <select name="rating" id="rating" class="mt-1.5 block w-full rounded-lg bg-slate-900 border-slate-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 border transition-colors duration-200">
                                <option value="5">⭐⭐⭐⭐⭐ Luar Biasa (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ Bagus Sekali (4/5)</option>
                                <option value="3">⭐⭐⭐ Bagus (3/5)</option>
                                <option value="2">⭐⭐ Cukup (2/5)</option>
                                <option value="1">⭐ Kurang (1/5)</option>
                            </select>
                        </div>

                        <!-- Feedback Message -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-slate-300">Masukan Anda <span class="text-red-400">*</span></label>
                            <textarea name="message" id="message" rows="5" required class="mt-1.5 block w-full rounded-lg bg-slate-900 border-slate-700 text-white placeholder-slate-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2.5 border transition-colors duration-200" placeholder="Mohon jelaskan pengalaman Anda dengan layanan kami..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-all duration-200 transform hover:-translate-y-0.5">
                                Kirim Masukan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Feedback Feed -->
            <div class="lg:col-span-7 mt-10 lg:mt-0">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-white">Testimoni Klien</h2>
                    <span class="inline-flex items-center rounded-full bg-slate-800 border border-slate-700 px-3 py-1 text-xs font-bold text-indigo-300 shadow-sm">
                        <?php echo count($feedbacks); ?> Ulasan
                    </span>
                </div>

                <div class="space-y-6">
                    <?php if (empty($feedbacks)): ?>
                        <!-- Empty State -->
                        <div class="bg-slate-800 rounded-2xl shadow-sm border border-slate-700 p-12 text-center">
                            <div class="mx-auto h-16 w-16 bg-slate-900 rounded-full flex items-center justify-center mb-4 border border-slate-700">
                                <svg class="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Belum ada testimoni</h3>
                            <p class="mt-2 text-sm text-slate-400 max-w-sm mx-auto">Jadilah yang pertama membagikan pengalaman Anda dengan solusi enterprise kami. Masukan Anda membantu kami berkembang.</p>
                        </div>
                    <?php else: ?>
                        <!-- Feedback Cards -->
                        <?php foreach ($feedbacks as $fb): ?>
                            <div class="bg-slate-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-slate-700 p-6 relative overflow-hidden group">
                                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500 transform origin-bottom scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>
                                
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4">
                                        <!-- Dynamic Avatar -->
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-800 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                            <?php 
                                                // VULNERABILITY: Raw output without sanitization could execute here if name has payload.
                                                echo mb_strtoupper(mb_substr($fb['name'], 0, 1)); 
                                            ?>
                                        </div>
                                        <div>
                                            <!-- VULNERABILITY 1: Name is not sanitized -->
                                            <h3 class="text-lg font-bold text-white"><?php echo $fb['name']; ?></h3>
                                            <p class="text-sm text-indigo-400 font-semibold mt-0.5">
                                                <!-- VULNERABILITY 2: Company is not sanitized -->
                                                <?php echo !empty($fb['company']) ? $fb['company'] : 'Klien Independen'; ?>
                                                <span class="text-slate-600 mx-2">|</span>
                                                <span class="text-slate-500 text-xs font-normal"><?php echo $fb['date']; ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Star Rating -->
                                    <div class="flex text-yellow-400 bg-slate-900 border border-slate-700 px-2 py-1 rounded-lg">
                                        <?php 
                                            $rating = (int)$fb['rating'];
                                            for($i=1; $i<=5; $i++) {
                                                echo $i <= $rating 
                                                    ? '<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>' 
                                                    : '<svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                                            }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="mt-5 text-slate-300 leading-relaxed bg-slate-900 rounded-xl p-4 border border-slate-750">
                                    <!-- VULNERABILITY 3: Message is not sanitized. The Core Flaw. -->
                                    <?php echo $fb['message']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </main>

</body>
</html>
