<?php
include "header.php";

echo 
'
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

                <a href="registration.html"><button>Register</button></a>
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