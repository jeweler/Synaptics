$(document).ready(function() {

    var filter_tpl = Mustache.compile($('#filter-tpl').html()),
            filter_name_tpl = Mustache.compile($('#filter-name-tpl').html()),
            feature_tpl = Mustache.compile($('#feature-tpl').html()),
            feature_range_tpl = Mustache.compile($('#feature-range-tpl').html()),
            feature_container = $('#feature-Values'),
            container = $('#filter-Values');

    $('#Product_subCategoryArr').on('change', function(e) {
        if (e.hasOwnProperty('added')) {
            add(e);
        }
        if (e.hasOwnProperty('removed')) {
            $('.sub_' + e.removed.id).remove();
        }
    });


    function add(e)
    {
        $.post('/admin/subCategories/getData', {id: e.added.id}, function(data) {
            data = $.parseJSON(data);
            for (var b in data.features) {
                if (data.features.hasOwnProperty(b)) {
                    if (data.features[b].format == 'range' || data.features[b].format == 'range_slash') {
                        var delimetr = data.features[b].format == 'range' ? '-' : '/';
                        feature_container.append(feature_range_tpl({index: b,
                            subCategory_id: e.added.id,
                            delimetr: delimetr,
                            name: data.features[b].name,
                            dimension: data.features[b].dimension
                        }));
                    }
                    else {
                        feature_container.append(feature_tpl({index: b,
                            subCategory_id: e.added.id,
                            name: data.features[b].name,
                            dimension: data.features[b].dimension
                        }));
                    }
                }
            }

            for (var a in data.filterValues) {
                if (data.filterValues.hasOwnProperty(a)) {
                    container.append(filter_name_tpl({name: a, subCategory_id: e.added.id}));

                    for (var i in data.filterValues[a]) {
                        if (data.filterValues[a].hasOwnProperty(i)) {
                            container.append(filter_tpl({
                                index: i,
                                name: data.filterValues[a][i],
                                subCategory_id: e.added.id
                            }));
                        }
                    }
                }
            }
        });
    }

});