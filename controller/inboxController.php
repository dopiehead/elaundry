<?php 
require("../class/auth.php");
$auth = new Auth(new Database());
$user_id = $auth->getUserId();
$conn = $auth->getConnection();


if (!$auth->checkLogin()) {
    echo "<p class='text-warning'>Invalid user ID. Please login again.</p>";
    header("Location:../public/login");
    exit;
} 

// ✅ Fetch user basic details
$getuser = $conn->prepare("SELECT * FROM user_profile WHERE id = ? AND verified = 1");
$getuser->bind_param("i", $user_id);

if ($getuser->execute()) {
    $result = $getuser->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ✅ User image check
        $user_image = $user['user_image'] ?? '';
        $extension = strtolower(pathinfo($user_image, PATHINFO_EXTENSION));
        $image_extension  = ['jpg','jpeg','png']; 

        if (!in_array($extension, $image_extension) || empty($user_image)) {
            $image = "https://placehold.co/400";  
        } else {
            $image = $user_image;
        }

        include("../contents/user-details.php");

    } else {
        echo "<p class='text-danger'>User not found or not verified.</p>";
    }
} else {
    echo "<p class='text-danger'>Database query failed: " . htmlspecialchars($getuser->error) . "</p>";
}

// Ensure this sets $conn
$you = $user_email; // Example static value – replace with your dynamic user
$limit = 2;
$page_number = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page_number - 1) * $limit;
// -----------------------------
// 2. Count Unique Conversations
// -----------------------------
$countQuery = "
    SELECT COUNT(*) AS total 
    FROM (
        SELECT sender_email 
        FROM messages 
        WHERE receiver_email = ? AND is_receiver_deleted = 0 
        GROUP BY sender_email
    ) AS sub";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->bind_param("s", $you);
$stmtCount->execute();
$total_rows = $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();
$total_pages = ceil($total_rows / $limit);

// -----------------------------
// 3. Count Unread Conversations
// -----------------------------
$countUnreadQuery = "
    SELECT COUNT(*) AS unread_total 
    FROM (
        SELECT sender_email 
        FROM messages 
        WHERE receiver_email = ? AND is_receiver_deleted = 0 AND has_read = 0 
        GROUP BY sender_email
    ) AS sub";
$stmtUnread = $conn->prepare($countUnreadQuery);
$stmtUnread->bind_param("s", $you);
$stmtUnread->execute();
$unread_count = $stmtUnread->get_result()->fetch_assoc()['unread_total'];
$stmtUnread->close();

// -----------------------------
// 4. Fetch Paginated Messages
// -----------------------------
$messagesQuery = "SELECT m.*
FROM messages m
INNER JOIN (
    SELECT sender_email, MAX(id) AS max_id
    FROM messages
    WHERE receiver_email = ? AND is_receiver_deleted = 0
    GROUP BY sender_email
) t ON m.id = t.max_id
ORDER BY m.has_read ASC, m.date DESC
LIMIT ?, ?;
";
$stmtMessages = $conn->prepare($messagesQuery);
$stmtMessages->bind_param("sii", $you, $offset, $limit);
$stmtMessages->execute();
$resultMessages = $stmtMessages->get_result();
?>