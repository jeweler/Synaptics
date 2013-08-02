$(document).ready(function() {
    var section = $('#section-input');

    $('#search-buttons input').click(function() {
        var id = $(this).attr('id');
        section.val(id);
        $('#search-submit').trigger('click');
    });

});