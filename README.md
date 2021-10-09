# Imagenator

Позволяет создать простенький сайт, не используя сложные фреймворки, Imagenator это мини-фраймворк который поддерживает роутинг (POST/GET), в котором есть встроеный шаблонизатор, встроенные миграции, модели и PHPunit

## Установка и Автозагрузка

Этот пакет сопровождается файлом [composer.json](./composer.json), что позволяет использовать 
[Composer](https://getcomposer.org/) для его инсталляции и автозагрузки.

Так же можно установить его загрузив [исходные коды](https://github.com/kalininDanil17Y/Imagenator/archive/refs/heads/main.zip) пакета в виде Zip архива 
или клонировав этот репозиторий. Все компоненты этого пакета загружают 
зависимости автоматически.

Перед использованием рекомендуется выполнить тестирование с помощью утилиты 
[PHPUnit](https://phpunit.readthedocs.io/ru/latest/) вызвав ее в корневом каталоге пакета.

<code>composer exec --verbose phpunit tests -- --coverage-text</code>

## Документация

### 1.0 Структура папок
Директория **app** содержит базовые классы для работы фреймворка<br>
В директории **public** находятся все зависимые файлы, такие как css, js, fonts и подобные<br>
Директория **src** хранит дополнительные файлы фреймворка, модели, контроллеры и миграции<br>

---
### 1.1 /public/index.php
Главный файл с которого всё начинается, подключает framework
Загружает <code>.env</code> и другое

---
### 1.2 Создание роута
В файле **/app/App.php** указываются роуты, и их обработчики
Пример создания

```php
  $router->addRoute('GET', '/', ['UploadController', 'showPage']);
```
Синтаксис
```php
  $router->addRoute('метод(GET или POST)', 'путь', ['название класса', 'метод класса']);
```
Для небольших страниц можно использовать функции вместо контроллера, пример
```php
  $router->addRoute('GET', '/', 'Imagenator\Controller\MyFunction');
```
<code>НЕ СОВЕТУЮ ИСПОЛЬЗОВАТЬ, т.к это не очень удобно, и не лучшее решение, лучше использовать анонимные функции</code><br><br>
Анонимные функции
```php
  $router->addRoute('POST', '/', function ($response, $request) {
    /*some code*/
  });
```
В контроллер передаются два параметра <code>$response</code> и <code>$request</code>

---
### 1.3 Создание конроллера
В папке **/src/Controller** создаются все контроллеры<br>
Пример контроллера (SecondController.php)
<br>
```php
namespace Imagenator\Controller;

class SecondController
{
    /**
     * @param $response
     * котроллер для формирования ответа
     * @param $request
     * содержит в себе информацию о запросе
     * @return mixed
     */
    public function blablabla($response, $request)
    {
        return $response->setBody("Hello))"); //Вывести на экран "Hello))"
    }
}
```
В метод передаются два параметра <code>$response</code> и <code>$request</code>

<code>$request</code> - содержит в себе всё об запросе ([подробнее](https://github.com/symfony/http-foundation))<br>
<code>$response</code> - содержит набор методов для формирования ответа

```no-highlight
$response и $request так-же передаются функциям в качестве параметров
```

---
### 1.4 Формирование ответа

```php
/**
* @param $response
* @param $request
* @return mixed
*/
public function form($response, $request)
{
    return $response->view('form') // Выбрать шаблон form из папки /templates, (без .twig)
        ->setStatus(201) //Указать что код ответа 201
        ->setHeader('Content-type', 'text/html;') //Указать Content-type
        ->setBody('sample text') //Выводит на экран текст (отменяет действие view)
        ->redirect('example.com', 'code (optional)'); //создаёт редирект
    /**
     * ВАЖНО вернуть $response отбратно используя return
     */
}
```
<code>ВАЖНО вернуть $response отбратно используя return</code>

Пример контроллера
```php
public function page($response, $request)
{
    $name = $request->request->get('name'); //получаем из POST поле 'name'
    return $response->view('index', ['name' => $name]) //Возвращаем шаблон с параметрами (в нашем случае с $name)
        ->setStatus(202); //Устанавливаем статус 202
}
```

---
### 1.5 База данных
Для работы с базой, необходимо настроить подключение к самой базе, в файле <code>.env</code> в корне проекта<br>
Директория с моделями <code>/src/Models/</code>
Документация по созданию и работе с моделями [тут](https://laravel.com/docs/8.x/eloquent)

---
### 1.6 Миграции
Директория с миграциями <code>/src/Migrations/</code>
Документация по настройке [тут](https://book.cakephp.org/phinx/0/en/index.html)
