<?php

if ($_SERVER['REQUEST'] === 'POST') {
    if (isset($_POST['user']) && isset($_POST['password'])) {

        include("../data_manipulation/connections.php");
        include("../data_manipulation/decoder.php");

        // set credentials
        $user = $_POST['user'];
        $password = $_POST['password'];

        // set clusters
        $centroids = 5;
        if (isset($_POST['clusters'])) {
            $clusters = $_POST['clusters'];
        }


        // get data from db
        $data = json_decode(get_data($user, $password));

        // init decoder
        $decoder = new Decoder($data);
        $data = $decoder->encode_data();

        // execute query on ahc server
        $ahc_args = [
            'clusters' => $clusters,
            'data' => json_encode($data)
        ];
        
        $data = call_api("http://algorithms:5000/ahc", $ahc_args);
        $result = $decoder->decode_data($data);

        // execute ahc algorithm on "algorithms" server and retrive result
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
?>