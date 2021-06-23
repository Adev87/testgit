
      $servername = "localhost";
      $username = "root";
      $password = "W0rld3xp0@dm1n";
      //$password = "";
      
     $mysqli = new mysqli($servername,$username,$password,"Electronics");
    get_list_devices($mysqli);
 
      


      
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
         echo var_dump($dv);
         insert_after_check_mac_last_update($conn,$dv);
     }
     curl_close ($ch);
  }
  function get_historical_data($conn,$mac,$date)
  {
      
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"http://3egreenserverapi.ddns.net:9001/api/data/clamp/device/historical");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
                "macaddress=$mac&date=$date");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
     $devices = json_decode($server_output);
     foreach ($devices["data"] as $dv)
     {
       insert_after_check_mac_last_update($conn,$dv);
      
     }
    curl_close ($ch);
  }
  function insert_after_check_mac_last_update($conn,$dv)
  {
    $sql = "selct * from electronics where address='".$dv->address."' and lastUpdated='.".$dv->lastUpdated.".'";
    $sql = "INSERT INTO electronics (address, current, watts,temperature,devicename,devicenickname,lastUpdated)
    VALUES ('".$dv->address."', '"
    .$dv->current."', '"
    .$dv->watts."', '"
    .$dv->temperature."', '"
    .$dv->devicename."', '"
    .$dv->devicenickname."', '"
    .$dv->lastUpdated."')";
    
    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
  
