<?php
// ============================
// CONFIG.PHP (SAFE VERSION)
// ============================

// ----------------------------
// PostgreSQL / MySQL DATABASE
// ----------------------------
$db_url = getenv('DATABASE_URL'); // Real DB URL from Render Environment

if (!$db_url) {
    die("DATABASE_URL not found in environment.");
}

$dbopts = parse_url($db_url);

$host = $dbopts["host"];
$port = $dbopts["port"] ?? 3306; // MySQL default port or use pgsql 5432 if PostgreSQL
$user = $dbopts["user"];
$pass = $dbopts["pass"];
$dbname = ltrim($dbopts["path"], '/');

// === Auto-detect MySQL or PostgreSQL ===
if (strpos($db_url, 'mysql') !== false) {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
} else {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// ----------------------------
// PAYCHANGU KEYS
// ----------------------------
$PAYCHANGU_PUBLIC_KEY = getenv('PAYCHANGU_PUBLIC_KEY');
$PAYCHANGU_SECRET_KEY = getenv('PAYCHANGU_SECRET_KEY');

if (!$PAYCHANGU_PUBLIC_KEY || !$PAYCHANGU_SECRET_KEY) {
    die("PayChangu keys missing in Render environment!");
}
?>
