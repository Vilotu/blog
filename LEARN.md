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
    
Создаем CRUD

для категорий для тегов и для постов

сначала создаем Create потом Read затем Update затем Delete

#### Create
создаем роут затем котроллер затем создаем переход на роут в view

#### Store
при создании store контроллера необходимо создать реквест

    php artisan make:request Admin/Category/StoreRequest

Папка Requests находиться в Http так как это относится к Http запросам
Request отсеивает ненужные нейминги и принимает только те что указаны в методе rules()

StoreRequest в методе autorize() первым делом меняем return true;

в метод rules() добавляем
указываем правило required для элементов необходимых к заполнению
и проверяем тип данных
    
    return [
        'title' => 'required|string'
    ];

Затем в StoreController на вход метода __invoke вставляем класс реквест

    public function __invoke(StoreRequest $request)

проверяем приходит ли что либо 
помещаем 
    
    $date = $request->validated();
    
dd($data);

в create.blade.php в форме после указания роута добавить

    method='POST'
и так же обязательно добавить токен 

    @csrf

так же в input указываем name='title'
так как реквест проверяет это 
названия этим полям необходимо давать так как указано в базе

в StoreController в методе invoke
после получаение $data = $request->validated()
отправляем полученное в базу методом firstOrCreate
 проверяет если существует значение возвращает его если нет создает
 (позволяет избежать дублирования)

    Category::firstOrCreate($data);
    
возвращяем редирект на индекс

    return redirect()->route('admin.category.index');

[создание миграции для удаления столбца в таблице](https://youtu.be/FMpJ8-5pnUQ?list=PLd2_Os8Cj3t8StX6GztbdMIUXmgPuingB&t=539)

в create.blade.php под полем input размещаем 

    @error('title')
      <div class="text-danger">Необходимо для заполнения</div>
    @enderror
павильнее было бы указать специальную переменную {{ message }} но она на английском


для того чтоб получить id и title категорий в view, необходимо получить коллекцию категорий в контроллере
затем передать их во вью в методе компакт, и в самом view сделать @foreach 

eсли роут получает какой то агрумент в post/{{$category}}
то в контроллере необходимо получить это __invoke(Category $category)

UpdateController

    public function __invoke(UpdateRequest $request,Category $category)
    {
        $data = $request->validated();
        $category->update($data);
        return view('admin.categories.show', compact('category'));
    }

в вью страницы edit помимо @csrf токена необходимо указать 

    method:"POST"
    
и так же добавить 

    @method('PATCH')

#### Удаление категорий


Добавляем soft delete

    php artisan make:migration add_column_soft_deletes_to_categories_table

в созданной миграции добавляем в методе up 

            $table->softDeletes();
            
и в методе down
    
            $table->dropSoftDeletes();

в модель Category добавляем трейт

    use SoftDeletes;

далее проверяем делаем

    php artisan migrate
    php artisan migrate:rollback
    
при ошибке Class 

    'Doctrine\DBAL\Driver\AbstractSQLiteDriver' not found

выполняем 

    composer require doctrine/dbal
в ошибках указано какие модули надо раскоментировать в php.ini
так же добавил в файл composer.json

    "require": {
    "ext-curl": "^7.4"
    }
    
а затем нужно сделать composer update


#### Delete

в роут добавляем

    Route::delete('/{category}', 'DeleteController')->name('admin.category.delete');

в DeleteController

    class DeleteController extends Controller
    {
        public function __invoke(Category $category)
        {
            $category->delete();
            return redirect()->route('admin.category.index');
        }
    }
    
редирект помогает не цеплять по новой передаваемые обьекты а пропускает по тому контроллеру в котором они уже переданны

необходима форма для работы удаления

        <form action="{{ route('admin.category.delete', $category->id) }}"
               method="post">
            @csrf
            @method('DELETE')
            <button type="submit" class:"border-0 bg-transparent">
                 <i class="fas fas fa-trash text-danger" role="button"></i>
            </button>
        </form>
    

Crud для тегов
копируем и редактируем нейпспейсы и переменные
роуты, контроллеры, вью, и реквесты

Добавляем софт делит в таблицу теги

    php artisan make:migration add_soft_delete_to_tags_table

в модель добавляем 

    use SoftDeletes;

Для того чтоб при сообщении о незаполненном поле
не сбрасывались введенные данные используем хелпер
{{ old('title') }}
в imput вставляем в value="
а в textarea между ><



[Отображение имени выбраного к загрузке изображения](https://youtu.be/oCwP0PsHmUk?list=PLd2_Os8Cj3t8StX6GztbdMIUXmgPuingB&t=131)

для того чтоб форма могла принимать изображение 
необходимо добавить 

    enctype="multipart/form-data"

Хранение изображений
из получаемого из формы обьекта достаем сгенерированное название файла
и с помощью класса 
    
    Storage::put('/images/, $previewImage)
сохраняем файл по указанному пути storage/images/some.jpg

        $data['preview_image'] = Storage::put('/images', $data['preview_image']);;

Проверка на существование в реквестах

                                если существует в таблице категории,id
            'category_id' => 'required|exists:categories,id'

для того чтобы подставить нужную (категорию) в список 
добавляем условие в теле тега сравнивая с помощью хелпера old()

                    <option
                        {{ old('category_id') ==  $category->id ? ' selected' : '' }}
                        value="{{ $category->id }}">{{ $category->title }}
                    </option>



многие ко многим
для того чтоб при редактировании постов теги попадали в свою отдельную таблицу
добавляем в модели Post метод tags()

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id');
    }

В StoreController получаем теги запроса

        $tagIds = $data['tag_ids'];
затем удаляем их из даты

        unset($data['tag_ids']);
помещаем экземпляр класса пост в переменную
и вызываем метод тегс

        $post = Post::firstOrCreate($data);
        $post->tags()->attach($tagIds);

Добавляем транзакцию
заворачиваем содержимое метода инвок в трайкеч

        try {
            $data = $request->validated();
            $tagIds = $data['tag_ids'];
            unset($data['tag_ids']);

            $data['preview_image'] = Storage::put('/images', $data['preview_image']);
            $data['main_image'] = Storage::put('/images', $data['main_image']);

            $post = Post::firstOrCreate($data);
            $post->tags()->attach($tagIds);
        } catch (\Exception $exception) {
            abort(404);
        }
Для того чтоб выбранные теги не сбрасывались 
проверяем что выбранные теги являются массивом
и сверяем в массиве получаемые теги с выбранныии???о

    <option {{ is_array(old('tag_ids')) && in_array($tag->id, old('tag_ids')) ? ' selected' : '' }} value="{{ $tag->id }}">{{ $tag->title }}</option>

вместо функции 

    array_column([],'id')
в ларавел можно использовать
 
    pluck('id)




для того чтоб преобразовать в абсолютный путь используем хелпер 
который начинает смотреть с папки public

    public_path(...)
    
в хелпер ассет можно добавлять папки в которые нужно смотреть
по умолчанию смотрт в корень public 

    asset('storage/' . ...)

    url('....)

для того чтоб отобразить изображения используем команду
пробрасывает символьную ссылку из public в storage

    php artisan storage:link
    
Для того чтоб уточнить папку куда сохранять изображения используем хелпер 
disk('имя папки')-> 

            $data['preview_image'] = Storage::disk('public')->put('/images', $data['preview_image']);
            $data['main_image'] = Storage::disk('public')->put('/images', $data['main_image']);

для того чтоб преобразовать коллекцию в массив используем зарезервированный метод 

    ->toArray()

метод sync()
удаляет все кроме того что в него добавлено???


не забываем указывать 
    
    enctype="multipart/form-data
Для формы в которой отправляются разные данные включая файлы






