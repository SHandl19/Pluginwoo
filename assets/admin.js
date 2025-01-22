jQuery(document).ready(function ($) {
    $('#reload-special-variants').on('click', function () {
        var productId = $('#post_ID').val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'reload_special_variants',
                product_id: productId
            },
            success: function (response) {
                if (response.success) {
                    $('#special-variants-body').html(response.data);
                } else {
                    alert('Fehler beim Laden der Varianten.');
                }
            }
        });
    });
});
