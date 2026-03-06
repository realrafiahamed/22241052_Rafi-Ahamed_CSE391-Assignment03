<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$result = $conn->query("
    SELECT 
        a.id AS appointment_id, c.name AS client_name, c.phone AS phone, ca.license_number AS car_license, ca.engine_number AS engine_number,
        m.name AS mechanic_name, m.max_slots, a.appointment_date, a.status FROM appointments a JOIN cars ca ON a.car_id = ca.id JOIN clients c ON ca.client_id = c.id
        JOIN mechanics m ON a.mechanic_id = m.id ORDER BY a.appointment_date DESC");?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 40px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        h2 { margin: 0; }
        .top-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .logout {
            padding: 8px 15px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .logout:hover { background: #a71d2a; }

        .manage-slots-btn {
            padding: 8px 15px;
            background: #fd7e14;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .manage-slots-btn:hover { background: #c96209; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:hover { background: #f1f1f1; }

        .edit-btn {
            padding: 6px 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .edit-btn:hover { background-color: #0056b3; }

        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <h2>Admin Dashboard — Appointments</h2>
    <div class="top-actions">
        <a href="manage_slots.php" class="manage-slots-btn">⚙ Manage Mechanic Slots</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="success-msg"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<table>
<tr>
    <th>ID</th>
    <th>Client Name</th>
    <th>Phone</th>
    <th>Car License</th>
    <th>Engine Number</th>
    <th>Mechanic</th>
    <th>Appointment Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['appointment_id'] ?></td>
    <td><?= htmlspecialchars($row['client_name']) ?></td>
    <td><?= htmlspecialchars($row['phone']) ?></td>
    <td><?= htmlspecialchars($row['car_license']) ?></td>
    <td><?= htmlspecialchars($row['engine_number']) ?></td>
    <td><?= htmlspecialchars($row['mechanic_name']) ?></td>
    <td><?= $row['appointment_date'] ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td>
        <a href="edit_appointment.php?id=<?= $row['appointment_id'] ?>" class="edit-btn">Edit</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>