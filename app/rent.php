<?php 
require __DIR__."./functions.php";
if(isset($_POST['rent']) && $_POST['rent'] != null){
    $rentid = $_POST['id'];
    // loan tablosunda id ile eşleşen kayıt var mı kontrol et
    $stmt = $db->prepare("SELECT expiry_date FROM loan WHERE id = :id");
    $stmt->execute(['id' => $rentid]);
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($loan) {
        // expiry_date'i 7 gün artır
        $new_expiry_date = date('Y-m-d H:i:s', strtotime($loan['expiry_date'] . ' +7 days'));

        // expiry_date güncellemesi
        $update_stmt = $db->prepare("UPDATE loan SET expiry_date = :new_expiry_date WHERE id = :id");
        $update_stmt->execute([
            'new_expiry_date' => $new_expiry_date,
            'id' => $rentid
        ]);

        header('Content-Type: application/json');
        $d['response'] = "Süre başarıyla 7 gün uzatıldı";
    } else {
        header('Content-Type: application/json');
        $d['response'] = "Hata pat";
    }
    print_r(json_encode($d, JSON_PRETTY_PRINT));
}

if(isset($_POST['end']) && $_POST['end'] != null){
    $rentid = $_POST['id'];
    $stmt = $db->prepare("SELECT book_id FROM loan WHERE id = :id");
    $stmt->execute(['id' => $rentid]);
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($loan) {
        // loan tablosunda end_time ve is_enden alanlarını güncelle
        $update_loan_stmt = $db->prepare("UPDATE loan SET end_time = CURRENT_TIMESTAMP, is_ended = 1 WHERE id = :id");
        $update_loan_stmt->execute(['id' => $rentid]);

        // books tablosunda ilgili kitabın stoğunu 1 arttır
        $book_id = $loan['book_id'];
        $update_book_stmt = $db->prepare("UPDATE books SET stock = stock + 1 WHERE id = :book_id");
        $update_book_stmt->execute(['book_id' => $book_id]);
        header('Content-Type: application/json');
        $d['response'] = "Kitap başarıyla iade edildi ve stok güncellendi";
    } else {
        header('Content-Type: application/json');
        $d['response'] = "Hata pat";
    }
    print_r(json_encode($d, JSON_PRETTY_PRINT));
}

?>