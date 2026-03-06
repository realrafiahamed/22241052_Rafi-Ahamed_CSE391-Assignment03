<?php
header('Content-Type: application/json');

if (!isset($_GET['mechanic_id']) || !isset($_GET['date'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;}

$mechanic_id = intval($_GET['mechanic_id']);
$date = $_GET['date'];

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['error' => 'Invalid date format']);
    exit;}

if ($_SERVER['SERVER_NAME'] == "localhost") {
    $conn = new mysqli("localhost", "root", "", "workshop");} 
else {
    $conn = new mysqli("sql102.infinityfree.com", "if0_41287606", "4eJsK5fyivU3J6", "if0_41287606_workshop");}
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;}

$stmt = $conn->prepare("
    SELECT COALESCE(o.max_slots, m.max_slots) AS effective_max
    FROM mechanics m
    LEFT JOIN mechanic_day_overrides o 
        ON o.mechanic_id = m.id AND o.override_date = ?
    WHERE m.id = ?");

$stmt->bind_param("si", $date, $mechanic_id);
$stmt->execute();
$stmt->bind_result($max);
$stmt->fetch();
$stmt->close();

if (!$max) {
    echo json_encode(['error' => 'Mechanic not found']);
    exit;}

$stmt2 = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id = ? AND appointment_date = ?");
$stmt2->bind_param("is", $mechanic_id, $date);
$stmt2->execute();
$stmt2->bind_result($booked);
$stmt2->fetch();
$stmt2->close();
$conn->close();

echo json_encode([
    'booked'    => (int)$booked, 'max'       => (int)$max, 'available' => max(0, (int)$max - (int)$booked)]);
?>