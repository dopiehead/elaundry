<!-- Locations Section -->
<section class="locations-section">
    <div class="container">
        <h2 class="section-title">We have people to help with your laundry anywhere you are</h2>
        
        <div class="location-tags">
            <?php
          require_once __DIR__ . '/../engine/connection.php';

            // ✅ Fetch all unique states
            $getstates = $con->prepare("SELECT DISTINCT state FROM states_in_nigeria ORDER BY state ASC");
            $getstates->execute();
            $result = $getstates->get_result();

            while ($row = $result->fetch_assoc()) {
                $st = $row['state'];

                // ✅ Count number of services/vendors in that state (optional)
                $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM user_profile WHERE user_location = ?");
                $countStmt->bind_param("s", $st);
                $countStmt->execute();
                $countRes = $countStmt->get_result();
                $countRow = $countRes->fetch_assoc();
                $serviceCount = $countRow['total'] ?? 0;
                $countStmt->close();
            ?>
                <a href="services?location=<?= urlencode($st) ?>" class="location-tag text-capitalize">
                    <?= htmlspecialchars($st) ?><?= $serviceCount > 0 ? " ($serviceCount)" : "" ?>
                </a>
            <?php } ?>
        </div>
    </div>
</section>
