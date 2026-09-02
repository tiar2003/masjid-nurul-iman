<?php
$conn = new mysqli("localhost","admin","rahasia123","kotak_amal");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tanggal = $_POST["tanggal"] ?? '';
    $nominal = $_POST["nominal"] ?? '';

    if ($nominal === "") {

        // Hapus data jika kosong
        $stmt = $conn->prepare("DELETE FROM terawih WHERE tanggal=?");
        $stmt->bind_param("s", $tanggal);
        $stmt->execute();

    } else {

        // Cek apakah sudah ada
        $cek = $conn->prepare("SELECT id FROM terawih WHERE tanggal=?");
        $cek->bind_param("s", $tanggal);
        $cek->execute();
        $result = $cek->get_result();

        if ($result->num_rows > 0) {

            // Update jika ada
            $stmt = $conn->prepare("UPDATE terawih SET nominal=? WHERE tanggal=?");
            $stmt->bind_param("is", $nominal, $tanggal);
            $stmt->execute();

        } else {

            // Insert jika belum ada
            $stmt = $conn->prepare("INSERT INTO terawih (tanggal, nominal) VALUES (?,?)");
            $stmt->bind_param("si", $tanggal, $nominal);
            $stmt->execute();
        }
    }

    exit;
}

// GET data
$result = $conn->query("SELECT * FROM terawih ORDER BY tanggal ASC");

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
