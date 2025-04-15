<?php
// process_appointment.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $car_license = trim($_POST['car_license'] ?? '');
    $car_engine = trim($_POST['car_engine'] ?? '');
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $mechanic_id = trim($_POST['mechanic'] ?? '');

    if (empty($client_name) || empty($address) || empty($phone) || empty($car_license) || empty($car_engine) || empty($appointment_date) || empty($mechanic_id)) {
        echo "<script>alert('All fields are required!'); window.location='appointment.php';</script>";
        exit;
    }

    try {
        // Check if client already has an appointment on this date
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE phone = ? AND appointment_date = ?");
        $stmt->execute([$phone, $appointment_date]);
        if ($stmt->fetchColumn() > 0) {
            echo "<script>alert('You already have an appointment on this date!'); window.location='appointment.php';</script>";
            exit;
        }

        // Check mechanic availability
        $stmt = $conn->prepare("SELECT COUNT(*) as count, m.max_slots, m.name 
                              FROM appointments a 
                              JOIN mechanics m ON a.mechanic_id = m.id 
                              WHERE a.mechanic_id = ? AND a.appointment_date = ?");
        $stmt->execute([$mechanic_id, $appointment_date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $current_count = $result['count'] ?? 0;
        $max_slots = $result['max_slots'] ?? 100;
        $mechanic_name = $result['name'] ?? 'Unknown';

        if ($current_count >= $max_slots) {
            echo "<script>alert('Mechanic $mechanic_name is fully booked for $appointment_date ($current_count/$max_slots slots used). Please select another mechanic or date.'); window.location='appointment.php';</script>";
            exit;
        }

        // Insert appointment
        $stmt = $conn->prepare("INSERT INTO appointments (client_name, address, phone, car_license, car_engine, appointment_date, mechanic_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$client_name, $address, $phone, $car_license, $car_engine, $appointment_date, $mechanic_id]);

        if ($success) {
            $stmt = $conn->prepare("SELECT id FROM appointments WHERE phone = ? AND appointment_date = ? AND mechanic_id = ?");
            $stmt->execute([$phone, $appointment_date, $mechanic_id]);
            $new_appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($new_appointment) {
                echo "<script>alert('Appointment booked successfully! ID: {$new_appointment['id']}'); window.location='appointment.php';</script>";
            } else {
                echo "<script>alert('Appointment booked but not found in database!'); window.location='appointment.php';</script>";
            }
        } else {
            echo "<script>alert('Failed to book appointment. No rows affected.'); window.location='appointment.php';</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Database error: " . htmlspecialchars($e->getMessage()) . "'); window.location='appointment.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.location='appointment.php';</script>";
}
?>