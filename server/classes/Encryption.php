<?php
class Encryption {
  function crypt($string, $action) {
    $secretKey = 'cvObsRffks';
    $secretIV = 'IDGZVIBsye';

    $output = false;
    $encryptMethod = "AES-256-CBC";
    $key = hash('sha256', $secretKey);
    $iv = substr(hash('sha256', $secretIV), 0, 16);

    if($action == 'e') {
      $output = base64_encode(openssl_encrypt($string, $encryptMethod, $key, 0, $iv));
    }
    else if($action == 'd'){
      $output = openssl_decrypt(base64_decode($string), $encryptMethod, $key, 0, $iv);
    }

    return $output;
  }
}
?>
