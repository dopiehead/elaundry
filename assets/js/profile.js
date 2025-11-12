
function openTab(evt, tabName) {
    $(".tabcontent").hide(); // hide all
    $(".tablinks").removeClass("active"); // remove active
    $("#" + tabName).show(); // show clicked tab
    evt.currentTarget.classList.add("active");
}
document.getElementById("defaultOpen").click();

$('.location').on('change', function () {
    var location = $(this).val();
    $.post("../engine/get-lga", { location: location }, function (data) {
        $('#lg').html(data);
    });
});

$('#editpage-details').on('submit', function (e) {
    e.preventDefault();
    $("#loading-image").show();

    $.ajax({
        type: "POST",
        url: "../engine/edit-page",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
            $("#loading-image").hide();
            if (response.status === "success") {
                Swal.fire("Success", response.message, "success");
                $("#editpage-details")[0].reset();
            } else {
                Swal.fire("Error", response.message || "Unknown error.", "error");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#loading-image").hide();
            Swal.fire("Error", "Request failed: " + textStatus, "error");
        }
    });
});

function cancel() {
    $("#editpage-details")[0].reset();
}

$('#editpage-form').on('submit', function (e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to change this?")) return;

    $("#loading-image").show();
    
    $.ajax({
        type: "POST",
        url: "../changeprofilepic",
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function (response) {
            $("#loading-image").hide();
            if (response.includes("1")) {
                Swal.fire("Success", "Image has been changed", "success");
                $("#editpage-form")[0].reset();
            } else {
                Swal.fire("Error", response, "error");
            }
        },
        error: function (xhr, status, error) {
            $("#loading-image").hide();
            Swal.fire("Error", "Request failed: " + error, "error");
        }
    });
});


function changeBackground(obj) {
$(obj).removeClass("bg-success");
$(obj).addClass("bg-danger");
$(obj).addClass("simple");

}

function save_data(obj, id, column) {
var customer = {
id: id,
column: column,
value: obj.innerHTML
}
$.ajax({
type: "POST",
url: "../controller/savedata-pricing",
data: customer,
dataType: 'json',
success: function(data){
   if (data) {

   swal({
       title:"Success",
       text:"Record saved",
       icon:"success",
   });  
    
     $(obj).removeClass("bg-danger");
     $(obj).removeClass("simple");  
     $(".table_client span").removeClass("border-bottom border-secondary");   
      
   }
               
   else{
       
       swal({
           icon:"error",
           title:"Oops...",
           text:"Record was not saved"
       });
   }
}
});
};
