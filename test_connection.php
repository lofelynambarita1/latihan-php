<?php
require_once(__DIR__ . '/config.php');

$conn = pg_connect("host=".DB_HOST." port=".DB_PORT." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASSWORD);

if ($conn) {
    echo "✅ Koneksi ke database berhasil!";
} else {
    echo "❌ Koneksi gagal: " . pg_last_error();
}
