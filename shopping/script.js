$(document).ready(function() {

    // Handle "Add to Cart" button click
    $(".add-to-cart").click(function() {
        var productId = $(this).closest(".product").data("id");

        $.ajax({
            url: 'add_to_cart.php',
            method: 'POST',
            data: { product_id: productId },
            success: function(response) {
                alert((response));
            },
            error: function() {
                alert("An error occurred while adding the product to the cart.");
            }
        });
    });

    // Handle "Buy Now" button click
    $(".buy-now").click(function() {
        var productId = $(this).closest(".product").data("id");

        $.ajax({
            url: 'buy_direct.php',
            method: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.redirect;
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("An error occurred while processing your purchase: " + error);
            }
        });
    });

    
    
});


;







