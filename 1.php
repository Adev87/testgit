<?php
$ch = curl_init();
$headers = [
'User-Agent: PostmanRuntime/7.28.4',
'Accept: */*',
'Accept-Encoding: gzip, deflate, br',
'Connection: keep-alive',
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL,"http://3egreenserverapi.ddns.net:9001/api/data/clamp/device/EXPO2021SG/list");
//    curl_setopt($ch, CURLOPT_URL,"http://f9e1-188-43-136-32.ngrok.io/1.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);
curl_close ($ch);
echo $server_output;
?>