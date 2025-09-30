<?php 
require("../class/auth.php");
$auth = new Auth(new Database());
$user_id = $auth->getUserId();
$conn = $auth->getConnection();
if (!$auth->checkLogin()) {
    echo "<p class='text-warning'>Invalid user ID. Please login again.</p>";
    header("Location:../public/login");
    exit;
}
if($_SESSION['user_type']=='customer'){
    header("Location:profile");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Overview</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard/dashboard.css">
    <link rel="stylesheet" href="../assets/css/dashboard/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard/overview.css">
    <link rel="stylesheet" href="../assets/css/dashboard/settings.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include ("components/sidebar.php"); ?>

    <!-- Main Content -->
    <div class="main-content bg-secondary">

        <!-- Header -->
        <?php include("components/overview.php"); ?>

        <!-- Navigation Tabs -->
        <div class="tabs-nav">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#overview">Overview</a>
                </li>
            </ul>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon customers">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-menu">
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                    <?php
                    $getCustomer = $conn->prepare("SELECT * FROM sp_request WHERE sp_id = ? AND pending = 1 ");
                    $getCustomer->bind_param("i",$user_id);
                    if($getCustomer->execute()):
                        $customerResult = $getCustomer->get_result();
                        $numCustomer = $customerResult->num_rows;
                    endif;
                    ?>
                    <div class="stat-value"><?= htmlspecialchars($numCustomer) ?></div>
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>Up 18%</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon members">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-menu">
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                    <div class="stat-value">0</div>
                    <div class="stat-label">Monthly income</div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-down"></i>
                        <span>Up 20%</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon users">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-menu">
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                    <?php
                    $getPendingrequest = $conn->prepare("SELECT * FROM sp_request WHERE sp_id = ? AND pending = 0 ");
                    $getPendingrequest->bind_param("i",$user_id);
                    if($getPendingrequest->execute()):
                        $requestPendingresult = $getPendingrequest->get_result();
                        $numRequest = $requestPendingresult->num_rows;
                    endif;
                    ?>
                    <div class="stat-value"><?= htmlspecialchars($numRequest) ?></div>
                    <div class="stat-label">Requests</div>
                    <div class="user-avatars">
                        <div class="user-avatar" style="background: linear-gradient(45deg, #ff6b6b, #ee5a6f);"></div>
                        <div class="user-avatar" style="background: linear-gradient(45deg, #4ecdc4, #44a08d);"></div>
                        <div class="user-avatar" style="background: linear-gradient(45deg, #45b7d1, #96c93d);"></div>
                        <div class="user-avatar" style="background: linear-gradient(45deg, #f093fb, #f5576c);"></div>
                        <div class="user-avatar" style="background: linear-gradient(45deg, #4facfe, #00f2fe);"></div>
                    </div>
                </div>
            </div>

            <!-- Activity Section -->
            <div class="activity-section">
                <div class="activity-header">
                    <div>
                        <h3 class="activity-title text-capitalize">
                            <?= htmlspecialchars($_SESSION['user_type']) ?> Activity History
                            <span class="activity-total"><?= htmlspecialchars($numRequest) ?>  Total</span>
                        </h3>
                        <p class="activity-subtitle">Here you can track your vendor's performance everyday</p>
                    </div>
                    <div class="activity-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search Order" id="searchInput">
                        </div>
                        <div class="filter-dropdown">
                            <button class="dropdown-btn" onclick="toggleDropdown('statusFilter')">
                                <i class="fas fa-dollar-sign"></i>
                                <span>Paid</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <div class="filter-dropdown">
                            <button class="dropdown-btn" onclick="toggleDropdown('categoryFilter')">
                                <i class="fas fa-tags"></i>
                                <span>All Category</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <button class="clear-filter" onclick="clearFilters()">
                            Clear All <i class="fas fa-times"></i>
                        </button>
                        <button class="add-customer-btn" onclick="addCustomer()">
                             Search for Customer
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Customer Image</th>
                                <th>Customer Name</th>
                                <th>Number of people barbing</th>
                                <th>Customer preference</th>
                               
                                <th>Customer location</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        
                    
                        $getrequest = $conn->prepare("SELECT * FROM sp_request WHERE sp_id = ? ORDER BY pending DESC");
                        $getrequest->bind_param("i",$user_id);
                        if($getrequest->execute()):
                            $requestResult = $getrequest->get_result();
                            while($request = $requestResult->fetch_assoc()):

                                include("../contents/request-content.php");

                                $getcustomerDetails = $conn->prepare("SELECT id,user_name,user_image FROM user_profile WHERE id = ?");
                                $getcustomerDetails->bind_param("i",$request_customer_id);
                                if($getcustomerDetails->execute()){
                                    $resultCustomerdetails = $getcustomerDetails->get_result();
                                    while ($user = $resultCustomerdetails->fetch_assoc()) {
                                      include("../contents/user-details.php");
                                    }
                                }
                                ?>
                            <tr>
                                <td><div style='height:60px;width:60px;border-radius:50%;'><img class='w-100' src="<?= htmlspecialchars($user_image) ?>" alt=""></div></td>
                                <td><div><a><?= htmlspecialchars($user_name) ?></a></div></td>
                                <td><div class='text-capitalize'><?= htmlspecialchars($request_number_of_people_clothes) ?><div></td>
                                <td><div class='text-capitalize'><?= htmlspecialchars(preg_replace("/_/"," ",$request_user_preference)) ?></div></td>
                                <td><div class='text-capitalize'><?= htmlspecialchars($request_location) ?></div></td>
                                <td><div class="date"><?=htmlspecialchars($request_date) ?></div></td>
                                <td>
                                    <div>
                                        <?php if($request_status == 0): ?>
                                            <a id="<?= htmlspecialchars($request_id) ?>" class="status-badge paid btn-accept text-decoration-none">Accept</a>
                                        <?php else : ?>
                                            <a id="<?= htmlspecialchars($request_id) ?>" class="status-badge failed btn-reject text-decoration-none">Reject</a>
                                        <?php endif ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="row-menu">
                                       
                                        <div class='delete-container w-100'>
                                            <a class="text-danger btn border-danger rounded status-badge btn-delete" id="<?= htmlspecialchars($request_id) ?>">
                                                 Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
$(document).on("click", ".btn-accept", function () {
    const id = $(this).attr("id");
    if (id.length > 0 && confirm("Are you sure you want to accept this request ?")) {
        $.ajax({
            url: "../controller/acceptController",
            method: "POST",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                swal({
                    title: response.status === "success" ? "Success" : "Error",
                    icon: response.status === "success" ? "success" : "error",
                    text: response.message
                });
            }
        });
    }
});

$(document).on("click", ".btn-reject", function () {
    const id = $(this).attr("id");
    if (id.length > 0 && confirm("Are you sure you want to reject this request ?")) {
        $.ajax({
            url: "../controller/rejectController",
            method: "POST",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                swal({
                    title: response.status === "success" ? "Success" : "Error",
                    icon: response.status === "success" ? "success" : "error",
                    text: response.message
                });
            }
        });
    }
});

$(document).on("click", ".btn-delete", function () {
    const id = $(this).attr("id");
    if (id.length > 0 && confirm("Are you sure you want to delete this request ?")) {
        $.ajax({
            url: "../controller/deleteOfferController",
            method: "POST",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                swal({
                    title: response.status === "success" ? "Success" : "Error",
                    icon: response.status === "success" ? "success" : "error",
                    text: response.message
                });
            }
        });
    }
});
</script>

<script>
$(document).on("click", ".options", function(e) {
    e.preventDefault();
    $(".delete-container").removeClass("d-none");
    $(this).closest(".row-menu").find(".delete-container").toggleClass("d-none");
});
</script>

</body>
</html>