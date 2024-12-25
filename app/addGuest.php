<?php
require __DIR__."./functions.php";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    
    $mrz_d_type = "I";
    $mrz_nationality = "TUR";
    $mrz_id = $data['TC'];
    $mrz_name = $data['AD'];
    $mrz_surname = $data['SOYAD'];
    $mrz_date = date('Y-m-d H:i:s');
    
    echo json_encode([
        'status' => 'success',
        'message' => 'MRZ verileri başarıyla ayrıştırıldı.',
        'data' => [
            'mrz_d_type' => $mrz_d_type,
            'mrz_nationality' => $mrz_nationality,
            'mrz_id' => $mrz_id,
            'mrz_name' => $mrz_name,
            'mrz_surname' => $mrz_surname,
            'mrz_date' => $mrz_date
        ]
    ]);

    $time_limit = date("Y-m-d H:i:s", strtotime("-5 minutes"));
    $query = "SELECT COUNT(*) FROM guests WHERE mrz_id = :mrz_id AND date > :time_limit";
    $stmt = $db->prepare($query);
    $stmt->execute(['mrz_id' => $mrz_id, 'time_limit' => $time_limit]);

    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Son 5 dakika içerisinde giriş yapılmış.']);
        exit;
    }

    $query = "SELECT COUNT(*) FROM users WHERE id = :mrz_id OR (name = :name AND surname = :surname)";
    $stmt = $db->prepare($query);
    $stmt->execute(['mrz_id' => $mrz_id, 'name' => $mrz_name, 'surname' => $mrz_surname]);
    $is_existing_user = $stmt->fetchColumn() > 0;

    $first_time = $is_existing_user ? 0 : 1;

    $query = "INSERT INTO guests (mrz_id, mrz_name, mrz_surname, mrz_nationality, first_time, date) 
              VALUES (:mrz_id, :mrz_name, :mrz_surname, :mrz_nationality, :first_time, :date)";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        'mrz_id' => $mrz_id,
        'mrz_name' => $mrz_name,
        'mrz_surname' => $mrz_surname,
        'mrz_nationality' => $mrz_nationality,
        'first_time' => $first_time,
        'date' => $mrz_date
    ]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Kayıt başarıyla eklendi.', 'first_time' => $first_time]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Kayıt eklenirken bir hata oluştu.']);
    }
}else{
    echo json_encode(['status' => 'error', 'message' => 'MRZ verisi eksik.']);
    exit;
}