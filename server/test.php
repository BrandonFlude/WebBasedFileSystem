<html>
<head>
  <?php require("assets/build/Head.php"); ?>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"><?php echo $_SERVER['HTTP_HOST']; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/files">File List</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="/test.php">Run Test</a>
        </li>
      </ul>
      <form class="form-inline my-2 my-lg-0" action="/search">
        <input class="form-control mr-sm-2" type="search" name="criteria" placeholder="Search" aria-label="Search">
        <input class="btn btn-outline-success my-2 my-sm-0" type="submit" value="search" name="search">
      </form>
    </div>
  </nav>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <center><h1>OodleLoad - Test Platform</h1></center>

          <form enctype="multipart/form-data" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <input name="uploaded" id="uploaded" type="file" class="form-control-file" />
              </div>
              <div class="form-group col-md-6">
                <label for="chunkSize">Chunk Size (bytes)</label>
                <input type="text" class="form-control" name="chunkSize" placeholder="1024" value="1024" />
              </div>
            </div>
            <input type="submit" name="runTest" class="btn btn-primary btn-block" value="Run Test" />
          </form>

        <?php
        // Include our configuration
        require("config/config.php");

        // Include our classes
        require("classes/Upload.php");
        require("classes/Core.php");
        require("classes/Split.php");

        $goCore = new Core;
        $clientIP = $goCore->getClientIP();

        if($_POST['runTest'])
        {
          $fileDescription = "A test upload file";
          $customSizeOfChunks = $_POST['chunkSize'];
          $fileOwner = $clientIP; // This could be a username, but IP for demo purposes.

          echo "<p>Filled in Form with data</p>";

          // Start new instances of our file classes
          $goUpload = new Upload;
          $goSplit = new Split;

          echo "<p>Started Upload and Split Classes</p>";

          // Find the extension for this file
          $extension = $goUpload->findExtension($_FILES['uploaded']['name']);

          // Generate a random name for this file to store
          $fileName = $goUpload->generateString(30).".";

          echo "<p>Generated new File Name</p>";

          // Create a backup of the file name, we'll use this for searching and listings
          $realFileName = $_FILES['uploaded']['name'];

          // Check the extension
          if($goUpload->checkExtension($extension) == true) {
            $saveLocation = $_SERVER['DOCUMENT_ROOT']."/uploads/";
            $saveLocation .= $fileName.$extension;

            move_uploaded_file($_FILES['uploaded']['tmp_name'], $saveLocation);
//            file_put_contents($saveLocation, file_get_contents($testImageURL));
            echo "<p>Upload file to upload directory</p>";
          }

          // Generate a checksum for the file
          $fileChecksum = md5_file($saveLocation);
          echo "<p>Checksum created: $fileChecksum</p>";

          // Upload file to database
          $goUpload->insertToDatabase($fileName.$extension, $realFileName, $extension, $server, $fileDescription, $fileOwner, $fileChecksum);
          echo "<p>Created entry in Database</p>";

          // Chunk up file
          $goSplit->splitFile($saveLocation, $customSizeOfChunks, $extension, $server);
          echo "<p>File Chunked and Uploaded</p>";

          echo "<p>Done!</p>";
        }
        ?>
      </div>
    </div>
  </div>
</body>
  <?php require("assets/build/Foot.php"); ?>
</html>
