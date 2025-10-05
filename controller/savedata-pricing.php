<?php
require '../class/auth.php';

$auth = new Auth(new Database());
$conn = $auth->getConnection();

if ($auth->isLoggedIn() && isset($_POST["id"], $_POST["column"], $_POST["value"])) {
    $id     = (int) $_POST["id"];
    $column = $_POST["column"];
    $value  = $_POST["value"];

    // ✅ Allow only known editable columns (use plain strings, not backticks)
    $allowed = [
        "sweat_shirt", "cotton", "hoodie", "towel", "trouser",
        "undies", "t_shirt", "bed_sheet", "short"
    ];

    if (!in_array($column, $allowed, true)) {
        echo "false"; 
        exit;
    }

    // ✅ Build safe query
    $sql = "UPDATE laundry_items SET $column = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $value, $id);

    if ($stmt->execute()) {
        echo "1";
    } else {
        echo "Update failed";
    }
    $stmt->close();
} else {
    echo "false";
}
?>
