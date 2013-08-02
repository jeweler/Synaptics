$(document).ready(function() {

     var recommend_tpl = Mustache.compile($('#recomend-product').html());
     var recommend_container = $('#recommend-block');
     var ids = [];

	$("#tree").jstree({
		"json_data" : {
			"ajax" : {
				"url" : "/admin/recommendProducts/getTreeData",
				"data" : function (n) {
					return { id : n.attr ? n.attr("id") : 0 };
				}
			}
		},
		"plugins" : [ "themes", "json_data", "ui" ]
	}).bind("select_node.jstree", function (e, data) {
        if (data.rslt.obj.data("class") == 'product') {
            var id  = data.rslt.obj.data("id");
            if($.inArray(id, ids) == -1) {
                recommend_container.append(recommend_tpl({
                    name: data.args[0].innerText,
                    index: id
                }));
                ids.push(id);
            }
        }
    });


});

$('a.delete').live('click', function(e) {
    $(this).parents('.recommend').remove();
    e.preventDefault();
});
