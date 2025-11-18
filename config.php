$PAYCHANGU_PUBLIC_KEY = getenv('PAYCHANGU_PUBLIC_KEY');
$PAYCHANGU_SECRET_KEY = getenv('PAYCHANGU_SECRET_KEY');
$db_url = getenv('DATABASE_URL');

if(!$db_url) die("DATABASE_URL not found.");

$dbopts = parse_url($db_url);
$host = $dbopts["host"];
$port = $dbopts["port"] ?? 5432;
$user = $dbopts["user"];
$pass = $dbopts["pass"];
$dbname = ltrim($dbopts["path"], '/');

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
    die("Database connection failed: ".$e->getMessage());
}
