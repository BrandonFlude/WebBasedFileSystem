<?php
  require("classes/Encryption.php");

  class Split {
    // Filename and Buffer in Bytes (1,048,576 == 1mb)
    function splitFile($file, $buffer, $extension, $server) {
        // Open the main file to read
        $fileHandle = fopen($file,'r');

        // Get the file size
        $fileSize = filesize($file);

        // Calculate the number of parts we need
        $parts = $fileSize / $buffer;

        // Path to store the chunks
        $storePath = $_SERVER['DOCUMENT_ROOT']."/chunks/";

        // Name of the actual file, we use this to add the .part.x on
        $fileName = basename($file);

        // Start SQL Connections
        $goUpload = new Upload;
        $goCore = new Core;
        $goEncrypt = new Encryption;

        // Loop through the file and split into into parts
        for($i=0; $i<$parts; $i++){

            // Read the $buffer chunk, starting at the last pointer
            $filePart = fread($fileHandle, $buffer);

            // The new filename with .part.x
            $filePartPath = $storePath.$fileName.".part.$i";

            // Create the file and upload it to /chunks/
            // This should be done on multiple servers so we
            // can recover files if a node goes down
            $fileNew = fopen($filePartPath, "w+");

            // Encrpt this chunk...
            $filePart = $goEncrypt->crypt($filePart, "e");

            // Write the chunk to this file on source server
            fwrite($fileNew, $filePart);

            // Close the file handler
            fclose($fileNew);

            // Move this chunk to two other servers to enable rebuilds
            //$moved = $goFTP->transferFile($fileNew, $this->randomServer());
            //$moved = $goFTP->transferFile($fileNew, $this->randomServer());

            // Insert this to the database
            $ipAddress = $goCore->getClientIP();

            $goUpload->insertToDatabase("$fileName.part.$i", "null", $extension, $server, "$fileName chunk", "$ipAddress", 'chunk, no checksum');
        }

        // Close the main file and return
        fclose($fileHandle);
        return true;
    }

    // This function is incredibly long winded and yes there might be a better way
    // However, it works, and this is the best way I can be prepared for any
    // number of systems running this without a database for Chris to maintain
    // It could all be done in one very large PHP function call, but I'll split it up
    // for readability
    /*
    function randomServer() {
      // This takes /var/www/html/server1.brandonflude.com down to server1.brandonflude.com
      $thisServer = substr(strrchr($_SERVER['DOCUMENT_ROOT'], "/"), 1);

      // Now split on the '.' to get server1, brandonflude, com
      $domainSplit = explode("/", $thisServer, 2);
      $subServer = $domainSplit[0];

      // Now we're left with server1, split this to remove the number with regular expressions
      $splitSubServer = preg_split('#(?<=\d)(?=[a-z])#i', $subServer);

      // In most cases (at least for testing) $splitSubServer[0] will equal our number.
      $currentServer = $splitSubServer[0];

      // Now we can get a random number from our 6 servers we're demoing with (1->6)
      // Do while loop allows us to exclude more servers in the future
      // Such as poor seeders

      do {
          $n = rand(1,6);
      } while($n = $currentServer);

      // Now reconstruct the server path with our new server number
      $newServer = $_SERVER['DOCUMENT_ROOT'];
      $newServer = explode('/', $newServer);
      unset($newServer[count($newServer) - 1]);
      $newServer = implode('/', $newServer);
      $newServer .= "/server$n";
      $newServer .= "/chunks/";

      return $newServer;
    }
    */
  }
?>
