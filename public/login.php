<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/login.css">

</head>
<body>
  <div class="container-fluid">
    <div class="row">
      
      <!-- Illustration Section -->
      <div class="col-lg-6 illustration-section text-center">
        <div class="illustration-container">
          <img src="../assets/images/auth.png" 
               alt="Illustration" 
               class="illustration-image">
        </div>
      </div>

      <!-- Login Section -->
      <div class="col-lg-6 login-section">
        <div class="login-card">
          <form id='login-form'>
            <h3 class="text-center mb-4 fw-bold text-dark">Welcome Back 👋</h3>

            <div class="mb-3">
              <label for="email" class="form-label">Email or Username</label>
              <input type="text" name="user_email" class="form-control" id="email" placeholder="Enter your username">
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="user_password" class="form-control" id="password" placeholder="••••••••">
            </div>
            
            <button type="submit" name="submit" id="submit" class="btn btn-login"><span style='display:none;' class="spinner-border text-white"></span><span class='signin-note'>Login</span></button>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="signup-text">Don't have an account? </span>
                <a href="create-account" class="signup-link">Sign up</a>
              </div>
              <a href="Forgot-password" class="text-secondary small text-decoration-none">Forgot password?</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


  <script>
$(document).on("click", "#submit", function(e) {
    e.preventDefault();

    $(".spinner-border").show();
    $(".signin-note").hide();
    $('.btn-login, .btn-forget').prop('disabled', true); // disable button(s)

    const formData = $("#login-form").serialize();

    if (formData.length > 0) {
        $.ajax({
            url: "../controller/loginController",
            type: "POST",
            data: formData,
            success: function(response) {
                $(".spinner-border").hide();
                $(".signin-note").show();
                $('.btn-login, .btn-forget').prop('disabled', false); // re-enable
                response = response.trim();

                if (response === "1") {
                    // ✅ redirect if success
                    window.location.href = "../protected/dashboard";
                } else {
                    // ❌ show error
                    swal("Notice", response || "Login failed. Please try again.", "warning");
                }
            },
            error: function(xhr, status, error) {
                $(".spinner-border").hide();
                $(".signin-note").show();
                $('.btn-login, .btn-forget').prop('disabled', false); // re-enable
                console.error("AJAX Error:", error);
                swal("Notice", "Something went wrong. Please try again.", "warning");
            }
        });
    } else {
        swal("Notice","Please fill in the required fields.","warning");
    }
});
</script>

</body>
</html>
