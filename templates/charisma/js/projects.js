$(function(){
	$('.deletephoto').click(function(e){
		$.ajax({
			url : "/admin/project-del-"+$(e.target).attr('albumid')+"-"+ $(e.target).attr('id') + ".html",
			success : function(msg) {
				console.log(msg);
				 $(e.target).parent().fadeOut(function(){
					$(e.target).parent().remove();
				 })
			}
		})
	})
	$('.addphoto').click(function(){
		$("input[type='file']").last().after($("<input>").attr({'type': 'file', 'name':'links[]'})).after("<br>");
	})
})