<?php
include 'layouts/config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
        throw new Exception('Invalid request method.');
    }

    if (isset($_GET['action']) && $_GET['action'] === 'list') {
        $result = $con->query("SELECT id, name FROM companiesexpense ORDER BY name");
        $companies = [];
        while ($row = $result->fetch_assoc()) {
            $companies[] = $row;
        }
        echo json_encode(['status' => 'success', 'companies' => $companies]);
        exit;
    }

    if (!isset($_POST['action'])) {
        throw new Exception('No action specified.');
    }

    $action = $_POST['action'];

    if ($action === 'add') {
        $name = trim($_POST['category_name'] ?? '');
        if (empty($name)) {
            throw new Exception('Company name is required.');
        }

        $stmt = $con->prepare("SELECT id FROM companiesexpense WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'exists', 'message' => 'Company already exists.']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        $stmt = $con->prepare("INSERT INTO companiesexpense (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Company added successfully!']);
        } else {
            throw new Exception('Failed to add company: ' . $con->error);
        }
        $stmt->close();
    } elseif ($action === 'edit') {
        $id = intval($_POST['category_id'] ?? 0);
        $name = trim($_POST['category_name'] ?? '');
        if ($id <= 0 || empty($name)) {
            throw new Exception('Invalid company ID or name.');
        }

        $stmt = $con->prepare("SELECT id FROM companiesexpense WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'exists', 'message' => 'Company name already exists.']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        $stmt = $con->prepare("UPDATE companiesexpense SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Company updated successfully!']);
        } else {
            throw new Exception('Failed to update company: ' . $con->error);
        }
        $stmt->close();
    } elseif ($action === 'delete') {
        $id = intval($_POST['category_id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('Invalid company ID.');
        }

        $stmt = $con->prepare("SELECT COUNT(*) as count FROM employee_expenses WHERE company_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete company as it is associated with expenses.']);
            exit;
        }

        $stmt = $con->prepare("DELETE FROM companiesexpense WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Company deleted successfully!']);
        } else {
            throw new Exception('Failed to delete company: ' . $con->error);
        }
        $stmt->close();
    } else {
        throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$con->close();
?>