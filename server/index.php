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
        <li class="nav-item active">
          <a class="nav-link" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/files">File List</a>
        </li>
        <li class="nav-item">
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
        <center><h1>OodleLoad!</h1></center>
        <?php
        // Include our configuration
        require("config/config.php");

        // Include our classes
        require("classes/Upload.php");
        require("classes/Core.php");
        require("classes/Split.php");

        $goCore = new Core;
        $clientIP = $goCore->getClientIP();

        if(isset($_POST['upload'])) {
          // First check if IP address and File Description fields have been populated
          $fileDescription = $_POST['fileDescription'];
          $fileOwner = $_POST['fileOwner']; // This could be a username, but IP for demo purposes.
          if($fileDescription == "") {
            // Field left empty, show an error
            $type = "warning";
            $message = "All fields must be populated in order to upload a file. Please try again.";
          } else {
            // Start new instances of our file classes
            $goUpload = new Upload;
            $goSplit = new Split;

            // Find the extension for this file
            $extension = $goUpload->findExtension($_FILES['uploaded']['name']);

            // Generate a random name for this file to store
            $fileName = $goUpload->generateString(30).".";

            // Create a backup of the file name, we'll use this for searching and listings
            $realFileName = $_FILES['uploaded']['name'];

            // Set the upload Directory
            $saveLocation = $_SERVER['DOCUMENT_ROOT']."/uploads/";
            $saveLocation .= $fileName.$extension;

            if($goUpload->checkExtension($extension) == true) {
              // Upload a raw copy of this file to our /uploads/ directory
              if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $saveLocation)) {
                // Upload a listing of the whole file (for same server downloads)
                $fileChecksum = md5_file($saveLocation);
                $goUpload->insertToDatabase($fileName.$extension, $realFileName, $extension, $server, $fileDescription, $fileOwner, $fileChecksum);

                // Buffer is configured in /config/config.php
                $goSplit->splitFile($saveLocation, $sizeOfChunk, $extension, $server);

                $type = "success";
                $message = "Your file has been uploaded successfully";

              } else {
                $type = "danger";
                $message = "Could not upload the file. Check the permission levels of the /uploads/ and /chunks/ directories.";
              }
            } else {
              $type = "danger";
              $message = ".$extension is not an allowed file type. Please try again.";
            }
          }
          // Display message
          echo $goCore->showAlert($type, $message);
        }
        ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <form enctype="multipart/form-data" method="post">
          <div class="form-group">
            <input name="uploaded" id="uploaded" type="file" class="form-control-file" />
          </div>
          <div class="uploadSettings" id="uploadSettings">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="fileDescription">File Desc.</label>
                <input type="text" class="form-control" name="fileDescription" placeholder="File Description">
              </div>
              <div class="form-group col-md-6">
                <label for="fileOwner">File Owner (IP)</label>
                <input type="text" class="form-control" name="fileOwner" placeholder="Your IP" value="<?php echo $clientIP; ?>" readonly>
              </div>
            </div>
          </div>
          <div class="form-group">
            <input type="submit" name="upload" value="Upload" class="btn btn-block btn-primary" />
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
  <?php require("assets/build/Foot.php"); ?>
</html>
