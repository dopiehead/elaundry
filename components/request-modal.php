<link rel="stylesheet" href="../assets/css/request-modal.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Modal Overlay -->
<div class="modal-overlay" id="requestModal">
  <div class="form-container position-relative">
    <span class="modal-close" id="toggleRequestModal">&times;</span>
    <h2>Send a request</h2>

    <!-- Service Request Form -->
    <form id="serviceRequestForm" method="POST">

    <input type="hidden" name="sp_id" id="sp_id" value="<?= htmlspecialchars($id) ?>" class="form-control" placeholder="Enter Service Provider ID" required>

      <!-- Number of Clothes -->
      <div class="mb-3">
        <label for="number_of_clothes" class="form-label  text-secondary">Number of Clothes</label>
        <input type="number" name="number_of_clothes" id="number_of_clothes" class="form-control" placeholder="Enter number of clothes" required>
      </div>

            <!-- Price -->
    <div class="mb-3">
        <label for="amount" class="form-label  text-secondary">Proposed Amount</label>
        <input type="number" name="amount" id="amount" step="750" min="750" class="form-control" placeholder="Enter Amount" required>
    </div>

      <!-- User Preference -->
      <div class="mb-3">
        <label for="user_preference" class="form-label  text-secondary">User Preference</label>
        <select name="user_preference" id="user_preference" class="form-select" required>
          <option value="">-- Select Preference --</option>
          <option value="wash_only">Wash Only</option>
          <option value="wash_and_iron">Wash & Iron</option>
          <option value="dry_clean">Dry Clean</option>
        </select>
      </div>

      <!-- Location -->
      <div class="mb-3">
        <label for="location" class="form-label text-secondary">Location</label>
        <input type="text" name="location" id="location" class="form-control" placeholder="Enter location" required>
      </div>

      <!-- Date -->
      <div class="mb-3">
        <label for="date" class="form-label  text-secondary">Date</label>
        <input type="date" name="date" id="date" class="form-control" required>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>

  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
    // Handle form submit
    $("#serviceRequestForm").on("submit", function(e){
        e.preventDefault();

        // Convert form to JSON
        let formData = {};
        $(this).serializeArray().forEach(function(item){
            formData[item.name] = item.value;
        });

        $.ajax({
            url: "../controller/requestController.php", // ✅ ensure correct path
            type: "POST",
            data: JSON.stringify(formData),  // send JSON
            contentType: "application/json", // tell PHP it’s JSON
            dataType: "json",                // expect JSON back
            beforeSend: function(){
                $("#serviceRequestForm button[type=submit]").prop("disabled", true).text("Submitting...");
            },
            success: function(res){
                if(res.success){  // ✅ backend returns JSON {success:true}
                    Swal.fire("✅ Success", res.message, "success");
                    $("#serviceRequestForm")[0].reset();
                    $("#requestModal").removeClass("active"); // close modal
                } else {
                    Swal.fire("⚠️ Notice", res.message, "warning");
                }
            },
            error: function(xhr, status, error){
                Swal.fire("❌ Error", "Something went wrong: " + error, "error");
            },
            complete: function(){
                $("#serviceRequestForm button[type=submit]").prop("disabled", false).text("Submit");
            }
        });
    });

    // Toggle modal
    $(document).on("click", "#toggleRequestModal", function () {
        $("#requestModal").toggleClass("active");
      
    });
});
</script>

