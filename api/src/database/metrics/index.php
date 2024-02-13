<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user']) && isset($_POST['password'])) {

        $mysqli = new mysqli("db", $_POST['user'], $_POST['password'], "logs");

        // catches errors from db connections
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $mysqli->connect_error;
            exit();
        }

        $statement;
        if (isset($_POST['host'])) {
            $host = $_POST['host'];

                // default order by

                $statement = $mysqli->prepare(
                    "SELECT datetime, COUNT(*) AS count
                    FROM logs
                    GROUP BY datetime
                    WHERE host = (?)");
                $statement->bind_param("s", $host);

        } else {

            // default select

            $statement = $mysqli->prepare(
                "SELECT datetime, COUNT(*) AS count
                FROM logs
                GROUP BY datetime;
            ");
        }
        $statement->execute();

        $result = $statement->get_result();
        $data = [];

        foreach ($result as $row) {
            $data[] = $row;
        }
        $statement->close();

        header('Content-Type: application/json');
        echo json_encode($data);

    } else {

        // triggersd if no credentials in request
        echo "No credentials, access denied";
        exit();
    }
} else {

    // triggers if wrong request
    echo "Error, wrong request";
    exit();
}

?>