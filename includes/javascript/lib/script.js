
function setLastPadding(){
	var t = $('.shop-menu th:last ul').width()-$('.shop-menu th:last').outerWidth();
	$(".shop-menu ul:last").css("left","-"+t+'px');
}

function setLiSize() {
	var t = 100/($(".shop-menu th").length)+"%";
	$(".shop-menu th, td").width(t);
	$(".shop-menu th .drop-down li").width("auto");
}

$(function(){
	if ($('.filters-area ul ').size() == 1) { $('.filters-area').hide()};
	$('a.fancy-box').fancybox();
	setLiSize();
	var tsp = ($(".shop-menu th").height()-$(".shop-menu div").height())/2+$(".shop-menu div").height()+2;
	//setLastPadding();
	$(".drop-down").css("top",tsp);
	$("#list-table").dataTable({
		"aaSorting": [],
		"bAutoWidth": false, // Disable the auto width calculation 
		"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength" : -1,
		"bPaginate": false


	  } );
	   
	$("#item-names").dataTable({
	"aaSorting": [],
	"bAutoWidth": false,
	"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
	"iDisplayLength" : -1,
	"bPaginate": false
	});
		
	var items = $("#item-names td");
	items.click(function(){
		items.removeClass("active");
		$(this).addClass("active");
	});
	
	$(".item-info .small").click(function(){
		var mas = $(this).attr('src').split('/');
		$(".item-info .small").removeClass('active');
		$(this).addClass('active');
		$(".item-info #big").attr('src',"/images/products//big/"+mas[4]);
	});
	$(".shield").parent().css("position","relative");
	$(".notification-big, .notification-small").parent().css("position","relative");
	$("table th .notification-small, table th .notification-big").css("visibility","hidden");
	
	if ($('#slides').length) {
		$('#slides').slides({
			play : 5000,
			pause : 5000,
			hoverPause : true,
		});
	}
	
	
	$(".accordion div.title").addClass("active");
	$(".accordion div.title:last").removeClass("active");
    $(".accordion div.text:last").hide();
    $(".accordion div.title").click(function(){
 
  $(this).next("div.text").slideToggle("slow");
  $(this).toggleClass("active");
     });
});

