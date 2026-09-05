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
// APPLY FOR SLOT
// --------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["apply"])) {

    // Check whether FarmerId exists in session
    if (!isset($_SESSION["FarmerId"])) {

        echo "<script>
                alert('Farmer ID not found. Please login again.');
                window.location.href = 'login.php';
              </script>";
        exit;
    }


    // Get FarmerId from session
    $farmerId = $_SESSION["FarmerId"];


    // Get selected slot details
    $centre = $_POST["Centre"];
    $location = $_POST["Location"];
    $mobileNo = $_POST["MobileNo"];
    $date = $_POST["Date"];


    // --------------------------------------------------
    // UPDATE SLOT
    // --------------------------------------------------

    $sql = "UPDATE slots
            SET FarmerId = ?
            WHERE Centre = ?
            AND Location = ?
            AND MobileNo = ?
            AND Date = ?
            AND FarmerId IS NULL";


    $stmt = mysqli_prepare($con, $sql);


    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $farmerId,
            $centre,
            $location,
            $mobileNo,
            $date
        );


        if (mysqli_stmt_execute($stmt)) {

            // Check whether any row was actually updated
            if (mysqli_stmt_affected_rows($stmt) > 0) {

                echo "<script>
                        alert('Slot booked successfully!');
                        window.location.href = 'book.php';
                      </script>";
                exit;

            } else {

                echo "<script>
                        alert('This slot is no longer available.');
                        window.location.href = 'book.php';
                      </script>";
                exit;
            }

        } else {

            echo "<script>
                    alert('Error while booking the slot.');
                  </script>";
        }


        mysqli_stmt_close($stmt);

    } else {

        echo "<script>
                alert('Database query error.');
              </script>";
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


    <!-- Font Awesome -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>


<body>


    <?php include "header.php"; ?>


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
            // GET AVAILABLE SLOTS
            // --------------------------------------------------

            $sql = "SELECT *
                    FROM slots
                    WHERE FarmerId IS NULL";


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


                                    <!-- Hidden slot information -->

                                    <input type="hidden"
                                           name="Centre"
                                           value="<?php echo htmlspecialchars($row["Centre"]); ?>">


                                    <input type="hidden"
                                           name="Location"
                                           value="<?php echo htmlspecialchars($row["Location"]); ?>">


                                    <input type="hidden"
                                           name="MobileNo"
                                           value="<?php echo htmlspecialchars($row["MobileNo"]); ?>">


                                    <input type="hidden"
                                           name="Date"
                                           value="<?php echo htmlspecialchars($row["Date"]); ?>">


                                    <!-- Apply Button -->

                                    <button type="submit"
                                            name="apply"
                                            class="apply-btn">

                                        Apply Now

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


</body>

</html>