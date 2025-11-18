<?php
session_start();
require 'config.php'; // DB + PayChangu keys

header("Content-Type: application/json");

// Get POST data from PayChangu (assuming JSON webhook)
$data = json_decode(file_get_contents("php://input"), true);

$payment_id = $data['payment_id'] ?? null;
$user_id = $data['customer']['id'] ?? null;
$status = $data['status'] ?? null;
$amount = $data['amount'] ?? 0;

if (!$payment_id || !$user_id || !$status) {
    echo json_encode(["status"=>"error","message"=>"Invalid callback data"]);
    exit;
}

// Only process successful payments
if ($status !== "success") {
    echo json_encode(["status"=>"error","message"=>"Payment not successful"]);
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

// Update user balance
$new_balance = $user['balance'] + $amount;
$update = $pdo->prepare("UPDATE users SET balance=? WHERE id=?");
$update->execute([$new_balance, $user_id]);

// Optional: log payment
$log = $pdo->prepare("INSERT INTO payments (payment_id, user_id, amount, status) VALUES (?,?,?,?)");
$log->execute([$payment_id, $user_id, $amount, $status]);

echo json_encode([
    "status"=>"success",
    "message"=>"Balance updated",
    "new_balance"=>$new_balance
]);
?>
