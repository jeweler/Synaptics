SYnaptics
=========
Контроллеры находятся в папке controller и их названия должны заканчиваться на _controller.php<br>
Каждый контроллер должен быть потомком класса Controller<br>
У контроллера есть методы:<br>
	- setTitle(String s)  				Установить заголовок страницы -> s<br>
	- titlePush(String s) 				Добавить в конец заголовка страницы добавить ->s<br>
	- getArg(String s)    				Получить параметр s из ссылки<br>
	- argSet(String s)	 				Проверить, установлен ли параметр s из ссылки<br>
	- jsAdd(String s)     				Добавить на страницу JavaScript с src = s<br>
	- cssAdd(String s)    				Добавить на страницу CSS с href = s<br>
	- getTfold(array layout) 			Возвращает положение оформления <br>
	- setTemp(String name, Object n)	Устанавливает переменную name для оформления<br>
	- setCont(String name, Object n)  	Устанавливает переменную name для содержимого<br>
Каждому action'у контроллера соответствует файл содержимого(view).. Который находится в папке /view/{controller}/{action}.html<br>
В view можно использовать следующие команды шаблонизатора<br>
	- %render({file}, {varname}) 					Для каждого объекта массива {varname} выводит шаблон из /render/{file}.html <br>
	- %each {varname} as {newvar}%{somecode}%end%   Для каждого значения массива {varname} выводит код {somecode}, где можно использовать переменную {newvar}<br>
	- %linkto {controller} {action} {param}=>{$value}...% Генерирует и выводит ссылку на {controller}->{action} с параметрами<br>
	- %{somevar}% 									Выводит переменную<br>
	- %repeat {num}%{somecode}%end%					Повторяет {num} раз код {somecode}<br>
	- %if {express}%{somecode1}%else%{somecode2}%end% Выводит somecode1 или somecode2 взависимости от {express}. %else%{somecode2} необязательно<br>
Если файл view не создан, то %content% в файле оформления можно задать из controllera $this->content = "somecontent"<br><br>

Все маршруты для приложения находятся  в файле /core/routes.php где переменная routes должна быть массивом, а каждый элемент массива соответствует одному маршруту..<br><br>
Пример:<br>
Array('string' => 'press/{id}', 'controller'=>'index', 'action'=>'readPress')<br>
В данном случае при переходе по ссылке press/{someid}.html будет использован контроллер index, а action readPress.. причём в контроллере, можно получить {id} вызвав $this->getArg("id")<br>

