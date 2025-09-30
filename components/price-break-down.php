<div class="pricing-section">
        <!-- Header -->
        <h1 class="section-title">Price breakdown for each wear</h1>
        
        <!-- Location Selection -->
        <div class="location-section">
            <div class="location-label">Enter your location</div>
            <select class="location-select">
            <?php

               require("../engine/connection.php");

               $getstates = $con->prepare("SELECT DISTINCT state FROM states_in_nigeria ORDER BY state ASC");
               $getstates->execute();
               $result = $getstates->get_result();

                while ($row = $result->fetch_assoc()) {
                $st = $row['state']; ?>
                <option value='<?= htmlspecialchars($st) ?>'><?= htmlspecialchars($st) ?></option>
                
            <?php } ?>
            </select>
        </div>

        <!-- Options -->
        <div class="options-section">
            <div class="option-item">
                <div class="radio-custom checked"></div>
                <span>I will provide the soap and chemicals</span>
            </div>
            <div class="option-item">
                <div class="radio-custom"></div>
                <span>I want ironing with the wash (₦500)</span>
            </div>
        </div>

        <!-- Pricing Table -->
        <div class="pricing-table">
        <?php
$items = [
    ["Sweat shirt", 500, "Cotton", 500], ["Hoodie", 500, "Towel", 500],
    ["Trouser", 500, "Undies", 500],  ["T-shirt", 500, "Short", 500],
    ["Towel", 500, "Trouser", 500],  ["Undies", 500, "T-shirt", 500],
    ["Bed sheet", 500, "Short", 500],
];
?>

<?php foreach ($items as $row): ?>
    <div class="pricing-row">
        <div class="item-name"><?= htmlspecialchars($row[0]) ?></div>
        <div class="item-price">₦<?= number_format($row[1]) ?></div>
        <div class="item-name" style="margin-left: 40px;"><?= htmlspecialchars($row[2]) ?></div>
        <div class="item-price">₦<?= number_format($row[3]) ?></div>
    </div>
<?php endforeach; ?>

        </div>

        <!-- Call to Action Section -->
        <div class="cta-section w-100">
            <div class="cta-content">
                <div class="cta-image"></div>
                <div class="cta-text">
                    <h2 class="cta-title">Are you looking for someone to pickup something for you?</h2>
                    <p class="cta-subtitle">Just with a click you can find someone</p>
                    <a href='../public/services' class="btn btn-primary">Go to search</a>
                    
                </div>
            </div>
        </div>
    </div>
