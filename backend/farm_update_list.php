<?php
require 'db.php';
require 'auth_guard.php';
require 'json.php';

$q = $conn->prepare("SELECT id, update_date, day_in_cycle, title, description, image_path
                     FROM farm_updates
                     WHERE farmer_id=?
                     ORDER BY id DESC
                     LIMIT 100");
$q->bind_param("i", $FARMER_ID);
$q->execute();
$res = $q->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) { $rows[] = $r; }
$q->close();

json_ok($rows);
