<?php
require("../class/auth.php");

$auth = new Auth(new Database());
$conn = $auth->getConnection();
$user_id = $auth->getUserId();

$spLocation = "";
if ($user_id !== null) {
    $sp_location = $conn->prepare("SELECT * FROM user_profile WHERE id = ? AND verified = 1 AND user_type != 'customer'");
    $sp_location->bind_param('i', $user_id);
    if ($sp_location->execute()) {
        $sp_result = $sp_location->get_result();
        while ($sp_data = $sp_result->fetch_assoc()) {
            $spLocation = trim(($sp_data['user_address'] ?? '') . ", " . ($sp_data['user_location'] ?? ''));
        }
    }
    $sp_location->close();
}

$totalRecords = 0;
$from = 0;
$to = 0;

if ($user_id) {
    $gettotal = $conn->prepare("SELECT COUNT(*) FROM user_profile WHERE verified = 1 AND user_type != 'customer' AND id != ?");
    $gettotal->bind_param("i", $user_id);
} else {
    $gettotal = $conn->prepare("SELECT COUNT(*) FROM user_profile WHERE verified = 1 AND user_type != 'customer'");
}

if ($gettotal->execute()) {
    $gettotal->bind_result($totalRecords);
    $gettotal->fetch();
    $gettotal->close();

    $num_per_page = 20;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $initial_page = ($page - 1) * $num_per_page;
    $from = $initial_page + 1;
    $to = min($initial_page + $num_per_page, $totalRecords);
}

// ✅ Header section
echo "<div class='results-header text-center mb-4'>
        <h5 class='fw-bold text-danger'>Total Records: " . $totalRecords . "</h5>
        <p class='text-muted'>" . $from . " - " . $to . " of <span class='fw-bold'>" . $totalRecords . "</span></p>
      </div>";

// ✅ Service grid container
echo "<div class='row g-4 service-grid'>";

$baseQuery = "SELECT * FROM user_profile WHERE verified = 1 AND user_type != 'customer'";

// Search filter
if (!empty($_POST['q'])) {
    $search = explode(" ", $conn->real_escape_string($_POST['q']));
    foreach ($search as $text) {
        $escaped = $conn->real_escape_string($text);
        $baseQuery .= " AND (user_name LIKE '%$escaped%' OR user_type LIKE '%$escaped%' OR user_image LIKE '%$escaped%' OR user_dob LIKE '%$escaped%' OR user_phone LIKE '%$escaped%' OR user_bio LIKE '%$escaped%' OR user_location LIKE '%$escaped%' OR lga LIKE '%$escaped%' OR user_address LIKE '%$escaped%' OR user_rating LIKE '%$escaped%' OR user_gender LIKE '%$escaped%' OR user_likes LIKE '%$escaped%' OR user_shares LIKE '%$escaped%' OR user_fee LIKE '%$escaped%' OR user_preference LIKE '%$escaped%')";
    }
}

// Location filter
if (!empty($_POST['locationFilter'])) {
    $locationFilter = $conn->real_escape_string($_POST['locationFilter']);
    $baseQuery .= " AND user_location LIKE '%$locationFilter%'";
}

// Laundry Type filter
if (!empty($_POST['user_type'])) {
    $typeFilter = $conn->real_escape_string($_POST['user_type']);
    $baseQuery .= " AND user_type LIKE '%$typeFilter%'";
}

// Preference filter
if (!empty($_POST['user_preference'])) {
    $preferenceFilter = $conn->real_escape_string($_POST['user_preference']);
    $baseQuery .= " AND user_preference LIKE '%$preferenceFilter%'";
}

// Gender filter
$gender_filter = isset($_POST['gender']) ? explode(',', $_POST['gender']) : [];
if ($gender_filter) {
    foreach ($gender_filter as $g) {
        $g = $conn->real_escape_string($g);
        $baseQuery .= " AND user_gender LIKE '%$g%'";
    }
}

// ✅ User services filter
if (!empty($_POST['user_services'])) {
    $serviceFilter = $conn->real_escape_string($_POST['user_services']);
    $filtered = str_replace("&", " ", $serviceFilter);
    $baseQuery .= " AND user_services LIKE '%$filtered%'";
}

// Price filter
$priceFrom = isset($_POST['price_from']) ? (int)$_POST['price_from'] : null;
$priceTo = isset($_POST['price_to']) ? (int)$_POST['price_to'] : null;

if ($priceFrom !== null && $priceTo !== null) {
    $baseQuery .= " AND user_fee BETWEEN $priceFrom AND $priceTo";
} elseif ($priceFrom !== null) {
    $baseQuery .= " AND user_fee >= $priceFrom";
} elseif ($priceTo !== null) {
    $baseQuery .= " AND user_fee <= $priceTo";
}

// Exclude logged-in user
if ($user_id) {
    $baseQuery .= " AND id != '" . (int)$user_id . "'";
}

// Sorting
$orderBy = $_POST['orderBy'] ?? 'date_added_DESC';
switch ($orderBy) {
    case 'date_added_ASC': $baseQuery .= " ORDER BY date_added ASC"; break;
    case 'date_added_DESC': $baseQuery .= " ORDER BY date_added DESC"; break;
    case 'user_name_ASC': $baseQuery .= " ORDER BY user_name ASC"; break;
    case 'user_name_DESC': $baseQuery .= " ORDER BY user_name DESC"; break;
    case 'age_ASC': $baseQuery .= " ORDER BY user_dob ASC"; break;
    case 'age_DESC': $baseQuery .= " ORDER BY user_dob DESC"; break;
    case 'user_rating_ASC': $baseQuery .= " ORDER BY user_rating ASC"; break;
    case 'user_rating_DESC': $baseQuery .= " ORDER BY user_rating DESC"; break;
    case 'user_fee_ASC': $baseQuery .= " ORDER BY user_fee ASC"; break;
    case 'user_fee_DESC': $baseQuery .= " ORDER BY user_fee DESC"; break;
    case 'nearest': $baseQuery .= " ORDER BY $distance DESC"; break;
    default: $baseQuery .= " ORDER BY date_added DESC"; break;
}

// Final query
$finalQuery = $baseQuery . " LIMIT $initial_page, $num_per_page";
$counter = $initial_page + 1;

$getlist = $conn->prepare($finalQuery);

if ($getlist->execute()):
    $result = $getlist->get_result();

    while ($user = $result->fetch_assoc()):
        include("../contents/user-details.php");

        $user_image = !empty($user_image) ? $user_image : "https://placehold.co/400";
        $age = !empty($user_dob) ? date_diff(date_create($user_dob), date_create('today'))->y : "N/A";

        // Google API Distance
        $apiKey = $_ENV['GOOGLE_DISTANCE_MATRIX'] ?? "";
        $origin = $user_full_address ?? "";
        $destination = $spLocation ?? "";
        $distance = 0;
        if (!empty($origin) && !empty($destination)) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($origin) . "&destinations=" . urlencode($destination) . "&mode=driving&units=metric&language=en-US&key=" . $apiKey;
            $response = json_decode(file_get_contents($url), true) ?? null;
            if ($response && $response['status'] == 'OK') {
                $element = $response['rows'][0]['elements'][0] ?? null;
                if ($element && $element['status'] === 'OK') {
                    $distance = $element['distance']['value'] ?? ""; // meters
                }
            }
        }
        ?>

        <!-- ✅ Service Card -->
        <div class="">
            <div class="service-card shadow-sm rounded  h-100">
                <div class="service-avatar text-center mb-3">
                    <a class="text-decoration-none text-dark" href="details?id=<?= urlencode($user['id']) ?>">
                        <img src="<?= htmlspecialchars($user_image) ?>" class="rounded-circle img-fluid" style="width:120px;height:120px;object-fit:cover;" alt="User image">
                    </a>
                </div>
                <div class="service-info text-center">
                    <h6 class="service-name mb-1">
                        <a href="details?id=<?= urlencode($user['id']) ?>" class="fw-bold text-dark"><?= htmlspecialchars($user_name) ?></a>
                    </h6>
                    <p class="text-muted small mb-1">Age: <?= htmlspecialchars($age) ?> | Gender: <?= htmlspecialchars($user_gender) ?></p>
                    <p class="text-muted small mb-1">Location: <?= htmlspecialchars($user_location) ?></p>
                    <?php if ($distance > 0): ?>
                        <p class="text-success small mb-2"><?= htmlspecialchars(round($distance / 1000, 2)) ?> km away</p>
                    <?php endif; ?>
                </div>
                <div class="service-stats d-flex justify-content-around mt-3">
                    <!-- <span class="stat-item small"><i class="fas fa-comment text-danger"></i> 324 comments</span> -->
                    <span class="stat-item small"><i class="fas fa-heart text-danger"></i> <?= htmlspecialchars($user_likes) ?> Likes</span>
                    <span class="stat-item small"><i class="fas fa-eye"></i> <?= htmlspecialchars($user_views) ?> Views</span>
                </div>
                <div class="service-actions text-center mt-3">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-share"></i> <?= htmlspecialchars($user_shares) ?> shares
                    </button>
                </div>
            </div>
        </div>

        <?php
    endwhile;
    echo "</div>"; // close row
else:
    echo '<p class="text-center text-muted">No Artisans found.</p>';
endif;

// Pagination
$total_num_page = ceil($totalRecords / $num_per_page);
$radius = 2;
echo "<div class='text-center mt-4'>";
if ($page > 1) {
    $previous = $page - 1;
    echo '<a class="btn btn-sm btn-success mx-1 prev" id="' . $previous . '">&lt;</a>';
}
for ($i = 1; $i <= $total_num_page; $i++) {
    if (($i <= $radius) || ($i > $page - $radius && $i < $page + $radius) || ($i > $total_num_page - $radius)) {
        if ($i == $page) {
            echo '<a class="btn btn-sm btn-success active-button mx-1" id="' . $i . '">' . $i . '</a>';
        } else {
            echo '<a class="btn btn-sm btn-outline-success mx-1" id="' . $i . '">' . $i . '</a>';
        }
    } elseif ($i == $page - $radius || $i == $page + $radius) {
        echo "... ";
    }
}
if ($page < $total_num_page) {
    $next = $page + 1;
    echo '<a class="btn btn-sm btn-success mx-1 next" id="' . $next . '">&gt;</a>';
}
echo "</div>";
?>
