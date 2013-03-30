$(function(){
	$('.deletephoto').click(function(e){
		$.ajax({
			url : "/admin/delone-photos-"+$(e.target).attr('albumid')+"-"+ $(e.target).attr('id') + ".html",
			success : function(msg) {
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