$(function(){
	$('form').on('submit', function(){
		$.ajax({
			type: "POST",
			data: "login="+$('input[name="username"]').val()+"&password="+$('input[name="password"]').val(),
			dataType : "json",
			url : "/admin/login.html",
			success : function(msg) {
				if(msg.state == 'error'){
					if(msg.num == 1){
						$('.alert-info').css('color','red').text('Логин/пароль не верный');
					}else if(msg.num == 2){
						$('.alert-info').css('color','red').text('Слишком много попыток входа');
					}
				}else{
					$('.alert-info').css('color','green').text('Успешная авторизация');
					window.location = "/admin/";
				}
			}
		})
		return false;
	})
})
