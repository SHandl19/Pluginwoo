jQuery(document).ready(function ($) {
    $('#reload-special-variants').on('click', function () {
        const productId = $('input#post_ID').val();

        $.ajax({
            url: specialTabAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'reload_special_variants',
                product_id: productId,
                security: specialTabAjax.security // Nonce hinzufügen
            },
            success: function (response) {
                if (response.success) {
                    $('#special-variants-body').html(response.data.html);
                } else {
                    alert(response.data.message || 'Fehler beim Aktualisieren der Varianten.');
                }
            },
            error: function () {
                alert('Ein unerwarteter Fehler ist aufgetreten.');
            }
        });
    });
});
