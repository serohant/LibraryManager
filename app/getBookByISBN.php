<?php 
require __DIR__."./functions.php";
header('Content-Type: application/json');
if(isset($_GET['isbn']) && $_GET['isbn'] != null && strlen($_GET['isbn']) == 13 ){
    $apiKey = '56734_db22f236f8d8e265c55d2d798c6ad5c9'; // API anahtarınızı buraya yerleştirin
    $isbn = $_GET['isbn'];   // Aramak istediğiniz kitabın ISBN numarası
    $url = "https://api2.isbndb.com/book/$isbn"; // API URL'si

    // cURL oturumu başlat
    $ch = curl_init();

    // cURL ayarları
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $apiKey",
        "Accept: */*"
    ]);

    // İsteği gönder ve yanıtı al
    $response = curl_exec($ch);

    // Hata kontrolü
    if(curl_errno($ch)) {
        echo 'Hata: ' . curl_error($ch);
    } else {
        // JSON yanıtını diziye çevir
        $bookData = json_decode($response, true);
        // Kitap bilgilerini ekrana yazdır
        print_r(json_encode($bookData,JSON_PRETTY_PRINT));
    }

    // cURL oturumunu kapat
    curl_close($ch);
    
}
if(isset($_POST['data']) && $_POST['data'] != null){
    try {
        $data = $_POST['data'];
     $isbn = $data['book']['isbn13']; // Gelen ISBN
      $stmt = $db->prepare("SELECT id, stock FROM books WHERE isbn = :isbn");
      $stmt->bindParam(':isbn', $isbn);
      $stmt->execute();
      $book = $stmt->fetch(PDO::FETCH_ASSOC);
  
      if ($book) {
          // Eğer ISBN mevcutsa, stoğu 1 arttır
          $newStock = $book['stock'] + 1;
          $updateStmt = $db->prepare("UPDATE books SET stock = :stock WHERE isbn = :isbn");
          $updateStmt->bindParam(':stock', $newStock);
          $updateStmt->bindParam(':isbn', $isbn);
          $updateStmt->execute();
          header('Content-Type: application/json');
          $d['response'] = "Kitap zaten mevcut stok başarıyla 1 arttırıldı";
      } else {
          // Eğer ISBN mevcut değilse, yeni kitap ekle
          $stmt = $db->prepare("INSERT INTO books (name, isbn, author, publisher, publishdate, subjects, pages, language, stock) 
                                 VALUES (:name, :isbn, :author, :publisher, :publishdate, :subjects, :pages, :language, :stock)");
  
          // Parametreleri bağla ve değerleri ata
          $name = $data['book']['title'];
        $isbn = $isbn;
        $authors = implode(', ', $data['book']['authors']);  // Diziye dönüştür ve değişkene ata
        $publisher = $data['book']['publisher'];
        $publishdate = $data['book']['date_published'];
        $subjects = implode(', ', $data['book']['subjects']); // Diziye dönüştür ve değişkene ata
        $pages = $data['book']['pages'];
        $language = $data['book']['language'];
        $stock = 1; // Yeni kitap için stok 1 olarak belirleniyor

        // Parametreleri bağla
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':author', $authors);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':publishdate', $publishdate);
        $stmt->bindParam(':subjects', $subjects);
        $stmt->bindParam(':pages', $pages);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':stock', $stock);
  
          // Sorguyu çalıştır
          $stmt->execute();
          header('Content-Type: application/json');
          $d['response'] = "Kitap başarıyla eklendi";
      }
  } catch (PDOException $e) {
      echo "Hata: " . $e->getMessage();
  }
  print_r(json_encode($d, JSON_PRETTY_PRINT));
  }


  if(isset($_POST['update']) && $_POST['update'] != null){
    try {
        $query = $db->prepare("UPDATE books SET
        stock = :stock
        WHERE isbn = :isbn");
        $update = $query->execute(array(
             "stock" => $_POST['stock'],
             "isbn" => $_POST['isbn']
        ));
        if ( $update ){
            header('Content-Type: application/json');
            $d['response'] = "Stok başarıyla güncellendi";
        }
    } catch (\PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
    print_r(json_encode($d, JSON_PRETTY_PRINT));
  }
?>