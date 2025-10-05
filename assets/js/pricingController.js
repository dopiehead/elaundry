
// Toggle option cards (no soap/iron anymore, but keep in case of future extra options)
function toggleOption(card, type) {
    $(card).toggleClass('active');
    const checkbox = $(card).find('input');
    checkbox.prop('checked', $(card).hasClass('active'));
}

// Save price data
function saveData(element, id) {
    let newValue = $(element).text().trim().replace(/,/g, '');

    if(isNaN(newValue) || newValue === '') {
        swal("Invalid Input", "Please enter a valid number.", "warning");
        $(element).text($(element).attr("data-old"));
        return;
    }

    const originalText = $(element).text();
    $(element).html('<span class="spinner"></span>');

    $.ajax({
        url: "../controller/savedata-pricing",
        type: "POST",
        data: { id: id, value: parseFloat(newValue) },
        success: function(response) {
            if(response == 1) {
                const formatted = parseFloat(newValue).toFixed(2);
                $(element).text(formatted).attr("data-old", formatted);

                swal({
                    title: "Success!",
                    text: "Price updated successfully",
                    icon: "success",
                    timer: 2000,
                    buttons: false
                });

                updateTotal();
            } else {
                $(element).text(originalText);
                swal("Error", response, "error");
            }
        },
        error: function() {
            $(element).text(originalText);
            swal("Error", "Failed to save data. Please try again.", "error");
        }
    });
}

// Update total price
function updateTotal() {
    let total = 0;
    $(".item-price .editable").each(function(){
        let val = parseFloat($(this).text().replace(/,/g, ''));
        if(!isNaN(val)) total += val;
    });
    $("#total-price").text(total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

// Handle laundry item selection
let selectedBadges = [];
$(document).on("click", ".laundry_items", function(e){
    e.preventDefault();
    const item = $(this).data("item");

    if (selectedBadges.includes(item)) {
        selectedBadges = selectedBadges.filter(b => b !== item);
        $(this).removeClass("selected");
    } else {
        selectedBadges.push(item);
        $(this).addClass("selected");
    }

    $("#selected_badges").val(selectedBadges.join(","));
});

// Submit selection
function submitSelection() {
    const selected = $("#selected_badges").val(); // e.g. "cotton,hoodie"
    const selectedBadges = selected ? selected.split(",") : []; // array

    if (selectedBadges.length === 0) {
        swal("Notice", "Please select at least one item", "info");
        return;
    }

    swal({
        title: "Confirm Submission",
        text: `You have selected ${selectedBadges.length} item(s)`,
        icon: "info",
        buttons: ["Cancel", "Submit"],
    }).then((willSubmit) => {
        if (willSubmit) {
            $.ajax({
                type: "POST",
                url: "../controller/priceProcess",
                data: {
                    selected_badges: selectedBadges.join(",") // keep consistent with PHP
                },
                dataType:"json",
                success: function(response){
                    if (response.status === "success") {
                        // Join all messages into one string
                        let msg = response.messages.join("\n");

                        swal("Success!", msg, "success");

                        $('#parent').load(location.href + " #child");

                    } else {
                        swal("Error", "Something went wrong", "error");
                    }
                },
                error: function() {
                    swal("Error", "Server error occurred", "error");
                }
            });
        }
    });
}


// Format numbers on page load
$(document).ready(function() {
    updateTotal();
});
$(document).on("click", ".delete-service", function(e) {
    e.preventDefault();

    if (!confirm("Do you want to delete this?")) {
        return false;
    }

    const id = $(this).attr("id");

    if (id && id.length > 0) {
        $.ajax({
            url: "../controller/delete-service", // ✅ ensure .php
            type: "POST",
            data: { id: id },
            dataType:"json",
            success: function(response) {
                if (response.status === "success") {
                    swal("Success", response.message, "success");

                    // ✅ remove deleted row/item from DOM
                    $(`#${id}`).closest(".service-row").fadeOut(500, function() {
                        $(this).remove();
                    });

                    $('#parent').load(location.href + " #child");

                } else {
                    swal("Notice", response.message, "warning");
                }
            },
            error: function() {

                swal("Error", "Something went wrong", "error");
            }
        });
    }
});


