<?php
require '../class/auth.php';
$auth = new Auth(new Database());
$conn = $auth->getConnection();
$customer_id = $auth->getUserId(); // Logged-in customer

header("Content-Type: application/json");

$input = file_get_contents("php://input");
$data  = json_decode($input, true);

if (
    isset($data['sp_id']) &&
    isset($data['number_of_clothes']) &&
    isset($data['amount']) &&
    isset($data['user_preference']) &&
    isset($data['location']) &&
    isset($data['date'])
) {
    $sp_id            = (int)$data['sp_id'];
    $number_of_clothes= (int)$data['number_of_clothes'];
    $proposed_amount  = (float)$data['amount'];
    $user_preference  = trim($data['user_preference']);
    $location         = trim($data['location']);
    $date             = $data['date'];

    $sql = "INSERT INTO sp_request 
            (sp_id, customer_id, number_of_clothes, user_preference, proposed_amount, location, date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "iiisdss",
            $sp_id,
            $customer_id,
            $number_of_clothes,
            $user_preference,
            $proposed_amount,
            $location,
            $date
        );

        if ($stmt->execute()) {
            echo json_encode([
                "success"    => true,
                "message"    => "Request inserted successfully.",
                "request_id" => $stmt->insert_id
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Query execution failed.",
                "error"   => $stmt->error
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Statement preparation failed.",
            "error"   => $conn->error
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing JSON data."
    ]);
}

$conn->close();
?>
