<?php
$statusMessage = '';
$statusType    = '';
$isSent = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $mobileNo = trim($_POST['mobile']);
    $apiKey   = 'txb_nGyx7qfMEEMkoosScaGRR0ph725lTbPu'; 
    $deviceId = '6aa42d91d14eb6cbaaecccbf'; 

    $recipientNumber = '+91' . $mobileNo;
    $messageText      = trim($_POST['message']);

    $payload = [
        'recipients' => [$recipientNumber],
        'message'    => $messageText
    ];

    if (!empty($deviceId)) {
        $payload['deviceId'] = $deviceId;
    }

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
        $statusMessage = 'cURL Network Error: ' . curl_error($ch);
        $statusType    = 'error';
    } else {
        curl_close($ch);
        $data = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            $isSent = true;
            $statusMessage = "SMS Queued Successfully!\n" . json_encode($data, JSON_PRETTY_PRINT);
            $statusType    = 'success';
        } else {
            $statusMessage = "Failed to send SMS (HTTP {$httpCode}).\nDetails: " . $response;
            $statusType    = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Sender</title>
    <!-- VCR OSD Mono Font -->
    <link href="https://fonts.cdnfonts.com/css/vcr-osd-mono" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'VCR OSD Mono', monospace;
        }

        body {
            background-color: #0f0f13;
            color: #00ff66;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: #181824;
            border: 2px solid #00ff66;
            box-shadow: 0 0 15px rgba(0, 255, 102, 0.2);
            border-radius: 8px;
            padding: 30px;
            width: 100%;
            max-width: 420px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #00ff66;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 5px rgba(0, 255, 102, 0.5);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            color: #a0a0c0;
        }

        .input-group {
            display: flex;
            align-items: center;
        }

        .prefix {
            background: #252538;
            border: 1px solid #00ff66;
            border-right: none;
            color: #00ff66;
            padding: 12px;
            font-size: 14px;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        input[type="tel"],
        textarea {
            width: 100%;
            padding: 12px;
            background: #0f0f13;
            border: 1px solid #00ff66;
            color: #ffffff;
            font-size: 14px;
            border-radius: 4px;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-group input[type="tel"] {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        input[type="tel"]:focus,
        textarea:focus {
            border-color: #ffffff;
            box-shadow: 0 0 8px rgba(0, 255, 102, 0.4);
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #00ff66;
            border: none;
            color: #0f0f13;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s ease;
            letter-spacing: 1px;
        }

        button:hover {
            background: #ffffff;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
        }

        .status-box {
            margin-top: 20px;
            padding: 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .status-box.success {
            background: rgba(0, 255, 102, 0.1);
            border: 1px solid #00ff66;
            color: #00ff66;
        }

        .status-box.error {
            background: rgba(255, 50, 50, 0.1);
            border: 1px solid #ff3232;
            color: #ff3232;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>SMS Gateway</h2>
    
    <form action="send_sms.php" method="post">
        <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <div class="input-group">
                <span class="prefix">+91</span>
                <input type="tel" name="mobile" id="mobile" placeholder="9876543210" required pattern="[0-9]{10}">
            </div>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" id="message" placeholder="Type your text message here..." required></textarea>
        </div>

        <button type="submit">SEND SMS</button>
    </form>

    <?php if ($isSent): ?>
        <script>
            alert("SMS sent Successfully!!");
        </script>
    <?php endif; ?>
</div>

</body>
</html>