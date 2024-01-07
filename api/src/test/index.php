<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user']) && isset($_POST['password'])) {

        $data = ['first' => "Hello",
                'second' => "World"];

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