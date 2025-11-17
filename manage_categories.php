<?php
header('Content-Type: application/json');
include 'layouts/config.php';

$response = ['status' => 'error', 'message' => 'Invalid action'];

if (isset($_GET['action']) && $_GET['action'] === 'list') {
    $categories = [];
    $result = $con->query("SELECT id, name FROM expense_categories ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    $response = ['status' => 'success', 'categories' => $categories];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $category_name = trim($_POST['category_name'] ?? '');
        if (empty($category_name)) {
            $response = ['status' => 'error', 'message' => 'Category name is required'];
        } else {
            // Check if category already exists
            $stmt = $con->prepare("SELECT id FROM expense_categories WHERE name = ?");
            $stmt->bind_param("s", $category_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $response = ['status' => 'exists', 'message' => 'Category already exists'];
            } else {
                $stmt = $con->prepare("INSERT INTO expense_categories (name) VALUES (?)");
                $stmt->bind_param("s", $category_name);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Category added successfully'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Failed to add category'];
                }
            }
            $stmt->close();
        }
    } elseif ($action === 'edit') {
        $category_id = intval($_POST['category_id'] ?? 0);
        $category_name = trim($_POST['category_name'] ?? '');
        if ($category_id <= 0 || empty($category_name)) {
            $response = ['status' => 'error', 'message' => 'Invalid category ID or name'];
        } else {
            // Check if category name already exists for another ID
            $stmt = $con->prepare("SELECT id FROM expense_categories WHERE name = ? AND id != ?");
            $stmt->bind_param("si", $category_name, $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $response = ['status' => 'exists', 'message' => 'Category name already exists'];
            } else {
                $stmt = $con->prepare("UPDATE expense_categories SET name = ? WHERE id = ?");
                $stmt->bind_param("si", $category_name, $category_id);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Category updated successfully'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Failed to update category'];
                }
            }
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        $category_id = intval($_POST['category_id'] ?? 0);
        if ($category_id <= 0) {
            $response = ['status' => 'error', 'message' => 'Invalid category ID'];
        } else {
            $stmt = $con->prepare("DELETE FROM expense_categories WHERE id = ?");
            $stmt->bind_param("i", $category_id);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Category deleted successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to delete category'];
            }
            $stmt->close();
        }
    }
}

echo json_encode($response);
?>