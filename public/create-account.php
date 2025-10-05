<?php 
require("../class/auth.php");
$auth = new Auth(new Database());
$conn = $auth->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("../components/links.php") ?>
    <title>Sign Up Form</title>
    <link rel="stylesheet" href="../assets/css/create-account.css">
</head>
<body>
    <div class="container-fluid">
        <h1 class='fw-bold mt-4 text-center'>Join us</h1>
        <div class="signup-container">
            <div class="row g-0">
                <!-- Left side - Form -->
                <div class="col-md-6">
                    <form id="signupForm" enctype="multipart/form-data">
                        <div class="signup-form">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="user_email" class="form-control" placeholder="you@example.com" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Name</label>
                                <input type="text" name="user_name" class="form-control" placeholder="Javier Johnson" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input type="password" name="user_password" class="form-control" placeholder="••••••••" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Re-type Password</label>
                                <input type="password" name="cpassword"  class="form-control" placeholder="••••••••" required>
                            </div>
                            
                            <div class="tag-container">
                                <div class="tag" data-value="User">User</div>
                                <div class="tag" data-value="Washer">Washer</div>
                                <div class="tag" data-value="Laundry">Laundry</div>
                            </div>

                            <input type="hidden" name="user_type" id="user_type">
                            
                            <button type="submit" class="btn btn-signup mt-3">Sign up</button>
                            
                            <div class="signin-link">
                                Already have an account? <a href="#">Sign in</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Right side - Profile Upload -->
                <div class="col-md-6">
                    <div class="profile-section">
                        <div class="profile-upload">
                            <div class="profile-avatar">
                                <img id="previewImage" src="" alt="Profile Preview" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;">
                            </div>
                            <div class="profile-camera">
                                <i class="fas fa-camera"></i>
                                <!-- Hidden file input -->
                                <input id="user_image" type="file" name="user_image" accept="image/*" style="display:none;">
                            </div>
                        </div>
                        <button type="button" class="btn upload-btn">Upload profile</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
$(function(){
    // Single tag selection
    $(".tag").on("click", function(){
        $(".tag").removeClass("active");
        $(this).addClass("active");
        $("#user_type").val($(this).data("value"));
    });

    // Trigger file input when button clicked
    $(".upload-btn").on("click", function(e){
        e.preventDefault();
        $("#user_image").click();
    });

    // Show preview inside img tag
    $("#user_image").on("change", function(){
        let file = this.files[0];
        if(file){
            let reader = new FileReader();
            reader.onload = function(e){
                $("#previewImage").attr("src", e.target.result).show();
            }
            reader.readAsDataURL(file);
        }
    });

    // Handle form submit with file upload
    $("#signupForm").on("submit", function(e){
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "../controller/registerController",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response == 1){

                swal("Success","Registration successful","success");

                 $("#signupForm")[0].reset();
                 
                }

                else{
                    swal("Notice",response,"warning"); 
                }
            },
            error: function(){
                swal("Error","Error while signing up.","error");
            }
        });
    });
});
</script>
</body>
</html>
