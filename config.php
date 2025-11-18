<?php
// ============================
// CONFIG.PHP
// ============================

$db_url = getenv('DATABASE_URL'); // From Render environment

if (!$db_url) {
    die("DATABASE_URL not found in environment.");
}

$dbopts = parse_url($db_url);
$host = $dbopts["host"];
$port = $dbopts["port"] ?? 5432; // default PostgreSQL port
$user = $dbopts["user"];
$pass = $dbopts["pass"];
$dbname = ltrim($dbopts["path"], '/');

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// PayChangu keys
$PAYCHANGU_PUBLIC_KEY = getenv('PAYCHANGU_PUBLIC_KEY');
$PAYCHANGU_SECRET_KEY = getenv('PAYCHANGU_SECRET_KEY');

if (!$PAYCHANGU_PUBLIC_KEY || !$PAYCHANGU_SECRET_KEY) {
    die("PayChangu keys missing in Render environment!");
}

// Helper functions
function is_logged() {
    return isset($_SESSION['user_id']);
}

function get_user($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
