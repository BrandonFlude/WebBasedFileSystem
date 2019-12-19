<?php
  require("config/config.php");
  require("classes/Sql.php");

  class Upload {
    // Find the extension of the file that's beeing uploaded.
    function findExtension($filename) {
      $filename = strtolower($filename) ;
      $exts = explode(".", $filename) ;
      $n = count($exts)-1;
      $exts = $exts[$n];
      return $exts;
    }

    // Limit the file types we will accept - this can always be expanded.
    function checkExtension($extension) {

      // Allow certain file formats - down to you to add what you want...
      // $allowedExtensions configured in /config/config.php
      global $allowedExtensions;
      if(in_array($extension, $allowedExtensions)) {
        return true;
      } else {
        return false;
      }
    }

    // Generate a 'random' file name, this stops us having multiple dog.png.
    // Also, I am going to upload plenty of pictures of my dog Astro for testing.
    function generateString($length) {
      $string = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
      return $string;
    }

    // Here we will insert the file's data into our database, there are probably some issues with this such as
    // uploading SQL text, but I am blocking .sql files so this should margainly help.
    function insertToDatabase($fileName, $realFileName, $extension, $server, $fileDescription, $fileOwner, $fileChecksum) {
      // New SQL instance
      $goSql = new Sql;

      $query = $goSql->runQuery("insert", "INSERT INTO docs (document_name, document_real_name, document_ext, file_location, file_description, file_owner, document_checksum) VALUES ('$fileName', '$realFileName', '$extension', '$server', '$fileDescription', '$fileOwner', '$fileChecksum')");
      return true;
    }
  }
?>
