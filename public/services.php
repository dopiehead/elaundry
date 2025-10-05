
<!DOCTYPE html>
<html lang="en">
<head>
    <title>eLaundry</title>
    <?php include("../components/links.php") ?>
    <link rel="stylesheet" href="../assets/css/products.css">
<body>
    <?php include("../components/nav.php") ?>
    <br><br>
    <div class="container-fluid py-4 ">
        <div class="row">

            <!-- Search Section -->
            <div class="col-12">
                <div class="search-section">
                    <form class="search-form">

                        <div style='box-shadow:0 0 3px rgba(0,0,0,0.5);' class="rounded rounded-pill mb-3 p-2">
                           <input type="search" name="q" id="q" class="form-control border-0" placeholder="Search for laundry or washer">
                        </div>

                        <div class="row g-3">
                            <!-- <div class="col-md-3">
                                <label class="form-label text-muted">How many cloth you want to wash</label>
                                <select class="form-select">
                                    <option>2</option>
                                    <option>5</option>
                                    <option>10</option>
                                    <option>20</option>
                                </select>
                            </div> -->
                            <div class="col-md-4">
                                <label class="form-label text-muted">Enter your location</label>

                                <?php include("../contents/states.php") ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Service type</label>
                                <select name='user_type' id='user_type' class="form-select">
                                     <option value=''>All</option>
                                    <option value='laundry'>Laundry shop</option>
                                    <option value='washer'>Washer</option>
                                  
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value='male'>Male</option>
                                    <option value='female'>Female</option>
                                    
                                </select>
                            </div>

                        </div>

 
                        <div class="row g-3 mt-2">

                            <div class="col-md-4">
                                <label class="form-label text-muted">Preference</label>
                                <select name='user_preference' class="form-select">
                                    <option value='home_service'>Home service</option>
                                    <option value='shop_owner'>Shop owner</option>
                                    <option value='both'>Both</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted">Sort</label>
                                <select name='orderBy' id="orderBy" class="form-select">
                                     <option value="date_added_ASC">Date Added (Oldest First)</option>
                                     <option value="date_added_DESC" selected>Date Added (Newest First)</option>
                                     <option value="user_name_ASC">User Name (A–Z)</option>
                                     <option value="user_name_DESC">User Name (Z–A)</option>
                                     <option value="age_ASC">Age (Youngest First)</option>
                                     <option value="age_DESC">Age (Oldest First)</option>
                                     <option value="user_rating_ASC">Rating (Lowest First)</option>
                                     <option value="user_rating_DESC">Rating (Highest First)</option>
                                     <option value="user_fee_ASC">Fee (Lowest First)</option>
                                     <option value="user_fee_DESC">Fee (Highest First)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted">Cleaning Style</label>
                                <select name='user_services' id="user_services" class="form-select">
                                    <option value="wash & fold">Wash & Fold</option>
                                    <option value="dry cleaning">Dry Cleaning</option>
                                    <option value="express service">Express Service</option>
                                </select>
                            </div>

                            <!-- <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="search-btn submit-search">Search</button>
                            </div> -->
                        </div>

                        <div class="price-note">
                            <strong>Price (₦750 per cloth)</strong><br>
                            1500 - this price is calculated automatically by the system based on how many cloth you put
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="row">

        <?php
          $getpricing = $conn->prepare("
          SELECT MAX(user_fee) AS max_price, MIN(user_fee) AS min_price  
          FROM user_profile  
          WHERE verified = 1 AND user_type != 'customer'
      ");
      
      if ($getpricing) {
          $getpricing->execute();
          $getpricing->bind_result($max_price, $min_price);
          $getpricing->fetch();
      }
        ?>
            <!-- Sidebar -->
            <div class="col-lg-3 order-lg-1 order-1 filters-mobile">
                <div class="sidebar">
                    <div class="filter-section">
                        <h5>Price</h5>
                        <div class="price-range d-flex flex-wrap gap-4">
                            <div class='d-flex flex-wrap'>
                              <label class='text-danger' for="">From :</label>
                              <input type="number" name='price_from' class='form-control' value="<?= $min_price ?>">
                            </div>

                            <div class='d-flex flex-wrap'>
                              <label class='text-danger' for="">To :</label>
                              <input type="number" name="price_to" class='form-control' value="<?= $max_price ?>">
                            </div>


                        </div>

                    </div>
                                      
                </div>


            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9 order-lg-2 order-2">
                <div class="list-container">
                   <div class='text-center w-100'>
                        <span style='height:150px; width:150px;' class='spinner-border text-primary fs-3'></span>
                   </div>
                </div>
            </div>
        </div>
    </div>




    <?php include("../components/footer.php") ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
   <!-- ✅ Load jQuery before your script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
     

<?php 
$location = isset($_GET['location']) ? htmlspecialchars($_GET['location'], ENT_QUOTES, 'UTF-8') : '';
if ($location): ?>
<script>
$(function(){
    const user_location = <?= json_encode($location) ?>;
    setTimeout(function(){
        // check if location exists in select options
        if ($(".locationFilter option[value='" + user_location + "']").length > 0) {
            $(".locationFilter").val(user_location).trigger("change");
        }
    }, 500); // adjust delay if needed (ms)
});
</script>
<?php endif ?>



<?php 
$service = isset($_GET['service']) ? htmlspecialchars($_GET['service'], ENT_QUOTES, 'UTF-8') : '';
if ($service): ?>
<script>
$(function(){
    const user_service = <?= json_encode($service) ?>;
    setTimeout(function(){
        // check if location exists in select options
        if ($("#user_services option[value='" + user_service + "']").length > 0) {
            $("#user_services").val(user_service).trigger("change");
        }
    }, 500); // adjust delay if needed (ms)
});
</script>
<?php endif ?>


    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
$(".list-container").load("../controller/serviceController.php");
$(document).ready(function() {

    // Search input
    $('#q').on('keyup', function(e) {
        e.preventDefault();
        fetchData($('#q').val());
    });

    // Location filter
    $('.locationFilter').on('change', function(e) {
        e.preventDefault();
        fetchData($('#q').val(), $(this).val());
    });

    // Service type filter
    $('#user_type').on('change', function(e) {
        e.preventDefault();
        fetchData($('#q').val(), $('.locationFilter').val(), $(this).val());
    });

    // Preference filter
    $('#user_preference').on('change', function(e) {
        e.preventDefault();
        fetchData($('#q').val(), $('.locationFilter').val(), $('#user_type').val(), $(this).val());
    });

    // Gender filter (select)
    $('#gender').on('change', function(e) {
        e.preventDefault();
        fetchData($('#q').val(), $('.locationFilter').val(), $('#user_type').val(), $('#user_preference').val(), $(this).val());
    });

    // Cleaning service filter
    $('#user_services').on('change', function() {
        fetchData($('#q').val(), $('.locationFilter').val(), $('#user_type').val(), $('#user_preference').val(), $('#gender').val(), $(this).val());
    });

    // Price range
    $('#price_from, #price_to').on('keyup change', function() {
        fetchData(
            $('#q').val(),
            $('.locationFilter').val(),
            $('#user_type').val(),
            $('#user_preference').val(),
            $('#gender').val(),
            $('#user_services').val(),
            $('#price_from').val(),
            $('#price_to').val()
        );
    });

    // Order by
    $('#orderBy').on('change', function(e) {
        e.preventDefault();
        fetchData(
            $('#q').val(),
            $('.locationFilter').val(),
            $('#user_type').val(),
            $('#user_preference').val(),
            $('#gender').val(),
            $('#user_services').val(),
            $('#price_from').val(),
            $('#price_to').val(),
            $(this).val()
        );
    });

    // Pagination
    $(document).on("click", ".btn-pagination", function(e) {
        e.preventDefault();
        fetchData(
            $('#q').val(),
            $('.locationFilter').val(),
            $('#user_type').val(),
            $('#user_preference').val(),
            $('#gender').val(),
            $('#user_services').val(),
            $('#price_from').val(),
            $('#price_to').val(),
            $('#orderBy').val(),
            $(this).attr("id")
        );
    });

    // Main AJAX function
    function fetchData(q, locationFilter, user_type, user_preference, gender, user_services, price_from, price_to, orderBy, page) {
        $(".spinner-border").show();

        $.ajax({
            url: "../controller/serviceController.php",
            type: "POST",
            data: {
                q: q || "",
                locationFilter: locationFilter || "",
                user_type: user_type || "",
                user_preference: user_preference || "",
                gender: gender || "",
                user_services: user_services || "",
                price_from: price_from || "",
                price_to: price_to || "",
                orderBy: orderBy || "",
                page: page || 1
            },
            success: function(data) {
                $(".spinner-border").hide();
                $(".list-container").html(data).show();
            },
            error: function(xhr, status, error) {
                $(".spinner-border").hide();
                console.error("AJAX Error:", status, error);
                $(".list-container").html("<div class='alert alert-danger w-100'>An error occurred while loading the data. Please try again later.</div>");
            }
        });
    }

});
</script>

</body>
</html>
