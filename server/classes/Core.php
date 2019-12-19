<?php
  class Core {
    // Show alerts, use bootstrap
    function showAlert($type, $alert) {
      $alert = "<div class='alert alert-$type' role='alert'>
      <center>$alert</center>
      </div>";
      return $alert;
    }

    function getClientIP() {
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
      return $ip;
    }
  }
?>
