<?php
// firebase_config.php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

// OPSI 1: Jika menggunakan Environment Variable (seperti contoh Anda)
/*
$firebaseCredentials = getenv('FIREBASE_CREDENTIALS');
if (!$firebaseCredentials) {
    die("Firebase credentials not set in environment variables.");
}
$serviceAccount = json_decode($firebaseCredentials, true);
if (!$serviceAccount) {
    die("Invalid Firebase credentials.");
}
*/

// OPSI 2: Menggunakan file JSON langsung (Lebih mudah untuk WAMP/Localhost)
// Pastikan Anda sudah mengunduh file JSON dari Firebase Console -> Project Settings -> Service Accounts -> Generate new private key
// Lalu simpan file tersebut di folder project ini dan sesuaikan nama filenya di bawah ini:
$serviceAccountFilePath = __DIR__ . '/firebase-credentials.json';

if (!file_exists($serviceAccountFilePath)) {
    die("File kredensial Firebase JSON tidak ditemukan di: " . $serviceAccountFilePath . ". Silakan unduh dari Firebase Console dan simpan di folder project.");
}

// Bypass SSL Verification khusus untuk Localhost WAMP dengan file cacert asli
$cacertPath = __DIR__ . '/cacert.pem';
if (file_exists($cacertPath)) {
    // Set environment variable agar Guzzle menggunakan cacert.pem lokal
    putenv('CURL_CA_BUNDLE=' . $cacertPath);
}

// Konfigurasi Firebase Auth & Database URL
$factory = (new Factory)
    ->withServiceAccount($serviceAccountFilePath)
    // URL Database Anda sesuai yang ada di app.js
    ->withDatabaseUri('https://db-crud-9d395-default-rtdb.asia-southeast1.firebasedatabase.app');

$database = $factory->createDatabase();

// Jika butuh auth
$auth = $factory->createAuth();

?>
