<?php 
require("../class/auth.php");

$auth = new Auth(new Database());
$conn = $auth->getConnection();

if (!$auth->checkLogin()) {
    echo "<p class='text-warning'>Invalid user ID. Please login again.</p>";
    header("Location:../public/login");
    exit;
} 

// update pending notifications to seen
$update_notifications = $conn->prepare("UPDATE user_notifications SET pending = 1 WHERE recipient_id = ?");
$update_notifications->bind_param("i", $_SESSION['user_id']);
$update_notifications->execute();

// get number of notifications
$get_notifications = $conn->prepare("SELECT * FROM user_notifications WHERE recipient_id = ? AND pending = 0 ORDER BY pending DESC, date DESC");
if ($get_notifications->bind_param("i", $_SESSION['user_id'])) {
    $get_notifications->execute();
    $result = $get_notifications->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/dashboard/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard/overview.css">
    <link rel="stylesheet" href="../assets/css/dashboard/settings.css">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
<?php include("components/sidebar.php"); ?>

<div class="main-content p-3">
    
    <?php include("components/overview.php"); ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <div class="notification-item bg-white shadow-sm rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-danger fw-bold">From: Admin</span>
                    <span class="text-primary fa fa-bell fa-lg ms-2"></span>
                </div>
                <span class="text-muted small"><?= htmlspecialchars($row['date']) ?></span>
            </div>

            <div class="mt-2 mb-2">
                <?php if($row['pending']==0):?>
                   <h6 class="text-secondary fw-bold mb-0"><?= htmlspecialchars($row['message']) ?></h6>
                <?php else:?>
                   <h6 class="text-secondary mb-0"><?= htmlspecialchars($row['message']) ?></h6>
                 <?php endif; ?>
            </div>

            <div class="text-end">
                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= htmlspecialchars($row['id']) ?>">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-secondary text-center shadow-sm">
            You have no new notifications.
        </div>
    <?php endif; ?>
</div>
<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $(".btn-delete").click(function() {
        const id = $(this).data("id");
        const notificationCard = $(this).closest(".notification-item");

        Swal.fire({
            title: 'Are you sure?',
            text: "This notification will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "delete-notification.php",
                    method: "POST",
                    data: { id: id },
                    success: function(response) {
                        if (response.trim() == "1") {
                            notificationCard.fadeOut(300, function() {
                                $(this).remove();
                            });
                            Swal.fire('Deleted!', 'Notification has been deleted.', 'success');
                        } else {
                            Swal.fire('Error', response, 'warning');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to delete notification. Try again later.', 'error');
                    }
                });
            }
        });
    });
});
</script>

</body>
</html>
<?php 
} else {
    echo "<div class='alert alert-danger text-center'>Error preparing SQL statement.</div>";
}
?>
