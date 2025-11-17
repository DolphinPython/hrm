<?php
include 'layouts/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($action === 'list') {
    // Return list of categories
    $categories = [];
    $result = $con->query("SELECT id, name FROM expense_categories ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $categories[] = ['id' => $row['id'], 'name' => htmlspecialchars($row['name'])];
    }
    echo json_encode(['categories' => $categories]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

if ($action === 'add') {
    $name = trim($_POST['category_name'] ?? '');

    if (empty($name)) {
        echo json_encode(["status" => "error", "message" => "Category name is required."]);
        exit;
    }

    // Check if exists
    $check = $con->prepare("SELECT id FROM expense_categories WHERE name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $con->prepare("INSERT INTO expense_categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Category added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding category: " . $con->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "exists", "message" => "Category already exists."]);
    }
    $check->close();
} elseif ($action === 'edit') {
    $id = intval($_POST['category_id'] ?? 0);
    $name = trim($_POST['category_name'] ?? '');

    if ($id <= 0 || empty($name)) {
        echo json_encode(["status" => "error", "message" => "Invalid category ID or name."]);
        exit;
    }

    // Check if name exists for another category
    $check = $con->prepare("SELECT id FROM expense_categories WHERE name = ? AND id != ?");
    $check->bind_param("si", $name, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $con->prepare("UPDATE expense_categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Category updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating category: " . $con->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "exists", "message" => "Category name already exists."]);
    }
    $check->close();
} elseif ($action === 'delete') {
    $id = intval($_POST['category_id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid category ID."]);
        exit;
    }

    // Check if category is used in expenses
    $check = $con->prepare("SELECT id FROM employee_expenses WHERE category_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Cannot delete category; it is used in expenses."]);
        $check->close();
        exit;
    }
    $check->close();

    $stmt = $con->prepare("DELETE FROM expense_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Category deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error deleting category: " . $con->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action."]);
}
?>