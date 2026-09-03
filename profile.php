<?php

session_start();

$login = false;

include "connect.php";

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

    <title>Profile</title>

    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/profile_style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Caacupe+One&display=swap"
        rel="stylesheet"
    >

</head>

<body>

<?php include "header.php"; ?>


<?php

if (!$login) {

    echo '
        <center>
            <div class="auth-card">

                <span class="status-text">
                    Not logged in
                </span>

                <div class="auth-buttons">

                    <a href="registration.php" class="btn btn-outline">
                        Register
                    </a>

                    <a href="login.php" class="btn btn-primary">
                        Login
                    </a>

                </div>

            </div>
        </center>
    ';

}


if ($login) {

    $farmerId = $_SESSION['FarmerId'];

    $sql = "SELECT * FROM `users` WHERE `FarmerId` = '$farmerId'";

    $result = mysqli_query($con, $sql);

    $row = mysqli_fetch_assoc($result);

echo'
        <div class="farmer-details">

        <h2>Farmer Details</h2>

        <div class="details-grid">

            <div class="detail-item">

                <span class="label">
                    Name
                </span>

                <span class="value">
                    ' . $row['Name'] .'
                </span>

            </div>


            <div class="detail-item">

                <span class="label">
                    Mobile
                </span>

                <span class="value">
                    '. $row['MobileNo'] .'
                </span>

            </div>


            <div class="detail-item">

                <span class="label">
                    Location
                </span>

                <span class="value">
                    '.$row['Location'].'
                </span>

            </div>


            <div class="detail-item">

                <span class="label">
                    Farmer ID
                </span>

                <span class="value">
                    '. $row['FarmerId'].'
                </span>

            </div>

        </div>

    </div>';
}
?>

</body>

</html>

