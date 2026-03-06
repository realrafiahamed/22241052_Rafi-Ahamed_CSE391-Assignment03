<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Car Workshop</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        background: linear-gradient(to bottom, #f0f2f5, #e0e7ff);
    }
    
    header {
        background: #007BFF;
        color: white;
        padding: 20px 40px;
        display: flex;
        justify-content: flex-end;
    }

    header a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        border: 2px solid white;
        padding: 8px 15px;
        border-radius: 6px;
    }

    header a:hover {
        background: white;
        color: #007BFF;
    }

    .navbar {
        background: #007BFF;
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        color: white;
        font-size: 22px;
        font-weight: bold;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 25px;
        font-weight: 500;
        transition: 0.3s;
    }

    .nav-links a:hover {
        color: #77acf0;
    }

    .admin-btn {
        border: 2px solid white;
        padding: 6px 12px;
        border-radius: 6px;
    }

    h1 {
        text-align: center;
        margin-top: 40px;
        font-size: 40px;
        color: #333;
    }

    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        padding: 50px;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        text-align: center;
        transition: 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h3 {
        margin-bottom: 10px;
    }

    .card p {
        font-size: 14px;
        color: #555;
        margin-bottom: 20px;
    }

    .book-btn {
        background: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
    }

    .book-btn:hover {
        background: #1e7e34;
    }
</style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="booking_form.php">Book Appointment</a>
            <a href="admin_login.php" class="admin-btn">Admin Login</a>
        </div>
    </nav>
</header>
<h1>Car Workshop</h1>
<div class="card-container">

    <div class="card">
        <h3>Jamal Bhuiya - M1</h3>
        <p>Engine Specialist<br>10 Years Experience</p>
        <a href="booking_form.php?mechanic=<?php echo urlencode('Jamal Bhuiya - M1'); ?>" class="book-btn">Book Appointment</a>
    </div>

    <div class="card">
        <h3>Karim Uddin - M2</h3>
        <p>Brake & Suspension Expert<br>8 Years Experience</p>
        <a href="booking_form.php?mechanic=<?php echo urlencode('Karim Uddin - M2'); ?>" class="book-btn">Book Appointment</a>
    </div>

    <div class="card">
        <h3>Rafique Islam - M3</h3>
        <p>Transmission Specialist<br>12 Years Experience</p>
        <a href="booking_form.php?mechanic=<?php echo urlencode('Rafique Islam - M3'); ?>" class="book-btn">Book Appointment</a>
    </div>

    <div class="card">
        <h3>Shohag Mia - M4</h3>
        <p>Electrical Systems Expert<br>6 Years Experience</p>
        <a href="booking_form.php?mechanic=<?php echo urlencode('Sohag Mia - M4'); ?>" class="book-btn">Book Appointment</a>
    </div>

    <div class="card">
        <h3>Sohel Rana - M5</h3>
        <p>Electrical Systems Expert<br>6 Years Experience</p>
        <a href="booking_form.php?mechanic=<?php echo urlencode('Sohel Rana - M5'); ?>" class="book-btn">Book Appointment</a>
    </div>

</div>
</body>
</html>