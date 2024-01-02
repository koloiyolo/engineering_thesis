<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user']) && isset($_POST['password']) && isset($_POST['host'])) {

        $mysqli = new mysqli("db", $_POST['user'], $_POST['password'], "logs");

        $host = $_POST['host'];

        // catches errors from db connections
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $mysqli->connect_error;
            exit();
        }

        $statement;
        if (isset($_POST['by'])) {
            $by = $_POST['by'];

            if (isset($_POST['order'])) {

                // order by with param

                $order = $_POST['order'];
                $statement = $mysqli->prepare("SELECT * FROM logs ORDER BY (?) (?) WHERE host = (?)");
                $statement->bind_param("is", $by, $order, $host);

            } else {

                // default order by

                $statement = $mysqli->prepare("SELECT * FROM logs ORDER BY (?) WHERE host = (?)");
                $statement->bind_param("is", $by, $host);

            }
        } else {

            // default select

            $statement = $mysqli->prepare("SELECT * FROM logs WHERE host = (?)");
            $statement->bind_param("is", $host);
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