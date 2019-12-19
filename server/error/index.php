<html>
<head>
  <?php require($_SERVER['DOCUMENT_ROOT']."/assets/build/Head.php");
    $error = $_GET['error'];
  ?>
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
        <br>
        <center><h1>
        <?php
        switch ($error) {
          case 'unavailable':
            echo "This File doesn't exist. Please try again";
            break;
          case 'nochunk':
            echo "A chunk couldn't be fulfilled. Please try again";
            break;
          case 'checksum':
            echo "Checksum mistmatch. This file has been deemed unsafe for download. Please try again";
            break;
          default:
            // Let the browser try and work it out for itself
            echo "An Error Occured";
            break;
        }
        ?>
        </h1></center>
      </div>
    </div>
  </div>
</body>
  <?php require($_SERVER['DOCUMENT_ROOT']."/assets/build/Foot.php"); ?>
</html>
