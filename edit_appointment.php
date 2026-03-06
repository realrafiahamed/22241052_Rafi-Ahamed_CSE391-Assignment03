<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}
$id = $_GET['id'] ?? 0;
if(!$id) die("Invalid appointment ID.");
$appt = $conn->prepare("
    SELECT a.id AS appointment_id, a.mechanic_id, a.appointment_date
    FROM appointments a
    WHERE a.id = ?
");
$appt->bind_param("i", $id);
$appt->execute();
$result = $appt->get_result();
$row = $result->fetch_assoc();
if(!$row) die("Appointment not found.");

$message = "";
if(isset($_POST['update'])){
    $new_mechanic = $_POST['mechanic_id'];
    $new_date = $_POST['appointment_date'];
    $slot_check = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id=? AND appointment_date=? AND id!=?");
    $slot_check->bind_param("isi", $new_mechanic, $new_date, $id);
    $slot_check->execute();
    $slot_check->bind_result($booked_count);
    $slot_check->fetch();
    $slot_check->close();

    $max_slots = $conn->query("SELECT max_slots FROM mechanics WHERE id=$new_mechanic")->fetch_assoc()['max_slots'];

    if($booked_count >= $max_slots){
        $message = "<p class='error'>This mechanic is fully booked on $new_date</p>";
    } else {
        $upd = $conn->prepare("UPDATE appointments SET mechanic_id=?, appointment_date=? WHERE id=?");
        $upd->bind_param("isi", $new_mechanic, $new_date, $id);
        $upd->execute();
        header("Location: admin_dashboard.php?msg=" . urlencode("Appointment updated successfully!"));
        exit();
        $upd->close();
        $row['mechanic_id'] = $new_mechanic;
        $row['appointment_date'] = $new_date;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Appointment</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        padding: 40px;
    }

    .container {
        background-color: white;
        max-width: 450px;
        margin: auto;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    h2 {
        text-align: center;
        color: #007BFF;
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    select, input[type="date"], button {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    button {
        background-color: #28a745;
        color: white;
        font-size: 18px;
        border: none;
        cursor: pointer;
        transition: background 0.3s;
    }

    button:hover {
        background-color: #1e7e34;
    }

    p.success {
        text-align: center;
        font-size: 16px;
        color: green;
        font-weight: bold;
    }

    p.error {
        text-align: center;
        font-size: 16px;
        color: red;
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Edit Appointment</h2>
    <form method="POST">
        <label>Mechanic</label>
        <select name="mechanic_id" required>
            <?php
            $mechs = $conn->query("SELECT id, name FROM mechanics");
            while($m = $mechs->fetch_assoc()){
                $selected = ($m['id'] == $row['mechanic_id']) ? "selected" : "";
                echo "<option value='{$m['id']}' $selected>{$m['name']}</option>";
            }
            ?>
        </select>
        <label>Appointment Date</label>
        <input type="date" name="appointment_date" value="<?php echo $row['appointment_date']; ?>" required>
        <button type="submit" name="update">Update Appointment</button>
        
    </form>
</div>
</body>
</html>