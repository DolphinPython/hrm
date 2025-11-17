<?php
// chat_page.php
include 'layouts/session.php';
include 'include/function.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = connect();
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}
if (!isset($_SESSION['id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Session not initialized']));
}
$emp_id = $_SESSION['id'];

if (isset($_REQUEST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_REQUEST['action']) {
            case 'send_message':
                $type = $_POST['type'];
                $id = $_POST['id'];
                $message = $_POST['message'];
                $file_path = '';
                $reply_to = isset($_POST['reply_to']) ? (int)$_POST['reply_to'] : null;

                if (!empty($_FILES['file'])) {
                    $file = $_FILES['file'];
                    $allowed_types = ['jpg', 'png', 'gif', 'jpeg', 'mp3', 'wav', 'ogg', 'mp4', 'mov', 'avi'];
                    $max_size = 10 * 1024 * 1024;
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    
                    if (!in_array($ext, $allowed_types) || $file['size'] > $max_size) {
                        throw new Exception('Invalid file type or size exceeded (max 10MB)');
                    }

                    $upload_dir = 'chat_uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $file_name = time() . '_' . basename($file['name']);
                    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
                        throw new Exception('Failed to upload file');
                    }
                    $file_path = $upload_dir . $file_name;
                }

                $message_type = 'text';
                if ($file_path) {
                    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                    $types = ['image' => ['jpg', 'png', 'gif', 'jpeg'], 'audio' => ['mp3', 'wav', 'ogg'], 'video' => ['mp4', 'mov', 'avi']];
                    foreach ($types as $type => $exts) {
                        if (in_array($ext, $exts)) {
                            $message_type = $type;
                            break;
                        }
                    }
                }

                $stmt = $conn->prepare("INSERT INTO hrm_chat_messages 
                    (sender_id, receiver_id, group_id, message, message_type, file_path, reply_to, status, seen, is_deleted, is_edited)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'sent', 0, 0, 0)");
                $receiver_id = $type === 'user' ? $id : null;
                $group_id = $type === 'group' ? $id : null;
                $stmt->bind_param("iiisssi", $emp_id, $receiver_id, $group_id, $message, $message_type, $file_path, $reply_to);
                $stmt->execute();

                echo json_encode(['status' => 'success']);
                exit;

            case 'get_messages':
                $type = $_GET['type'];
                $id = $_GET['id'];
                $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
                $limit = 50;
                $messages = [];

                if ($type === 'user') {
                    $stmt = $conn->prepare("SELECT m.*, e.fname, e.lname, e.image,
                        r.message as reply_message, r.sender_id as reply_sender_id, re.fname as reply_fname, re.lname as reply_lname
                        FROM hrm_chat_messages m
                        JOIN hrm_employee e ON m.sender_id = e.id
                        LEFT JOIN hrm_chat_messages r ON m.reply_to = r.message_id
                        LEFT JOIN hrm_employee re ON r.sender_id = re.id
                        WHERE (m.receiver_id = ? AND m.sender_id = ?) OR (m.receiver_id = ? AND m.sender_id = ?)
                        ORDER BY m.timestamp ASC LIMIT ? OFFSET ?");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param("iiiiii", $id, $emp_id, $emp_id, $id, $limit, $offset);
                } else {
                    $stmt = $conn->prepare("SELECT m.*, e.fname, e.lname, e.image,
                        r.message as reply_message, r.sender_id as reply_sender_id, re.fname as reply_fname, re.lname as reply_lname
                        FROM hrm_chat_messages m
                        JOIN hrm_employee e ON m.sender_id = e.id
                        LEFT JOIN hrm_chat_messages r ON m.reply_to = r.message_id
                        LEFT JOIN hrm_employee re ON r.sender_id = re.id
                        WHERE m.group_id = ?
                        ORDER BY m.timestamp ASC LIMIT ? OFFSET ?");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param("iii", $id, $limit, $offset);
                }

                if (!$stmt->execute()) {
                    throw new Exception("Query execution failed: " . $stmt->error);
                }
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $row['is_me'] = $row['sender_id'] == $emp_id;
                    $messages[] = $row;
                }

                if ($type === 'user') {
                    $stmt = $conn->prepare("UPDATE hrm_chat_messages 
                        SET seen = 1 
                        WHERE receiver_id = ? AND sender_id = ? AND seen = 0");
                    $stmt->bind_param("ii", $emp_id, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE hrm_chat_messages 
                        SET seen = 1 
                        WHERE group_id = ? AND sender_id != ? AND seen = 0");
                    $stmt->bind_param("ii", $id, $emp_id);
                }
                $stmt->execute();

                $stmt = $conn->prepare("SELECT COUNT(*) as unread_count 
                    FROM hrm_chat_messages 
                    WHERE (receiver_id = ? OR group_id IN (SELECT group_id FROM hrm_chat_group_members WHERE user_id = ?)) 
                    AND sender_id != ? AND seen = 0");
                $stmt->bind_param("iii", $emp_id, $emp_id, $emp_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $unread_count = $result->fetch_assoc()['unread_count'];

                echo json_encode([
                    'messages' => $messages,
                    'unread_count' => $unread_count
                ]);
                exit;

            case 'get_group_members':
                $group_id = $_GET['group_id'];
                $stmt = $conn->prepare("SELECT e.id, e.fname, e.lname 
                    FROM hrm_chat_group_members m 
                    JOIN hrm_employee e ON m.user_id = e.id 
                    WHERE m.group_id = ?");
                $stmt->bind_param("i", $group_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $members = [];
                while ($row = $result->fetch_assoc()) {
                    $members[] = $row;
                }
                echo json_encode($members);
                exit;

            case 'get_contacts':
                $contacts = [];
                
                $stmt = $conn->prepare("SELECT e.id, e.fname, e.lname, 
                    (SELECT COUNT(*) FROM hrm_chat_messages m WHERE m.receiver_id = ? AND m.sender_id = e.id AND m.seen = 0) as unread_count,
                    (SELECT MAX(timestamp) FROM hrm_chat_messages m 
                     WHERE (m.receiver_id = ? AND m.sender_id = e.id) OR (m.receiver_id = e.id AND m.sender_id = ?)) as last_message_time
                    FROM hrm_employee e WHERE e.id != ?");
                $stmt->bind_param("iiii", $emp_id, $emp_id, $emp_id, $emp_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $contacts[] = [
                        'type' => 'user',
                        'id' => $row['id'],
                        'name' => $row['fname'] . ' ' . $row['lname'],
                        'is_creator' => false,
                        'unread_count' => $row['unread_count'],
                        'last_message_time' => $row['last_message_time'] ?: '1970-01-01 00:00:00'
                    ];
                }

                $stmt = $conn->prepare("SELECT g.group_id, g.group_name, g.created_by,
                    (SELECT COUNT(*) FROM hrm_chat_messages m WHERE m.group_id = g.group_id AND m.sender_id != ? AND m.seen = 0) as unread_count,
                    (SELECT MAX(timestamp) FROM hrm_chat_messages m WHERE m.group_id = g.group_id) as last_message_time
                    FROM hrm_chat_groups g
                    JOIN hrm_chat_group_members m ON g.group_id = m.group_id
                    WHERE m.user_id = ?");
                $stmt->bind_param("ii", $emp_id, $emp_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $contacts[] = [
                        'type' => 'group',
                        'id' => $row['group_id'],
                        'name' => $row['group_name'],
                        'is_creator' => $row['created_by'] == $emp_id,
                        'unread_count' => $row['unread_count'],
                        'last_message_time' => $row['last_message_time'] ?: '1970-01-01 00:00:00'
                    ];
                }

                echo json_encode($contacts);
                exit;

            case 'create_group':
                $groupName = $_POST['group_name'];
                $members = $_POST['members'] ?? [];
                
                if (empty($groupName)) {
                    throw new Exception('Group name is required');
                }

                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("INSERT INTO hrm_chat_groups (group_name, created_by) VALUES (?, ?)");
                    $stmt->bind_param("si", $groupName, $emp_id);
                    $stmt->execute();
                    $groupId = $conn->insert_id;

                    $stmt = $conn->prepare("INSERT INTO hrm_chat_group_members (group_id, user_id) VALUES (?, ?)");
                    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM hrm_employee WHERE id = ?");
                    $stmt->bind_param("ii", $groupId, $emp_id);
                    $stmt->execute();

                    foreach ($members as $memberId) {
                        $checkStmt->bind_param("i", $memberId);
                        $checkStmt->execute();
                        $checkResult = $checkStmt->get_result();
                        if ($checkResult->fetch_row()[0] > 0) {
                            $stmt->bind_param("ii", $groupId, $memberId);
                            $stmt->execute();
                        }
                    }

                    $conn->commit();
                    echo json_encode(['status' => 'success', 'group_id' => $groupId]);
                } catch (Exception $e) {
                    $conn->rollback();
                    throw $e;
                }
                exit;

            case 'add_group_members':
                $groupId = $_POST['group_id'];
                $newMembers = $_POST['members'] ?? [];

                if (empty($newMembers)) {
                    throw new Exception('No members selected to add');
                }

                $stmt = $conn->prepare("SELECT created_by FROM hrm_chat_groups WHERE group_id = ?");
                $stmt->bind_param("i", $groupId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if (!$row || $row['created_by'] != $emp_id) {
                    throw new Exception('Only the group creator can add members');
                }

                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("INSERT INTO hrm_chat_group_members (group_id, user_id) VALUES (?, ?)");
                    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM hrm_employee WHERE id = ?");
                    foreach ($newMembers as $memberId) {
                        $checkStmt->bind_param("i", $memberId);
                        $checkStmt->execute();
                        $checkResult = $checkStmt->get_result();
                        $count = $checkResult->fetch_row()[0];
                        
                        if ($count > 0) {
                            $stmt->bind_param("ii", $groupId, $memberId);
                            $stmt->execute();
                        }
                    }

                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                } catch (Exception $e) {
                    $conn->rollback();
                    throw $e;
                }
                exit;

            case 'remove_group_member':
                $groupId = $_POST['group_id'];
                $userId = $_POST['user_id'];

                $stmt = $conn->prepare("SELECT created_by FROM hrm_chat_groups WHERE group_id = ?");
                $stmt->bind_param("i", $groupId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if (!$row || $row['created_by'] != $emp_id) {
                    throw new Exception('Only the group creator can remove members');
                }

                if ($userId == $emp_id) {
                    throw new Exception('You cannot remove yourself from the group');
                }

                $stmt = $conn->prepare("DELETE FROM hrm_chat_group_members WHERE group_id = ? AND user_id = ?");
                $stmt->bind_param("ii", $groupId, $userId);
                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception('User is not a member of this group');
                }

                echo json_encode(['status' => 'success']);
                exit;

            case 'clear_chat':
                $type = $_POST['type'];
                $id = $_POST['id'];

                if ($type === 'user') {
                    $stmt = $conn->prepare("UPDATE hrm_chat_messages 
                        SET is_deleted = 1 
                        WHERE ((receiver_id = ? AND sender_id = ?) OR (receiver_id = ? AND sender_id = ?)) 
                        AND is_deleted = 0");
                    $stmt->bind_param("iiii", $id, $emp_id, $emp_id, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE hrm_chat_messages 
                        SET is_deleted = 1 
                        WHERE group_id = ? AND is_deleted = 0");
                    $stmt->bind_param("i", $id);
                }

                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception('No messages to clear');
                }

                echo json_encode(['status' => 'success']);
                exit;

            case 'exit_group':
                $groupId = $_POST['group_id'];

                $stmt = $conn->prepare("DELETE FROM hrm_chat_group_members WHERE group_id = ? AND user_id = ?");
                $stmt->bind_param("ii", $groupId, $emp_id);
                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception('You are not a member of this group');
                }

                echo json_encode(['status' => 'success']);
                exit;

            case 'delete_group':
                $groupId = $_POST['group_id'];

                $stmt = $conn->prepare("SELECT created_by FROM hrm_chat_groups WHERE group_id = ?");
                $stmt->bind_param("i", $groupId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if (!$row || $row['created_by'] != $emp_id) {
                    throw new Exception('Only the group creator can delete the group');
                }

                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("DELETE FROM hrm_chat_group_members WHERE group_id = ?");
                    $stmt->bind_param("i", $groupId);
                    $stmt->execute();

                    $stmt = $conn->prepare("DELETE FROM hrm_chat_groups WHERE group_id = ?");
                    $stmt->bind_param("i", $groupId);
                    $stmt->execute();

                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                } catch (Exception $e) {
                    $conn->rollback();
                    throw $e;
                }
                exit;

            case 'edit_group':
                $groupId = $_POST['group_id'];
                $newName = $_POST['new_name'];

                if (empty($newName)) {
                    throw new Exception('New group name is required');
                }

                $stmt = $conn->prepare("SELECT COUNT(*) FROM hrm_chat_group_members WHERE group_id = ? AND user_id = ?");
                $stmt->bind_param("ii", $groupId, $emp_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_row()[0];

                if ($count == 0) {
                    throw new Exception('You must be a group member to edit the name');
                }

                $stmt = $conn->prepare("UPDATE hrm_chat_groups SET group_name = ? WHERE group_id = ?");
                $stmt->bind_param("si", $newName, $groupId);
                $stmt->execute();

                echo json_encode(['status' => 'success']);
                exit;

            case 'delete_message':
                $messageId = $_POST['message_id'];

                $stmt = $conn->prepare("UPDATE hrm_chat_messages SET is_deleted = 1 WHERE message_id = ? AND sender_id = ?");
                $stmt->bind_param("ii", $messageId, $emp_id);
                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception('You can only delete your own messages');
                }

                echo json_encode(['status' => 'success']);
                exit;

            case 'edit_message':
                if (!isset($_POST['message_id']) || !isset($_POST['new_message'])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                    exit;
                }

                $messageId = (int)$_POST['message_id'];
                $newMessage = trim($_POST['new_message']);

                if (empty($newMessage)) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'New message content is required']);
                    exit;
                }
                if (!$messageId) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
                    exit;
                }

                $stmt = $conn->prepare("UPDATE hrm_chat_messages SET message = ?, is_edited = 1 WHERE message_id = ? AND sender_id = ?");
                if (!$stmt) {
                    throw new Exception('Prepare failed: ' . $conn->error);
                }

                $stmt->bind_param("sii", $newMessage, $messageId, $emp_id);
                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    http_response_code(403);
                    echo json_encode(['status' => 'error', 'message' => 'You can only edit your own messages or message not found']);
                    exit;
                }

                echo json_encode(['status' => 'success']);
                exit;
        }
    } catch (Exception $e) {
        error_log("Chat error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat Room</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .chat-container { 
            height: 100vh; 
            display: flex; 
            overflow: hidden; 
        }
        .chat-sidebar { 
            width: 300px; /* Fixed width for sidebar */
            background: #f8f9fa; 
            border-right: 1px solid #dee2e6; 
            overflow-y: auto; /* Scrollable */
            height: 100vh; /* Full height */
            flex-shrink: 0; /* Prevent shrinking */
        }
        .chat-sidebar .p-3 { 
            padding: 15px; 
        }
        .chat-right { 
            flex: 1; /* Take remaining space */
            display: flex; 
            flex-direction: column; 
            height: 100vh; /* Full height */
            position: fixed; /* Fixed position */
            right: 0; 
            width: calc(100% - 300px); /* Adjust width based on sidebar */
        }
        .chat-header { 
            background: #f8f9fa; 
            padding: 10px 20px; 
            border-bottom: 1px solid #dee2e6; 
            font-size: 1.2em; 
            font-weight: bold; 
            cursor: pointer; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-shrink: 0; /* Prevent header from shrinking */
        }
        .chat-header.group { color: #28a745; }
        .chat-header.user { color: #007bff; }
        .chat-area { 
            display: flex; 
            flex: 1; /* Fill remaining space */
            overflow: hidden; 
        }
        .chat-messages { 
            flex: 1; 
            overflow-y: auto; 
            padding: 20px; 
        }
        .group-members-sidebar { 
            width: 0; 
            background: #f8f9fa; 
            border-left: 1px solid #dee2e6; 
            overflow-y: auto; 
            transition: width 0.3s; 
            height: 100%; /* Match chat area height */
        }
        .group-members-sidebar.open { 
            width: 250px; 
        }
        .group-members-list { 
            padding: 10px; 
        }
        .group-member { 
            padding: 5px 0; 
            border-bottom: 1px solid #eee; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .remove-member-btn { 
            color: #dc3545; 
            cursor: pointer; 
        }
        .message { margin-bottom: 15px; display: flex; position: relative; }
        .message.me { justify-content: flex-end; }
        .message-content {
            max-width: 70%; padding: 10px 15px; border-radius: 15px; background: #f1f0f0; word-break: break-word; position: relative;
        }
        .message.me .message-content { background: #007bff; color: white; }
        .chat-input { 
            padding: 20px; 
            background: #fff; 
            border-top: 1px solid #dee2e6; 
            flex-shrink: 0; /* Prevent input from shrinking */
        }
        .chat-media { max-width: 300px; max-height: 200px; border-radius: 10px; }
        .cursor-pointer { cursor: pointer; }
        #messageInput:focus { outline: none; border-color: #007bff; }
        .group-actions { float: right; }
        .dropdown-toggle::after { display: none; }
        .dropdown-menu { min-width: 100px; }
        .three-dots { font-size: 20px; padding: 0 10px; }
        .unread-name { color: #28a745; font-weight: bold; }
        .unread-badge { margin-left: 10px; }
        .message-actions { position: absolute; top: 5px; right: 5px; }
        .message.me .message-actions { left: 5px; right: auto; }
        .deleted-message { font-style: italic; color: #6c757d; }
        .edited-label { font-size: 0.8em; color: #6c757d; margin-left: 5px; }
        .text-end.small { font-size: 0.85em; color: #6c757d; }
        .message.me .text-end.small { color: #ffffff; }
        .mention { color: #007bff; font-weight: bold; }
        .reply-preview { 
            background: #e9ecef; 
            padding: 5px 10px; 
            border-radius: 10px; 
            margin-bottom: 5px; 
            font-size: 0.9em; 
            max-width: 100%; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
        .message.me .reply-preview { background: #cce5ff; }
        .mention-suggestions {
            position: absolute; 
            background: white; 
            border: 1px solid #dee2e6; 
            border-radius: 5px; 
            max-height: 200px; 
            overflow-y: auto; 
            z-index: 1000; 
            width: 200px; 
        }
        .mention-suggestion { 
            padding: 5px 10px; 
            cursor: pointer; 
        }
        .mention-suggestion:hover { 
            background: #f8f9fa; 
        }
        .file-download { 
            margin-top: 5px; 
            display: block; 
            color: #007bff; 
            text-decoration: none; 
        }
        .message.me .file-download { color: #ffffff; }
        .file-container { margin-top: 5px; }
    </style>
</head>
<body>

<div class="container-fluid chat-container">
    <div class="chat-sidebar">
        <div class="p-3">
            <button class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#groupModal">
                Create Group
            </button>
            <div id="contactsList"></div>
        </div>
    </div>

    <div class="chat-right">
        <div class="chat-header" id="chatHeader">
            <span id="chatTitle"></span>
            <button class="btn btn-outline-danger btn-sm" id="clearChatBtn" style="display:none;">Clear Chat</button>
        </div>
        <div class="chat-area">
            <div class="chat-messages" id="chatMessages"></div>
            <div class="group-members-sidebar" id="groupMembersSidebar">
                <div class="group-members-list" id="groupMembersList"></div>
            </div>
        </div>
        <div class="chat-input position-relative">
            <div id="mentionSuggestions" class="mention-suggestions d-none"></div>
            <div class="input-group">
                <input type="file" id="fileInput" class="d-none" accept=".jpg,.png,.gif,.jpeg,.mp3,.wav,.ogg,.mp4,.mov,.avi">
                <button class="btn btn-outline-secondary" type="button" onclick="$('#fileInput').click()">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="text" id="messageInput" class="form-control" placeholder="Type your message... Use @ to mention" autocomplete="off">
                <button class="btn btn-primary" id="sendButton" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalLabel">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="groupName" class="form-control mb-3" placeholder="Group name" autocomplete="off">
                <h6>Select Members:</h6>
                <div id="groupMembers">
                    <?php 
                    $stmt = $conn->prepare("SELECT id, fname, lname FROM hrm_employee WHERE id != ?");
                    $stmt->bind_param("i", $emp_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?= $row['id'] ?>" id="member<?= $row['id'] ?>">
                        <label class="form-check-label" for="member<?= $row['id'] ?>"><?= $row['fname'] ?> <?= $row['lname'] ?></label>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createGroupBtn">Create</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGroupModalLabel">Edit Group Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="editGroupName" class="form-control" placeholder="New group name" autocomplete="off">
                <input type="hidden" id="editGroupId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveGroupNameBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Members Modal -->
<div class="modal fade" id="addMembersModal" tabindex="-1" aria-labelledby="addMembersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMembersModalLabel">Add Members to Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Select Members to Add:</h6>
                <div id="addGroupMembers">
                    <?php 
                    $stmt = $conn->prepare("SELECT id, fname, lname FROM hrm_employee WHERE id != ?");
                    $stmt->bind_param("i", $emp_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?= $row['id'] ?>" id="addMember<?= $row['id'] ?>">
                        <label class="form-check-label" for="addMember<?= $row['id'] ?>"><?= $row['fname'] ?> <?= $row['lname'] ?></label>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addMembersBtn">Add Members</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Message Modal -->
<div class="modal fade" id="editMessageModal" tabindex="-1" aria-labelledby="editMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMessageModalLabel">Edit Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="editMessageText" class="form-control" autocomplete="off">
                <input type="hidden" id="editMessageId" name="editMessageId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveMessageBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentChat = null;
const pollInterval = 3000;
let pollTimer = null;
let replyingTo = null;
let groupMembers = [];
let isMembersSidebarOpen = false;
let lastMessageCount = 0;
let unreadCount = 0;
let isCreator = false;

$(document).ready(() => {
    loadContacts();
    setupEventHandlers();
});

function setupEventHandlers() {
    $('#sendButton').click(sendMessage);
    $('#messageInput').on('keypress', function(e) {
        if (e.which === 13) sendMessage();
    });
    $('#createGroupBtn').click(createGroup);
    $('#saveGroupNameBtn').click(saveGroupName);
    $('#saveMessageBtn').click(saveMessage);
    $('#addMembersBtn').click(addGroupMembers);
    $('#clearChatBtn').click(clearChat);

    $('#messageInput').on('input', handleMentionInput);
    $('#chatHeader').on('click', toggleGroupMembersSidebar);

    $('.modal').on('hidden.bs.modal', () => $('#messageInput').focus());
    $(window).on('unload', () => clearInterval(pollTimer));
}

function handleMentionInput() {
    const input = $('#messageInput').val();
    const cursorPos = $('#messageInput')[0].selectionStart;
    const textBeforeCursor = input.substring(0, cursorPos);
    const lastAt = textBeforeCursor.lastIndexOf('@');

    if (lastAt !== -1 && cursorPos > lastAt) {
        const searchTerm = textBeforeCursor.substring(lastAt + 1).toLowerCase();
        if (currentChat && currentChat.type === 'group') {
            if (!groupMembers.length) loadGroupMembers();
            const filtered = groupMembers.filter(m => 
                `${m.fname} ${m.lname}`.toLowerCase().includes(searchTerm)
            );
            
            if (filtered.length) {
                const inputOffset = $('#messageInput').offset();
                $('#mentionSuggestions')
                    .removeClass('d-none')
                    .css({
                        top: inputOffset.top - 100,
                        left: inputOffset.left
                    })
                    .html(filtered.map(m => `
                        <div class="mention-suggestion" onclick="selectMention('${m.fname} ${m.lname}')">
                            ${m.fname} ${m.lname}
                        </div>
                    `).join(''));
                return;
            }
        }
    }
    $('#mentionSuggestions').addClass('d-none');
}

function selectMention(name) {
    const input = $('#messageInput').val();
    const cursorPos = $('#messageInput')[0].selectionStart;
    const textBeforeCursor = input.substring(0, cursorPos);
    const lastAt = textBeforeCursor.lastIndexOf('@');
    const newText = input.substring(0, lastAt + 1) + name + input.substring(cursorPos);
    $('#messageInput').val(newText);
    $('#mentionSuggestions').addClass('d-none');
    $('#messageInput').focus();
}

function loadGroupMembers() {
    if (currentChat && currentChat.type === 'group') {
        $.get(`chat_page.php?action=get_group_members&group_id=${currentChat.id}`, members => {
            groupMembers = members;
            if (isMembersSidebarOpen) {
                showGroupMembers();
            }
        });
    }
}

function loadContacts() {
    $.get('chat_page.php?action=get_contacts', contacts => {
        contacts.sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time));
        $('#contactsList').html(contacts.map(contact => `
            <div class="card mb-2 cursor-pointer">
                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    <span onclick="openChat('${contact.type}', ${contact.id}, '${contact.name}', ${contact.is_creator})" 
                          class="${contact.unread_count > 0 ? 'unread-name' : ''}">
                        ${contact.type === 'group' ? '👥 ' : '👤 '}
                        ${contact.name}
                    </span>
                    <div class="group-actions dropdown">
                        ${contact.unread_count > 0 ? `
                            <span class="badge bg-danger unread-badge">${contact.unread_count}</span>
                        ` : ''}
                        ${contact.type === 'group' ? `
                            <button class="btn btn-link text-dark three-dots" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                ⋮
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" 
                                       onclick="showEditGroupModal(${contact.id}, '${contact.name.replace(/'/g, "\\'")}'); return false;">Edit</a></li>
                                <li><a class="dropdown-item text-warning" href="#" 
                                       onclick="exitGroup(${contact.id}); return false;">Exit</a></li>
                                ${contact.is_creator ? `
                                    <li><a class="dropdown-item" href="#" 
                                           onclick="showAddMembersModal(${contact.id}); return false;">Add Members</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" 
                                           onclick="deleteGroup(${contact.id}); return false;">Delete</a></li>
                                ` : ''}
                            </ul>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join(''));
    }).fail(() => alert('Failed to load contacts'));
}

function openChat(type, id, name, creatorStatus) {
    if (pollTimer) clearInterval(pollTimer);
    currentChat = { type, id };
    groupMembers = [];
    replyingTo = null;
    isCreator = creatorStatus && type === 'group';

    const header = $('#chatHeader');
    $('#chatTitle').text(name);
    header.removeClass('group user');
    header.addClass(type === 'group' ? 'group' : 'user');

    $('#chatMessages').html('<div class="text-center mt-3">Loading messages...</div>');
    const sidebar = $('#groupMembersSidebar');
    sidebar.removeClass('open');
    $('#groupMembersList').html('');
    isMembersSidebarOpen = false;
    header.off('click').on('click', type === 'group' ? toggleGroupMembersSidebar : null);

    $('#clearChatBtn').show();

    lastMessageCount = 0;
    loadMessages();
    pollTimer = setInterval(loadMessages, pollInterval);
}

function toggleGroupMembersSidebar() {
    if (currentChat && currentChat.type === 'group') {
        isMembersSidebarOpen = !isMembersSidebarOpen;
        const sidebar = $('#groupMembersSidebar');
        sidebar.toggleClass('open', isMembersSidebarOpen);
        if (isMembersSidebarOpen) {
            if (!groupMembers.length) loadGroupMembers();
            else showGroupMembers();
        } else {
            $('#groupMembersList').html('');
        }
    }
}

function showGroupMembers() {
    if (groupMembers.length) {
        $('#groupMembersList').html(`
            <h6>Members:</h6>
            ${groupMembers.map(member => `
                <div class="group-member">
                    ${member.fname} ${member.lname}
                    ${isCreator && member.id != <?=$emp_id?> ? `
                        <span class="remove-member-btn" onclick="removeGroupMember(${currentChat.id}, ${member.id})">
                            <i class="fas fa-times"></i>
                        </span>
                    ` : ''}
                </div>
            `).join('')}
        `);
    } else {
        $('#groupMembersList').html('<div class="text-center">No members found</div>');
    }
}

function removeGroupMember(groupId, userId) {
    if (!confirm('Are you sure you want to remove this member from the group?')) return;

    $.post('chat_page.php?action=remove_group_member', {
        group_id: groupId,
        user_id: userId
    }, response => {
        if (response.status === 'success') {
            loadGroupMembers();
            loadContacts();
        } else {
            alert('Error removing member: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while removing member'));
}

function clearChat() {
    if (!currentChat) return;
    if (!confirm('Are you sure you want to clear all messages in this chat?')) return;

    $.post('chat_page.php?action=clear_chat', {
        type: currentChat.type,
        id: currentChat.id
    }, response => {
        if (response.status === 'success') {
            loadMessages();
        } else {
            alert('Error clearing chat: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while clearing chat'));
}

function loadMessages() {
    if (!currentChat) return;
    
    $.get(`chat_page.php?action=get_messages&type=${currentChat.type}&id=${currentChat.id}`, response => {
        const messages = response.messages;
        unreadCount = response.unread_count;

        $('#chatMessages').html(messages.map(msg => {
            const senderName = msg.is_me ? 'You' : `${msg.fname} ${msg.lname}`;
            const messageContent = msg.is_deleted ? 'Sender deleted this message' : 
                (msg.message_type !== 'text' ? renderMedia(msg) : formatMessage(msg.message));
            return `
            <div class="message ${msg.is_me ? 'me' : ''}">
                <div class="message-content ${msg.is_deleted ? 'deleted-message' : ''}">
                    ${msg.reply_to ? `
                        <div class="reply-preview">
                            ${msg.reply_sender_id === <?=$emp_id?> ? 'You' : `${msg.reply_fname} ${msg.reply_lname}`}: 
                            ${msg.reply_message ? msg.reply_message.substring(0, 50) + (msg.reply_message.length > 50 ? '...' : '') : 'Message deleted'}
                        </div>
                    ` : ''}
                    ${msg.message && msg.message.trim() ? `<div>${messageContent}</div>` : ''}
                    ${msg.file_path ? `<div class="file-container">${renderMedia(msg)}</div>` : ''}
                    <div class="text-end small mt-1">
                        ${senderName} • ${new Date(msg.timestamp).toLocaleTimeString()}
                        ${msg.is_edited && !msg.is_deleted ? '<span class="edited-label">Edited</span>' : ''}
                    </div>
                    ${!msg.is_deleted ? `
                        <div class="message-actions dropdown">
                            <button class="btn btn-link text-dark" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                ⋮
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" 
                                       onclick="replyToMessage(${msg.message_id}); return false;">Reply</a></li>
                                ${msg.is_me ? `
                                    <li><a class="dropdown-item" href="#" 
                                           onclick="showEditMessageModal('${msg.message_id}', '${encodeURIComponent(msg.message)}'); return false;">Edit</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" 
                                           onclick="deleteMessage(${msg.message_id}); return false;">Delete</a></li>
                                ` : ''}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            </div>
            `;
        }).join(''));

        if (messages.length > lastMessageCount) {
            lastMessageCount = messages.length;
            updateNotification();
        } else if (unreadCount === 0) {
            document.title = 'Chat System';
        }

        scrollToBottom();
        loadContacts();
    }).fail((xhr, status, error) => {
        console.error('Error loading messages:', status, error, xhr.responseText);
        $('#chatMessages').html('<div class="text-center text-danger">Error loading messages</div>');
    });
}

function updateNotification() {
    if (unreadCount > 0) {
        document.title = `Chat System (${unreadCount})`;
        const url = new URL(window.location.href);
        url.searchParams.set('unread', unreadCount);
        window.history.replaceState({}, document.title, url);
    } else {
        document.title = 'Chat System';
        const url = new URL(window.location.href);
        url.searchParams.delete('unread');
        window.history.replaceState({}, document.title, url);
    }
}

function formatMessage(message) {
    return message.replace(/@(\w+\s\w+)/g, '<span class="mention">@$1</span>');
}

function renderMedia(msg) {
    const fileName = msg.file_path.split('/').pop();
    switch (msg.message_type) {
        case 'image':
            return `
                <img src="${msg.file_path}" class="chat-media" alt="Uploaded image">
                <a href="${msg.file_path}" download="${fileName}" class="file-download">Download ${fileName}</a>
            `;
        case 'video':
            return `
                <video controls class="chat-media"><source src="${msg.file_path}"></video>
                <a href="${msg.file_path}" download="${fileName}" class="file-download">Download ${fileName}</a>
            `;
        case 'audio':
            return `
                <audio controls><source src="${msg.file_path}"></audio>
                <a href="${msg.file_path}" download="${fileName}" class="file-download">Download ${fileName}</a>
            `;
        default:
            return `<a href="${msg.file_path}" download="${fileName}" class="file-download">Download ${fileName}</a>`;
    }
}

function replyToMessage(messageId) {
    replyingTo = messageId;
    $('#messageInput').focus();
    $('#messageInput').attr('placeholder', `Replying to message ${messageId}...`);
}

function sendMessage() {
    if (!currentChat) {
        $('#chatMessages').html('<div class="text-center text-warning">Please select a chat to start messaging</div>');
        return;
    }
    
    const message = $('#messageInput').val().trim();
    if (!message && !$('#fileInput')[0].files[0]) return;

    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('type', currentChat.type);
    formData.append('id', currentChat.id);
    formData.append('message', message);
    if (replyingTo) formData.append('reply_to', replyingTo);
    const file = $('#fileInput')[0].files[0];
    if (file) formData.append('file', file);

    $.ajax({
        url: 'chat_page.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: (response) => {
            if (response.status === 'success') {
                $('#messageInput').val('');
                $('#fileInput').val('');
                $('#messageInput').attr('placeholder', 'Type your message... Use @ to mention');
                replyingTo = null;
                loadMessages();
                loadContacts();
            } else {
                alert('Failed to send message: ' + (response.message || 'Unknown error'));
            }
        },
        error: (xhr, status, error) => {
            console.error('Error sending message:', status, error, xhr.responseText);
            alert('Network error occurred while sending message');
        }
    });
}

function createGroup() {
    const groupName = $('#groupName').val().trim();
    const members = Array.from($('#groupMembers input:checked')).map(el => el.value);
    
    if (!groupName) {
        alert('Please enter a group name');
        return;
    }

    $.post('chat_page.php?action=create_group', {
        group_name: groupName,
        members: members
    }, response => {
        if (response.status === 'success') {
            $('#groupModal').modal('hide');
            loadContacts();
        } else {
            alert('Error creating group: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while creating group'));
}

function showAddMembersModal(groupId) {
    $('#addMembersModal').data('groupId', groupId);
    $('#addMembersModal').modal('show');
}

function addGroupMembers() {
    const groupId = $('#addMembersModal').data('groupId');
    const newMembers = Array.from($('#addGroupMembers input:checked')).map(el => el.value);

    if (!newMembers.length) {
        alert('Please select at least one member to add');
        return;
    }

    $.post('chat_page.php?action=add_group_members', {
        group_id: groupId,
        members: newMembers
    }, response => {
        if (response.status === 'success') {
            $('#addMembersModal').modal('hide');
            loadContacts();
            if (currentChat && currentChat.type === 'group' && currentChat.id === groupId) {
                loadMessages();
                if (isMembersSidebarOpen) loadGroupMembers();
            }
        } else {
            alert('Error adding members: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while adding members'));
}

function exitGroup(groupId) {
    if (!confirm('Are you sure you want to exit this group?')) return;

    $.post('chat_page.php?action=exit_group', { group_id: groupId }, response => {
        if (response.status === 'success') {
            if (currentChat && currentChat.type === 'group' && currentChat.id === groupId) {
                currentChat = null;
                $('#chatMessages').html('<div class="text-center">You have exited the group</div>');
                $('#groupMembersSidebar').removeClass('open');
                $('#groupMembersList').html('');
                $('#clearChatBtn').hide();
                clearInterval(pollTimer);
            }
            loadContacts();
        } else {
            alert('Error exiting group: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while exiting group'));
}

function deleteGroup(groupId) {
    if (!confirm('Are you sure you want to delete this group? This cannot be undone.')) return;

    $.post('chat_page.php?action=delete_group', { group_id: groupId }, response => {
        if (response.status === 'success') {
            if (currentChat && currentChat.type === 'group' && currentChat.id === groupId) {
                currentChat = null;
                $('#chatMessages').html('<div class="text-center">Group has been deleted</div>');
                $('#groupMembersSidebar').removeClass('open');
                $('#groupMembersList').html('');
                $('#clearChatBtn').hide();
                clearInterval(pollTimer);
            }
            loadContacts();
        } else {
            alert('Error deleting group: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while deleting group'));
}

function showEditGroupModal(groupId, currentName) {
    $('#editGroupId').val(groupId);
    $('#editGroupName').val(currentName);
    $('#editGroupModal').modal('show');
}

function saveGroupName() {
    const groupId = $('#editGroupId').val();
    const newName = $('#editGroupName').val().trim();

    if (!newName) {
        alert('Please enter a new group name');
        return;
    }

    $.post('chat_page.php?action=edit_group', {
        group_id: groupId,
        new_name: newName
    }, response => {
        if (response.status === 'success') {
            $('#editGroupModal').modal('hide');
            loadContacts();
            if (currentChat && currentChat.type === 'group' && currentChat.id === groupId) {
                $('#chatTitle').text(newName);
                loadMessages();
            }
        } else {
            alert('Error editing group name: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while editing group name'));
}

function deleteMessage(messageId) {
    if (!confirm('Are you sure you want to delete this message?')) return;

    $.post('chat_page.php?action=delete_message', { message_id: messageId }, response => {
        if (response.status === 'success') {
            loadMessages();
        } else {
            alert('Error deleting message: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while deleting message'));
}

function showEditMessageModal(messageId, currentMessage) {
    $('#editMessageId').val(messageId);
    $('#editMessageText').val(decodeURIComponent(currentMessage));
    $('#editMessageModal').modal('show');
}

function saveMessage() {
    const messageId = $('#editMessageId').val();
    const newMessage = $('#editMessageText').val().trim();

    if (!newMessage) {
        alert('Please enter a new message');
        return;
    }

    $.post('chat_page.php?action=edit_message', {
        message_id: messageId,
        new_message: newMessage
    }, response => {
        if (response.status === 'success') {
            $('#editMessageModal').modal('hide');
            loadMessages();
        } else {
            alert('Error editing message: ' + response.message);
        }
    }, 'json').fail(() => alert('Network error occurred while editing message'));
}

function scrollToBottom() {
    const messages = document.getElementById('chatMessages');
    messages.scrollTop = messages.scrollHeight;
}
</script>
</body>
</html>