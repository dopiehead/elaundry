<?php
$total = 0;
$page = isset($page) && !empty($page) ? filter_var($page,  FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
if(!$page):
?>
<!-- Dashboard Content -->
<div class="dashboard-content p-4">
    <!-- Page Header -->
    <div class="page-header">
        <h2>
            <div class="header-icon">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                Price List Management
                <div class="subtitle">
                    <i class="fas fa-info-circle"></i>
                    Click on prices to edit them instantly
                </div>
            </div>
        </h2>
    </div>

    <!-- Item Selection Card -->
    <div class="selection-card">
        <div class="section-title">
            <i class="fas fa-tshirt"></i>
            Select Laundry Items
        </div>
        
        <div class="badge-container">
            <?php 
            $badges = [
                'sweat_shirt' => 'Sweat Shirt',  'cotton'  => 'Cotton',
                'hoodie'      => 'Hoodie',       'towel'   => 'Towel',   'trouser'   => 'Trouser',
                'undies'      => 'Undies',       't_shirt' => 'T-Shirt', 'bed_sheet' => 'Bed Sheet',
                'short'       => 'Short'
            ]; 

            foreach ($badges as $key => $label) {
                echo "<span data-item='".$key."' class='laundry-badge laundry_items'>" 
                     . $label . "</span>";
            }
            ?>
        </div>

        <!-- Hidden field to store selected badges -->
        <input type="hidden" id="selected_badges" name="selected_badges">

        <!-- Options Section -->
        <!-- <div class="section-title">
            <i class="fas fa-cog"></i>
            Additional Services
        </div> -->

        <!-- <div class="options-grid">
            <div class="option-card" onclick="toggleOption(this, 'soap')">
                <input type="radio" name="soap" class='soap' value='soap' id="soap-option">
                <label for="soap-option">
                    <i class="fas fa-soap"></i> I will provide soap & chemicals
                </label>
            </div>
            
            <div class="option-card" onclick="toggleOption(this, 'iron')">
                <input type="radio" name="iron" class='iron' value='iron' id="iron-option">
                <label for="iron-option">
                    <i class="fas fa-iron"></i> Add ironing service
                </label>
                <span class="option-price">+₦500</span>
            </div>
        </div> -->

        <div class="submit-section">
            <button type="submit" class="btn-submit" onclick="submitSelection()">
                <i class="fas fa-check-circle"></i> Submit Selection
            </button>
        </div>
    </div>

 <?php endif ?> 

    <!-- Pricing Table Card -->
    <div class="pricing-card">
        <div class="section-title">
            <i class="fas fa-list-ul"></i>
            Current Price List
        </div>

        <div class="pricing-table">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-tag"></i> Item Name</th>
                        <th><i class="fas fa-money-bill-wave"></i> Price (₦)</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php
            
                $getitems = $conn->prepare("
                    SELECT * FROM laundry_items 
                    WHERE user_id = ?
                ");
                $getitems->bind_param("i", $user_id);
                $getitems->execute();
                
                $result = $getitems->get_result();
                if($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $total += (float)$row['item_price'];
                ?>
                    <tr class='service-row' id="<?= $row['id'] ?>">
                        <td class="item-name text-capitalize">
                            <i class="fas fa-circle" style="font-size: 6px; color: #667eea;"></i>
                            <?= htmlspecialchars( str_replace("_"," ",$row['item_name'])) ?>
                        </td>
                        <td class="item-price">
                            <span class="editable"  contenteditable="true"
                                data-old="<?= number_format((float)$row['item_price'], 2) ?>"
                                onblur="saveData(this, '<?= $row['id'] ?>');">
                                <?= number_format((float)$row['item_price'], 2) ?>
                            </span>
                        </td>
                        <?php if(!$page): ?>
                        <td>
                            <a style='cursor:pointer;' class='delete-service' id="<?= $row['id'] ?>"><i class='fa fa-trash'></i></a>
                        </td>
                        <?php endif ?>
                    </tr>
                <?php endwhile;
                else:
                 echo"Price is not set yet.";
                endif;
                ?>
                <?php $getitems->close(); ?>

                <tr class="total-row">
                    <td><strong><i class="fas fa-calculator"></i> TOTAL</strong></td>
                    <td class="item-price">
                       <div id='parent'>
                            <div id='child'>
                                <strong class="total-amount">₦<span id="total-price"><?= number_format((float)$total, 2) ?></span> </strong>
                            </div>
                       </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="../assets/js/pricingController.js"></script>


