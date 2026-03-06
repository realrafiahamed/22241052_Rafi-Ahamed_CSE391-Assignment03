<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['SERVER_NAME'] == "localhost") {
    $conn = new mysqli("localhost", "root", "", "workshop");} 
else {
    $conn = new mysqli("sql102.infinityfree.com", "if0_41287606", "4eJsK5fyivU3J6", "if0_41287606_workshop");}
if ($conn->connect_error) {
    showError("Connection failed: " . $conn->connect_error);}

function showError($msg) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Failed</title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }
    .error-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 40px 50px;
        max-width: 460px;
        width: 100%;
        text-align: center;
        border-top: 5px solid #dc3545;
    }
    .icon {
        font-size: 52px;
        margin-bottom: 15px;
    }
    h2 {
        color: #dc3545;
        margin: 0 0 12px 0;
        font-size: 22px;
    }
    p {
        color: #555;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    .btn {
        display: inline-block;
        padding: 11px 28px;
        background: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 7px;
        font-size: 15px;
        font-weight: bold;
        transition: 0.2s;
    }
    .btn:hover { background: #0056b3; }
</style>
</head>
<body>
<div class="error-card">
    <div class="icon">❌</div>
    <h2>Booking Failed</h2>
    <p><?= htmlspecialchars($msg) ?></p>
    <a href="javascript:history.back()" class="btn">Go Back</a>
</div>
</body>
</html>
<?php
    exit();
}

$name= $_POST['client_name']?? '';
$address= $_POST['address']?? '';
$phone= $_POST['phone']?? '';
$license= $_POST['car_license']?? '';
$engine= $_POST['engine_number']?? '';
$appointment_date = $_POST['appointment_date'] ?? '';
$mechanic_name= $_POST['mechanic']?? '';

if (empty($name) || empty($address) || empty($phone) || empty($license) || empty($engine) || empty($appointment_date) || empty($mechanic_name)) {
    showError("All fields are required.");}

$get_mech = $conn->prepare("SELECT id, max_slots FROM mechanics WHERE name=?");
$get_mech->bind_param("s", $mechanic_name);
$get_mech->execute();
$get_mech->bind_result($mechanic_id, $default_max);
$get_mech->fetch();
$get_mech->close();

if (!$mechanic_id) {
    showError("Invalid mechanic selected.");}

$override_check = $conn->prepare("
    SELECT COALESCE(o.max_slots, ?) AS effective_max FROM mechanics m LEFT JOIN mechanic_day_overrides o 
    ON o.mechanic_id = m.id AND o.override_date = ? WHERE m.id = ?");
$override_check->bind_param("isi", $default_max, $appointment_date, $mechanic_id);
$override_check->execute();
$override_check->bind_result($max_slots);
$override_check->fetch();
$override_check->close();

$check_client = $conn->prepare("SELECT id FROM clients WHERE phone=?");
$check_client->bind_param("s", $phone);
$check_client->execute();
$check_client->store_result();

if ($check_client->num_rows > 0) {
    $check_client->bind_result($client_id);
    $check_client->fetch();} 
else {
    $insert_client = $conn->prepare("INSERT INTO clients (name, address, phone) VALUES (?, ?, ?)");
    $insert_client->bind_param("sss", $name, $address, $phone);
    $insert_client->execute();
    $client_id = $insert_client->insert_id;
    $insert_client->close();}
$check_client->close();

$check_car = $conn->prepare("SELECT id FROM cars WHERE license_number=? OR engine_number=?");
$check_car->bind_param("ss", $license, $engine);
$check_car->execute();
$check_car->store_result();

if ($check_car->num_rows > 0) {
    $check_car->bind_result($car_id);
    $check_car->fetch();
} else {
    $insert_car = $conn->prepare("INSERT INTO cars (client_id, license_number, engine_number) VALUES (?, ?, ?)");
    $insert_car->bind_param("iss", $client_id, $license, $engine);
    $insert_car->execute();
    $car_id = $insert_car->insert_id;
    $insert_car->close();}
$check_car->close();

$check_duplicate = $conn->prepare("
    SELECT a.id FROM appointments a JOIN cars c ON a.car_id = c.id WHERE (c.license_number=? OR c.engine_number=?) AND a.appointment_date=?");
$check_duplicate->bind_param("sss", $license, $engine, $appointment_date);
$check_duplicate->execute();
$check_duplicate->store_result();
if ($check_duplicate->num_rows > 0) {
    $check_duplicate->close();
    showError("This car already has an appointment on " . date('d M Y', strtotime($appointment_date)) . ". Please choose a different date.");}
$check_duplicate->close();

$check_slots = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id=? AND appointment_date=?");
$check_slots->bind_param("is", $mechanic_id, $appointment_date);
$check_slots->execute();
$check_slots->bind_result($booked_count);
$check_slots->fetch();
$check_slots->close();

if ($booked_count >= $max_slots) {
    showError("$mechanic_name is fully booked on " . date('d M Y', strtotime($appointment_date)) . ". Please choose another mechanic or date.");}

$insert_appt = $conn->prepare("INSERT INTO appointments (car_id, mechanic_id, appointment_date) VALUES (?, ?, ?)");
$insert_appt->bind_param("iis", $car_id, $mechanic_id, $appointment_date);

if ($insert_appt->execute()) {
    header("Location: confirmation.php?name=" . urlencode($name) .
        "&mechanic=" . urlencode($mechanic_name) .
        "&date=" . urlencode($appointment_date));
    exit();
} else {
    showError("Something went wrong while booking. Please try again.");}
$insert_appt->close();
$conn->close();
?>