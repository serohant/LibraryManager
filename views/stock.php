<?php 
$pagename = "Stok Takibi";
session_start(); 

require __DIR__."/header.php";

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
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ISBN</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kitap Adı</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Yazar</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Yayın Evi</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Konu(Lar)</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sayfa Sayısı</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dil</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stok</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                  <button class="text-white btn btn-sm bg-primary ms-auto mb-0 " data-bs-toggle="modal" data-bs-target="#exampleModalSignUp"> Kitap Ekle </button>
                </th>
              </tr>
            </thead>
            <tbody> <?php 
                $query = $db->query("SELECT * FROM books", PDO::FETCH_ASSOC);
                if ($query->rowCount()) {
                    foreach ($query as $row) {
                      $jsonData = json_encode($row, JSON_UNESCAPED_UNICODE);
                        echo '
							<tr>
								<td class="align-middle text-center text-sm">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['isbn'].'</p>
								</td>
								<td class="align-middle text-center">
									<p class="text-center text-xs text-secondary mb-0">'.$row['name'].'</p>
								</td>
								<td class="align-middle text-center">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['author'].'</p>
								</td>
								<td class="align-middle text-center text-sm">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['publisher'].'</p>
								</td>
								<td class="align-middle text-center text-sm">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['subjects'].'</p>
								</td>
								<td class="align-middle text-center text-sm">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['pages'].'</p>
								</td>
								<td class="align-middle text-center text-sm">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['language'].'</p>
								</td>
								<td class="align-middle text-center text-sm">
									<p class="text-center text-xs font-weight-bold mb-0">'.$row['stock'].'</p>
								</td>
								<td class="align-middle text-center text-sm">
											<button onclick="update(this)" type="submit" class="btn btn-sm bg-gradient-dark ms-auto mb-0" data-bs-toggle="modal" data-bs-target="#updateModal" 
                    data-book="'.htmlspecialchars(json_encode($row, JSON_UNESCAPED_UNICODE)).'">
                    Güncelle
                </button>
									</td>
								</tr>';
                    }
                }else{
                  echo '
                  <tr class="no-data">
                  <td colspan="9">Stok bulunamadı</td>
              </tr>';
            }

                ?> </tbody>
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
              <h3 class="font-weight-bolder text-primary text-gradient">Kitap Ekle</h3>
            </div>
            <div class="card-body pb-3">
              <form method="POST" role="form text-left">
                <label>ISBN</label>
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="ISBN" aria-label="ISBN" aria-describedby="name-addon" name="isbn" id="isbn">
                </div>
                <div class="card d-none" id="datacard">
                  <div class="card-body">
                    <p class="card-text" id="data"></p>
                    <a id="add" class="btn btn-primary">Sisteme ekle</a>
                    <small>
                      <p>Eğer ilgili kitap sistemde zaten mevcutsa stoğu 1 arttırır.</p>
                    </small>
                  </div>
                </div>
                <div class="text-center">
                  <button type="button" id="checkisbn" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Bilgi Getir</button>
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
              <div class="row">
                  <div class="col-md-6">
                    <label>ISBN</label>
                      <div class="input-group mb-3">
                        <input type="number" disabled class="form-control" placeholder="ISBN" aria-label="ISBN" aria-describedby="name-addon" name="uisbn" id="uisbn">
                      </div>
                  </div>
                  <div class="col-md-6">
                    <label>Kitap Adı</label>
                      <div class="input-group mb-3">
                        <input type="text" disabled class="form-control" placeholder="Kitap adı" value="" aria-label="name" aria-describedby="name-addon" name="name" id="name">
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label>Yazar (, ile ayırın)</label>
                      <div class="input-group mb-3">
                        <input type="text" disabled class="form-control" placeholder="Yazar" value="" aria-label="yazar" aria-describedby="name-addon" name="yazar" id="yazar">
                      </div>
                  </div>
                  <div class="col-md-6">
                    <label>Yayın Evi</label>
                      <div class="input-group mb-3">
                        <input type="text" disabled class="form-control" placeholder="Yayın Evi" value="" aria-label="yayinevi" aria-describedby="name-addon" name="yayinevi" id="yayinevi">
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label>Yayın Yılı</label>
                      <div class="input-group mb-3">
                        <input type="text" disabled class="form-control" placeholder="Yayın Yılı" value="" aria-label="yil" aria-describedby="name-addon" name="yil" id="yil">
                      </div>
                  </div>
                  <div class="col-md-6">
                    <label>Sayfa</label>
                      <div class="input-group mb-3">
                        <input type="text" disabled class="form-control" placeholder="Sayfa" aria-label="sayfa" aria-describedby="name-addon" name="sayfa" id="sayfa">
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label>Dil </label>
                      <div class="input-group mb-3">
                        <input type="text" disabled class="form-control" placeholder="Dil" value="" aria-label="dil" aria-describedby="name-addon" name="dil" id="dil">
                      </div>
                  </div>
                  <div class="col-md-6">
                    <label>Stok</label>
                      <div class="input-group mb-3">
                        <input type="number" class="form-control" placeholder="Stok" value="" aria-label="stok" aria-describedby="name-addon" name="stok" id="stok">
                      </div>
                  </div>
                </div>
                <div class="text-center">
                  <button type="button" id="updateBook" class="btn bg-gradient-primary btn-lg btn-rounded w-100 mt-4 mb-0">Güncelle</button>
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
  var lastData = ""
  $('#checkisbn').click(function() {
    var isbn = $('#isbn').val();
    $.ajax({
      url: '/app/getBookByISBN.php', // Sunucudaki endpoint
      type: 'GET', // HTTP metodu
      data: {
        isbn: isbn
      }, // Gönderilecek parametre
      success: function(response) {
        $('#datacard').removeClass("d-none")
        // Dönen veriyi konsola yazdır
        lastData = response
        var bookInfo = `
                        
			<strong>Kitap Adı:</strong> ${response.book.title} 
			<br>
				<strong>Yazar(lar):</strong> ${response.book.authors.join(', ')} 
				<br>
					<strong>Yayınevi:</strong> ${response.book.publisher} 
					<br>
						<strong>Yayın Tarihi:</strong> ${response.book.date_published} 
						<br>
							<strong>Sayfa Sayısı:</strong> ${response.book.pages} 
							<br>
								<strong>ISBN-13:</strong> ${response.book.isbn13} 
								<br>
                    `;
        // Bilgileri HTML'e ekle
        $('#data').html(bookInfo);
      },
      error: function(xhr, status, error) {
        // Hata mesajını konsola yazdır
        console.error("Hata:", status, error);
      }
    });
  })
  $('#add').click(function() {
    $.ajax({
      url: '/app/getBookByISBN.php', // Sunucudaki endpoint
      type: 'POST', // HTTP metodu
      data: {
        data: lastData
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

  function update(button) {
    // data-book attribute'undan JSON verisini alıyoruz
    var data = JSON.parse(button.getAttribute('data-book'));
    // Modal verilerini güncelliyoruz
    $('#uisbn').val(data.isbn);
    $('#name').val(data.name);
    $('#yazar').val(data.author);
    $('#yayinevi').val(data.publisher);
    $('#yil').val(data.publishdate);
    $('#sayfa').val(data.pages);
    $('#dil').val(data.language);
    $('#stok').val(data.stock);
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