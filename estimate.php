<?php

session_start();
include "connect.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("Location: login.php");
    exit;
}

$farmerId = $_SESSION['FarmerId'];

$bookingId = isset($_GET['booking_id'])
    ? (int)$_GET['booking_id']
    : 0;

$sql = "SELECT
            sb.BookingId,
            sb.FarmerId,
            sb.Quantity,
            sb.TokenNo,
            sb.StartTime,
            sb.EndTime,
            u.Name,
            sb.Crop,
            s.Centre,
            s.Location,
            s.Date
        FROM slot_bookings sb

        JOIN users u
            ON sb.FarmerId = u.FarmerId

        JOIN slots s
            ON sb.SNo = s.SNo

        WHERE sb.BookingId = ?
        AND sb.FarmerId = ?";

$stmt = mysqli_prepare($con, $sql);

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

$crop = $booking['Crop'];

$sql = "SELECT *
        FROM crop_rates
        WHERE crop_name = ?
        LIMIT 1";

$stmt = mysqli_prepare($con, $sql);

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

$ratePerQuintal = (float)$rate['rate_per_quintal'];

$quantity = (float)$booking['Quantity'];

$estimatedAmount =
    ($quantity / 100) * $ratePerQuintal;


?>


<!DOCTYPE html>
<html>

<head>

<title>Procurement Estimate</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f4f7f2;
    margin: 0;
    padding: 40px;
}

.card {
    max-width: 600px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.10);
}

.title {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
}

.row {
    display: flex;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #eee;
}

.total {
    margin-top: 25px;
    padding: 20px;
    background: #eef7e9;
    border-radius: 12px;
    text-align: center;
}

.amount {
    font-size: 38px;
    font-weight: bold;
}

.note {
    margin-top: 20px;
    font-size: 14px;
    color: #666;
}

</style>

</head>

<body>

<div class="card">

    <div class="title">
        🌾 Procurement Value
    </div>

    <div class="row">
        <span>Farmer</span>
        <strong>
            <?php echo htmlspecialchars($booking['Name']); ?>
        </strong>
    </div>

    <div class="row">
        <span>Crop</span>
        <strong>
            <?php echo htmlspecialchars($crop); ?>
        </strong>
    </div>

    <div class="row">
        <span>Booked Quantity</span>
        <strong>
            <?php echo $quantity; ?> KG
        </strong>
    </div>

    <div class="row">
        <span>Applicable Rate</span>
        <strong>
            ₹<?php echo number_format($ratePerQuintal); ?>/quintal
        </strong>
    </div>

    <div class="row">
        <span>Equivalent Quantity</span>
        <strong>
            <?php echo number_format($quantity / 100, 2); ?> quintals
        </strong>
    </div>

    <div class="total">

        <div>Estimated Procurement Value</div>

        <div class="amount">
            ₹<?php echo number_format($estimatedAmount, 2); ?>
        </div>

    </div>
    <a href="payment.php?booking_id=<?php echo $bookingId; ?>" style="text-decoration: none;">
    <button type="button" style="
        background-color: #059669;
        color: #ffffff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 16px;
        font-weight: 600;
        padding: 12px 28px;
        margin-top: 1em;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        outline: none;
    " 
    onmouseover="this.style.backgroundColor='#047857'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';" 
    onmouseout="this.style.backgroundColor='#059669'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';"
    onmousedown="this.style.transform='translateY(1px)';">
        Pay ₹<?php echo number_format($estimatedAmount, 2); ?>
    </button>
</a>
    <!--
    <div class="note">

        ℹ️ This is an estimated value.
        Final payment is calculated after
        verified weighment and quality assessment.

    </div>
-->
</div>

</body>

</html>