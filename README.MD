### Установка
```
$ composer require seiweb/yii2-image "*"
```
### Настройка

```
 'modules'=>[
        'swb_image' => [
            'class' => 'seiweb\image\Module',
            'components'=>[
                'storage'=>[
                    'class'=>'common\CustomStorage',
                    'originalDor'=>'_original', // сюда складываются оригиналы
                    'cacheDir'=>'_modified', //сюда складываются всякие превью
                ],
                'imageManager'=>[
                    'class'=>\Intervention\Image\ImageManager::class,
                    'driver'=>'gd' //or imagick
                ]
            ],
            'originalResizeTo'=>[1280,768], //уменьшать загружаемый файл до
            'maxFileCount'=>15,
            //'watermarkFilePath' => '@frontend/web/uploads/watermark.png',
            'watermarkOpacity'=>20,
            'uploadFileMaxSize'=>1000*1000*2
        ],
    ]
```

### Применение миграций
```
 php yii migrate/up --migrationPath=@seiweb/image/migrations
```
