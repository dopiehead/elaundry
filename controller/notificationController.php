<?php
require("../class/auth.php");

$auth = new Auth(new Database());
$conn = $auth->getConnection();

if (!$auth->checkLogin()) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Mark all as seen
if (isset($_POST['mark_seen'])) {
    $stmt = $conn->prepare("UPDATE user_notifications SET pending = 1 WHERE recipient_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo json_encode(["status" => "success"]);
    exit;
}

// ✅ Delete a notification
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM user_notifications WHERE id = ? AND recipient_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Notification deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Delete failed"]);
    }
    exit;
}

// ✅ Fetch notifications
$stmt = $conn->prepare("SELECT * FROM user_notifications WHERE recipient_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

header("Content-Type: application/json");
echo json_encode($notifications);
exit;
?>
