<?php

// fetch data from db

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
?>