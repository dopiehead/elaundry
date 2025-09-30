
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eLaundry - Details</title>
  <?php include("../components/links.php") ?>
  <link rel="stylesheet" href="../assets/css/details.css">
  <link rel="stylesheet" href="../assets/css/nav.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
  <link rel="stylesheet" href="../assets/css/contact.css">

</head>
<body>

  <?php include("../components/nav.php") ?>
  <?php include("../components/infoController.php") ?>
  <br><br>

  <div class="container d-flex flex-md-row flex-column contact-container">

    <!-- Notes Section -->
    <div class="notes col-md-5 align-items-center full-height">
      <p>
        Welcome to <strong>eLaundry</strong> – your modern solution for stress-free laundry.  
        We provide <strong>convenience</strong>, <strong>quality</strong>, and <strong>timely delivery</strong> for all your laundry needs.
      </p>
    </div>

    <!-- Contact Section -->
    <div class="contact-section col-md-7">
      <h6>Contact Us</h6>

      <form method="post" id="contactForm">
        <div class="form-group">
          <input type="text" name="fullname" id='fullname' placeholder="Your Name" value="<?= htmlspecialchars($user_name) ?? "" ?>" required>
        </div>

        <div class="form-group">
          <input type="text" name="subject" id='subject' placeholder="Your Subject" required>
        </div>

        <div class="form-group">
          <input type="email" name="email" id='email' placeholder="Your Email" <?= htmlspecialchars($user_email) ?? "" ?> required>
        </div>

        <div class="form-group">
          <textarea name="message" id='message' placeholder="Your Message..." required></textarea>
        </div>

        <button class="btn-submit" type="submit" name="submit">Send Message</button>
      </form>
    </div>

  </div>
  <br><br>

  <!-- Footer -->
  <?php include("../components/footer.php") ?> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- ✅ jQuery (make sure this is included before script) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function(){

  $("#contactForm").on("submit", function(e){
    e.preventDefault(); // stop normal submit

    // Collect values
    const fullname = $.trim($("#fullname").val());
    const email    = $.trim($("#email").val());
    const message  = $.trim($("#message").val());
    const subject  = $.trim($("#subject").val());

    // ✅ Basic validation
    if(fullname.length < 3){
      alert("Please enter your full name (at least 3 characters).");
      return false;
    }

    if(email === "" || !/^\S+@\S+\.\S+$/.test(email)){
      alert("Please enter a valid email address.");
      return false;
    }

    if(message.length < 5){
      alert("Message must be at least 5 characters long.");
      return false;
    }

    // ✅ Send via AJAX
    $.ajax({
      url: "../controller/contactController",
      type: "POST",
      data: {
        fullname: fullname,
        email: email,
        message: message,
        subject:subject,
        submit: true
      },
      beforeSend: function(){
        $(".btn-submit").prop("disabled", true).text("Sending...");
      },
      success: function(response){
        alert("Message sent successfully!");
        $("#contactForm")[0].reset();
        $(".btn-submit").prop("disabled", false).text("Send Message");
      },
      error: function(xhr, status, error){
        alert("An error occurred: " + error);
        $(".btn-submit").prop("disabled", false).text("Send Message");
      }
    });

  });

});
</script>

</body>
</html>
