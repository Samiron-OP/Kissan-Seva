<?php

session_start();
include "connect.php";

/*
|--------------------------------------------------------------------------
| RAZORPAY TEST MODE KEYS
|--------------------------------------------------------------------------
|
| Get these from:
| Razorpay Dashboard → Test Mode → API Keys
|
*/

$keyId = "rzp_test_TbPci3j2dxTsAN";
$keySecret = "TjgIStTrVfE5H4NDiA6yObrT";


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("Location: login.php");
    exit;
}

$farmerId = $_SESSION['FarmerId'];


/*
|--------------------------------------------------------------------------
| PAYMENT SUCCESS CALLBACK
|--------------------------------------------------------------------------
|
| Razorpay sends these values after successful Checkout:
|
| razorpay_payment_id
| razorpay_order_id
| razorpay_signature
|
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $razorpayPaymentId = $_POST['razorpay_payment_id'] ?? '';
    $razorpayOrderId   = $_POST['razorpay_order_id'] ?? '';
    $razorpaySignature = $_POST['razorpay_signature'] ?? '';

    if (
        empty($razorpayPaymentId) ||
        empty($razorpayOrderId) ||
        empty($razorpaySignature)
    ) {
        die("Invalid payment response.");
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY RAZORPAY SIGNATURE
    |--------------------------------------------------------------------------
    |
    | Razorpay requires server-side signature verification.
    |
    */

    $generatedSignature = hash_hmac(
        'sha256',
        $razorpayOrderId . '|' . $razorpayPaymentId,
        $keySecret
    );


    if (!hash_equals($generatedSignature, $razorpaySignature)) {

        die("
            <h2>Payment Verification Failed</h2>
            <p>The payment signature could not be verified.</p>
        ");
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PAYMENT RECORD
    |--------------------------------------------------------------------------
    */

    $sql = "UPDATE payments
            SET
                razorpay_payment_id = ?,
                razorpay_signature = ?,
                payment_status = 'Paid',
                paid_at = NOW()
            WHERE razorpay_order_id = ?
            AND FarmerId = ?";

    $stmt = mysqli_prepare($con, $sql);

    if (!$stmt) {
        die("Database error.");
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $razorpayPaymentId,
        $razorpaySignature,
        $razorpayOrderId,
        $farmerId
    );

    mysqli_stmt_execute($stmt);

    /*
    |--------------------------------------------------------------------------
    | SUCCESS PAGE
    |--------------------------------------------------------------------------
    */

    echo '
    <!DOCTYPE html>
    <html>
    <head>

        <title>Payment Successful</title>

        <meta name="viewport"
              content="width=device-width, initial-scale=1">

        <style>

            body {
                margin: 0;
                padding: 40px 20px;
                font-family: Arial, sans-serif;
                background: #f4f8f1;
            }

            .card {
                max-width: 550px;
                margin: 60px auto;
                background: white;
                padding: 40px;
                border-radius: 20px;
                text-align: center;
                box-shadow: 0 10px 35px rgba(0,0,0,0.10);
            }

            .success {
                font-size: 70px;
                margin-bottom: 15px;
            }

            h1 {
                color: #2e7d32;
                margin-bottom: 10px;
            }

            .payment-id {
                background: #f5f5f5;
                padding: 15px;
                border-radius: 10px;
                margin: 25px 0;
                word-break: break-all;
            }

            .btn {
                display: inline-block;
                padding: 13px 25px;
                background: #2e7d32;
                color: white;
                text-decoration: none;
                border-radius: 8px;
            }

        </style>

    </head>

    <body>

        <div class="card">

            <div class="success">
                ✓
            </div>

            <h1>
                Payment Successful
            </h1>

            <p>
                Your estimated payout has been successfully processed.
            </p>

            <div class="payment-id">

                <strong>
                    Payment ID
                </strong>

                <br><br>

                ' . htmlspecialchars($razorpayPaymentId) . '

            </div>

            <a href="profile.php" class="btn">
                Back to Profile
            </a>

        </div>

    </body>
    </html>
    ';

    exit;
}


/*
|--------------------------------------------------------------------------
| GET BOOKING ID
|--------------------------------------------------------------------------
*/

$bookingId = isset($_GET['booking_id'])
    ? (int) $_GET['booking_id']
    : 0;


if ($bookingId <= 0) {
    die("Invalid booking ID.");
}


/*
|--------------------------------------------------------------------------
| FETCH BOOKING
|--------------------------------------------------------------------------
|
| IMPORTANT:
| We do NOT trust an amount sent from the browser.
|
| We fetch the farmer's actual booking from the database.
|
*/

$sql = "SELECT
            sb.BookingId,
            sb.FarmerId,
            sb.Quantity,
            sb.Crop,
            sb.TokenNo,
            u.Name,
            u.MobileNo,
            s.Centre,
            s.Date,
            s.Location

        FROM slot_bookings sb

        JOIN users u
            ON sb.FarmerId = u.FarmerId

        JOIN slots s
            ON sb.SNo = s.SNo

        WHERE sb.BookingId = ?
        AND sb.FarmerId = ?";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die("Database error.");
}

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $bookingId,
    $farmerId
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$booking = mysqli_fetch_assoc($result);


if (!$booking) {
    die("Booking not found.");
}


/*
|--------------------------------------------------------------------------
| FETCH CROP RATE
|--------------------------------------------------------------------------
*/

$crop = $booking['Crop'];

$sql = "SELECT
            crop_name,
            rate_per_quintal

        FROM crop_rates

        WHERE crop_name = ?

        LIMIT 1";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die("Database error.");
}

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $crop
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$rate = mysqli_fetch_assoc($result);


if (!$rate) {
    die("Rate unavailable for this crop.");
}


/*
|--------------------------------------------------------------------------
| CALCULATE ESTIMATED AMOUNT
|--------------------------------------------------------------------------
*/

$quantity = (float) $booking['Quantity'];

$ratePerQuintal = (float) $rate['rate_per_quintal'];


/*
| 1 quintal = 100 kg
*/

$estimatedAmount =
    ($quantity / 100) * $ratePerQuintal;


/*
|--------------------------------------------------------------------------
| VALIDATE AMOUNT
|--------------------------------------------------------------------------
*/

if ($estimatedAmount <= 0) {
    die("Invalid estimated amount.");
}


/*
|--------------------------------------------------------------------------
| CHECK IF ALREADY PAID
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            payment_status,
            razorpay_payment_id

        FROM payments

        WHERE BookingId = ?
        AND FarmerId = ?

        ORDER BY id DESC

        LIMIT 1";

$stmt = mysqli_prepare($con, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $bookingId,
    $farmerId
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$existingPayment = mysqli_fetch_assoc($result);


if (
    $existingPayment &&
    $existingPayment['payment_status'] === 'Paid'
) {

    die("
        <h2>Payment Already Completed</h2>
        <p>Payment ID: "
        . htmlspecialchars($existingPayment['razorpay_payment_id']) .
        "</p>
    ");
}


/*
|--------------------------------------------------------------------------
| RAZORPAY AMOUNT
|--------------------------------------------------------------------------
|
| Razorpay expects the smallest currency unit.
|
| ₹11,500
|
| becomes
|
| 1150000 paise
|
*/

$amountInPaise = (int) round($estimatedAmount * 100);


/*
|--------------------------------------------------------------------------
| CREATE RAZORPAY ORDER
|--------------------------------------------------------------------------
*/

$receipt = "KSN_" . $bookingId . "_" . time();

$orderData = [
    "amount" => $amountInPaise,
    "currency" => "INR",
    "receipt" => $receipt,
    "notes" => [
        "booking_id" => (string) $bookingId,
        "farmer_id" => (string) $farmerId,
        "crop" => (string) $crop
    ]
];


$ch = curl_init(
    "https://api.razorpay.com/v1/orders"
);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($orderData)
);

curl_setopt(
    $ch,
    CURLOPT_USERPWD,
    $keyId . ":" . $keySecret
);

curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "Content-Type: application/json"
    ]
);

curl_setopt($ch, CURLOPT_TIMEOUT, 30);


$response = curl_exec($ch);

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);


if (curl_errno($ch)) {

    $error = curl_error($ch);

    curl_close($ch);

    die("Razorpay connection error: " . $error);
}

curl_close($ch);


$order = json_decode($response, true);


if (
    $httpCode < 200 ||
    $httpCode >= 300 ||
    !isset($order['id'])
) {

    echo "<h2>Razorpay Order Creation Failed</h2>";

    echo "<pre>";
    print_r($order);
    echo "</pre>";

    exit;
}


$razorpayOrderId = $order['id'];


/*
|--------------------------------------------------------------------------
| SAVE PAYMENT RECORD
|--------------------------------------------------------------------------
*/

$sql = "INSERT INTO payments
        (
            BookingId,
            FarmerId,
            amount,
            razorpay_order_id,
            payment_status
        )

        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            'Created'
        )";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die("Unable to create payment record: " . mysqli_error($con));
}

mysqli_stmt_bind_param(
    $stmt,
    "isds",
    $bookingId,
    $farmerId,
    $estimatedAmount,
    $razorpayOrderId
);

if (!mysqli_stmt_execute($stmt)) {
    die("Unable to save payment record: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>

<html>

<head>

    <title>Kissan Seva - Payment</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 20px;
            font-family: Arial, sans-serif;
            background: #f4f8f1;
        }

        .card {
            max-width: 550px;
            margin: 50px auto;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.10);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .icon {
            font-size: 55px;
        }

        h1 {
            margin: 10px 0;
            color: #285b2a;
        }

        .subtitle {
            color: #777;
        }

        .row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .label {
            color: #777;
        }

        .value {
            font-weight: bold;
        }

        .amount-box {
            margin-top: 25px;
            padding: 25px;
            background: #edf7e9;
            border-radius: 15px;
            text-align: center;
        }

        .amount-label {
            color: #555;
            margin-bottom: 8px;
        }

        .amount {
            font-size: 40px;
            font-weight: bold;
            color: #285b2a;
        }

        #pay-button {
            width: 100%;
            margin-top: 25px;
            padding: 16px;
            border: none;
            border-radius: 10px;
            background: #285b2a;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        #pay-button:hover {
            background: #1f4821;
        }

        .test-mode {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }

    </style>

</head>


<body>


<div class="card">


    <div class="header">

        <div class="icon">
            🌾
        </div>

        <h1>
            Kissan Seva
        </h1>

        <div class="subtitle">
            Estimated Procurement Payout
        </div>

    </div>


    <div class="row">

        <span class="label">
            Farmer
        </span>

        <span class="value">
            <?php
            echo htmlspecialchars(
                $booking['Name']
            );
            ?>
        </span>

    </div>


    <div class="row">

        <span class="label">
            Crop
        </span>

        <span class="value">
            <?php
            echo htmlspecialchars(
                $crop
            );
            ?>
        </span>

    </div>


    <div class="row">

        <span class="label">
            Quantity
        </span>

        <span class="value">
            <?php
            echo number_format(
                $quantity,
                2
            );
            ?>
            KG
        </span>

    </div>


    <div class="row">

        <span class="label">
            Rate
        </span>

        <span class="value">

            ₹<?php

            echo number_format(
                $ratePerQuintal,
                2
            );

            ?>

            / Quintal

        </span>

    </div>


    <div class="amount-box">

        <div class="amount-label">
            Estimated Payout
        </div>

        <div class="amount">

            ₹<?php

            echo number_format(
                $estimatedAmount,
                2
            );

            ?>

        </div>

    </div>


    <button
        id="pay-button"
        type="button">

        Pay Estimated Amount

    </button>


    <div class="test-mode">

        Razorpay Test Mode · No real money will be deducted

    </div>


</div>


<script>

    const options = {

        key: "<?php echo htmlspecialchars($keyId); ?>",

        amount: "<?php echo $amountInPaise; ?>",

        currency: "INR",

        name: "Kissan Seva",

        description: "Estimated Procurement Payout",

        order_id: "<?php echo htmlspecialchars($razorpayOrderId); ?>",

        prefill: {

            name: "<?php
                echo htmlspecialchars(
                    $booking['Name']
                );
            ?>",

            contact: "<?php
                echo htmlspecialchars(
                    $booking['MobileNo']
                );
            ?>"

        },

        notes: {

            booking_id: "<?php
                echo htmlspecialchars(
                    $bookingId
                );
            ?>",

            farmer_id: "<?php
                echo htmlspecialchars(
                    $farmerId
                );
            ?>"

        },

        theme: {

            color: "#285b2a"

        },

        handler: function (response) {

            /*
             * Send Razorpay response to PHP
             * for server-side signature verification.
             */

            const form =
                document.createElement("form");

            form.method = "POST";

            form.action = "payment.php";


            const fields = {

                razorpay_payment_id:
                    response.razorpay_payment_id,

                razorpay_order_id:
                    response.razorpay_order_id,

                razorpay_signature:
                    response.razorpay_signature

            };


            for (
                const key in fields
            ) {

                const input =
                    document.createElement("input");

                input.type = "hidden";

                input.name = key;

                input.value = fields[key];

                form.appendChild(input);

            }


            document.body.appendChild(form);

            form.submit();

        },

        modal: {

            ondismiss: function () {

                console.log(
                    "Payment window closed"
                );

            }

        }

    };


    const razorpay =
        new Razorpay(options);


    document
        .getElementById("pay-button")
        .onclick = function (e) {

            razorpay.open();

            e.preventDefault();

        };

</script>


</body>

</html>