<?php
session_start();
require "db.php";

// ----------------------
// 1. Collect Inputs
// ----------------------
$user_type = $_POST["user_type"] ?? "";
$email_phone = trim($_POST["email_phone"] ?? "");
$password = $_POST["password"] ?? "";

if (!$user_type || !$email_phone || !$password) {
    exit("Missing fields");
}

// ----------------------
// 2. Login Handlers
// ----------------------

if ($user_type === "farmer") {

    // Login with email or phone
    $stmt = $conn->prepare("SELECT id, name, farming_type, password_hash FROM farmers WHERE email=? OR phone=?");
    $stmt->bind_param("ss", $email_phone, $email_phone);

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) exit("Farmer not found");

    $stmt->bind_result($id, $name, $farming_type, $hash);
    $stmt->fetch();

    if (!password_verify($password, $hash)) exit("Incorrect password");

    $_SESSION["farmer_id"] = $id;
    $_SESSION["farmer_name"] = $name;

    // Farming-based redirection
    if ($farming_type === "crop") exit("FARMER_CROP");
    if ($farming_type === "poultry") exit("FARMER_POULTRY");
    if ($farming_type === "dairy") exit("FARMER_DAIRY");

    exit("FARMER_CROP"); // default fallback
}



if ($user_type === "investor") {

    $stmt = $conn->prepare("SELECT id, name, password_hash FROM investors WHERE email=?");
    $stmt->bind_param("s", $email_phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) exit("Investor not found");

    $stmt->bind_result($id, $name, $hash);
    $stmt->fetch();

    if (!password_verify($password, $hash)) exit("Incorrect password");

    $_SESSION["investor_id"] = $id;
    $_SESSION["investor_name"] = $name;

    exit("INVESTOR_OK");
}



if ($user_type === "supplier") {

    $stmt = $conn->prepare("SELECT id, business_name, password_hash FROM suppliers WHERE email=? OR phone=?");
    $stmt->bind_param("ss", $email_phone, $email_phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) exit("Supplier not found");

    $stmt->bind_result($id, $business_name, $hash);
    $stmt->fetch();

    if (!password_verify($password, $hash)) exit("Incorrect password");

    $_SESSION["supplier_id"] = $id;
    $_SESSION["supplier_name"] = $business_name;

    exit("SUPPLIER_OK");
}

exit("INVALID_ROLE");
?>
