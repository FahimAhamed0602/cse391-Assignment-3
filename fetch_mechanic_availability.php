<?php
// fetch_mechanic_availability.php
header('Content-Type: application/json');

include 'config.php';

try {
    $date = $_GET['date'] ?? '';
    $initial = isset($_GET['initial']);

    if ($initial) {
        $stmt = $conn->query("SELECT id, name, max_slots FROM mechanics");
        $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $available = array_map(function($m) {
            return [
                'id' => $m['id'],
                'name' => $m['name'],
                'slots' => $m['max_slots']
            ];
        }, $mechanics);
        echo json_encode(['error' => null, 'available' => $available]);
        exit;
    }

    if (empty($date)) {
        echo json_encode(['error' => 'No date provided', 'available' => []]);
        exit;
    }

    $stmt = $conn->query("SELECT id, name, max_slots FROM mechanics");
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $available = [];
    foreach ($mechanics as $mechanic) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count 
                              FROM appointments 
                              WHERE mechanic_id = ? AND appointment_date = ?");
        $stmt->execute([$mechanic['id'], $date]);
        $count = $stmt->fetchColumn();

        $remaining_slots = $mechanic['max_slots'] - $count;
        if ($remaining_slots > 0) {
            $available[] = [
                'id' => $mechanic['id'],
                'name' => $m['name'],
                'slots' => $remaining_slots
            ];
        }
    }

    echo json_encode(['error' => null, 'available' => $available]);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . htmlspecialchars($e->getMessage()), 'available' => []]);
}
?>