<?php 
$pagename = "Ödünç Alınanlar";
session_start();
require __DIR__."/header.php"; 
if ($_POST) {
  $data = $_POST;

  // Kullanıcı kontrolü
  $stmt = $db->prepare("SELECT id FROM users WHERE tc_no = :tc");
  $stmt->execute(['tc' => $data['tc']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
      echo "<script>Swal.fire({
          title: 'Hata!',
          html: 'Kullanıcı bulunamadı!',
          icon: 'error',
          confirmButtonText: 'Tamam'
      }).then(function() {
          window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
      })</script>";
      exit(); // İşlemi durdur
  }

  $user_id = $user['id'];

  // Kitap kontrolü
  $stmt = $db->prepare("SELECT id, stock FROM books WHERE isbn = :isbn");
  $stmt->execute(['isbn' => $data['isbn']]);
  $book = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$book || $book['stock'] < 1) {
      echo "<script>Swal.fire({
          title: 'Hata!',
          html: 'ISBN Numarasını ya da stoğu kontrol edin!',
          icon: 'error',
          confirmButtonText: 'Tamam'
      }).then(function() {
          window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
      })</script>";
      exit(); // İşlemi durdur
  }

  $book_id = $book['id'];

  // loan tablosuna kayıt ekle
  try {
      $db->beginTransaction();

      $stmt = $db->prepare("INSERT INTO loan (user_id, book_id, start_date, expiry_date, end_time) 
                             VALUES (:user_id, :book_id, CURRENT_TIMESTAMP, :expiry_date, NULL)");
      $stmt->execute([
          'user_id' => $user_id,
          'book_id' => $book_id,
          'expiry_date' => $data['expiry_date']
      ]);

      // Kitap stoğunu güncelle
      $stmt = $db->prepare("UPDATE books SET stock = stock - 1 WHERE id = :book_id");
      $stmt->execute(['book_id' => $book_id]);

      $db->commit();

      // Başarılı işlem sonrası sayfayı yeniden yükle
      echo "<script>Swal.fire({
          title: 'Başarılı!',
          html: 'Kitap başarıyla ödünç verildi!',
          icon: 'success',
          confirmButtonText: 'Tamam'
      }).then(function() {
          window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
      })</script>";
      exit();
  } catch (Exception $e) {
      $db->rollBack();
      echo "Hata: " . $e->getMessage();
  }
}

?> <div class="container-fluid py-4">
  <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <div class="row">
    <div class="col-lg-12 col-12">
      <div class="card">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kira Numarası</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı ismi</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kitap ismi</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kira Başlangıç Zamanı</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kira Bitiş Zamanı</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Teslim tarihi</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Teslim durumu</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  <button class="text-white btn btn-sm bg-primary ms-auto mb-0 " data-bs-toggle="modal" data-bs-target="#exampleModalSignUp"> Ödünç Ekle </button>
                </th>
              </tr>
            </thead>
            <tbody> <?php 

// loan tablosundaki tüm verileri çek
$query = "SELECT * FROM loan";
$stmt = $db->query($query);

// Verileri işlemek
// Verileri bir diziye topla
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // user_id ve book_id'yi al
    $userId = $row['user_id'];
    $bookId = $row['book_id'];

    // users tablosunda user_id'yi ara
    $userQuery = "SELECT name, surname FROM users WHERE id = :userId";
    $userStmt = $db->prepare($userQuery);
    $userStmt->execute(['userId' => $userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $fullName = $user ? $user['name'] . ' ' . $user['surname'] : 'Kullanıcı bulunamadı';

    // Teslim durumu
    $is_ended = $row['is_ended'] == 1 ? "Teslim Edildi" : "Teslim Edilmedi";

    // books tablosunda book_id'yi ara
    $bookQuery = "SELECT name FROM books WHERE id = :bookId";
    $bookStmt = $db->prepare($bookQuery);
    $bookStmt->execute(['bookId' => $bookId]);
    $book = $bookStmt->fetch(PDO::FETCH_ASSOC);

    $bookName = $book ? $book['name'] : 'Kitap bulunamadı';

    // Bitiş tarihi kontrolü
    $bitistarih = $row['end_time'] == null ? "Teslim Edilmedi" : date("d/m/Y H:i", strtotime($row['end_time']));

    // Verileri diziye ekle
    $data[] = [
        'id' => $row['id'],
        'fullName' => $fullName,
        'bookName' => $bookName,
        'start_date' => date("d/m/Y H:i", strtotime($row['start_date'])),
        'expiry_date' => date("d/m/Y H:i", strtotime($row['expiry_date'])),
        'end_time' => $bitistarih,
        'is_ended' => $is_ended,
        'row_data' => json_encode($row, JSON_UNESCAPED_UNICODE),
    ];
}

// Eğer veri yoksa "asd" yazdır, aksi halde verileri göster
if (empty($data)) {
    echo '<tr><td colspan="8" class="text-center">Kiralanmış kitap bulunamadı</td></tr>';
} else {
    foreach ($data as $rowData) {
        echo '
        <tr>
            <td class="text-center align-middle text-sm">
                <p class="text-center text-xs font-weight-bold mb-0">' . $rowData['id'] . '</p>
            </td>
            <td class="text-center align-middle">
                <p class="text-center text-xs text-secondary mb-0">' . $rowData['fullName'] . '</p>
            </td>
            <td class="text-center align-middle">
                <p class="text-center text-xs font-weight-bold mb-0">' . $rowData['bookName'] . '</p>
            </td>
            <td class="text-center align-middle text-sm">
                <p class="text-center text-xs font-weight-bold mb-0">' . $rowData['start_date'] . '</p>
            </td>
            <td class="text-center align-middle">
                <p class="text-center text-xs font-weight-bold mb-0">' . $rowData['expiry_date'] . '</p>
            </td>
            <td class="text-center align-middle">
                <p class="text-center text-xs font-weight-bold mb-0">' . $rowData['end_time'] . '</p>
            </td>
            <td class="text-center align-middle">
                <p class="text-center text-xs font-weight-bold mb-0">' . $rowData['is_ended'] . '</p>
            </td>
            <td class="align-middle text-center text-sm">
                <button onclick="update(this)" type="submit" class="btn btn-sm bg-gradient-dark ms-auto mb-0" data-bs-toggle="modal" data-bs-target="#updateModal" 
                data-book="' . htmlspecialchars($rowData['row_data']) . '">
                    Güncelle
                </button>
            </td>
        </tr>';
    }
}
?></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-md-4">
  <!-- Button trigger modal -->
  <!-- Modal -->
  <div class="modal fade" id="exampleModalSignUp" tabindex="-1" role="dialog" aria-labelledby="exampleModalSignTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-body p-0">
          <div class="card card-plain">
            <div class="card-header pb-0 text-left">
              <h3 class="font-weight-bolder text-primary text-gradient">Ödünç Ekle</h3>
            </div>
            <div class="card-body pb-3">
              <form method="POST" role="form text-left">
                <label>TC Kimlik</label>
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="TC Kimlik" aria-label="TC Kimlik" aria-describedby="name-addon" name="tc" id="tc">
                </div>
                <label>ISBN</label>
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="ISBN" aria-label="ISBN" aria-describedby="name-addon" name="isbn" id="isbn">
                </div>
                <div class="row">
                  <div class="col-md-6">
                  <div class="form-group">
                    <label for="example-datetime-local-input" class="form-control-label">Alım Tarihi</label>
                    <input disabled class="form-control" type="datetime-local" value="<?=(new DateTime())->format('Y-m-d\TH:i:s')?>" id="example-datetime-local-input">
                  </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                    <label for="example-datetime-local-input" class="form-control-label">Teslim Tarihi</label>
                    <input class="form-control" type="datetime-local" value="<?=(new DateTime('+14 days'))->format('Y-m-d\TH:i:s')?>" name="expiry_date" min="<?=(new DateTime('+7 days'))->format('Y-m-d\TH:i:s')?>" id="example-datetime-local-input">
                  </div>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" id="createLoan" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Kayıt Oluştur</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-body p-0">
          <div class="card card-plain">
            <div class="card-header pb-0 text-left">
              <h3 class="font-weight-bolder text-primary text-gradient">Kitap güncelle</h3>
            </div>
            <div class="card-body pb-3">
              <form method="POST" role="form text-left">                
              <input disabled type="hidden" name="rentid" value="" id="rentid">
              <div class="row">
                  <div class="col-md-6">
                    <label>Kullanıcı ID</label>
                      <div class="input-group mb-3">
                        <input type="number" disabled class="form-control" placeholder="Kullanıcı ID" aria-label="Kullanıcı ID" aria-describedby="name-addon" name="userid" id="userid">
                      </div>
                  </div>
                  <div class="col-md-6">
                    <label>Kitap ID</label>
                      <div class="input-group mb-3">
                        <input type="number" disabled class="form-control" placeholder="Kitap ID" value="" aria-label="Kitap ID" aria-describedby="name-addon" name="bookid" id="bookid">
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                  <div class="form-group">
                    <label for="example-datetime-local-input" class="form-control-label">Alım Tarihi</label>
                    <input disabled class="form-control" type="datetime-local" value="" id="date1">
                  </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                    <label for="example-datetime-local-input" class="form-control-label">Teslim Tarihi</label>
                    <input disabled class="form-control" type="datetime-local" value="" name="expiry_date" min="" id="date2">
                  </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                  <div class="text-center">
                  <button type="button" id="endloan" class="btn bg-gradient-primary btn-rounded">Teslim al</button>
                </div>
                  </div>
                  <div class="col-md-6">
                  <div class="text-center">
                  <button type="button" id="adddays" class="btn bg-gradient-primary btn-rounded">7 gün ekle</button>
                </div>
                  </div>
                </div>
                <div class="text-center">
                  <button type="button" data-bs-dismiss="modal" class="btn bg-gradient-primary btn-lg btn-rounded">Vazgeç</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>

$("#adddays").click(function(){
    $.ajax({
      url: '/app/rent.php', // Sunucudaki endpoint
      type: 'POST', // HTTP metodu
      data: {
        rent: 1,
        id: $('#rentid').val()
      }, // Gönderilecek parametre
      success: function(response) {
        swal.fire({
          title: "Başarılı!",
          text: response.response,
          icon: "success"
        }).then(function() {
          location.reload();
        });
      }})
  })

  $("#endloan").click(function(){
    $.ajax({
      url: '/app/rent.php', // Sunucudaki endpoint
      type: 'POST', // HTTP metodu
      data: {
        end: 1,
        id: $('#rentid').val()
      }, // Gönderilecek parametre
      success: function(response) {
        swal.fire({
          title: "Başarılı!",
          text: response.response,
          icon: "success"
        }).then(function() {
          location.reload();
        });
      }})
  })


  function update(button) {
    // data-book attribute'undan JSON verisini alıyoruz
    var data = JSON.parse(button.getAttribute('data-book'));
    console.log(data)
    // Modal verilerini güncelliyoruz
    $('#rentid').val(data.id);
    $('#userid').val(data.user_id);
    $('#bookid').val(data.book_id);
    $('#date1').val(data.start_date.replace(' ', 'T').slice(0, 16));
    $('#date2').val(data.expiry_date .replace(' ', 'T').slice(0, 16));
}

$('#updateBook').click(function(){

  $.ajax({
      url: '/app/getBookByISBN.php', // Sunucudaki endpoint
      type: 'POST', // HTTP metodu
      data: {
        update: 2,
        isbn: $('#uisbn').val(),
        stock: $('#stok').val()
      }, // Gönderilecek parametre
      success: function(response) {
        swal.fire({
          title: "Başarılı!",
          text: response.response,
          icon: "success"
        }).then(function() {
          location.reload();
        });
      },
      error: function(xhr, status, error) {
        // Hata mesajını konsola yazdır
        console.error("Hata:", status, error);
      }
    });
})

</script>