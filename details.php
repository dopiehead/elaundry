<!DOCTYPE html>
<html lang="en">
<head>

    <title> Laundry</title>
    <?php include("components/links.php") ?>
    <link rel="stylesheet" href="assets/css/details.css">
    <link rel="stylesheet" href="assets/css/pricingcontroller.css">
    <link rel="stylesheet" href="assets/css/message-modal.css">
</head>
<body>
<?php include("components/message-modal.php") ?>
<?php include("components/nav.php") ?>
<?php include ("controller/detailsController.php") ?>
    <!-- Header Section -->
     <br><br>
    <div class="header-section mt-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="profile-container">
                        <div class="profile-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <div class="flex-grow-1 text-white">
                            <div class="character-illustration">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <?php if($payment_status == 1) : ?>
                    <a id="toggleModal"  class="request-btn">Send message</a>
                    <?php elseif(!$auth->isLoggedIn()): ?>
                        <a href="login?details=<?= urlencode($redirectUrl) ?>"  class="request-btn">Send message</a>
                        <?php else: ?>
                            <a href="payment_status"  class="request-btn">Send message</a>
                    <?php endif ?>
                </div>
            </div>
        </div>


    </div>
    <div class='user-container'>

           <img src="<?= htmlspecialchars($image) ?>" alt="">

    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Service Info -->
                 <br><br>
                <div class="bg-white rounded p-4 shadow-sm mb-4">
                    <h1 class="service-title"><?= htmlspecialchars($user_name) ?></h1>
                    <div class="service-meta">
    <span  class="text-dark btn-like"  data-id="<?= (int)$id ?>" 
        id="like-btn-<?= (int)$id ?>"  style="cursor:pointer;"
    >
        <i class="fa fa-thumbs-up"></i> 
        <span class="like-count" id="like-count-<?= (int)$id ?>">
            <?= htmlspecialchars($user_likes) ?>
        </span>
    </span> 
    likes · <?= htmlspecialchars($user_shares) ?> shares · <?= htmlspecialchars($countComments) ?> comments
</div>

                    <div class="rating mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span class="ms-2 text-muted"><?= (float)$user_rating ?></span>
                    </div>
                    <p class="description-text">
                    <?= htmlspecialchars($user_bio) ?>
                    </p>

                </div>

                <div class='d-flex justify-content-between'>
                <?php if ($payment_status == 1): ?>
    <!-- ✅ User has paid -->
    <button class="btn btn-request btn-info" id="toggleRequestModal">Request</button>

<?php elseif (!$auth->isLoggedIn()): ?>
    <!-- 🚪 Not logged in → redirect to login -->
    <a class="btn btn-request btn-info" 
       href="login?details=<?= urlencode($redirectUrl) ?>">
       Request
    </a>

<?php else: ?>
    <!-- 💳 Logged in but no active payment -->
    <a class="btn btn-request btn-info" href="payment_status">
        Request
    </a>
<?php endif; ?>
  
                  
                    <!-- Trigger button -->
                <?php if ($auth->isLoggedIn()): ?>
                 <button class='report-btn' id="openReportModal"> <i class='fa fa-flag'></i> Report User</button> 
                <?php else : ?>
                    <a class='report-btn text-decoration-none' href="login?details=<?= urlencode($redirectUrl) ?>"> <i class='fa fa-flag'></i> Report User</a> 
                <?php endif ?>
                </div>

                <!-- Galleries Section -->
                <div class="galleries-section">
                    <h2 class="galleries-title">Galleries</h2>

<div class="gallery-grid mb-3">
    <?php foreach ($firstTwo as $gallery): ?>
        <div class="gallery-item">
            <div class="gallery-placeholder">
                <img src="<?= htmlspecialchars($gallery['img']) ?>" alt="<?= htmlspecialchars($gallery['alt']) ?>">
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Last three -->
<div class="bottom-row">
    <?php foreach ($lastThree as $gallery): ?>
        <div class="gallery-item">
            <div class="gallery-placeholder">
                 <img src="<?= htmlspecialchars($gallery['img']) ?>" alt="<?= htmlspecialchars($gallery['alt']) ?>">
            </div>
        </div>
    <?php endforeach; ?>
</div>               
            </div>
        </div>
    </div>

    <br><br>

    <?php include ("controller/pricingController.php") ?>

    <!-- price break down goes here -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="comments-container">
                    <!-- Comments Title -->
                    <h3 class="comments-title">Comments</h3>
                    <!-- Comment Form -->
                    <div class="comment-form">
                        <form id="commentForm">
                        <div class="d-flex gap-2">
                            <textarea name="comment" class="comment-input flex-grow-1" placeholder="Drop a comment" rows="2"></textarea>
                        </div>
                        <input type="hidden" name="user_id" value='<?= htmlspecialchars($user_id) ?>'>
                        <input type="hidden" name="sender_email" value='<?= isset($_SESSION['email']) ? filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL): "" ?>'>
                        <input type="hidden" name="sender_name" value='<?= isset($_SESSION['name']) ? filter_var($_SESSION['name'], FILTER_SANITIZE_STRING): "" ?>'>
                        <div class="d-flex justify-content-end">
                            <button name='submit' type='submit' class="send-btn">Send</button>
                        </div>
                        </form>
                    </div>
                    
                    <!-- Comments List -->
                    <div class="comments-list">
                        <!-- Comment 1 -->
                        <?php if ($commentResult->num_rows > 0): ?>
    <?php while ($comments = $commentResult->fetch_assoc()): ?>
        <div class="comment-item">
            <div class="comment-avatar">
                <div style="width: 100%; height: 100%; background: linear-gradient(45deg, #8B4513, #D2691E); border-radius: 50%;"></div>
            </div>
            <div class="comment-content">
                <div class="comment-header">
                    <span class="commenter-name">
                        <?= htmlspecialchars($comments['sender_name']) ?>
                    </span>
                    <!-- <button class="connect-btn">Connect</button> -->
                </div>
                <div class="comment-text">
                    <?= htmlspecialchars($comments['comment']) ?>
                </div>
                <div class="comment-emojis">
                    <span class="emoji-reactions">❤️🔥🎈</span>
                </div>
                <div class="comment-actions">
                    <span class="comment-time"><?= $auth->timeAgo($comments['date']) ?></span>
                    <!-- <button class="comment-action">Like</button> -->
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
  
    <!-- footer goes here -->
     <?php include("components/footer.php") ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>

    <?php include("components/report-modal.php") ?>
    <?php include("components/request-modal.php") ?>
    <script>
$(function(){
    $("#commentForm").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "controller/addcommentController",   // your PHP backend
            type: "POST",
            data: $(this).serialize(),
            dataType: "json", // ✅ expect JSON
            beforeSend: function(){
                $("#commentForm button[type=submit]")
                    .prop("disabled", true)
                    .text("Posting...");
            },
            success: function(response){
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: response.message
                    });
                    $("#commentForm")[0].reset();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error){
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Something went wrong: " + error
                });
            },
            complete: function(){
                $("#commentForm button[type=submit]")
                    .prop("disabled", false)
                    .text("Post Comment");
            }
        });
    });
});
</script>
<script>
$(document).ready(function(){

    $(document).on("click", ".btn-like", function(e){
        e.preventDefault();

        let btn = $(this);
        let sp_id = btn.data("id"); // service provider ID
        let counter = $("#like-count-" + sp_id);

        $.ajax({
            url: "controller/likesController", // your backend endpoint
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ sp_id: sp_id }),
            beforeSend: function(){
                btn.css("opacity", "0.6"); // visual feedback
            },
            success: function(response){
                try {
                    let res = (typeof response === "string") ? JSON.parse(response) : response;

                    if(res.success){
                        Swal.fire("👍 Liked", res.message, "success");
                        // increment count visually
                        let newCount = parseInt(counter.text()) + 1;
                        counter.text(newCount);
                    } else {
                        Swal.fire("⚠️ Notice", res.message, "warning");
                    }
                } catch (err){
                    console.error("JSON Parse Error:", err);
                    Swal.fire("❌ Error", "Unexpected response.", "error");
                }
            },
            error: function(xhr, status, error){
                Swal.fire("❌ Error", "Something went wrong: " + error, "error");
            },
            complete: function(){
                btn.css("opacity", "1");
            }
        });
    });

});
</script>

<script>
$(document).on("click", "#toggleModal", function () {
    $("#messageModal").toggleClass("active"); // smooth toggle (can use .toggle() if you prefer)
   
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>