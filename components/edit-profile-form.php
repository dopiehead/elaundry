<?php
// Make sure $user, $about, $service, $user_phone, etc. are defined before including this file.
?>
<form id="editpage-details" class="card p-4">
    <h5 class="card-title mb-3">Edit Profile</h5>
    <input type="hidden" name="user_type" value="<?= htmlspecialchars($user['user_type'] ?? $_SESSION['user_type']) ?>">

    <div class="mb-3">
        <input type="text" name="user_name" class="form-control" placeholder="<?php echo $_SESSION['user_type'] !== 'customer' ? 'Business ' : ''; ?>Name"
               value="<?= htmlspecialchars($user['user_name'] ?? '') ?>">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md">
            <input type="password" name="user_password" class="form-control" placeholder="New Password">
        </div>
        <div class="col-md">
            <input type="password" name="cpassword" class="form-control" placeholder="Confirm Password">
        </div>
    </div>

    <?php if ($_SESSION['user_type'] !== 'customer'): ?>
        <div class="mb-3">
            <input type="text" name="bank_name" class="form-control" placeholder="Bank Name"
                   value="<?= htmlspecialchars($bank_name ?? '') ?>">
        </div>
        <div class="mb-3">
            <input type="text" name="account_number" class="form-control" placeholder="Account Number"
                   value="<?= htmlspecialchars($account_number ?? '') ?>">
        </div>
    <?php endif; ?>

    <h6>Contact Information</h6>
    <div class="row g-3 mb-3">
        <div class="col-md">
            <input type="text" name="country" class="form-control" placeholder="Country"
                   value="<?= htmlspecialchars($user['country'] ?? 'Nigeria') ?>">
        </div>
        <div class="col-md">
            <input type="text" name="user_phone" class="form-control" placeholder="Phone number"
                   value="<?= htmlspecialchars($user_phone ?? '') ?>">
        </div>
        <div class="col-md">
            <input type="text" name="whatsapp" class="form-control" placeholder="WhatsApp"
                   value="<?= htmlspecialchars($whatsapp ?? '') ?>">
        </div>
    </div>

    <h6>Address Details</h6>
    <div class="mb-3">
        <select name="user_location" class="form-select location text-capitalize">
            <option value="">Select State</option>
           <?php include("../contents/states.php") ?>
        </select>
    </div>

    <span id="lg" class="d-block mb-3"></span>

    <div class="mb-3">
        <input type="text" name="user_address" class="form-control" placeholder="Enter full address"
               value="<?= htmlspecialchars($user_address ?? '') ?>">
    </div>

    <?php if ($_SESSION['user_type'] !== 'customer'): ?>




        <h6>About Your Organisation</h6>
        <div class="mb-3">
            <textarea class="form-control" name="about" rows="3"
                      placeholder="About Your Organization"><?= htmlspecialchars($about ?? '') ?></textarea>
        </div>


        <h6>Select Service Type</h6>  
        <div class="mb-3">
          <select class="form-control" name="services[]" id="">
                <option value="home_service">Home Service</option>
                <option value="shop_owner">Shop Owner</option>
            </select>
        </div>

        <h6>Availability</h6>
        <div class="row g-3 mb-3">
            <div class="col-md">
                <select name="day" class="form-select">
                    <option value="">Select Day</option>
                    <?php
                    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    foreach ($days as $d) {
                        $sel = (isset($day) && $day === $d) ? "selected" : "";
                        echo "<option value=\"$d\" $sel>$d</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md">
                <input type="time" name="opening_time" class="form-control" placeholder="Opening Time"
                       value="<?= htmlspecialchars($opening_time ?? '') ?>">
            </div>
            <div class="col-md">
                <input type="time" name="closing_time" class="form-control" placeholder="Closing Time"
                       value="<?= htmlspecialchars($closing_time ?? '') ?>">
            </div>
        </div>
    <?php endif; ?>

    <h6>Social Media</h6>
    <div class="row g-3 mb-3">
        <div class="col-md">
            <input type="url" name="facebook" class="form-control" placeholder="Facebook URL"
                   value="<?= htmlspecialchars($facebook ?? '') ?>">
        </div>
        <div class="col-md">
            <input type="url" name="twitter" class="form-control" placeholder="Twitter URL"
                   value="<?= htmlspecialchars($twitter ?? '') ?>">
        </div>
        <div class="col-md">
            <input type="url" name="linkedin" class="form-control" placeholder="LinkedIn URL"
                   value="<?= htmlspecialchars($linkedin ?? '') ?>">
        </div>
        <div class="col-md">
            <input type="url" name="instagram" class="form-control" placeholder="Instagram URL"
                   value="<?= htmlspecialchars($instagram ?? '') ?>">
        </div>
    </div>

    <div id="loading-image" class="text-center mb-3" style="display: none;">
        <span class="spinner-border text-success fs-2"></span>
    </div>

    <div class="d-flex gap-3">
        <button type="button" class="btn btn-danger" onclick="cancel()">Cancel</button>
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</form>
