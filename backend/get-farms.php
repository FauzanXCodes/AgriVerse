<?php
session_start();
header("Content-Type: application/json");
require "db.php";

/* 
   Farm data source is funding_requests table
   with status = 'open'
*/

$sql = "
    SELECT fr.id, fr.farmer_id, fr.crop, fr.location, fr.investment_needed,
           fr.duration_months, fr.risk_level, f.name AS farmer_name
    FROM funding_requests fr
    LEFT JOIN farmers f ON fr.farmer_id=f.id
    WHERE fr.status='open'
";

$res = $conn->query($sql);

$farms = [];
while ($row = $res->fetch_assoc()) {
    $farms[] = $row;
}

echo json_encode(["status"=>"OK","farms"=>$farms]);
?>
