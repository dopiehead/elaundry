<?php 

require("../class/auth.php");
$auth = new Auth(new Database());
$conn = $auth->getConnection();
$user_id = $auth->getUserId();
// Set sender details if session is active
if (isset($_SESSION['user_id'])) {
    $sender = $_SESSION['user_email'];
    $senderName =  $_SESSION['user_name'];
} else {
    // If session check is required, uncomment this block:
    echo "<script>location.href='../sign-in.php'</script>";
    exit();
}

// Get user_name from URL and sanitize
$user_name = isset($_GET['user_name']) ? mysqli_real_escape_string($conn, $_GET['user_name']) : '';

// Debug output removed: echo $user_name;

// Mark messages as read
$conn->query("UPDATE messages SET has_read = 1 WHERE sender_email='" . $conn->real_escape_string($user_name) . "' AND receiver_email = '" . $conn->real_escape_string($sender) . "'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard/chat.css">
</head>
<body>

<!-- Chat Header -->
<div class="chat-header d-flex align-items-center p-3 bg-primary text-white">
    <a href="inbox.php" class="text-white me-3">
        <i class="fa fa-chevron-left"></i> Back
    </a>
    <div class="user-avatar bg-light text-dark rounded-circle px-3 py-2 fw-bold"><?= strtoupper(substr($user_name, 0, 2)) ?></div>
</div>

<!-- Messages -->
<div class="container-fluid message-box" id="messages-container" style="height: 70vh; overflow-y: scroll;">
    <div id="parent">
        <div id="child">
           <?php
              $read = $conn->query("SELECT * FROM messages 
              WHERE is_sender_deleted = 0 AND 
              ((sender_email = '$sender' AND receiver_email = '$user_name') 
              OR (sender_email = '$user_name' AND receiver_email = '$sender')) 
              ORDER BY date ASC");

              while ($messages = $read->fetch_assoc()):
              $is_sender = $messages['sender_email'] == $sender;
           ?>
               <div class="<?= $is_sender ? 'sender-box bg-primary text-white p-2 my-2 rounded' : 'receiver-box bg-light p-2 my-2 rounded' ?>">
                   <p class="mb-1"><?= htmlspecialchars($messages['compose']) ?></p>
                   <small class="d-block text-end">
                       <?= $is_sender ? ($messages['has_read'] ? "Seen" : "Sent") : "Received" ?>
                       on <?= date('M j, Y h:i A', strtotime($messages['date'])) ?>
                   </small>
               </div>
           <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Result placeholder -->
<div class="result px-3 py-1"></div>

<!-- Message Input Form -->
<div class="message-form-container p-3 border-top bg-white">
    <form id="message-form" class="d-flex align-items-center gap-2">
        <input type="hidden" name="user_name" value="<?= htmlspecialchars($sender) ?>">
        <input type="hidden" name="sentby" value="<?= htmlspecialchars($sender) ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($senderName) ?>">
        <input type="hidden" name="sentto" value="<?= htmlspecialchars($user_name) ?>">

        <textarea name="message" class="form-control message-input" rows="1" placeholder="Type your message here..." required></textarea>
        <button type="submit" class="btn btn-primary send-button">
            <i class="fa fa-paper-plane"></i>
        </button>
    </form>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#message-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "../engine/message-process.php",
            data: $('#message-form').serialize(),
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $('.result').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('.message-input').val(''); // clear input
                } else {
                    $('.result').html('<div class="alert alert-danger">' + response.message + '</div>');
                }

                $('.result').fadeIn().delay(3000).fadeOut('slow');

                // Scroll chat to bottom
                var objDiv = document.getElementById('messages-container');
                objDiv.scrollTop = objDiv.scrollHeight;
            },
            error: function(xhr, status, error) {
                // Debug info
                console.error("AJAX Error Status:", status);
                console.error("AJAX Error Thrown:", error);
                console.error("Server Response:", xhr.responseText);

                // Try to parse server response for readable message
                let message = "Server error. Please try again.";
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.message) message = res.message;
                } catch (e) {
                    // If JSON parsing fails, use generic message
                }

                $('.result').html('<div class="alert alert-danger">AJAX Error: ' + message + '</div>');
                $('.result').fadeIn().delay(4000).fadeOut('slow');
            }
        });
    });

    // Optional: refresh messages (not ideal for high traffic)
    setInterval(function() {
        $("#parent").load(location.href + " #child");
    }, 2500);
});
</script>


</body>
</html>
