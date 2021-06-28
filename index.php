      $servername = "localhost";
      $username = "root";
      $password = "W0rld3xp0@dm1n";
      //$password = "";
      
     $mysqli = new mysqli($servername,$username,$password);
     // get_list_devices($mysqli);
     $mysqli = new mysqli($servername,$username,$password);
      //get_list_devices($mysqli);
      $sql = "
      CREATE DATABASE `Electrical_IoT`CHARACTER SET utf8 COLLATE utf8_bin; 
       ";
      run_query($mysqli,$sql);
      $sql = "
      CREATE DATABASE `Water_IoT`CHARACTER SET utf8 COLLATE utf8_bin; 
       ";
      run_query($mysqli,$sql);
      $sql=" CREATE DATABASE `WasteMgt`CHARACTER SET utf8 COLLATE utf8_bin; 
      ";
     
      run_query($mysqli,$sql);
      $sql = "USE Electrical_IoT;";
      run_query($mysqli,$sql);
      $sql = "USE Electrical_IoT;
      CREATE TABLE `electrical_iot`.`Device` ( `id` INT NOT NULL AUTO_INCREMENT, `mac` VARCHAR(20), `devicename` VARCHAR(50), `devicenickname` VARCHAR(50)
      , PRIMARY KEY (`id`)     
      )

      CHARSET=utf8 COLLATE=utf8_bin; 
      CREATE TABLE `electrical_iot`.`Device_Data` ( `id` INT NOT NULL AUTO_INCREMENT, `device_id` INT, `LocX` INT, `LocY` INT, `LocZ` INT, `current` DOUBLE, `watts` DOUBLE, `temperature` DOUBLE, `time` VARCHAR(50), PRIMARY KEY (`id`) , 
      KEY `device_id` (`device_id`) , FULLTEXT INDEX `time` (`time`) ); 
      ";
      run_query($mysqli,$sql);
    
      $sql = "USE Water_IoT;
      CREATE TABLE `Water_IoT`.`Device` ( `id` INT NOT NULL AUTO_INCREMENT, 
      `sensor_id` VARCHAR(20)
      , `devicename` VARCHAR(50), 
      `devicenickname` VARCHAR(50),
       PRIMARY KEY (`id`) 
        ) CHARSET=utf8 COLLATE=utf8_bin; 
      CREATE TABLE `Water_IoT`.`Device_Data` ( `id` INT NOT NULL AUTO_INCREMENT, 
      `device_id` INT, `LocX` INT, `LocY` INT, `LocZ` INT, `current` DOUBLE, `watts` DOUBLE, `temperature` DOUBLE, `time` VARCHAR(50), PRIMARY KEY (`id`) , 
      KEY `device_id` (`device_id`) , FULLTEXT INDEX `time` (`time`) ); 
      ";
      run_query($mysqli,$sql);
     
      $sql = "USE WasteMgt;
   
      CREATE TABLE `WasteMgt`.`Device_Data` ( `id` INT NOT NULL AUTO_INCREMENT, 
      `AgentID` VARCHAR(20),
      `LocX` INT, 
      `LocY` INT, `LocZ` INT, 
      `weight` DOUBLE, 
      `time` VARCHAR(50)
      , PRIMARY KEY (`id`) , 
      KEY `AgentID` (`AgentID`) , FULLTEXT INDEX `time` (`time`) ); 
      ";
      run_query($mysqli,$sql);
     $mysqli->close();
    
  function run_query($mysqli,$sql)
  {
    if ($mysqli->multi_query($sql) === TRUE) {
      echo "table dropped<br>";
      while ($mysqli->next_result());
    } else {
      echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
  }
     $mysqli->close();
    
    
  function get_list_devices($conn)
  {
    $ch = curl_init();

     curl_setopt($ch, CURLOPT_URL,"http://3egreenserverapi.ddns.net:9001/api/data/clamp/device/SG%20HDB-1/list");
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
     $server_output = curl_exec($ch);
     $devices = json_decode($server_output);
     foreach ($devices as $dv)
     {
         insert_device($conn,$dv);
     }
     curl_close ($ch);
  }
  
  function insert_device($conn,$dv)
  {
    $id = get_device_id_from_mac($conn,$dv->address);
    if ($id<=0)
    {
      $sql = "INSERT INTO devices (mac, devicename,devicenickname)
    VALUES ('".$dv->address."', '"
    .$dv->devicename."', '"
    .$dv->devicenickname."')";
    
    if ($conn->query($sql) === TRUE) {
      echo "New device created successfully<br>";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    }
    $id = get_device_id_from_mac($conn,$dv->address);
    $dv->device_id = $id;
    fill_device_data($conn,$dv);
  }
  function fill_device_data($conn,$dv)
  {
    insert_after_check_mac_last_update($conn,$dv);

    $mac = $dv->address;
    $date = "2021-06-01_2021-06-30";  
    $ch = curl_init();
    

    curl_setopt($ch, CURLOPT_URL,"http://3egreenserverapi.ddns.net:9001/api/data/clamp/device/historical");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
                "macaddress=$mac&date=$date");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    $devices = json_decode($server_output);
    foreach ($devices->data as $dt)
     {
      $dt->lastUpdated = $dt->time;
      $dt->device_id = $dv->device_id;
      insert_after_check_mac_last_update($conn,$dt);
      
     }
    curl_close ($ch);
    sleep(21);
  }
  function get_device_id_from_mac($conn,$address)
  {
    $sql = "select * from devices where mac='".$address."'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    
    return $row["id"];
    }
    
    }
    return 0;
  }
  function insert_after_check_mac_last_update($conn,$dv)
  {
    $dv->lastUpdated = str_replace(".000Z","",$dv->lastUpdated);
    $dv->lastUpdated = date("Y-m-d H:i:s", strtotime($dv->lastUpdated));
    
    $sql = "select * from device_history where device_id='".$dv->device_id."' and time='".$dv->lastUpdated."'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    echo "History data alredy exist id: " . $row["id"]. " <br>";
    return;
    }
    
    } else {
  
    }
    
    $sql = "INSERT INTO device_history (device_id, current, watts,temperature,time)
    VALUES ('".$dv->device_id."', '"
    .$dv->current."', '"
    .$dv->watts."', '"
    .$dv->temperature."', '"
    .$dv->lastUpdated."')";
    
    if ($conn->query($sql) === TRUE) {
      echo "New device history created successfully<br>";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

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
  
