<?php

echo 
'<!DOCTYPE html>
<head>
<link rel="stylesheet" href="CSS//nav_style.css">
<link rel = "stylesheet" href="CSS//style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caacupe+One&display=swap"rel="stylesheet">
</head>

<body>

';?>


<?php 
include "header.php";
echo'

<section class="menu-section">

<div class="menu-intro">
    <h1>Kissan Seva</h1>
    <p>
        Access essential agricultural services quickly and easily.
        Register, book your procurement slot, and track your payments
        all in one place.
    </p>
</div>

    <div class="card-container">

        <!-- Card 1 -->
        <div class="card">
            <img src="Images/card1.jpg"
                 alt="Register">

            <div class="card-content">
                <h2> Register</h2>

                <p>
                    Register to create your account.
                </p>

                <a href="registration.php"><button>Register</button></a>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="card">
            <img src="Images/card2.jpg"
                 alt="Book">

            <div class="card-content">
                <h2>Book your Slot</h2>

                <p>
                    Book your slot for the nearest procurement center
                </p>

                <button>Book</button>
            </div>
        </div>


        <!-- Card 3 -->
        <div class="card">
            <img src="Images/card3.jpg"
                 alt="View">

            <div class="card-content">
                <h2>View Status</h2>

                <p>
                    View status of your procurement & payments recieved
                </p>

                <button>View</button>
            </div>
        </div>

    </div>

</body>
</html>

    ';
?>