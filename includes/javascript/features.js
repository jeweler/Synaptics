$(document).ready(function() {

    var feature_tpl = Mustache.compile($('#feature-tpl').html()),
        container = $('#features-container');

    $('#add-feature').click(function() {
        container.append(feature_tpl({
            index: container.find('.row').length
        }));
    });


});

$('a.delete-feature').live('click', function(e) {
    $(this).parents('.row').remove();
});