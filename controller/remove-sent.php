<?php 
require("../class/auth.php");
$auth = new Auth(new Database());
$conn = $auth->getConnection();
if (isset($_POST['id'])) {

    $id = $conn->real_escape_string($_POST['id'] ?? "");
    // Prepare the SQL statement
    $sql = "UPDATE messages SET is_sender_deleted = 1 WHERE receiver_email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameter
        mysqli_stmt_bind_param($stmt, "s", $id);
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "1";
            exit();
        } else {
            echo "Error executing delete statement.";
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing SQL statement.";
    }
} else {
    echo "Invalid request.";
}
?>
