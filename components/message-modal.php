<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="modal" id="messageModal">
  <div class="form-container modal-content">
    <span class="modal-close" id="modal-close">&times;</span>
    <h2 class='modal-header'>Send a Message</h2>

    <form id="messageForm" class="message-form">
      <div>
        <label for="sender_email">Your Email</label>
        <input type="email" id="sender_email" name="sender_email"
          value="<?= isset($_SESSION['email']) ? filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL) : ''; ?>"
          placeholder="Enter your email">
      </div>   

      <input type="hidden" id="receiver_email" name="receiver_email" value="<?= htmlspecialchars($user_email) ?>">
      <input type="hidden" id="subject" name="subject" value="<?= 'I want a ' . htmlspecialchars($user_type) ?>">

      <div>
        <label for="compose">Message</label>
        <textarea id="compose" name="compose" placeholder="Write your message"></textarea>
      </div>

      <button name="submit" type="submit" class="btn-submit">Send</button>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
    $("#messageForm").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "../controller/messageController",
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function(){
                $("#messageForm button[type=submit]").prop("disabled", true).text("Sending...");
            },
            success: function(res){
                if(res == 1){
                    Swal.fire("✅ Success", "Message sent successfully", "success");
                    $("#messageForm")[0].reset();
                } else {
                    Swal.fire("Notice", res, "warning");
                }
            },
            error: function(xhr, status, error){
                Swal.fire("❌ Error", "Something went wrong: " + error, "error");
            },
            complete: function(){
                $("#messageForm button[type=submit]").prop("disabled", false).text("Send");
            }
        });
    });

    // Optional: Toggle modal
    $("#toggleModal").on("click", function() {
        $("#messageModal").addClass("modal-active");
    });
    
    $("#modal-close").on("click",function(){
      $("#messageModal").removeClass("modal-active");
    });




});
</script>

