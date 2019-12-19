<?php
  class SQL {

    // SQL Connection details - in reality this would all be private through an API connection
    // but it's just for the labs so *shrug*
    private $host = "brandonflude.com";
    private $uname = "brandon";
    private $passwd = "dis!sys";
    private $database = "file_system";

    // Connect to the SQL Server and return our connection
    function connect() {
      $connection = mysqli_connect($this->host, $this->uname, $this->passwd, $this->database);
      if(!mysqli_connect_errno()) {
        return $connection;
      } else {
        return false;
      }
    }

    // Nice and tidy reusable MySQL query function for easy calling in the future
    public function runQuery($type, $query) {
      $connection = $this->connect();
      switch($type) {
        // These cases will return a true or false, whether it happened or not
        case 'insert':
        case 'update':
        case 'delete':
           mysqli_query($connection, $query);
           if (mysqli_affected_rows($connection) > 0) {
             $this->disconnect($connection);
             return true;
           } else {
             $this->disconnect($connection);
             return false;
           }
           break;
        // This case will return us data!
        case 'select':
           $query = mysqli_query($connection, $query);
           $rows = mysqli_num_rows($query);
           if($rows == 0) {
             $this->disconnect($connection);
             return false;
           } else {
             return $query;
           }
           break;
      }
    }

    // Simple closing of SQL Connection - always call this.
    function disconnect($connection) {
      mysqli_close($connection);
    }
  }
?>
