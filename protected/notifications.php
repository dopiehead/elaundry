<?php 
require("../class/auth.php");

$auth = new Auth(new Database());
if (!$auth->checkLogin()) {
    header("Location:../public/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/dashboard/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard/overview.css">
    <link rel="stylesheet" href="../assets/css/dashboard/settings.css">
</head>
<body>
<?php include("components/sidebar.php"); ?>
<div class="main-content p-3">
    <?php include("components/overview.php"); ?>
    <div id="notification-container" class="mt-3"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    loadNotifications();

    // ✅ Load notifications dynamically
    function loadNotifications() {
        $.getJSON("../controller/notificationController.php", function(data) {
            let html = "";
            if (data.length > 0) {
                data.forEach(row => {
                    html += `
                    <div class="notification-item bg-white shadow-sm rounded p-3 mb-3 service-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-danger fw-bold">From: Admin</span>
                                <span class="text-primary fa fa-bell fa-lg ms-2"></span>
                            </div>
                            <span class="text-muted small">${row.date}</span>
                        </div>
                        <div class="mt-2 mb-2">
                            <h6 class="text-secondary fw-bold mb-0">${row.message}</h6>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>`;
                });
            } else {
                html = `<div class='alert alert-secondary text-center shadow-sm'>You have no new notifications.</div>`;
            }
            $("#notification-container").html(html);
        });
    }

    // ✅ Delete notification
    $(document).on("click", ".delete-btn", function() {
        const id = $(this).data("id");
        const card = $(this).closest(".notification-item");

        Swal.fire({
            title: 'Are you sure?',
            text: "This notification will be deleted permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../controller/notificationController.php", { delete_id: id }, function(response) {
                    const res = JSON.parse(response);
                    if (res.status === "success") {
                        card.fadeOut(300, function() { $(this).remove(); });
                        Swal.fire('Deleted!', res.message, 'success');
                    } else {
                        Swal.fire('Error', res.message, 'warning');
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
