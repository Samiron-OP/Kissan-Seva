<?php

session_start();

$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include "connect.php";

    $farmerId = trim($_POST["farmerId"] ?? "");
    $password = $_POST["password"] ?? "";

    // Check empty fields
    if ($farmerId === "" || $password === "") {

        $showError = true;

    } else {

        // Prepare SQL statement
        $stmt = mysqli_prepare(
            $con,
            "SELECT FarmerId, password FROM users WHERE FarmerId = ?"
        );

        if ($stmt) {

            // Bind Farmer ID
            mysqli_stmt_bind_param($stmt, "s", $farmerId);

            // Execute query
            mysqli_stmt_execute($stmt);

            // Get result
            $result = mysqli_stmt_get_result($stmt);

            // Check if Farmer ID exists
            if ($result && mysqli_num_rows($result) == 1) {

                $row = mysqli_fetch_assoc($result);

                // Compare password
                if ($password == $row["password"]) {
                    
                    // Login successful
                    $_SESSION["loggedin"] = true;
                    $_SESSION["FarmerId"] = $row["FarmerId"];
                    echo $_SESSION['loggedin'];
                    // Redirect
                    header("Location: index.php");
                    exit();

                } else {

                    // Wrong password
                    $showError = true;
                }

            } else {

                // Farmer ID doesn't exist
                $showError = true;
            }

            mysqli_stmt_close($stmt);

        } else {

            $showError = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Farmer Login</title>

    <link rel="stylesheet" href="CSS/login_style.css">

</head>

<body>

<nav class="navbar">

    <a href="index.php">

        <div class="logo">
            Kissan Seva
        </div>

    </a>

</nav>


<div class="card">

    <header class="hero">

        <div class="hero-icon">

            <svg width="56" height="56"
                 viewBox="0 0 54 54"
                 shape-rendering="crispEdges">

                <rect x="6" y="6" width="6" height="6" fill="#86B85F"/>
                <rect x="12" y="6" width="6" height="6" fill="#86B85F"/>
                <rect x="6" y="12" width="6" height="6" fill="#3D6127"/>
                <rect x="12" y="12" width="6" height="6" fill="#5B8C3E"/>
                <rect x="18" y="18" width="6" height="6" fill="#5B8C3E"/>

                <rect x="36" y="6" width="6" height="6" fill="#86B85F"/>
                <rect x="30" y="6" width="6" height="6" fill="#86B85F"/>
                <rect x="36" y="12" width="6" height="6" fill="#3D6127"/>
                <rect x="30" y="12" width="6" height="6" fill="#5B8C3E"/>
                <rect x="24" y="18" width="6" height="6" fill="#5B8C3E"/>

                <rect x="24" y="18" width="6" height="6" fill="#3D6127"/>
                <rect x="24" y="24" width="6" height="6" fill="#3D6127"/>
                <rect x="24" y="30" width="6" height="6" fill="#3D6127"/>
                <rect x="24" y="36" width="6" height="6" fill="#3D6127"/>

                <rect x="12" y="42" width="6" height="6" fill="#6B4A38"/>
                <rect x="18" y="42" width="6" height="6" fill="#3E2723"/>
                <rect x="24" y="42" width="6" height="6" fill="#3E2723"/>
                <rect x="30" y="42" width="6" height="6" fill="#3E2723"/>
                <rect x="36" y="42" width="6" height="6" fill="#6B4A38"/>

            </svg>

        </div>


        <div class="hero-text">

            <h1>FARMER LOGIN</h1>

            <p>Sign in to manage your harvest listings</p>

        </div>

    </header>


    <form id="loginForm"
          method="POST"
          action="login.php"
          novalidate>


        <!-- FARMER ID -->

        <div class="field" data-field="farmerId">

            <label for="farmerId">

                <span class="label-dot"></span>

                Farmer ID

            </label>


            <div class="input-wrap">

                <input
                    type="text"
                    id="farmerId"
                    name="farmerId"
                    placeholder="e.g. FID20458"
                    autocomplete="username"
                    required
                >

            </div>


            <div class="error-msg">

                <span class="bang"></span>

                <span class="error-text"></span>

            </div>

        </div>


        <!-- PASSWORD -->

        <div class="field" data-field="password">

            <label for="password">

                <span class="label-dot"></span>

                Password

            </label>


            <div class="input-wrap">

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >


                <button
                    type="button"
                    class="pw-toggle"
                    id="pwToggle"
                    aria-label="Show password"
                >

                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                        id="eyeIcon"
                    >

                        <path
                            d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"
                            fill="none"
                            stroke="#3E2723"
                            stroke-width="2"
                            stroke-linejoin="round"
                        />

                        <circle
                            cx="12"
                            cy="12"
                            r="2.8"
                            fill="none"
                            stroke="#3E2723"
                            stroke-width="2"
                        />

                    </svg>

                </button>

            </div>


            <!-- PHP ERROR -->

        

                <div
                    class="error-msg"
                    style="display: flex;"
                >

                    <span class="bang"></span>

                    <span class="error-text">
                        Invalid Farmer ID or Password.
                    </span>

                </div>

  


        </div>


        <!-- OPTIONS -->

        <div class="options-row">

            <label class="remember">

                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                >

                Remember me

            </label>


            <a
                href="#"
                class="forgot-link"
                id="forgotLink"
            >
                Forgot password?
            </a>

        </div>


        <!-- LOGIN BUTTON -->

        <button
            type="submit"
            class="submit"
            id="submitBtn"
        >
            LOG IN
        </button>


        <!-- SUCCESS TOAST -->

        <div class="toast" id="toast">

            <span class="check"></span>

            <span id="toastText">
                Login successful! Redirecting&hellip;
            </span>

        </div>


    </form>


    <footer class="note">

        New here? <a href="registration.php">Register</a> on the sign-up page
        &middot;
        Your data stays on this form only

    </footer>

</div>


<!-- JavaScript -->

<script src="Scripts/login_JS.js"></script>

</body>

</html>