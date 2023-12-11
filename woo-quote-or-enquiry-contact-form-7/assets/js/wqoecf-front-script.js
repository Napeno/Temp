var loading_img_path = jQuery(".wqoecf-pop-up-box").attr("data-loader-path");
jQuery("body").on("click", ".wqoecf_enquiry_button", function () {
    jQuery("body").append('<div class="wqoecf_loading"><img src="' + loading_img_path + '" class="wqoecf_loader"></div>');
    var loading = jQuery(".wqoecf_loading");
    loading.show();
    var product_title = jQuery(this).attr("data-product-title");
    var product_price = jQuery(this).attr("data-product-price");
    var product_id = jQuery(this).attr("data-product-id");

    // Add console.log to check the value of product_id
    console.log('Product ID:', product_id);

    jQuery(".wqoecf-pop-up-box .wpcf7 > form")[0].reset();
    jQuery("div.wqoecf-pop-up-box .wpcf7 input[name='product-name']").val(product_title);
    jQuery('.wqoecf-pop-up-box div.wpcf7>form input[name="product-name"]').attr("readonly", true);
    jQuery("div.wqoecf-pop-up-box .wpcf7 input[name='product-price']").val(product_price);
    jQuery('.wqoecf-pop-up-box div.wpcf7>form input[name="product-price"]').attr("readonly", true);
    jQuery("div.wqoecf-pop-up-box .wpcf7 input[name='product-id']").val(product_id);
    jQuery('.wqoecf-pop-up-box div.wpcf7>form input[name="product-id"]').attr("readonly", true);
    loading.remove();

    jQuery(".wqoecf-pop-up-box").fadeIn();
});
