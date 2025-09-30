<?php
ini_set('display_errors', 1);
header("Content-Type: application/json");

require '../class/auth.php';

// ✅ Initialize Auth & Database
$auth = new Auth(new Database());
$conn = $auth->getConnection();
$rater_id = $auth->getUserId(); // current logged-in user

// ✅ Read raw POST body (JSON)
$input = file_get_contents("php://input");
$data  = json_decode($input, true);

// ✅ Validate input
if (isset($data['sp_id']) && !empty($rater_id)) {
    $sp_id = (int)$data['sp_id'];

    // ✅ Step 1: Check if already liked
    $check = $conn->prepare("SELECT id FROM user_likes WHERE rater_id = ? AND sp_id = ?");
    $check->bind_param("ii", $rater_id, $sp_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "message" => "You already liked this service provider."
        ]);
        $check->close();
        exit;
    }
    $check->close();

    // ✅ Step 2: Insert into user_likes
    $date = date("Y-m-d H:i:s");
    $sql = "INSERT INTO user_likes (rater_id, sp_id, date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iis", $rater_id, $sp_id, $date);

        if ($stmt->execute()) {
            $like_id = $stmt->insert_id;

            // ✅ Step 3: Update user_profile.likes (+1)
            $update = $conn->prepare("UPDATE user_profile SET user_likes = user_likes + 1 WHERE id = ?");
            $update->bind_param("i", $sp_id);
            $update->execute();
            $update->close();

            echo json_encode([
                "success"   => true,
                "message"   => "Like recorded successfully.",
                "like_id"   => $like_id
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to insert like.",
                "error"   => $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Statement preparation failed.",
            "error"   => $conn->error
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing data (sp_id required, must be logged in)."
    ]);
}

$conn->close();
?>
