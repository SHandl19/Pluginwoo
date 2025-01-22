jQuery(document).ready(function ($) {
    $('.swatch-option').on('click', function () {
        var selectedVariant = $(this).data('variant');
        $('.swatch-option').removeClass('selected');
        $(this).addClass('selected');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'load_special_variation',
                variant: selectedVariant,
                product_id: $('input[name="product_id"]').val()
            },
            success: function (response) {
                if (response.success) {
                    $('.price').text(response.data.price);
                    $('.stock-status').text(response.data.stock > 0 ? 'Verfügbar' : 'Nicht verfügbar');
                }
            }
        });
    });
});
