<?php
require 'config.php'; // DB + PayChangu keys

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // allow InfinityFree frontend
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$amount = floatval($data['amount'] ?? 0);
$user_id = intval($data['user_id'] ?? 0);
$user_name = $data['user_name'] ?? "User";

if($user_id <= 0){
    echo json_encode(["status"=>"error","message"=>"Invalid user ID"]);
    exit;
}

if($amount < 50 || $amount > 500000000){
    echo json_encode(["status"=>"error","message"=>"Amount must be between 50 MWK and 500,000,000 MWK"]);
    exit;
}

// Generate transaction reference
$tx_ref = "ZATHTR_" . time() . "_" . $user_id;

// Insert pending transaction
$stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, tx_ref, status) VALUES (?, 'deposit', ?, ?, 'pending')");
$stmt->execute([$user_id, $amount, $tx_ref]);

// Prepare PayChangu payload
$payload = [
    "amount" => $amount,
    "currency" => "MWK",
    "first_name" => $user_name,
    "last_name" => "",
    "email" => "",
    "callback_url" => "https://zathu-backend.onrender.com/payment_callback.php",
    "return_url" => "https://zathutrade.gt.tc/dashboard.php?msg=Deposit%20pending",
    "tx_ref" => $tx_ref,
    "customization" => ["title"=>"Deposit on ZathuTrade","description"=>"User deposit"],
    "meta" => ["user_id"=>$user_id]
];

// Call PayChangu API
$ch = curl_init("https://api.paychangu.com/payment");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Authorization: Bearer $PAYCHANGU_SECRET_KEY",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if($err){
    echo json_encode(["status"=>"error","message"=>"Curl Error: ".$err]);
    exit;
}

$res_data = json_decode($response, true);

if(isset($res_data['status']) && $res_data['status'] === "success" && isset($res_data['data']['checkout_url'])){
    echo json_encode(["status"=>"success","checkout_url"=>$res_data['data']['checkout_url']]);
    exit;
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to initiate payment."]);
    exit;
}
?>
