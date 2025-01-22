jQuery(document).ready(function ($) {
    $('#reload-special-variants').on('click', function (e) {
        e.preventDefault();

        var $form = $('#reload-variants-form');
        var data = $form.serialize();

        $('#update-message').text('Lade...');

        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                $('#special-variants-body').html(response.data.html);
                $('#update-message').text(response.data.message).css('color', 'green');
            } else {
                $('#update-message').text(response.data.message || 'Fehler').css('color', 'red');
            }
        }).fail(function () {
            $('#update-message').text('Ajax-Anfrage fehlgeschlagen.').css('color', 'red');
        });
    });
});
