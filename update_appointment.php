<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['new_date'];
    $new_mechanic = $_POST['new_mechanic'];

    try {
        // Check mechanic availability for new date
        $stmt = $conn->prepare("SELECT COUNT(*) as count, m.max_slots 
                              FROM appointments a 
                              JOIN mechanics m ON a.mechanic_id = m.id 
                              WHERE a.mechanic_id = ? AND a.appointment_date = ? AND a.id != ?");
        $stmt->execute([$new_mechanic, $new_date, $appointment_id]);
        $result = $stmt->fetch();
        
        if ($result['count'] >= $result['max_slots']) {
            echo "<script>alert('Selected mechanic is fully booked for this date!'); window.location='admin.php';</script>";
            exit;
        }

        // Update appointment
        $stmt = $conn->prepare("UPDATE appointments 
                              SET appointment_date = ?, mechanic_id = ? 
                              WHERE id = ?");
        $stmt->execute([$new_date, $new_mechanic, $appointment_id]);

        echo "<script>alert('Appointment updated successfully!'); window.location='admin.php';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location='admin.php';</script>";
    }
}
?>