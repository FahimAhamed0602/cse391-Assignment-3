<?php
// fetch_appointments.php
header('Content-Type: application/json');

include 'config.php';

try {
    // Fetch appointments
    $stmt = $conn->query("SELECT a.*, m.name as mechanic_name 
                        FROM appointments a 
                        JOIN mechanics m ON a.mechanic_id = m.id 
                        ORDER BY a.appointment_date");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch mechanics for update form
    $stmt = $conn->query("SELECT id, name FROM mechanics");
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'appointments' => $appointments,
        'mechanics' => $mechanics,
        'error' => null
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'appointments' => [],
        'mechanics' => [],
        'error' => 'Database error: ' . htmlspecialchars($e->getMessage())
    ]);
}
?>