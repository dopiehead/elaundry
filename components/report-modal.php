
<!-- Modal Overlay -->
<div id="reportModal" class="report-modal-overlay d-none">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        
        <h2>Report User</h2>

        <form id="reportForm" method="post">
         
            <input type="hidden" name="vendor_name" value="<?= htmlspecialchars($user_name) ?>" required>
          
            <input type="hidden" name="vendor_email" value="<?= htmlspecialchars($user_email) ?>" required>
        
            <input type="hidden" name="user_type" value="<?= htmlspecialchars($user_type) ?>" required>
          
            <input type="hidden" name="sender_email" value="<?= isset($_SESSION['email']) ? filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL) : "" ?>">

            <label>Issue</label>
            <textarea name="issue" required></textarea>

            <button type="submit">Report</button>
        </form>
    </div>
</div>

<!-- jQuery + SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../assets/css/reportUser.css">

<script>
$(function(){
    // Open Modal
    $(document).on("click", "#openReportModal", function(){
        
        $("#reportModal").fadeIn();
        $("#reportModal").removeClass("d-none");
        $("#reportModal").addClass("d-flex");
    });

    // Close Modal
    $(document).on("click", ".close-modal, #reportModal",function(e){
        if(e.target !== this) return; // prevent closing when clicking inside modal-content
        $("#reportModal").fadeOut();
        $("#reportModal").addClass("d-none");
        $("#reportModal").removeClass("d-flex");
    });

    // ✅ AJAX Submit
    $("#reportForm").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "../controller/reportUserController",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function(){
                $("#reportForm button[type=submit]").prop("disabled", true).text("Reporting...");
            },
            success: function(response){
                if(response.success){
                    Swal.fire("Success", response.message, "success");
                    $("#reportForm")[0].reset();
                    $("#reportModal").fadeOut();
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function(xhr, status, error){
                Swal.fire("Error", "Request failed: " + error, "error");
            },
            complete: function(){
                $("#reportForm button[type=submit]").prop("disabled", false).text("Report");
            }
        });
    });
});
</script>
