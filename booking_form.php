<?php
$selectedMechanic = "";
$isFixed = false;
if (isset($_GET['mechanic']) && !empty($_GET['mechanic'])) {
    $selectedMechanic = urldecode($_GET['mechanic']);
    $isFixed = true;
}

if ($_SERVER['SERVER_NAME'] == "localhost") {
    $conn = new mysqli("localhost", "root", "", "workshop");} 
else {
    $conn = new mysqli("sql102.infinityfree.com", "if0_41287606", "4eJsK5fyivU3J6", "if0_41287606_workshop");}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);}

$mechanics = $conn->query("SELECT id, name, max_slots FROM mechanics");?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 400px;
        margin: 100px auto;
        padding: 40px;
        background-color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 12px;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #007BFF;
    }
    form label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }
    form input, form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 16px;
        box-sizing: border-box;
    }
    form button {
        width: 100%;
        padding: 15px;
        font-size: 18px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }
    form button:hover {
        background-color: #1e7e34;
    }
    form button:disabled {
        background-color: #999;
        cursor: not-allowed;
    }
    #slotMessage {
        color: red;
        font-weight: bold;
        display: none;
        margin-bottom: 10px;
    }
    .slot-hint {
        font-size: 13px;
        color: #888;
        margin-top: -15px;
        margin-bottom: 15px;
    }
</style>
</head>
<body>

<div class="container">
<h2>Book Appointment</h2>
<form action="submit_appointment.php" method="POST">
    <label for="client_name">Full Name</label>
    <input type="text" id="client_name" name="client_name" required>

    <label for="address">Address</label>
    <input type="text" id="address" name="address" required>

    <label for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" required>

    <label for="car_license">Car License Number</label>
    <input type="text" id="car_license" name="car_license" required>

    <label for="engine_number">Engine Number</label>
    <input type="text" id="engine_number" name="engine_number" required>

    <label for="appointment_date">Appointment Date</label>
    <input type="date" id="dateInput" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>

    <label>Select Mechanic</label><br>
    <p class="slot-hint">Pick a date first to see available slots.</p>
  
    <select name="mechanic" id="mechanicSelect" required <?php if ($isFixed) echo 'disabled'; ?>>
        <option value="">-- Select Mechanic --</option>
        <?php while ($row = $mechanics->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($row['name']); ?>"
                    data-id="<?php echo $row['id']; ?>"
                    data-max="<?php echo $row['max_slots']; ?>"
                    <?php if ($isFixed && $row['name'] == $selectedMechanic) echo 'selected'; ?>>
                <?php echo htmlspecialchars($row['name']); ?> (max: <?php echo $row['max_slots']; ?>)
            </option>
        <?php endwhile; ?>
    </select>

    <?php if ($isFixed): ?>
        <input type="hidden" name="mechanic" value="<?php echo htmlspecialchars($selectedMechanic); ?>">
    <?php endif; ?>

    <p id="slotMessage"></p>
    <button type="submit" id="submitBtn">Book Now</button>
</form>
</div>

<script>
const mechanicSelect = document.getElementById('mechanicSelect');
const dateInput      = document.getElementById('dateInput');
const slotMessage    = document.getElementById('slotMessage');
const submitBtn      = document.getElementById('submitBtn');
let slotData = {};

function updateSlots() {
    const date = dateInput.value;

    if (!date) {
        Array.from(mechanicSelect.options).forEach(option => {
            if (!option.dataset.id) return;
            const max = option.dataset.max;
            option.text = `${option.value} (max: ${max})`;
            option.disabled = false;
        });
        slotMessage.style.display = 'none';
        submitBtn.disabled = false;
        return;}

    slotData = {};
    let pending = 0;
    Array.from(mechanicSelect.options).forEach(option => {
        if (!option.dataset.id) return;
        pending++;
        const mechId = option.dataset.id;
        fetch(`check_slots.php?mechanic_id=${mechId}&date=${encodeURIComponent(date)}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) return;
                option.text = `${option.value.split('(')[0].trim()} (${data.booked}/${data.max} booked)`;
                option.disabled = (data.booked >= data.max);
                slotData[mechId] = data;
                pending--;
                if (pending === 0) validateSelection();
            })
            .catch(() => { pending--; });
    });
}

function validateSelection() {
    const selectedOption = mechanicSelect.options[mechanicSelect.selectedIndex];
    if (!selectedOption || !selectedOption.dataset.id) {
        slotMessage.style.display = 'none';
        submitBtn.disabled = false;
        return;}

    const mechId = selectedOption.dataset.id;
    const data   = slotData[mechId];

    if (data && data.booked >= data.max) {
        slotMessage.textContent  = "This mechanic is fully booked on this date. Please choose another.";
        slotMessage.style.display = "block";
        submitBtn.disabled = true;
    } else {
        slotMessage.style.display = "none";
        submitBtn.disabled = false;
    }
}
dateInput.addEventListener('change', updateSlots);
mechanicSelect.addEventListener('change', validateSelection);
</script>
</body>
</html>