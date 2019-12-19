<html>
<head>
  <?php require($_SERVER['DOCUMENT_ROOT']."/assets/build/Head.php"); ?>
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
        <li class="nav-item active">
          <a class="nav-link" href="/files">File List</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/test.php">Run Test</a>
        </li>
      </ul>
      <form class="form-inline my-2 my-lg-0" action="/search">
        <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <input class="btn btn-outline-success my-2 my-sm-0" type="submit" value="search" name="search">
      </form>
    </div>
  </nav>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">File Name</th>
                <th scope="col">File Description</th>
                <th scope="col">File Type</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              require($_SERVER['DOCUMENT_ROOT']."/classes/Sql.php");
              $goSql = new SQL;

              // Initiate $counter
              $counter = 0;

              // Select everything that meets our criteria and loop this through in a table
              $data = $goSql->runQuery("select", "SELECT document_name, document_real_name, document_ext, file_description FROM docs WHERE document_real_name != 'null'");
              if($data != false) {
                while($row = mysqli_fetch_array($data)) {
                  $counter++;
                  $fileName = $row['document_real_name'];
                  $fileDescription = $row['file_description'];
                  $docExtension = $row['document_ext'];
                  $downloadName = $row['document_name'];
                  echo "<tr>
                    <th scope='row'>$counter</th>
                    <td>$fileName</td>
                    <td>$fileDescription</td>
                    <td>$docExtension</td>
                    <td>
                      <a href='/uploads/$downloadName' class='btn btn-block btn-success' target='_blank'>Download</a>
                    </td>
                    <tr>";
                }
              } else {
                echo "<tr>
                  <th scope='row'>0</th>
                  <td>No Files Found</td>
                  <tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
  <?php require($_SERVER['DOCUMENT_ROOT']."/assets/build/Foot.php"); ?>
</html>
