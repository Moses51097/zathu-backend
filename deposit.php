<?php
session_start();
require 'config.php'; // DB + PayChangu keys

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;
$amount = $data['amount'] ?? 0;

if(!$user_id || $amount <= 0){
    echo json_encode(["status"=>"error","message"=>"Invalid input"]);
    exit;
}

// Check user exists
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo json_encode(["status"=>"error","message"=>"User not found"]);
    exit;
}

// --- PayChangu API Call ---
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paychangu.com/v1/payment",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        "amount" => $amount,
        "currency" => "MWK",
        "public_key" => $PAYCHANGU_PUBLIC_KEY,
        "customer" => [
            "id" => $user_id
        ],
        "callback_url" => "https://zathu-backend.onrender.com/payment_callback.php"
    ]),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $PAYCHANGU_SECRET_KEY"
    ],
]);

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

// Send checkout URL back to frontend
echo json_encode([
    "status"=>"success",
    "checkout_url"=>$data['checkout_url'] ?? null
]);
?>
