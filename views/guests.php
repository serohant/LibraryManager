<?php 
$pagename = "Ziyaretçiler";


require __DIR__."/header.php";

?> <div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-12 col-12">
      <div class="card">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">AD SOYAD</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TC KIMLIK</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IRK</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TARIH</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">KAYITLI MI?</th>
              </tr>
            </thead>
            <tbody>
                <?php 
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
                        echo '<tr>
                                <td class="align-middle text-center">
                                  <p class="text-center text-xs text-secondary mb-0">'.$row['mrz_name'].' '.$row['mrz_surname'].'</p>
                                </td>
                                <td class="align-middle text-center">
                                  <p class="text-center text-xs font-weight-bold mb-0">'.substr($row['mrz_id'],0,3).'******'.substr($row['mrz_id'],9,2).'</p>
                                </td>
                                <td class="align-middle text-center">
                                  <p class="text-center text-xs font-weight-bold mb-0">'.$row['mrz_nationality'].'</p>
                                </td>
                                <td class="align-middle text-center">
                                  <p class="text-center text-xs font-weight-bold mb-0">'.$row['date'].'</p>
                                </td>
                                <td class="align-middle text-center">
                                  <p class="text-center text-xs font-weight-bold mb-0">'.$row['first_time'].'</p>
                                </td>
                            </tr>';
                    }
                }else{
                  echo '
                  <tr class="no-data">
                  <td colspan="7">Günlük ziyaret kaydı bulunamadı</td>
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