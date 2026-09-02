<?php
$conn = new mysqli("localhost","admin","rahasia123","kotak_amal");

$conn->query("TRUNCATE TABLE terawih");

echo "ok";
