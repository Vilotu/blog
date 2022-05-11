Устанавливаем проект

    composer create-project laravel/laravel example-app

устанавливаем ui
    
    composer require laravel/ui
    
устанавливаем бутстрап нативно
    
    php artisan ui bootstrap

устанавливаем авторизацию 
    
    php artisan ui:auth

далее 

    npm install && npm run dev
    
повторить run dev

при ошибке WARNING in child compilations

    npm install autoprefixer@10.4.5 --save-exact
    
[Ссылка](https://stackoverflow.com/questions/72083968/1-warning-in-child-compilations-use-stats-children-true-resp-stats-child)

раcкомментировать в php.ini production

    extension=pdo_sqlite
    
в .env заменяем весь блок с подключением к базе на 
    
    DB_CONNECTION=sqlite

в папке database создаем файл database.sqlite

создаем таблицу со связанной моделью 

    php artisan make:model Post -m
    
аналогично создаем таблицы с моделями Category, Tags и сводную таблицу с моделью PostTag

#### Заполняем миграции

при отношении один ко многим например посты и категории
необходимо добавить форейн кей в таблицу где (много)
у поста может быть только одна категория 
а у категории может быть много постов

    $table->unsignedBigInteger('category_id')->nullable();    
     
    $table->index('category_id', 'post_category_idx');
    $table->foreign('category_id', 'post_category_fk')->on('categories')->references('id');
    
при отношении многие ко многим например посты и тэги 
форейн кей и idx добавляются в сводную таблицу 

    $table->unsignedBigInteger('post_id');
    $table->unsignedBigInteger('tag_id');

    //IDX
    $table->index('post_id', 'post_tag_post_idx');
    $table->index('tag_id', 'post_tag_tag_idx');
    //FK
    $table->foreign('post_id', 'post_tag_post_idx')->on('posts')->references('id');
    $table->foreign('tag_id', 'post_tag_tag_idx')->on('tags')->references('id');

Создаем явную привязку моделей к таблицам
так же добавляем правило позволяющее изменять данные в таблице

в модели Category добавляем

    protected $table = 'categories';
    protected $guarded = false;
    
аналогично в модели Post и Tag

    protected $table = 'posts';
    protected $guarded = false;

и так же в PostTag
названия таблиц как указано в миграциях

    protected $table = 'post_tags';
    protected $guarded = false;



делаем миграцию 
    
    php artisan migrate

### Для работы контроллеров обязательно

заходим в app/Providers/RouteServiceProvider.php
и раскомментируем сточку 

    protected $namespace = 'App\\Http\\Controllers';

Создаем контроллер с неймспейсом

    php artisan make:controller Main/IndexController

в контроллере создаем метод invoke()

Далее переносим верстку в views

для корректной работы подключаемых файлов стилей используем хелпер
{{ asset() }}

    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/loader.js') }}"></script>

#### Множественное копирование ctrl + shift + right

#### windows не понимает относительные пути к php extentions 
исправил прописав абсолютный путь 

    extension_dir="C:\full\path\to\php\ext"
    

