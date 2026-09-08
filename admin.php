<?php
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Cname = $_POST["centre"];
    $Location = $_POST["location"];
    $mobileNo = $_POST["mobile"];
    $date = $_POST["date"];
    
    $sql = "INSERT INTO `slots` (`SNo`, `Centre`, `Location`, `MobileNo`, `Date`, `FarmerId`, `Status`) VALUES (NULL, '$Cname', '$Location', '$mobileNo', '$date', NULL, 'N');";
    $result = mysqli_query($con, $sql);

    if ($result) {
        echo "<script>
                alert('Submission successful!');
                window.location.href = 'admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Submission unsuccessful. Please try again.');
                window.history.back();
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Centre Slot Form</title>

    <link rel="stylesheet" href="CSS/slot_form.css">
</head>

<body>

    <div class="form-container">

        <h2>Centre Slot Registration</h2>
        <p>Enter the details below to register a slot.</p>

        <form action="admin.php" method="POST">

            <!-- Centre -->
            <div class="form-group">
                <label for="centre">Centre</label>
                <input 
                    type="text" 
                    id="centre" 
                    name="centre" 
                    placeholder="Enter centre name"
                    required
                >
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="location">Location</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    placeholder="Enter location"
                    required
                >
            </div>

            <!-- Mobile Number -->
            <div class="form-group">
                <label for="mobile">Mobile No</label>
                <input 
                    type="tel" 
                    id="mobile" 
                    name="mobile" 
                    placeholder="Enter mobile number"
                    pattern="[0-9]{10}"
                    maxlength="10"
                    required
                >
            </div>

            <!-- Date -->
            <div class="form-group">
                <label for="date">Date</label>
                <input 
                    type="text" 
                    id="date" 
                    name="date" 
                    required
                    placeholder="Dec 24, 2026"
                >
            </div>

            
            <button type="submit">Submit</button>

        </form>

    </div>

</body>
</html>