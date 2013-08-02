$(function() {

	if (window.innerWidth > 1000) {
		$("body").append("<div class='upButton' id='upButton'> <div class='left' ></div><span class='right'>Вверх</span></div>");
		var elem = document.getElementById('upButton')
		$("body div.upButton").hide();
		
		
		var scrlPnt = 0;
		$(".upButton").click(function() {
			if (scrlPnt != 0) {
				$(window).scrollTop(scrlPnt);
				$("body div.upButton div.left").removeClass("reverse");
				$(".upButton span.right").text("Bверх");
				scrlPnt = 0;
			} else {
				scrlPnt = $(window).scrollTop();
				$("body div.upButton div.left").addClass("reverse");
				$(".upButton span.right").text("Вниз");
				$(window).scrollTop(1);
			};
		});
		
		
		$(window).scroll(function() {
			
			
			if ($(window).scrollTop() > 70) {
				$("body div.upButton").show();
				$("body div.upButton div.left").removeClass("reverse");
				$(".upButton span.right").text("Bверх");
				scrlPnt = 0;
				var dspHeight = window.innerHeight;
				elem.style.cssText = "height: " + dspHeight + ";"



				$(window).scroll(function() {
					
					
					if ((window.innerHeight + $(window).scrollTop()) >= (document.height - 33)) {
						var scrlBot = (window.innerHeight + $(window).scrollTop()) - (document.height - 33);
						dspHeight = dspHeight - scrlBot - 19;
						elem.style.cssText = " height: " + dspHeight + ";";
						dspHeight = window.innerHeight;
					};
					
					if ($(window).scrollTop() < 29) {
						var scrlTop = 24 - $(window).scrollTop();
						elem.style.cssText = " height: " + dspHeight + "; top:" + scrlTop + ";";
						dspHeight = window.innerHeight;
					};
					
					
					if ($(window).scrollTop() == 0) {
						$("body div.upButton").hide();
						scrlPnt = 0;
					};
					
				});
			};
		});
	}
});
