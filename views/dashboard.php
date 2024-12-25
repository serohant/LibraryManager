<?php 
$pagename = "Ana Sayfa";


require __DIR__."/header.php";

$bugun = date('Y-m-d');
$query = $db->prepare("SELECT COUNT(*) FROM guests WHERE DATE(date) = :bugun");
$query->execute(['bugun' => $bugun]);
$totalvisitercount = $query->fetchColumn();


$stmt = $db->query("SELECT COUNT(*) as row_count FROM books");
$row_info = $stmt->fetch(PDO::FETCH_ASSOC);
$row_count = $row_info['row_count'];

// 2. Tüm kitapların stock değerlerinin toplamını hesapla
$stmt = $db->query("SELECT SUM(stock) as total_stock FROM books");
$stock_info = $stmt->fetch(PDO::FETCH_ASSOC);
$total_stock = $stock_info['total_stock'];
if (!isset($total_stock)) {
  $total_stock = 0;
}

// Sonuçları ekrana yazdır
$bookstock = $row_count." ($total_stock)";

?> 

<div class="container-fluid py-4">
  <div class="row mt-4">
    <div class="col-lg-6 col-md-6 col-12">
      <div class="card">
        <span class="mask bg-dark opacity-10 border-radius-lg"></span>
        <div class="card-body p-3 position-relative">
          <div class="row">
            <div class="col-8 text-start">
              <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                <i class="ni ni-cart text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
              </div>
              <h5 class="text-white font-weight-bolder mb-0 mt-3"> <?=$bookstock?> </h5>
              <span class="text-white text-sm">Toplam Kitap Stoğu</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-6 col-12 mt-4 mt-md-0">
      <div class="card">
        <span class="mask bg-dark opacity-10 border-radius-lg"></span>
        <div class="card-body p-3 position-relative">
          <div class="row">
            <div class="col-8 text-start">
              <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                <i class="ni ni-like-2 text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
              </div>
              <h5 class="text-white font-weight-bolder mb-0 mt-3"> <?=$totalvisitercount?> </h5>
              <span class="text-white text-sm">Toplam Günlük Ziyaretçi</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 pt-4">
      <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
          <p>Son gelen 5 ziyaretçi</p>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">AD SOYAD</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TC KIMLIK</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TARIH</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">KAYITLI MI?</th>
              </tr>
            </thead>
            <tbody> <?php 
                $bugun = date('Y-m-d');
                $query = $db->prepare("SELECT * FROM guests WHERE DATE(date) = :bugun");
                $query->execute(['bugun' => $bugun]);
                if ($query->rowCount()) {
                    foreach ($query as $row) {
                        if($row['first_time'] == 1){
                            $row['first_time'] = "HAYIR";
                        }else{
                            $row['first_time'] = "EVET";
                        }
                        echo '
							<tr>
									<td class="text-center">
										<p class="text-center text-xs text-secondary mb-0">'.$row['mrz_name'].' '.$row['mrz_surname'].'</p>
									</td>
									<td class="text-center">
										<p class="text-center text-xs font-weight-bold mb-0">'.$row['mrz_id'].'</p>
									</td>
									<td class="text-center">
										<p class="text-center text-xs font-weight-bold mb-0">'.$row['date'].'</p>
									</td>
									<td class="text-center">
										<p class="text-center text-xs font-weight-bold mb-0">'.$row['first_time'].'</p>
									</td>
								</tr>';
                    }
                }else{
                  echo '
                  <tr class="no-data">
                  <td colspan="6">Günlük ziyaret kaydı bulunamadı</td>
              </tr>';
            }
                ?> </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 pt-4">
      <div class="card">
      <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
          <p>Teslim süresi yaklaşan 5</p>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kira Numarası</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kullanıcı ID</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kitap ID</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kira Başlangıç Zamanı</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kira Bitiş Zamanı</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              
              $sql = "SELECT * FROM loan 
              WHERE is_ended = 0 
              AND end_time IS NULL 
              ORDER BY expiry_date ASC 
              LIMIT 5";
  

              $stmt = $db->prepare($sql);
              $stmt->execute();
              $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
              if (count($rows) < 0) {
                foreach ($rows as $row) {
                  echo '
                <tr>
                    <td class="text-center">
                      <p class="text-center text-xs text-secondary mb-0">'.$row['id'].'</p>
                    </td>
                    <td class="text-center">
                      <p class="text-center text-xs text-secondary mb-0">'.$row['user_id'].'</p>
                    </td>
                    <td class="text-center">
                      <p class="text-center text-xs font-weight-bold mb-0">'.$row['book_id'].'</p>
                    </td>
                    <td class="text-center text-sm">
                      <p class="text-center text-xs font-weight-bold mb-0">'.date("d/m/Y H:i", strtotime($row['start_date'])).'</p>
                    </td>
                    <td >
                      <p class="text-center text-xs font-weight-bold mb-0">'.date("d/m/Y H:i", strtotime($row['expiry_date'])).'</p>
                    </td>
                  </tr>';
                }
              }else{
                echo '
                <tr class="no-data">
                <td colspan="5">Teslimi yaklaşan kira bulunamadı</td>
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
</div>