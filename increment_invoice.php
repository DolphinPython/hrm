
<?php
include 'layouts/config.php'; // Ensure this includes your database connection

header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

try {
    // Check if database connection is valid
    if (!$con) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $current_invoice = isset($input['invoice_number']) ? intval($input['invoice_number']) : 0;

    if ($current_invoice < 0) {
        throw new Exception("Invalid invoice number: " . $current_invoice);
    }

    // Begin transaction
    if (!$con->begin_transaction()) {
        throw new Exception("Failed to start transaction: " . $con->error);
    }

    // Fetch the latest invoice number with locking
    $result = $con->query("SELECT invoice_number FROM invoice_numbers ORDER BY id DESC LIMIT 1 FOR UPDATE");
    if ($result === false) {
        throw new Exception("Failed to fetch invoice number: " . $con->error);
    }

    $new_invoice = $current_invoice;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_invoice = max($row['invoice_number'] + 1, $current_invoice + 1);
    } else {
        $new_invoice = max(1000, $current_invoice + 1);
    }

    // Insert new invoice number
    $stmt = $con->prepare("INSERT INTO invoice_numbers (invoice_number) VALUES (?)");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $con->error);
    }

    $stmt->bind_param("i", $new_invoice);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert invoice number: " . $stmt->error);
    }
    $stmt->close();

    // Commit transaction
    if (!$con->commit()) {
        throw new Exception("Failed to commit transaction: " . $con->error);
    }

    echo json_encode([
        'status' => 'success',
        'new_invoice_number' => $new_invoice,
        'message' => 'Invoice number incremented successfully'
    ]);
} catch (Exception $e) {
    $con->rollback();
    error_log("Error in increment_invoice.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$con->close();
?>
