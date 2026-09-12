<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
$mobileNo = $_POST['mobile'];
// 1. Setup API Credentials
$apiKey   = 'txb_nGyx7qfMEEMkoosScaGRR0ph725lTbPu'; // Replace with your TextBee API Key
$deviceId = '6aa42d91d14eb6cbaaecccbf';       // Optional: Device ID if you have multiple devices

// 2. Prepare SMS Data
$recipientNumber = '+91' . $mobileNo; // Must include '+' and Country Code (E.164 format)
$messageText      = $_POST['message'];

// 3. Prepare Payload
$payload = [
    'recipients' => [$recipientNumber],
    'message'    => $messageText
];

// If using a specific device ID, attach it to the payload
if (!empty($deviceId)) {
    $payload['deviceId'] = $deviceId;
}

// 4. Send HTTP POST Request via cURL
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://api.textbee.dev/api/v1/gateway/send-sms',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'x-api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ],
    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'cURL Network Error: ' . curl_error($ch);
} else {
    curl_close($ch);
    $data = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        echo "SMS Queued Successfully!\n";
        echo "Response: " . json_encode($data, JSON_PRETTY_PRINT);
    } else {
        echo "Failed to send SMS. HTTP Code: {$httpCode}\n";
        echo "Error Details: " . $response;
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SmS Sender</title>
</head>

<style>
  
</style>
<body>
    <form action="send_sms.php" method="post" >
        <label for="mobileNo">Enter Mobile No</label>
        <input type="tel" name="mobile" id="mobile">
        <br>
        <hr>
        <i><label for="message">Enter message</label></i>
        <input type="text" name="message" id="message">
        <br>
        <button type = "submit">Send</button>
    </form>
</body>
</html>
