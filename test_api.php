<?php
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://ali-express1.p.rapidapi.com/search?query=tshirt&page=1",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: ali-express1.p.rapidapi.com",
        "x-rapidapi-key: 127885cd19msh55c199eefe5c41cp12412djsn790ef4fad021"
    ],
]);

$response = curl_exec($curl);
curl_close($curl);

echo "<pre>";
print_r(json_decode($response, true));
