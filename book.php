<?php

session_start();

$login = false;

include "connect.php";

// --------------------------------------------------
// CHECK LOGIN
// --------------------------------------------------

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    $login = false;
} else {
    $login = true;
}

// --------------------------------------------------
// BOOK SLOT - QUANTITY BASED QUEUE
//
// Working hours: 09:00 AM - 05:00 PM = 480 minutes
//
// Processing time:
//     (480 / centre daily capacity) * farmer quantity
//
// Queue:
//     Token 11 starts at 09:00
//     Each next token starts when the previous token ends
// --------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["apply"])) {

    if (!$login || !isset($_SESSION["FarmerId"])) {
        echo "<script>
                alert('Please login again.');
                window.location.href = 'login.php';
              </script>";
        exit;
    }

    $farmerId = $_SESSION["FarmerId"];
    $sno = isset($_POST["SNo"]) ? (int)$_POST["SNo"] : 0;
    $quantity = isset($_POST["Quantity"]) ? (int)$_POST["Quantity"] : 0;

    if ($sno <= 0 || $quantity <= 0) {
        echo "<script>
                alert('Please enter a valid quantity.');
                window.location.href = 'book.php';
              </script>";
        exit;
    }

    mysqli_begin_transaction($con);

    try {

        // --------------------------------------------------
        // 1. LOCK AND GET CENTRE DETAILS
        // --------------------------------------------------

        $sql = "SELECT Centre, Location, Date, TotalQuantity, RemainingQuantity
                FROM slots
                WHERE SNo = ?
                FOR UPDATE";

        $stmt = mysqli_prepare($con, $sql);

        if (!$stmt) {
            throw new Exception("Could not read procurement centre.");
        }

        mysqli_stmt_bind_param($stmt, "i", $sno);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) != 1) {
            mysqli_stmt_close($stmt);
            throw new Exception("Procurement centre not found.");
        }

        $slot = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $totalQuantity = (int)$slot["TotalQuantity"];
        $remainingQuantity = (int)$slot["RemainingQuantity"];

        if ($totalQuantity <= 0) {
            throw new Exception("This centre has no valid daily capacity.");
        }

        if ($quantity > $remainingQuantity) {
            throw new Exception(
                "Only " . $remainingQuantity . " kg is remaining at this centre."
            );
        }

        // --------------------------------------------------
        // 2. WORKING HOURS
        // --------------------------------------------------

        $openingTime = new DateTime("09:00:00");
        $closingTime = new DateTime("17:00:00");

        $totalWorkingMinutes =
            ($closingTime->getTimestamp() - $openingTime->getTimestamp()) / 60;

        // Time required for 1 kg at this centre.
        $minutesPerKg = $totalWorkingMinutes / $totalQuantity;

        // Time required for this farmer.
        $processingMinutes = $quantity * $minutesPerKg;

        // --------------------------------------------------
        // 3. REBUILD CURRENT QUEUE TIMINGS
        //
        // This also handles old bookings whose StartTime/
        // EndTime are NULL.
        // --------------------------------------------------

        $sql = "SELECT BookingId, TokenNo, Quantity
                FROM slot_bookings
                WHERE SNo = ?
                ORDER BY TokenNo ASC, BookingId ASC";

        $stmt = mysqli_prepare($con, $sql);

        if (!$stmt) {
            throw new Exception("Could not read existing queue.");
        }

        mysqli_stmt_bind_param($stmt, "i", $sno);
        mysqli_stmt_execute($stmt);

        $queueResult = mysqli_stmt_get_result($stmt);

        $nextStartTime = clone $openingTime;
        $lastToken = 10;

        while ($booking = mysqli_fetch_assoc($queueResult)) {

            $bookingToken = (int)$booking["TokenNo"];
            $bookingQuantity = (int)$booking["Quantity"];

            // Keep the highest existing token.
            if ($bookingToken > $lastToken) {
                $lastToken = $bookingToken;
            }

            $bookingMinutes = $bookingQuantity * $minutesPerKg;

            $bookingStart = clone $nextStartTime;
            $bookingEnd = clone $nextStartTime;

            $bookingSeconds = (int)round($bookingMinutes * 60);
            $bookingEnd->modify("+{$bookingSeconds} seconds");

            // Update old/missing queue times as well.
            $updateSql = "UPDATE slot_bookings
                          SET StartTime = ?, EndTime = ?
                          WHERE BookingId = ?";

            $updateStmt = mysqli_prepare($con, $updateSql);

            if (!$updateStmt) {
                mysqli_stmt_close($stmt);
                throw new Exception("Could not update queue timings.");
            }

            $startValue = $bookingStart->format("H:i:s");
            $endValue = $bookingEnd->format("H:i:s");

            mysqli_stmt_bind_param(
                $updateStmt,
                "ssi",
                $startValue,
                $endValue,
                $booking["BookingId"]
            );

            if (!mysqli_stmt_execute($updateStmt)) {
                mysqli_stmt_close($updateStmt);
                mysqli_stmt_close($stmt);
                throw new Exception("Could not update queue timings.");
            }

            mysqli_stmt_close($updateStmt);

            $nextStartTime = $bookingEnd;
        }

        mysqli_stmt_close($stmt);

        // First token is 11.
        $tokenNo = ($lastToken < 11) ? 11 : $lastToken + 1;

        // --------------------------------------------------
        // 4. CALCULATE NEW FARMER'S START/END TIME
        // --------------------------------------------------

        $startTime = clone $nextStartTime;
        $endTime = clone $startTime;

        $processingSeconds = (int)round($processingMinutes * 60);
        $endTime->modify("+{$processingSeconds} seconds");

        // Do not allow a booking that goes beyond 5 PM.
        if ($endTime > $closingTime) {
            throw new Exception(
                "This quantity cannot be processed within today's 9:00 AM to 5:00 PM working hours."
            );
        }

        $startValue = $startTime->format("H:i:s");
        $endValue = $endTime->format("H:i:s");

        // --------------------------------------------------
        // 5. INSERT BOOKING
        // --------------------------------------------------

        $sql = "INSERT INTO slot_bookings
                (SNo, FarmerId, Quantity, TokenNo, StartTime, EndTime)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $sql);

        if (!$stmt) {
            throw new Exception("Could not create booking.");
        }

        mysqli_stmt_bind_param(
            $stmt,
            "isiiss",
            $sno,
            $farmerId,
            $quantity,
            $tokenNo,
            $startValue,
            $endValue
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Could not save booking.");
        }

        mysqli_stmt_close($stmt);

        // --------------------------------------------------
        // 6. REDUCE REMAINING QUANTITY
        // --------------------------------------------------

        $sql = "UPDATE slots
                SET RemainingQuantity = RemainingQuantity - ?
                WHERE SNo = ?
                AND RemainingQuantity >= ?";

        $stmt = mysqli_prepare($con, $sql);

        if (!$stmt) {
            throw new Exception("Could not update remaining quantity.");
        }

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $quantity,
            $sno,
            $quantity
        );

        if (!mysqli_stmt_execute($stmt) ||
            mysqli_stmt_affected_rows($stmt) != 1) {

            mysqli_stmt_close($stmt);
            throw new Exception("Could not update remaining quantity.");
        }

        mysqli_stmt_close($stmt);

        // --------------------------------------------------
        // 7. MARK CENTRE FULL IF CAPACITY IS EXHAUSTED
        // --------------------------------------------------

        $sql = "UPDATE slots
                SET Status = 'F'
                WHERE SNo = ?
                AND RemainingQuantity = 0";

        $stmt = mysqli_prepare($con, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $sno);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // --------------------------------------------------
        // 8. COMMIT
        // --------------------------------------------------

        mysqli_commit($con);

        $displayStart = $startTime->format("h:i A");
        $displayEnd = $endTime->format("h:i A");

        echo "<script>
                alert(" . json_encode(
                    "Booking successful!\n" .
                    "Token No: " . $tokenNo . "\n" .
                    "Quantity: " . $quantity . " kg\n" .
                    "Expected time: " . $displayStart . " - " . $displayEnd
                ) . ");
                window.location.href = 'book.php';
              </script>";

        exit;

    } catch (Exception $e) {

        mysqli_rollback($con);

        echo "<script>
                alert(" . json_encode($e->getMessage()) . ");
                window.location.href = 'book.php';
              </script>";

        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book a Slot</title>


    <!-- CSS -->

    <link rel="stylesheet" href="CSS/nav_style.css">

    <link rel="stylesheet" href="CSS/bookstyle.css">

    <link rel="stylesheet" href="CSS/style.css">

    <link rel="stylesheet" href="CSS/translate.css">

    <!-- Font Awesome -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>


<body>


    <?php include "header.php"; ?>

<div id="google_translate_element"></div>
    <main class="main-content">


        <?php if (!$login): ?>

            <center>

                <div class="auth-card" style="margin:5em 4em">

                    <span class="status-text">
                        Not logged in
                    </span>


                    <div class="auth-buttons">

                        <a href="registration.php"
                           class="btn btn-outline">
                            Register
                        </a>


                        <a href="login.php"
                           class="btn btn-primary">
                            Login
                        </a>

                    </div>

                </div>

            </center>

        <?php endif; ?>


        <?php

        if ($login) {


            // --------------------------------------------------
            // GET AVAILABLE SLOTS (SORTED BY NEAREST LOCATION IF LAT/LNG PRESENT)
            // --------------------------------------------------
// echo "<pre>";
// echo "LAT: ";
// var_dump($_SESSION['user_lat'] ?? 'NOT SET');

// echo "LNG: ";
// var_dump($_SESSION['user_lng'] ?? 'NOT SET');
// echo "</pre>";

            if (isset($_SESSION['user_lat']) && isset($_SESSION['user_lng'])) {

                $userLat = (float)$_SESSION['user_lat'];
                $userLng = (float)$_SESSION['user_lng'];

                $sql = "SELECT *,
                        ST_Distance_Sphere(
                            POINT(Longitude, Latitude),
                            POINT($userLng, $userLat)
                        ) / 1000 AS distance
                        FROM slots
                        WHERE RemainingQuantity > 0
                        AND Latitude IS NOT NULL
                        AND Longitude IS NOT NULL
                        ORDER BY distance ASC";

            } else {

                $sql = "SELECT *
                        FROM slots
                        WHERE RemainingQuantity > 0
                        ORDER BY SNo ASC";
            }

            $result = mysqli_query($con, $sql);
            // Check whether slots exist

            if (mysqli_num_rows($result) > 0) {


                // --------------------------------------------------
                // DISPLAY EACH SLOT
                // --------------------------------------------------

                while ($row = mysqli_fetch_assoc($result)) {

        ?>


                    <center>

                        <div class="card"
                             style="margin-top:2em; margin-bottom:2em;">


                            <!-- CARD HEADER -->

                            <div class="card-header">


                                <h2 class="centre-name">

                                    <?php echo htmlspecialchars($row["Centre"]); ?>

                                </h2>


                                <div class="scheduled-badge">

                                    <i class="fa-regular fa-calendar-check"></i>

                                    <span>

                                        <?php echo htmlspecialchars($row["Date"]); ?>

                                    </span>

                                </div>

                            </div>


                            <!-- CARD FOOTER -->

                            <div class="card-footer">


                                <div class="contact-info">


                                    <!-- LOCATION -->

                                    <div class="info-item">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <span>

                                            <?php echo htmlspecialchars($row["Location"]); ?>

                                        </span>

                                    </div>


                                    <!-- MOBILE NUMBER -->

                                    <div class="info-item">

                                        <i class="fa-solid fa-phone"></i>

                                        <span>

                                            +91
                                            <?php echo htmlspecialchars($row["MobileNo"]); ?>

                                        </span>

                                    </div>


                                </div>


                                <!-- APPLY FORM -->

                                <form method="POST" action="book.php">

    <!-- Identify the exact slot -->
    <input
        type="hidden"
        name="SNo"
        value="<?php echo (int)$row["SNo"]; ?>"
    >


    <!-- Show remaining quantity -->
    <div style="margin: 15px 0;">

        <strong>
            Remaining Quantity:
        </strong>

        <?php echo (int)$row["RemainingQuantity"]; ?>

    </div>


    <!-- Farmer enters quantity -->
    <div style="margin: 15px 0;">

        <label>
            <strong>Quantity to book:</strong>
        </label>

        <input
            type="number"
            name="Quantity"
            min="1"
            max="<?php echo (int)$row["RemainingQuantity"]; ?>"
            required
        >

    </div>


    <!-- Book -->
    <button
        type="submit"
        name="apply"
        class="apply-btn"
    >
        Book Slot
    </button>

</form>

                            </div>

                        </div>

                    </center>


        <?php

                }

            } else {

        ?>


                <center>

                    <div class="auth-card" style="margin:5em 4em">

                        <span class="status-text">

                            No slots available.

                        </span>

                    </div>

                </center>


        <?php

            }

        }

        ?>


    </main>

<script type="text/javascript">
  function googleTranslateElementInit() {
    new google.translate.TranslateElement(
      {pageLanguage: 'en'}, 
      'google_translate_element'
    );
  }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
<?php if ($login): ?>

<script>
document.addEventListener("DOMContentLoaded", function () {

    console.log("Location script started");

    // Prevent continuous reload
    if (sessionStorage.getItem("location_saved") === "1") {
        console.log("Location already saved. No reload.");
        return;
    }

    if (!navigator.geolocation) {
        console.error("Geolocation is not supported.");
        return;
    }

    console.log("Requesting GPS...");

    navigator.geolocation.getCurrentPosition(

        function (position) {

            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            console.log("GPS Latitude:", lat);
            console.log("GPS Longitude:", lng);
            console.log("Accuracy:", position.coords.accuracy);

            fetch("save_location.php", {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },
                body:
                    "lat=" + encodeURIComponent(lat) +
                    "&lng=" + encodeURIComponent(lng)
            })

            .then(response => response.text())

            .then(data => {

                console.log("Server:", data);

                // Mark location as saved BEFORE reload
                sessionStorage.setItem(
                    "location_saved",
                    "1"
                );

                // Reload only once
                window.location.reload();
            })

            .catch(error => {

                console.error(
                    "Error saving location:",
                    error
                );

            });

        },

        function (error) {

            console.error(
                "GPS Error:",
                error.message
            );

        },

        {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 10000
        }
    );

});
</script>

<?php endif; ?>

</body>

</html>