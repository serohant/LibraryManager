<?php 

require 'config.php';


function alert($title, $message, $type = "success"){
    return $showalert = "<script>Swal.fire({
  title: $title,
  text: $message,
  icon: $type,
  confirmButtonText: 'Tamam'
})</script>";
}

?>