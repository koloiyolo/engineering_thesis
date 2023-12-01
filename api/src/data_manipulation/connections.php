<?php

// fetch data for algorithms from db

function get_data($user, $password)
{
    $mysqli = new mysqli("db", $user, $password, "logs");

    // catches errors from db connections
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $statement = $mysqli->prepare("SELECT datetime, tags, message FROM logs");
    $statement->execute();
    $result = $statement->get_result();
    $data = [];

    foreach ($result as $row) {
        $data[] = $row;
    }
    $statement->close();

    return json_encode($data);
}


function call_api($url, $post_data)
{

    // Encode the JSON data
    $json_content = http_build_query($post_data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_content);

    $result = curl_exec($curl);
    if ($result === false) {
        echo "Error processing request";
        return false;
    }
    curl_close($curl);
    return json_decode($result);
}
?>