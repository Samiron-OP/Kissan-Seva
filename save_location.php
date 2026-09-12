<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['lat']) &&
    isset($_POST['lng'])) {

    $lat = filter_var($_POST['lat'], FILTER_VALIDATE_FLOAT);
    $lng = filter_var($_POST['lng'], FILTER_VALIDATE_FLOAT);

    if ($lat === false || $lng === false ||
        $lat < -90 || $lat > 90 ||
        $lng < -180 || $lng > 180) {

        http_response_code(400);
        echo "Invalid location";
        exit;
    }

    $_SESSION['user_lat'] = $lat;
    $_SESSION['user_lng'] = $lng;

    echo "Location saved";
    exit;
}

http_response_code(400);
echo "Location not received";
?>