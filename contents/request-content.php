<?php 
$request_id = isset($request['id']) ? $conn->real_escape_string($request['id']) : "Not available";
$request_sp_id = isset($request['sp_id']) ? $conn->real_escape_string($request['sp_id']) : "Not available";
$request_customer_id = isset($request['customer_id']) ? $conn->real_escape_string($request['customer_id']) : "Not available";
$request_number_of_people_clothes = isset($request['number_of_clothes']) ? $conn->real_escape_string($request['number_of_clothes']) : "Not available";
$request_user_preference = isset($request['user_preference']) ? $conn->real_escape_string($request['user_preference']) : "Not available";
$request_location = isset($request['location']) ? $conn->real_escape_string($request['location']) : "Not available";
$request_status = isset($request['pending']) ? $conn->real_escape_string($request['pending']) : "Not available";
$request_date = isset($request['date']) ? $conn->real_escape_string($request['date']) : "Not available";
?>