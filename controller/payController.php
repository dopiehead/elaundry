<?php
require("../class/auth.php");

$auth = new Auth(new Database());
$conn = $auth->getConnection();

// Check required GET params
if (
    isset($_GET['status'], $_GET['reference'], $_GET['id'], $_GET['user_type'], $_GET['amount'])
) {
    $reference = htmlspecialchars($_GET['reference']);
    $status = htmlspecialchars($_GET['status']);
    $amount = (float) $_GET['amount'];
    $user_type = htmlspecialchars($_GET['user_type']);
    $user_id = (int) $_GET['id'];

    // Fetch user name for display
    $stmt = $conn->prepare("SELECT user_name FROM user_profile WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_name);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id || empty($user_name)) {
        echo "<p>User not found.</p>";
        exit;
    }

    // ✅ Proceed if payment is successful
    if ($status === 'success') {

        // Update payment status
        $update = $conn->prepare("UPDATE user_profile SET payment_status = 1 WHERE id = ?");
        $update->bind_param("i", $user_id);
        $update->execute();
        $update->close();

        // Determine duration and expiry date
        switch ($amount) {
            case 1000:
                $duration = '1 day';
                $expiry_date = date('Y-m-d H:i:s', strtotime('+1 day'));
                break;
            case 2500:
                $duration = '1 week';
                $expiry_date = date('Y-m-d H:i:s', strtotime('+1 week'));
                break;
            case 4000:
                $duration = '1 month';
                $expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));
                break;
            default:
                $duration = 'Custom';
                $expiry_date = date('Y-m-d H:i:s');
        }

        // Check for duplicate payment
        $check = $conn->prepare("SELECT id FROM subscriptions WHERE user_id = ? AND reference = ?");
        $check->bind_param("is", $user_id, $reference);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $check->close();

            // Insert subscription
            $insert = $conn->prepare("
                INSERT INTO subscriptions (price, user_id, user_type, reference, duration, expiry_date)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insert->bind_param("dissss", $amount, $user_id, $user_type, $reference, $duration, $expiry_date);

            if (!$insert->execute()) {
                echo "<p>Error saving subscription: " . htmlspecialchars($insert->error) . "</p>";
            }

            $insert->close();
        } else {
            echo "<p>Duplicate payment detected.</p>";
        }

    } else {
        echo "<p>Payment failed or was canceled.</p>";
    }
} else {
    echo "<p>Invalid request. Missing required parameters.</p>";
}

$conn->close();
?>
