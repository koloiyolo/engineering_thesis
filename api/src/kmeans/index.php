<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "bp1";
    if (isset($_POST['user']) && isset($_POST['password'])) {
        echo "bp2";

        include("../data_manipulation/connections.php");
        include("../data_manipulation/decoder.php");

        // set credentials
        $user = $_POST['user'];
        $password = $_POST['password'];

        // set centroids
        $centroids = 3;
        if (isset($_POST['centroids'])) {
            $centroids = $_POST['centroids'];
        }

        // set iterations
        $iterations = 100;
        if (isset($_POST['iterations'])) {
            $centroids = $_POST['iterations'];
        }


        // get data from db
        $data = json_decode(get_data($user, $password));

        // init decoder
        $decoder = new Decoder($data);
        $data = $decoder->encode_data();

        // execute query on kmeans server
        $kmeans_args = [
            'centroids' => $centroids,
            'iterations' => $iterations,
            'data' => json_encode($data)
        ];
        $data = call_api("http://algorithms:5000/kmeans", $kmeams_args);
        $result = $decoder->decode_data($data);

        // execute kmeans algorithm on "algorithms" server and retrive result
        header('Content-Type: application/json');
        echo "bp3";
        echo json_encode($result);
    }
} else {
    echo "Wrong Request Method";
}
?>