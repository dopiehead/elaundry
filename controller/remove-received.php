<?php 
session_start();
if (isset($_POST['id'])) {
    require("../class/auth.php");
    $auth = new Auth(new Database());
    $conn = $auth->getConnection();
    $id = $conn->real_escape_string($_POST['id']);
    // Prepare the SQL statement
    $sql = "UPDATE messages SET is_receiver_deleted = 1 WHERE sender_email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameter
        mysqli_stmt_bind_param($stmt, "s", $id);
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "1";
        } else {
            echo "Error in executing delete statement.";
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error in preparing SQL statement.";
    }
    // Close the connection (optional)
    // mysqli_close($conn);
}
?>
