<?php 
$pagename = "Kiosk";


require __DIR__."/header.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isbn'])) {
    $isbn = $_POST['isbn'];
    
    // ISBN kontrolü
    if (!empty($isbn)) {
        // SQL sorgusu
        $sql = "SELECT * FROM books WHERE isbn = :isbn";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->execute();
        
        // Sonucu getir
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($book) {
          $showalert = "<script>Swal.fire({
            title: 'Başarılı!',
            html: 'ISBN: ".$book['isbn']."<br> Kitap Adı: ".$book['name']." <br> Yazar: ".$book['author']." <br> Yayın Evi: ".$book['publisher']." <br>Konu(lar): ".$book['subjects']." <br>Stok: ".$book['stock']."',
            icon: 'success',
            confirmButtonText: 'Tamam'
          })</script>";  
        } else {
            $showalert = "<script>Swal.fire({
              title: 'Hata!',
              text: 'Bu ISBN numarasıyla eşleşen bir isim bulunamadı',
              icon: 'error',
              confirmButtonText: 'Tamam'
            })</script>";
        }
    } else {
      $showalert = "<script>Swal.fire({
        title: 'Hata!',
        text: 'Lütfen bir ISBN numarası girin',
        icon: 'error',
        confirmButtonText: 'Tamam'
      })</script>";
    }
} 
// Formdan gelen author bilgisini al
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['author'])) {
    $author = "%".trim($_POST['author'])."%"; // Girdiği temizle

    if (!empty($author)) {
        // SQL sorgusu
        $sql = "SELECT * FROM books WHERE author LIKE :author";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':author', $author, PDO::PARAM_STR);
        $stmt->execute();

        // Sonuçları getir
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($books) {
          $html = "";
            foreach ($books as $book) {
              $html .= "Kitap Adı: ".$book['name']." ISBN: ".$book['isbn']." <br>";
            }
            $showalert = "<script>Swal.fire({
              title: 'Başarılı!',
              html: '".$html."',
              icon: 'success',
              confirmButtonText: 'Tamam'
            })</script>"; 
        } else {
          $showalert = "<script>Swal.fire({
            title: 'Hata!',
            text: 'Bu yazar ile eşleşen bir kitap bulunamadı.',
            icon: 'error',
            confirmButtonText: 'Tamam'
          })</script>";
        }
    } else {
        $showalert = "<script>Swal.fire({
          title: 'Hata!',
          text: 'Lütfen bir yazar adı girin.',
          icon: 'error',
          confirmButtonText: 'Tamam'
        })</script>";
    }
}
// Formdan gelen name bilgisini al
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = "%".trim($_POST['name'])."%"; // Girdiği temizle

    if (!empty($name)) {
        // SQL sorgusu
        $sql = "SELECT * FROM books WHERE name LIKE :name";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();

        // Sonuçları getir
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($books) {
          $html = "";
          foreach ($books as $book) {
            $html .= "Kitap Adı: ".$book['name']." ISBN: ".$book['isbn']." <br>";
          }
          $showalert = "<script>Swal.fire({
            title: 'Başarılı!',
            html: '".$html."',
            icon: 'success',
            confirmButtonText: 'Tamam'
          }).then(function() {
          location.reload();
        })</script>
          "; 
        } else {
          $showalert = "<script>Swal.fire({
            title: 'Hata!',
            text: 'Bu isimle eşleşen bir kitap bulunamadı.',
            icon: 'error',
            confirmButtonText: 'Tamam'
          })</script>";  
        }
    } else {
      $showalert = "<script>Swal.fire({
        title: 'Hata!',
        text: 'Lütfen bir kitap adı girin.',
        icon: 'error',
        confirmButtonText: 'Tamam'
      })</script>";
    }
}

?>
<div class="container-fluid py-4">
      <div class="row">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="isbn-tab" data-bs-toggle="tab" data-bs-target="#isbn" type="button" role="tab" aria-controls="isbn" aria-selected="true">ISBN</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="author-tab" data-bs-toggle="tab" data-bs-target="#author" type="button" role="tab" aria-controls="author" aria-selected="false">Yazar Adı</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="name-tab" data-bs-toggle="tab" data-bs-target="#name" type="button" role="tab" aria-controls="name" aria-selected="false">Kitap Adı</button>
  </li>
</ul>
<div class="px-0 tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="isbn" role="tabpanel" aria-labelledby="isbn-tab">
<div class="card z-index-0"style="border-top-left-radius: 0;">
              <div class="card-header text-center pt-4">
                <h5>ISBN SORGULAMA</h5>
              </div>
              <div class="row px-xl-5 px-sm-4 px-3">
                
                
                
                <div class="mt-2 position-relative text-center">
                  
                </div>
              </div>
              <div class="card-body">
                <form method="POST" role="form text-left">
                  <div class="mb-3">
                    <input name="isbn" min="13" max="13" type="text" class="form-control" placeholder="Lütfen ISBN numarası giriniz" aria-label="Name" aria-describedby="email-addon">
                  </div>
                  
                  
                  
                  <div class="text-center">
                    <button type="submit" class="btn bg-primary w-100 my-4 mb-2 text-white">SORGULA</button>
                  </div>
                  
                </form>
              </div>
            </div>
  </div>
  <div class="tab-pane fade" id="author" role="tabpanel" aria-labelledby="author-tab"><div class="card z-index-0"style="border-top-left-radius: 0;">
              <div class="card-header text-center pt-4">
                <h5>YAZAR ADI SORGULAMA</h5>
              </div>
              <div class="row px-xl-5 px-sm-4 px-3">
                
                
                
                <div class="mt-2 position-relative text-center">
                  
                </div>
              </div>
              <div class="card-body">
                <form method="POST" role="form text-left">
                  <div class="mb-3">
                    <input name="author" type="text" class="form-control" placeholder="lütfen yazar adı giriniz" aria-label="Name" aria-describedby="email-addon">
                  </div>
                  
                  
                  
                  <div class="text-center">
                    <button type="submit" class="btn bg-primary w-100 my-4 mb-2 text-white">SORGULA</button>
                  </div>
                  
                </form>
              </div>
            </div></div>
  
  <div class="tab-pane fade" id="name" role="tabpanel" aria-labelledby="name-tab"><div class="card z-index-0"style="border-top-left-radius: 0;">
              <div class="card-header text-center pt-4">
                <h5>KİTAP ADI SORGULAMA</h5>
              </div>
              <div class="row px-xl-5 px-sm-4 px-3">
                
                
                
                <div class="mt-2 position-relative text-center">
                  
                </div>
              </div>
              <div class="card-body">
                <form method="POST" role="form text-left">
                  <div class="mb-3">
                    <input name="name" type="text" class="form-control" placeholder="lütfen kitap adı giriniz" aria-label="Name" aria-describedby="email-addon">
                  </div>
                  
                  
                  
                  <div class="text-center">
                    <button type="submit" class="btn bg-primary w-100 my-4 mb-2 text-white">SORGULA</button>
                  </div>
                  
                </form>
              </div>
            </div></div>
</div>
</div>
</div>

