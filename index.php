
      $servername = "localhost";
      $username = "root";
      $password = "W0rld3xp0@dm1n";
      
      
     $mysqli = new mysqli($servername,$username,$password,"Electronics");
    
 
      dropandcreatetable($mysqli);


      
    $mysqli->close();
    function dropandcreatetable($conn)
    {
      $sql = "DROP TABLE electronics";
      $conn->query($sql);
      $sql = "CREATE TABLE 
        `devices` 
        ( `id` INT NOT NULL AUTO_INCREMENT, 
        `mac` VARCHAR(30), `devicename` VARCHAR(50), `devicenickname` VARCHAR(50), PRIMARY KEY (`id`) );    ";
      $conn->query($sql);
      $sql = "CREATE TABLE `device_history` 
      ( `id` INT NOT NULL AUTO_INCREMENT, 
      `device_id` INT, `current` DOUBLE, `watts` DOUBLE, `temperature` DOUBLE, `time` VARCHAR(50), 
      PRIMARY KEY (`id`) , KEY `device_id` (`device_id`) , FULLTEXT INDEX `time` (`time`) ); 
      ";
      $conn->query($sql);
    }