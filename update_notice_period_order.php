<?php
include 'layouts/session.php';
include 'layouts/config.php';
$conn = $con;

if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['admin', 'hr'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order']) && is_array($_POST['order'])) {
    $order = array_map('intval', $_POST['order']);
    $response = ['status' => 'success', 'message' => 'Order updated successfully'];

    try {
        $valid_steps = [];
        $stmt = $conn->prepare("SELECT step_id FROM notice_period_steps");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $valid_steps[] = $row['step_id'];
        }

        if (empty($order)) {
            throw new Exception('Order array is empty');
        }
        $unique_order = array_unique($order);
        if (count($unique_order) !== count($order)) {
            throw new Exception('Duplicate step IDs detected');
        }
        foreach ($order as $step_id) {
            if (!in_array($step_id, $valid_steps)) {
                throw new Exception("Invalid step ID: $step_id");
            }
        }
        if (count($order) !== count($valid_steps)) {
            throw new Exception('Incomplete step list provided');
        }

        $conn->begin_transaction();
        $sql = "UPDATE notice_period_steps SET step_order = ? WHERE step_id = ?";
        $stmt = $conn->prepare($sql);
        foreach ($order as $index => $step_id) {
            $stmt->bind_param("ii", $index, $step_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update step order');
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $response = ['status' => 'error', 'message' => $e->getMessage()];
        http_response_code(400);
        error_log("Error in update_notice_period_order.php: " . $e->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>