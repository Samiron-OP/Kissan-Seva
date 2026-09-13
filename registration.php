<?php

$registered = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "connect.php";
    $name = $_POST["name"];
    $mobile = $_POST["mobile"];
    $pass = $_POST["password"];
    $location = $_POST["location"];
    $farmerid = $_POST["farmerId"];

    // Hash the password securely using bcrypt/Argon2id (managed automatically)
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // Use prepared statements to execute safely
    $sql = "INSERT INTO `users` (`SNo`, `Name`, `MobileNo`, `Password`, `Location`, `FarmerId`) VALUES (NULL, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $name, $mobile, $hashed_pass, $location, $farmerid);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $registered = true;
            mysqli_stmt_close($stmt);
            header("Location: login.php");
            exit();
        } else {
            echo "Something went wrong during execution.";
        }
    } else {
        echo "Database error: Unable to prepare statement.";
    }
}


echo '
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmer Registration</title>
  <link rel="stylesheet" href="CSS/registration_style.css">


</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">

      <div class="logo">
        <a href="index.php">Kissan Seva</a>
      </div>

    </nav>

    <div id="google_translate_element"></div>


    <!-- YOUR EXISTING REGISTRATION FORM -->
    <!-- keep everything that is already here -->
    <div class="registration-wrapper">
    <div class="card pixel-corners ">
      <header class="hero">
        <div class="hero-icon">
          <!-- pixel sprout mascot -->
          <svg width="56" height="56" viewBox="0 0 54 54" shape-rendering="crispEdges">
            <rect x="6" y="6" width="6" height="6" fill="#86B85F" />
            <rect x="12" y="6" width="6" height="6" fill="#86B85F" />
            <rect x="6" y="12" width="6" height="6" fill="#3D6127" />
            <rect x="12" y="12" width="6" height="6" fill="#5B8C3E" />
            <rect x="18" y="18" width="6" height="6" fill="#5B8C3E" />

            <rect x="36" y="6" width="6" height="6" fill="#86B85F" />
            <rect x="30" y="6" width="6" height="6" fill="#86B85F" />
            <rect x="36" y="12" width="6" height="6" fill="#3D6127" />
            <rect x="30" y="12" width="6" height="6" fill="#5B8C3E" />
            <rect x="24" y="18" width="6" height="6" fill="#5B8C3E" />

            <rect x="24" y="18" width="6" height="6" fill="#3D6127" />
            <rect x="24" y="24" width="6" height="6" fill="#3D6127" />
            <rect x="24" y="30" width="6" height="6" fill="#3D6127" />
            <rect x="24" y="36" width="6" height="6" fill="#3D6127" />

            <rect x="12" y="42" width="6" height="6" fill="#6B4A38" />
            <rect x="18" y="42" width="6" height="6" fill="#3E2723" />
            <rect x="24" y="42" width="6" height="6" fill="#3E2723" />
            <rect x="30" y="42" width="6" height="6" fill="#3E2723" />
            <rect x="36" y="42" width="6" height="6" fill="#6B4A38" />
          </svg>
        </div>
        <div class="hero-text">
          <h1>REGISTER HERE</h1>
          <p>Sign up to list and sell your harvest</p>
        </div>
      </header>

      <form action="registration.php" method="post" id="regForm" novalidate>

        <div class="field" data-field="name">
          <label for="name"><span class="label-dot"></span>Full name</label>
          <div class="input-wrap">
            <input type="text" id="name" name="name" placeholder="e.g. Ramesh Patil" autocomplete="name">
          </div>
          <div class="error-msg"><span class="bang"></span><span class="error-text"></span></div>
        </div>

        <div class="field" data-field="mobile">
          <label for="mobile"><span class="label-dot"></span>Mobile number</label>
          <div class="input-wrap">
            <input type="numbersss" id="mobile" name="mobile" placeholder="10-digit number" inputmode="numeric" maxlength="10"
              autocomplete="tel">
          </div>
          <div class="error-msg"><span class="bang"></span><span class="error-text"></span></div>
        </div>

        <div class="field" data-field="password">
          <label for="password"><span class="label-dot"></span>Password</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" placeholder="At least 6 characters"
              autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password">
              <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" id="eyeIcon">
                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" fill="none" stroke="#3E2723" stroke-width="2"
                  stroke-linejoin="round" />
                <circle cx="12" cy="12" r="2.8" fill="none" stroke="#3E2723" stroke-width="2" />
              </svg>
            </button>
          </div>
          <div class="error-msg"><span class="bang"></span><span class="error-text"></span></div>
        </div>

        <div class="field" data-field="location">
          <label for="location"><span class="label-dot"></span>Location (village / district)</label>
          <div class="input-wrap">
            <input type="text" id="location" name="location" placeholder="e.g. Kadipikonda, Telangana"
              autocomplete="address-level2">
          </div>
          <div class="error-msg"><span class="bang"></span><span class="error-text"></span></div>
        </div>

        <div class="field" data-field="farmerId">
          <label for="farmerId"><span class="label-dot"></span>Farmer ID</label>
          <div class="input-wrap">
            <input type="text" id="farmerId" name="farmerId" placeholder="e.g. FID20458">
          </div>
          <div class="error-msg"><span class="bang"></span><span class="error-text"></span></div>
        </div>

        <button type="submit" class="submit">REGISTER</button>
        '?>

        
      </form>

      <footer class="note">All fields are required &middot; Your data stays on this form only</footer>
    </div>

  </div>

    <script src="Scripts/registration_JS.js">

    </script>
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

  </body>

</html>';
