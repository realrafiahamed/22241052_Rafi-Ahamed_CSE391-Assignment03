<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
$message= ""; $msgType = ""; $selected_date = $_GET['date'] ?? date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mechanic_id= intval($_POST['mechanic_id']);
    $override_date = $_POST['override_date'];
    $new_max= intval($_POST['max_slots']);

    if ($new_max < 1 || $new_max > 20) {
        $message = "Slots must be between 1 and 20.";
        $msgType = "error";
    } else {
        $check = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id=? AND appointment_date=?");
        $check->bind_param("is", $mechanic_id, $override_date);
        $check->execute();
        $check->bind_result($booked);
        $check->fetch();
        $check->close();

        if ($new_max < $booked) {
            $message = "Cannot set to $new_max — already $booked bookings exist on this date.";
            $msgType = "error";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO mechanic_day_overrides (mechanic_id, override_date, max_slots)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE max_slots = ?
            ");
            $stmt->bind_param("isii", $mechanic_id, $override_date, $new_max, $new_max);
            $stmt->execute();
            $stmt->close();
            $message = "Slot limit set to $new_max for " . date('d M Y', strtotime($override_date)) . ".";
            $msgType = "success";
        }
    }
    $selected_date = $_POST['override_date'];}

$mechanics_result = $conn->prepare("
    SELECT m.id, m.name, m.max_slots AS default_max, COALESCE(o.max_slots, m.max_slots) AS effective_max, (SELECT COUNT(*) FROM appointments a WHERE a.mechanic_id = m.id AND a.appointment_date = ?) AS booked_today
    FROM mechanics m LEFT JOIN mechanic_day_overrides o ON o.mechanic_id = m.id AND o.override_date = ? ORDER BY m.id");

$mechanics_result->bind_param("ss", $selected_date, $selected_date);
$mechanics_result->execute();
$mechanics = $mechanics_result->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Slots</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 30px 40px; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    h2 { margin: 0; color: #333; }
    .back-btn { padding: 8px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; }
    .back-btn:hover { background: #545b62; }
    .date-picker-bar {
        background: white; padding: 18px 25px; border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex;
        align-items: center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap;
    }
    .date-picker-bar label { font-weight: bold; font-size: 15px; color: #333; }
    .date-picker-bar input[type="date"] { padding: 9px 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; }
    .date-picker-bar button { padding: 9px 20px; background: #007BFF; color: white; border: none; border-radius: 6px; font-size: 15px; cursor: pointer; font-weight: bold; }
    .date-picker-bar button:hover { background: #0056b3; }
    .message { padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
    .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .message.error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.09); border-top: 4px solid #007BFF; }
    .card.overridden { border-top-color: #fd7e14; }
    .card.full { border-top-color: #dc3545; }
    .card h3 { margin: 0 0 8px 0; font-size: 15px; color: #333; }
    .card .date-label { font-size: 13px; color: #888; margin-bottom: 10px; }
    .card .date-label strong { color: #007BFF; }
    .booking-bar-wrap { margin-bottom: 14px; }
    .booking-bar-label { display: flex; justify-content: space-between; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #555; }
    .booking-bar { height: 10px; background: #e9ecef; border-radius: 5px; overflow: hidden; }
    .booking-bar-fill { height: 100%; border-radius: 5px; background: #28a745; }
    .booking-bar-fill.warn { background: #ffc107; }
    .booking-bar-fill.full { background: #dc3545; }
    .badge { font-size: 11px; border-radius: 4px; padding: 2px 7px; margin-bottom: 12px; display: inline-block; }
    .badge.override { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    .badge.default  { background: #e2e3e5; color: #383d41; }
    .slot-form { display: flex; gap: 8px; align-items: center; margin-top: 10px; }
    .slot-form input[type="number"] { width: 65px; padding: 7px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; text-align: center; }
    .slot-form button { padding: 7px 14px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: bold; }
    .slot-form button:hover { background: #0056b3; }
    .slot-form label { font-size: 13px; font-weight: bold; color: #555; }
</style>
</head>
<body>

<div class="top-bar">
    <h2>Manage Mechanic Slots</h2>
    <a href="admin_dashboard.php" class="back-btn"> Dashboard</a>
</div>

<form method="GET" class="date-picker-bar">
    <label>Select Date:</label>
    <input type="date" name="date" value="<?= htmlspecialchars($selected_date) ?>" min="<?= date('Y-m-d') ?>">
    <button type="submit">View</button>
</form>

<?php if ($message): ?>
    <div class="message <?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="cards-grid">
<?php while ($m = $mechanics->fetch_assoc()):
    $booked= $m['booked_today'];
    $effective= $m['effective_max'];
    $default= $m['default_max'];
    $isOverride = ($effective != $default);
    $pct = $effective > 0 ? min(100, round(($booked / $effective) * 100)) : 100;
    $fillClass = $pct >= 100 ? 'full' : ($pct >= 70 ? 'warn' : '');
    $cardClass = $pct >= 100 ? 'full' : ($isOverride ? 'overridden' : '');
?>
    <div class="card <?= $cardClass ?>">
        <h3><?= htmlspecialchars($m['name']) ?></h3>
        <div class="date-label">
            Slots for <strong><?= date('d M Y', strtotime($selected_date)) ?></strong>
        </div>

        <?php if ($isOverride): ?>
            <span class="badge override">⚠ Override active (default: <?= $default ?>)</span>
        <?php else: ?>
            <span class="badge default">Default (<?= $default ?> slots)</span>
        <?php endif; ?>

        <div class="booking-bar-wrap">
            <div class="booking-bar-label">
                <span>Booked: <?= $booked ?> / <?= $effective ?></span>
                <span><?= $pct ?>%</span>
            </div>
            <div class="booking-bar">
                <div class="booking-bar-fill <?= $fillClass ?>" style="width:<?= $pct ?>%"></div>
            </div>
        </div>

        <form method="POST" class="slot-form">
            <input type="hidden" name="mechanic_id"   value="<?= $m['id'] ?>">
            <input type="hidden" name="override_date" value="<?= htmlspecialchars($selected_date) ?>">
            <label>Set max:</label>
            <input type="number" name="max_slots" value="<?= $effective ?>" min="1" max="20" required>
            <button type="submit">Save</button>
        </form>
    </div>
<?php endwhile; ?>
</div>
</body>
</html>