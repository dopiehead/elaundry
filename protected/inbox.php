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

// ✅ Check login
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <!-- CSS Links -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard/overview.css">
    <link rel="stylesheet" href="../assets/css/dashboard/inbox.css">
    <link rel="stylesheet" href="../assets/css/dashboard/settings.css">

</head>
<body>

    <?php include("components/sidebar.php"); ?> 

    <div class="main-content">
        <?php include("components/overview.php"); ?>

        <!-- Inbox Header -->
        <table>
            <thead>
                <tr>
                    <th colspan="3" id="inbox">Inbox (<?php echo $unread_count; ?>)</th>
                    <th><a href="inbox.php" id="refresh">Refresh</a></th>
                </tr>
            </thead>
        </table>

        <!-- Messages Table -->
        <table>
            <thead>
                <tr style="background-color: rgba(192,192,192,0.1);">
                    <th>Action</th>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultMessages->fetch_assoc()):
                    $sender_email = $row['sender_email'];

                    // Count unread from this sender
                    $stmtUserCount = $conn->prepare("
                        SELECT COUNT(*) AS unreadCount 
                        FROM messages 
                        WHERE sender_email = ? AND receiver_email = ? 
                          AND is_receiver_deleted = 0 AND has_read = 0
                    ");
                    $stmtUserCount->bind_param("ss", $sender_email, $you);
                    $stmtUserCount->execute();
                    $unreadFromUser = $stmtUserCount->get_result()->fetch_assoc()['unreadCount'];
                    $stmtUserCount->close();

                    $unreadBadge = ($unreadFromUser > 0) ? "<span class='numbering'>($unreadFromUser)</span> " : "";
                ?>
                <tr id="<?= $row['id'] ?>" class="border_bottom">
                    <td>
                        <a class="remove text-danger" id="<?= htmlspecialchars($sender_email) ?>">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                    <td>
                        <a href="chat.php?user_name=<?= urlencode($sender_email) ?>">
                            <?= htmlspecialchars(substr($sender_email, 0, 4)) ?>
                        </a>
                    </td>
                    <td style="<?= $row['has_read'] == 0 ? 'font-weight:bold;' : '' ?>">
                        <a href="chat.php?user_name=<?= urlencode($sender_email) ?>" class="reply">
                            <?= $unreadBadge . htmlspecialchars($row['subject']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="margin-top: 20px;">
            <?php if ($page_number > 1): ?>
                <a href="messages.php?page=<?= $page_number - 1 ?>">Prev</a>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <a class="<?= $page == $page_number ? 'active' : '' ?>" href="messages.php?page=<?= $page ?>">
                    <?= $page ?>
                </a>
            <?php endfor; ?>

            <?php if ($page_number < $total_pages): ?>
                <a href="messages.php?page=<?= $page_number + 1 ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
$(document).ready(function () {
    // General delete handler for both received and sent messages
    $('.remove, .removeSent').on('click', function (e) {
        e.preventDefault();

        if (!confirm("Are you sure you want to delete this?")) return;

        const $button = $(this);
        const id = $button.attr('id');
        const rowElement = $button.closest('tr');
        const isSent = $button.hasClass('removeSent');
        const url = isSent ? 'engine/remove-sent.php' : 'engine/remove-received.php';

        $.ajax({
            url: url,
            method: 'POST',
            data: { id: id },
            success: function (response) {
                if (response == 1) {
                    swal({
                        text: "Message has been deleted",
                        icon: "success"
                    });
                    rowElement.fadeOut('slow', function () { $(this).remove(); });
                } else {
                    swal({
                        icon: "error",
                        text: response
                    });
                }
            },
            error: function () {
                swal({
                    icon: "error",
                    text: "An error occurred while deleting the message."
                });
            }
        });
    });
});

</script>

</body>
</html>
