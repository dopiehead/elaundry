<?php

/**
 * Helper function to count rows in a table
 * 
 * @param mysqli $conn - Database connection
 * @param string $sql - SQL query with placeholders
 * @param array  $params - Params to bind
 * @param string $types - Param types (e.g. "s", "i")
 * @return int - Count result
 */
function getCount($conn, $sql, $params = [], $types = "")
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return (int)$count;
}

// ✅ Count all washers (non-customers)
$countWashers = getCount(
    $conn,
    "SELECT COUNT(*) AS count FROM user_profile WHERE user_type != ?",
    ["customer"],
    "s"
);

// ✅ Count all users
$countUsers = getCount(
    $conn,
    "SELECT COUNT(*) AS count FROM user_profile"
);
