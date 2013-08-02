$(document).ready(function() {

    var filter_tpl = Mustache.compile($('#filter-tpl').html()),
        container = $('#filters-container');

    $('#add-filter-value').click(function() {
        container.append(filter_tpl({
            index: container.find('.row').length
        }));
    });

});

$('a.delete-feature').live('click', function(e) {
    $(this).parents('.row').remove();
    e.preventDefault();
});
