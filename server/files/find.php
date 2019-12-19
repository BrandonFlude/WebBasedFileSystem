<?php
  // Include our SQL class, making sure we get it right from the root.
  require($_SERVER["DOCUMENT_ROOT"]."/classes/Sql.php");
  require($_SERVER["DOCUMENT_ROOT"]."/classes/Core.php");
  require($_SERVER["DOCUMENT_ROOT"]."/classes/Encryption.php");

  // Include configuration file
  require($_SERVER["DOCUMENT_ROOT"]."/config/config.php");

  // Get the file they are looking for (from URL)
  $fileName = $_GET['filename'];

  // We're now going to run a query to look for this file to see if it's on this server
  $goSql = new Sql;
  $goCore = new Core;
  $goEncrypt = new Encryption;

  // Run a query to see if this file exists in whole on the server
  $data = $goSql->runQuery("select", "SELECT document_real_name, document_name, document_ext FROM docs WHERE document_name = '$fileName' AND file_location = '$server'");

  // Check if result exists
  if($data != false) {
    // File exists on this server, pull it down directly.
    $row = mysqli_fetch_assoc($data);
    $documentExtension = $row['document_ext'];

    // Set the document to be the file
    $document = file_get_contents("$server/uploads/$fileName");
  } else {
    // Doesn't exist on peer, so we'll go check for chunks on other servers!
    $data = $goSql->runQuery("select", "SELECT DISTINCT document_name, MAX(document_ext), MAX(file_location) FROM docs WHERE document_name LIKE '$fileName.%' GROUP BY document_name ORDER BY document_name ASC");
    if($data != false)
    {
      // Loop through
      // This loop contains every part of the requested file
      while($row = mysqli_fetch_array($data)) {
        $fileLocation = $row['MAX(file_location)'];
        $documentExtension = $row['MAX(document_ext)'];

        $chunkName = $row['document_name'];

        $chunkContent = file_get_contents("$fileLocation/chunks/$chunkName");
        if($chunkContent != "") {
          // Chunk is there, add it to our chunk builder
          // Decrypt this chunk
          $chunkBuilder .= $goEncrypt->crypt($chunkContent, "d");
        } else {
          // Chunk isn't there, so we need to check where else this chunk is (not the one that just failed on us)
          $chunkFinder = $goSql->runQuery("select", "SELECT document_name, document_ext, file_location FROM docs WHERE document_name = '$chunkName' AND file_location != '$fileLocation'");
          while($potentialChunk = mysqli_fetch_array($chunkFinder))
          {
            // Loop through each potential chunk and check if it is available
            $fileLocation = $potentialChunk['file_location'];
            $documentExtension = $potentialChunk['document_ext'];
            $chunkName = $potentialChunk['document_name'];

            // Check this chunk's availablity
            $chunkContent = file_get_contents("$fileLocation/chunks/$chunkName");
            if($chunkContent != "")
            {
              //$chunkBuilder .= base64_decode($chunkContent);
              // Decrypt this chunk
              $chunkBuilder .= $goEncrypt->crypt($chunkContent, "d");

              // Exit while loop
              break 1;
            }
          }
          // If we ever get here, it means a chunk couldn't be fulfilled.
          // Direct to error page
          header("Location: ../error/?error=nochunk");
        }

        // We'll now fetch this chunk and save it to this peer
        if($fileLocation != $server) {
          file_put_contents("../chunks/$chunkName", fopen("$fileLocation/chunks/$chunkName", "r"));
          $ipAddress = $goCore->getClientIP();

          // And now update the database to say we have a copy of this chunk
          $goSql->runQuery("insert", "INSERT INTO docs (document_name, document_real_name, document_ext, file_location, file_description, file_owner, document_checksum) VALUES ('$chunkName', 'null', '$documentExtension', '$server', 'null', '$ipAddress', 'chunk, no checksum')");
        }
      }

      $document = $chunkBuilder;

      // Change the mime type to display/download the requested file
      if($config_displayInBrowser == false) {
        switch ($documentExtension) {
          case 'png':
            header('Content-type: image/png');
            break;
          case 'jpg':
          case 'jpeg':
            header('Content-type: image/jpg');
            break;
          case 'gif':
            header('Content-type: image/gif');
            break;
          default:
            // Let the browser try and work it out for itself
            header('Content-type: ');
            break;
        }
      } else {
       header('Content-type: ');
      }

     // Fetch original file checksum
     $query = $goSql->runQuery("select", "SELECT document_checksum FROM docs WHERE document_name = '$fileName' LIMIT 1");
     $row = mysqli_fetch_assoc($query);
     $originalChecksum = $row['document_checksum'];
     // Create new file checksum
     $newChecksum = md5($document);

     if($originalChecksum == $newChecksum) {
       // Get original file name for downloading
       $query = $goSql->runQuery("select", "SELECT document_real_name FROM docs WHERE document_name = '$fileName' LIMIT 1");
       $row = mysqli_fetch_array($query);
       $documentRealName = $row['document_real_name'];

       header('Content-Disposition: attachment; filename="' . $documentRealName . '"');
       echo $document;
     } else {
       // Issue with the checksum, direct to an error page
       header("Location: ../error/?error=checksum");
     }
    } else {
      // No File is available, send to error page
      header("Location: ../error/?error=unavailable");
    }
  }

?>
