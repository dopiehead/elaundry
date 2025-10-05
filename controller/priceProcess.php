<?php
require("../class/auth.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$auth = new Auth(new Database());
$conn = $auth->getConnection();
$user_id = $auth->getUserId();

$selected = $_POST['selected_badges'] ?? '';
$selectedArray = $selected ? explode(",", $selected) : [];
$item_price = 750; // int

$response = []; // ✅ collect responses

if (!empty($selectedArray)) {
    foreach ($selectedArray as $item_name) {
        $item_name = trim($item_name);
        $clean_name = str_replace("_", " ", $item_name); // ✅ nicer display name

        // ✅ Check if item already exists for this user
        $check = $conn->prepare("SELECT id FROM laundry_items WHERE user_id = ? AND item_name = ?");
        if (!$check) {
            $response[] = "Prepare failed: " . $conn->error;
            continue;
        }
        $check->bind_param("is", $user_id, $item_name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            // ✅ Insert if not already added
            $insert = $conn->prepare("INSERT INTO laundry_items(user_id, item_name, item_price) VALUES (?, ?, ?)");
            if (!$insert) {
                $response[] = "Prepare failed: " . $conn->error;
                continue;
            }
            $insert->bind_param("isi", $user_id, $item_name, $item_price);

            if ($insert->execute()) {
                $response[] = "$clean_name added";
            } else {
                $response[] = "Error saving $clean_name";
            }
            $insert->close();
        } else {
            $response[] = "$clean_name already exists";
        }

        $check->close();
    }
} else {
    $response[] = "No badges selected.";
}

// ✅ Return JSON response instead of concatenated echoes
header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "messages" => $response
]);
