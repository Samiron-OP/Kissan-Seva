<?php
$registered = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "connect.php";
    $name = $_POST["name"];
    $mobile = $_POST["mobile"];
    $pass = $_POST["password"];
    $location = $_POST["location"];
    $farmerid = $_POST["farmerId"];
    $crop = $_POST["crop"];
    $qunatity = $_POST["quantity"];


    $sql = "INSERT INTO `users` (`SNo`, `Name`, `MobileNo`, `Password`, `Location`, `FarmerId`, `Crop`, `Quantity`) VALUES (NULL, '$name', '$mobile', '$pass', '$location', '$farmerid', '$crop', '$qunatity');";

    $result = mysqli_query($con, $sql);

    if($result){
      $registered = true;
      header("Location: login.php");
      exit();
    }
    else{
        echo "Something is wrong";
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
            <input type="tel" id="mobile" name="mobile" placeholder="10-digit number" inputmode="numeric" maxlength="10"
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

        <div class="field" data-field="crop">
          <label for="crop"><span class="label-dot"></span>Crop name</label>
          <div class="input-wrap">
            <input type="text" id="crop" name="crop" placeholder="e.g. Cotton, Paddy, Turmeric">
          </div>
          <div class="error-msg"><span class="bang"></span><span class="error-text"></span></div>
        </div>

        <div class="field" data-field="quantity">
          <label for="quantity"><span class="label-dot"></span>Quantity</label>
          <div class="input-wrap quantity-row">
            <input type="number" id="quantity" name="quantity" placeholder="0" min="0" step="0.1">
            <span class="unit">KG</span>
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

  </body>

</html>';
