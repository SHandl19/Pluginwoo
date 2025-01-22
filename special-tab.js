$('#reload-special-variants').on('click', function () {
    const productId = $('#post_ID').val(); // Produkt-ID holen
    $.ajax({
        url: ajaxurl, // AJAX-Endpunkt
        method: 'POST',
        data: {
            action: 'reload_special_variants',
            product_id: productId,
            security: woocommerce_special_product.nonce // Security-Token
        },
        success: function (response) {
            if (response.success) {
                $('#special-variants-body').html(response.data.html); // Tabelle aktualisieren
            } else {
                alert(response.data.message || 'Fehler beim Laden der Varianten.');
            }
        },
        error: function () {
            alert('Fehler beim Laden der Varianten.');
        }
    });
});
