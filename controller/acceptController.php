<?php
require("../class/auth.php");
$auth = new Auth(new Database());
$conn = $auth->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $request_id = intval($_POST['id']);

    $stmt = $conn->prepare("UPDATE barber_request SET pending = 1 WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Offer accepted"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to accept offer"]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
