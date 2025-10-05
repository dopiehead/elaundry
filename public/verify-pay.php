<?php 
include("../controller/subscriptionController.php"); 
include("../controller/payController.php"); 

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"  rel="stylesheet"/>
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;700&family=Roboto&display=swap" rel="stylesheet">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
     <link rel="stylesheet" href="../assets/css/verify-pay.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center bg-light" style="height: 100vh; margin: 0;">

<?php
// ✅ Make sure these are set by payController.php
if (isset($status) && $status === 'success') {
    if (isset($user_name, $amount, $reference, $duration)) {
        ?>
        <div class='container-fluid checkmark-container d-flex justify-content-center align-items-center gap-2 p-5 h-100'>
            <div class='card shadow bg-white px-4 py-2 d-flex flex-column gap-2' style="max-width: 500px; width: 100%;">
                <div class='text-center d-flex justify-content-center'>
                    <span class='border border-success border-3 rounded-circle fs-3 px-3 py-2 text-success checkmark-circle'>
                        <i class="fa fa-check"></i>
                    </span>
                </div>

                <div class='d-flex justify-content-between'>
                    <span>Transaction Reference</span>
                    <span class='fw-bold'><?= htmlspecialchars($reference); ?></span>
                </div>

                <div class='d-flex justify-content-between'>
                    <span>Client's Name</span>
                    <span class='text-capitalize fw-bold'><?= htmlspecialchars($user_name); ?></span>
                </div>

                <div class='d-flex justify-content-between'>
                    <span>Subscription Duration</span>
                    <span class='fw-bold'><?= htmlspecialchars($duration); ?></span>
                </div>

                <p class='mt-4 text-secondary'>
                    Your payment has been processed successfully. Your profile is now active.
                </p>

                <?php if (isset($user_type) && $user_type !== "customer") { ?>
                    <p class='text-center text-secondary'>
                        <a href='../protected/dashboard.php' class='text-secondary text-decoration-none'>Back to Dashboard</a>
                    </p>
                <?php } ?>
            </div>
        </div>
        <?php
    } else {
        echo "<p class='text-danger'>Missing transaction details.</p>";
    }
} else {
    echo "<p class='text-danger'>Payment failed or invalid request. Please try again.</p>";
}

if (isset($conn)) {
    $conn->close();
}
?>

</body>
</html>
