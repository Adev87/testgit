$servername = "localhost";
      $username = "root";
      $password = "W0rld3xp0@dm1n";
      //$password = "";
      
     $mysqli = new mysqli($servername,$username,$password);
     // get_list_devices($mysqli);
     $sql = "DROP DATABASE `Water_IoT`; ";
      if ($mysqli->query($sql) === TRUE) {
        echo "Creating DB";
      
      $sql = "USE Water_IoT;
      CREATE TABLE `Water_IoT`.`Device` ( `id` INT NOT NULL AUTO_INCREMENT,
      `mac` VARCHAR(20),
       `devicename` VARCHAR(50), 
      `devicenickname` VARCHAR(50),
       PRIMARY KEY (`id`) 
        ) CHARSET=utf8 COLLATE=utf8_bin; 
      CREATE TABLE `Water_IoT`.`Device_Data` ( `id` INT NOT NULL AUTO_INCREMENT, 
      `device_id` INT, `LocX` DOUBLE, `LocY` DOUBLE, `LocZ` DOUBLE, `current` DOUBLE, `watts` DOUBLE, `temperature` DOUBLE, `time` VARCHAR(50), PRIMARY KEY (`id`) , 
      KEY `device_id` (`device_id`) , FULLTEXT INDEX `time` (`time`) ); 
      ";
      run_query($mysqli,$sql);
      $sql = "INSERT INTO `Water_IoT`.`Device` (`mac`,`devicename`,`devicenickname`) 
      VALUES  ('1:1:1:1','WG-001','Water Feature');
      INSERT INTO `Water_IoT`.`Device` (`devicename`,`devicenickname`) 
      VALUES  ('2:2:2:2','WG-002','F&B Area');
      INSERT INTO `Water_IoT`.`Device` (`devicename`,`devicenickname`) 
      VALUES  ('3:3:3:3','WG-003','Irrigations');
      INSERT INTO `Water_IoT`.`Device` (`devicename`,`devicenickname`) 
      VALUES  ('4:4:4:4','WG-004','Toilets');
      ";
      run_query($mysqli,$sql);
      $sql = "INSERT INTO `Water_IoT`.`Device_Data` (`device_id`,`LocX`,`LocY`,`LocZ`,`current`,`watts`,`temperature`,`time`) 
      VALUES  ('1','1','1','1','21','36','22','2021-06-15 11:06:12');
      INSERT INTO `Water_IoT`.`Device_Data` (`device_id`,`LocX`,`LocY`,`LocZ`,`current`,`watts`,`temperature`,`time`) 
      VALUES  ('2','2','2','2','55','66','77','2021-06-11 12:11:11');
      INSERT INTO `Water_IoT`.`Device_Data` (`device_id`,`LocX`,`LocY`,`LocZ`,`current`,`watts`,`temperature`,`time`) 
      VALUES  ('3','3','3','3','77','77','77','2021-06-16 01:26:15');
      INSERT INTO `Water_IoT`.`Device_Data` (`device_id`,`LocX`,`LocY`,`LocZ`,`current`,`watts`,`temperature`,`time`) 
      VALUES  ('4','4','4','4','88','88','88','2021-06-17 15:18:19');
      ";
      run_query($mysqli,$sql);
     
      
      } else {
   
        echo "Error: " . $sql . "<br>" . $mysqli->error;
      }
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
    
