<?php
// firebase_config.php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

// Ambil credentials dari environment variable (Untuk Render/Production)
$firebaseCredentials = getenv('FIREBASE_CREDENTIALS');
$serviceAccountFilePath = __DIR__ . '/firebase-credentials.json';

// Bypass SSL Verification khusus untuk Localhost WAMP dengan file cacert asli
$cacertPath = __DIR__ . '/cacert.pem';
if (file_exists($cacertPath)) {
    // Set environment variable agar Guzzle menggunakan cacert.pem lokal
    putenv('CURL_CA_BUNDLE=' . $cacertPath);
}

$factory = (new Factory);

if ($firebaseCredentials) {
    // Jalur Production: Gunakan JSON string dari Environment Variable
    $serviceAccount = json_decode($firebaseCredentials, true);
    if (!$serviceAccount) {
        die("Invalid Firebase credentials in environment variable.");
    }
    $factory = $factory->withServiceAccount($serviceAccount);
} else {
    // Jalur Development: Gunakan file JSON lokal
    if (!file_exists($serviceAccountFilePath)) {
        die("File kredensial Firebase JSON tidak ditemukan di: " . $serviceAccountFilePath . ". Silakan unduh dari Firebase Console dan simpan di folder project, ATAU setel environment variable FIREBASE_CREDENTIALS.");
    }
    $factory = $factory->withServiceAccount($serviceAccountFilePath);
}

// Konfigurasi Database URL
$factory = $factory->withDatabaseUri('https://db-crud-9d395-default-rtdb.asia-southeast1.firebasedatabase.app');

$database = $factory->createDatabase();

// Jika butuh auth
$auth = $factory->createAuth();

?>
