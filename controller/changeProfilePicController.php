<?php

require('../class/auth.php');

$auth = new Auth(new Database());
$conn = $auth->getConnection();
$id =  $auth->getUserId();

// --- Cloudinary Setup ---
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;


// Cloudinary config (using .env values)
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => ['secure' => true]
]);

$cloudinary = new Cloudinary();

$allowed_extensions = ['jpg', 'jpeg', 'png'];

// Validate user ID
if (empty($id) || !ctype_digit($id)) {
    echo "Invalid user ID.";
    exit;
}

// Check file input
if (!isset($_FILES['fileupload']) || empty($_FILES['fileupload']['name'])) {
    echo "Choose Image file to upload!!!";
    exit;
}

$file = $_FILES['fileupload'];
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$image_temp_name = $file['tmp_name'];

// Validate extension
if (!in_array($file_extension, $allowed_extensions)) {
    echo "Please upload valid Image in PNG or JPEG only!!!";
    exit;
}

// Validate MIME type
$mime = mime_content_type($image_temp_name);
if (!in_array($mime, ['image/jpeg', 'image/png'])) {
    echo "Invalid image format!";
    exit;
}

// Validate image content
$image_info = getimagesize($image_temp_name);
if ($image_info === false) {
    echo "Uploaded file is not a valid image!";
    exit;
}

// Check size (2MB)
$maxFileSize = 2 * 1024 * 1024;
if ($file['size'] > $maxFileSize) {
    echo "File size exceeds the 2MB limit.";
    exit;
}

// ✅ Upload to Cloudinary instead of local folder
try {
    $uploadResult = (new UploadApi())->upload($image_temp_name, [
        'folder' => 'profiles/', // Store inside profiles folder on Cloudinary
        'public_id' => bin2hex(random_bytes(8)), // Unique name
        'overwrite' => true,
        'resource_type' => 'image'
    ]);

    // Cloudinary secure URL
    $imageUrl = $uploadResult['secure_url'];

    // Update DB with Cloudinary URL
    $stmt = $conn->prepare("UPDATE user_profile SET user_image=? WHERE id=?");
    $stmt->bind_param("si", $imageUrl, $id);

    if ($stmt->execute()) {
        echo "1"; // Success
    } else {
        echo "Database error: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    echo "Cloudinary upload failed: " . $e->getMessage();
}
