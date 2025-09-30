<?php 

$numNotifications = 0;
$user_image = ''; // default image fallback

if (isset($_SESSION['user_id'])) {
    // Fetch notifications count
    $getnotification = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE recipient_id = ? AND pending = 0");
    if ($getnotification) {
        $getnotification->bind_param("i", $_SESSION['user_id']);
        if ($getnotification->execute()) {
            $getnotification->bind_result($numNotifications);
            $getnotification->fetch();
        }
        $getnotification->close();
    }

    // Fetch user image
    $getimage = $conn->prepare("SELECT user_image FROM user_profile WHERE id = ?");
    if ($getimage) {
        $getimage->bind_param('i', $_SESSION['user_id']);
        $getimage->execute();
        $getimage->bind_result($fetched_image);
        if ($getimage->fetch() && !empty($fetched_image)) {
            $user_image = $fetched_image;
        }
        $getimage->close();
    }
}
?>

<div class="header d-flex justify-content-between align-items-center px-3 py-2 bg-white shadow-sm">
    <h1 class="header-title h5 mb-0">Overview</h1>

    <div class="header-right d-flex align-items-center gap-3">
        <a href="notifications.php" class="position-relative text-decoration-none text-dark" aria-label="Notifications">
            <i class="fa fa-bell fa-lg"></i>
            <?php if ($numNotifications > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                      aria-live="polite" aria-atomic="true">
                    <?= htmlspecialchars($numNotifications) ?>
                </span>
            <?php endif; ?>
        </a>

        <div class="profile-img rounded-circle bg-secondary" style="width: 36px; height: 36px; overflow: hidden;">
            <img src="<?= "../" .htmlspecialchars($user_image) ?>" alt="User profile image" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>
</div>
