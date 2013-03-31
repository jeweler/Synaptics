SYnaptics
=========
Контроллеры находятся в папке controller и их названия должны заканчиваться на _controller.php
Каждый контроллер должен быть потомком класса Controller
У контроллера есть методы:
	- setTitle(String s)  				Установить заголовок страницы -> s
	- titlePush(String s) 				Добавить в конец заголовка страницы добавить ->s
	- getArg(String s)    				Получить параметр s из ссылки
	- argSet(String s)	 				Проверить, установлен ли параметр s из ссылки
	- jsAdd(String s)     				Добавить на страницу JavaScript с src = s
	- cssAdd(String s)    				Добавить на страницу CSS с href = s
	- getTfold(array layout) 			Возвращает положение оформления 
	- setTemp(String name, Object n)	Устанавливает переменную name для оформления
	- setCont(String name, Object n)  	Устанавливает переменную name для содержимого
Каждому action'у контроллера соответствует файл содержимого(view).. Который находится в папке /view/{controller}/{action}.html
В view можно использовать следующие команды шаблонизатора
	- %render({file}, {varname})% 					Для каждого объекта массива {varname} выводит шаблон из /render/{file}.html 
	- %each {varname} as {newvar}%{somecode}%end%   Для каждого значения массива {varname} выводит код {somecode}, где можно использовать переменную {newvar}
	- %linkto {controller} {action} {param}=>{$value}...% Генерирует и выводит ссылку на {controller}->{action} с параметрами
	- %{somevar}% 									Выводит переменную
	- %repeat {num}%{somecode}%end%					Повторяет {num} раз код {somecode}
	- %if {express}%{somecode1}%else%{somecode2}%end% Выводит somecode1 или somecode2 взависимости от {express}. %else%{somecode2} необязательно
Если файл view не создан, то %content% в файле оформления можно задать из controllera $this->content = "somecontent"

Все маршруты для приложения находятся  в файле /core/routes.php где переменная routes должна быть массивом, а каждый элемент массива соответствует одному маршруту..
Пример:
Array('string' => 'press/{id}', 'controller'=>'index', 'action'=>'readPress')
В данном случае при переходе по ссылке press/{someid}.html будет использован контроллер index, а action readPress.. причём в контроллере, можно получить {id} вызвав $this->getArg("id")

