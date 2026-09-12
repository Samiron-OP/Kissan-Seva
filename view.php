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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Slot</title>


    <!-- NAVBAR CSS -->
    <link rel="stylesheet" href="CSS/nav_style.css">

    <!-- PAGE CSS -->
    <link rel="stylesheet" href="CSS/view_style.css">

    <link rel="stylesheet" href="CSS/translate.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar {
            width: 100%;
            min-height: 64px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 10px 25px;

            background-color: #1F2937;

            position: relative;
            z-index: 1000;
        }
    </style>
</head>


<body>


    <!-- ==============================
         NAVBAR
         ============================== -->

    <?php include "header.php"; ?>

<div id="google_translate_element"></div>
    <!-- ==============================
         CARD
         ============================== -->
      <?php


        if (!$login){
            echo '
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
            
            </center>';
            }

        

        if ($login) {
            $timeStart = 9;
            $timeEnd;
            $farmerId = $_SESSION['FarmerId'];
            
            $sql = "SELECT
                    u.Name,
                    s.Date,
                    sb.TokenNo,
                    sb.StartTime,
                    sb.EndTime,
                    sb.Quantity,
                    s.Location,
                    s.Status,
                    s.Centre
                FROM slot_bookings sb
                JOIN users u
                    ON sb.FarmerId = u.FarmerId
                JOIN slots s
                    ON sb.SNo = s.SNo
                WHERE sb.FarmerId = '". $farmerId . "'
                ORDER BY s.Date, sb.TokenNo;";

            $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

        echo '
    <main class="card">

        <!-- CARD HEADER -->

        <div class="card-header">

            <div class="field-group">

                <span class="label">
                    Date
                </span>

                <span class="value">
                    ' .$row["Date"]. '
                </span>

            </div>';
            if ($row["Status"] == 'N') {
                echo '
                <div class="status-badge">
                Pending
                </div>
                ';
                }

            
            if ($row["Status"] == 'Y') {
                echo '
                <div class="status-badge verified">
                Success
                </div>
                ';
                }

            
        echo '
        </div>


        <!-- CARD BODY -->

        <div class="card-body">


            <!-- NAME -->

            <div class="field-group">

                <span class="label">
                    Name
                </span>

                <span class="value">
                    ' . $row["Name"] . '
                </span>

            </div>


            <!-- TIME + QUANTITY -->

            <div class="grid-2">

                <div class="field-group">

                    <span class="label">
                        Estimated Time
                    </span>

                    <span class="value">
                        '.$row["StartTime"].' ~ '. $row["EndTime"].'
                    </span>

                </div>


                <div class="field-group">

                    <span class="label">
                        Quantity
                    </span>

                    <span class="value">
                        '. $row["Quantity"]. ' KG
                    </span>

                </div>

            </div>


            <!-- LOCATION -->

            <div class="location-row">

                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24">

                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>

                </svg>


                <span class="location-text">
                    '.$row["Centre"] .', '. $row["Location"].'
                </span>

            </div>

        </div>

    </main>';
                }
            }
        }
?>
<script type="text/javascript">
  function googleTranslateElementInit() {
    new google.translate.TranslateElement(
      {pageLanguage: 'en'}, 
      'google_translate_element'
    );
  }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>

</html>