# Imagenator

Запустить сервер <code>php -S localhost:8080</code><br>

На <code>GET: /</code> находится форма, данные с формы отправляются на <code>POST: /</code>
И выводятся.
Используюется один контроллер <code>IndexController</code>
<br><br>

На <code>GET: /hellYeah</code> находится вывод <code>dump($request)</code>, используется функция
<br>
<br>
Встроен шаблонизатор <code>Twig</code>, файлы шаблонов находятся в папке src/Views/
<br>
Настройка роутеров в файле <code>src/Routers.php</code>