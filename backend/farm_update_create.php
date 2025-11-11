<?php
require 'db.php';
require 'auth_guard.php';
require 'json.php';

$update_date = $_POST['update_date'] ?? '';
$day = intval($_POST['day'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$update_date || !$title) json_err('Missing fields');

$uploadDir = __DIR__ . '/../uploads/farm_updates/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'])) json_err('INVALID_IMAGE');
    $newName = uniqid('upd_') . '.' . $ext;
    $target = $uploadDir . $newName;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) json_err('UPLOAD_FAILED');
    $image_path = 'uploads/farm_updates/' . $newName; // relative path for frontend
}

$stmt = $conn->prepare("INSERT INTO farm_updates
(farmer_id, update_date, day_in_cycle, title, description, image_path)
VALUES (?,?,?,?,?,?)");
$stmt->bind_param("isisss", $FARMER_ID, $update_date, $day, $title, $description, $image_path);
$stmt->execute();
$stmt->close();

json_ok('CREATED');
