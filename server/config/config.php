<?php
  // Some clever code that will find what server we are actually running on here.
  // Allowing us to access files from other servers
  //$server = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $server = "http://$_SERVER[HTTP_HOST]";

  // This will stop files from downloading - useful!
  $config_displayInBrowser = false;

  // All extensions you wish to allow to be uploaded
  $allowedExtensions = array("png", "jpg", "jpeg", "gif", "txt", "doc", "docx", "pdf", "xls",
                             "xlsx", "iso", "zip", "csv", "rtf");

  // Size of each individual chunk of the master file
  $sizeOfChunk = 1048576; // Actually 1Mb.

?>
