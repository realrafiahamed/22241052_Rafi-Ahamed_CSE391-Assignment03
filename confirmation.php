<?php
if (!isset($_GET['name'])) {
    header("Location: index.php");
    exit();
}
$name = $_GET['name'];
$mechanic = $_GET['mechanic'];
$date = $_GET['date'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: #ffffff;
            padding: 50px;
            width: 420px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
        }
        h2 {
            margin-top: 15px;
            color: #333;
        }
        .info {
            margin: 20px 0;
            text-align: left;
            font-size: 16px;
        }
        .info p {
            margin: 8px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            background: #2a5298;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .btn:hover {
            background: #1e3c72;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="success-icon">✔</div>
    <h2>Appointment Confirmed</h2>

    <div class="info">
        <p><strong>Client Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Mechanic:</strong> <?php echo htmlspecialchars($mechanic); ?></p>
        <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($date); ?></p>
    </div>
    <a class="btn" href="index.php">Back to Home</a>
</div>
</body>
</html>