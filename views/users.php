<?php 
$pagename = "Kullanıcılar";
session_start(); 

require __DIR__."/header.php";

function deleteUser($userId) {
  global $db;
  // Veritabanı bağlantı bilgileri
  try {
      // PDO ile veritabanı bağlantısı
      
      // Kullanıcı silme sorgusu
      $sql = "DELETE FROM users WHERE id = :id";
      $stmt = $db->prepare($sql);

      // Parametrenin atanması
      $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

      // Sorguyu çalıştır
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        echo "<script>Swal.fire({
          title: 'Hata!',
          html: 'Kullanıcı başarıyla silindi!',
          icon: 'error',
          confirmButtonText: 'Tamam'
      }).then(function() {
          window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
      })</script>";
      exit(); // İşlemi durdur
      } else {
        echo "<script>Swal.fire({
          title: 'Hata!',
          html: 'Belirtilen id ile kullanıcı bulunamadı!',
          icon: 'error',
          confirmButtonText: 'Tamam'
      }).then(function() {
          window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
      })</script>";
      exit(); // İşlemi durdur
      }
  } catch (PDOException $e) {
      echo "Hata: " . $e->getMessage();
  }
}
function addUser($tc_no, $name, $surname, $phone_number, $mail_address, $permission=0) {
  global $db;
  // Veritabanı bağlantı bilgileri
  
  $phone_number = "90".$phone_number;
  try {
      // PDO ile veritabanı bağlantısı
      
      // Kullanıcı ekleme sorgusu
      $sql = "INSERT INTO users (tc_no, name, surname, phone_number, mail_address, permission) 
              VALUES (:tc_no, :name, :surname, :phone_number, :mail_address, :permission)";
      $stmt = $db->prepare($sql);

      // Parametrelerin atanması
      $stmt->bindParam(':tc_no', $tc_no, PDO::PARAM_INT);
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
      $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
      $stmt->bindParam(':mail_address', $mail_address, PDO::PARAM_STR);
      $stmt->bindParam(':permission', $permission, PDO::PARAM_INT);

      // Sorguyu çalıştır
      $stmt->execute();

      
  } catch (PDOException $e) {
      echo "Hata: " . $e->getMessage();
  }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['tc']) && $_POST['tc'] != null) {
      $tc_no = $_POST['tc'];
      $name = $_POST['İsim'];
      $surname = $_POST['Soyisim'];
      $phone_number = $_POST['tel'];
      $mail_address = $_POST['mail'];
      addUser($tc_no, $name, $surname, $phone_number, $mail_address);

      // PRG Modeli: Kullanıcıyı yeniden yönlendir
      echo "<script>Swal.fire({
        title: 'Hata!',
        html: 'Kullanıcı başarıyla eklendi!',
        icon: 'success',
        confirmButtonText: 'Tamam'
    }).then(function() {
        window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
    })</script>";
    exit(); // İşlemi durdur
  }

  if (isset($_POST['removeUser']) && $_POST['removeUser'] != null) {
      deleteUser($_POST['removeUser']);
      echo "<script>Swal.fire({
        title: 'Hata!',
        html: 'Kullanıcı başarıyla silindi!',
        icon: 'error',
        confirmButtonText: 'Tamam'
    }).then(function() {
        window.location.href = window.location.pathname; // Sayfayı yönlendirerek POST verilerini temizle
    })</script>";
    exit(); // İşlemi durdur
  }
}

?> <div class="container-fluid py-4">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 48 || charCode > 57))
        return false;

    return true;
}
</script>
  <div class="row">
    <div class="col-lg-12 col-12">
      <div class="card">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TC KIMLIK</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">AD SOYAD</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TELEFON NUMARASI</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">MAİL</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><button class="text-white btn btn-sm bg-primary ms-auto mb-0 " data-bs-toggle="modal" data-bs-target="#exampleModalSignUp">
                  Kullanıcı Ekle
                </button></th>
              </tr>
            </thead>
            <tbody>
                <?php 
                $query = $db->query("SELECT * FROM users", PDO::FETCH_ASSOC);
                if ($query->rowCount()) {
                    foreach ($query as $row) {
                        echo '<tr>
                        <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">'.$row['tc_no'].'</p>
                                </td>
                                <td>
                                  <p class="text-xs text-secondary mb-0">'.$row['name'].' '.$row['surname'].'</p>
                                </td>
                                <td>
                                  <p class="text-center text-xs font-weight-bold mb-0">'.$row['phone_number'].'</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">'.$row['mail_address'].'</p>
                                </td>
                                 <td class="align-middle text-center text-sm">
                                  <form method="POST"><input type="hidden" name="removeUser" value="'.$row['id'].'" ><button type="submit" class="btn btn-sm bg-gradient-dark ms-auto mb-0">
                  Kullanıcı Sil
                </button></form>
                                </td>
                            </tr>';
                    }
                }else{
                  echo '
                  <tr class="no-data">
                  <td colspan="9">Kullanıcı bulunamadı</td>
              </tr>';
            }

                ?>
            </tbody>
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
                  <h3 class="font-weight-bolder text-primary text-gradient">Kullanıcı Ekle</h3>
              </div>
              <div class="card-body pb-3">
                <form method="POST" role="form text-left">
                  <label>İsim</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="İsim" aria-label="Name" aria-describedby="name-addon" name="İsim">
                  </div>
                  <label>Soyisim</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Soyisim" aria-label="Email" aria-describedby="email-addon" name="Soyisim">
                  </div>
                  <label>TC Kimlik Numarası</label>
                  <div class="input-group mb-3">
                    <input type="number" class="form-control" placeholder="TC Kimlik Numarası" aria-label="Password" aria-describedby="password-addon" name="tc">
                  </div>
                  <label>Telefon Numarası</label>
                  <div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">+90</span>
  </div>
  <input type="tel" class="form-control" placeholder="Telefon Numarası" aria-label="Email" aria-describedby="email-addon" min="5000000000" name="tel" aria-describedby="basic-addon1" maxLength="10" onkeypress="return isNumberKey(event)">
</div>                    
                  <label>Mail Adresi</label>
                  <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Mail Adresi" aria-label="Email" aria-describedby="email-addon" name="mail">
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Oluştur</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>