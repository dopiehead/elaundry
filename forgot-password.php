<!DOCTYPE html>
<html lang="en">
<head>
   <?php include("components/links.php") ?>
  <title>Forgot Password</title>
  <link rel="stylesheet" href="assets/css/forgot-password.css">
</head>
<body>
  <div class="container-fluid">
    <!-- Title -->
    <!-- <h2 class="page-title">Forgot Password</h2> -->

    <div class="row flex-grow-1">
      <!-- Illustration Section -->
      <div class="col-lg-6 illustration-section">

        <div class="illustration-container">
          <img src="assets/images/auth.png" alt="Illustration" class="illustration-image">
        </div>
      </div>

      <!-- Form Section -->
      <div class="col-lg-6 login-section">
        <div class="login-card">
          <form id="forget-form">
            <div class="mb-3">
              <label for="email" class="form-label">Email or Username</label>
              <input type="text" name="user_email" class="form-control" id="email" placeholder="Enter your email or username">
            </div>
            <button type="submit" name="submit" id="submit" class="btn btn-login btn-forget">
                <span style='display:none;' class='spinner-border text-white'></span>
                <span class='signin-note'>Submit</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery (latest version from official CDN) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

       <script>
$(document).on("click", "#submit", function(e) {
    e.preventDefault();
    $(".spinner-border").show();
    $(".signin-note").hide();
    $('.btn-forget').prop('disabled', true);
    const formData = $("#forget-form").serialize();

    if (formData.length > 0) {
        $.ajax({
            url: "controller/forgetController.php",
            data: formData,
            type: "POST",
            success: function(response) {
                $(".spinner-border").hide();
                $(".signin-note").show();
                $('.btn-forget').prop('disabled', flase);
                response = response.trim(); // clean whitespace

                if (response === "1") {
                    // ✅ redirect if success
                   swal({
                    "Success","Reset link sent to your email","success"
                   })
                } else {
                    // ❌ show error
                    alert("Notice",response  || "Registration failed. Please try again.","warning");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                alert("Something went wrong. Please try again.");
            }
        });
    } else {
        alert("Please fill in the required fields.");
    }
});
</script>

</body>
</html>
