## Client Settings for Evolution CMS 3.x

<img src="https://img.shields.io/badge/PHP-%3E=7.3-green.svg?php=7.3"> <img src="https://img.shields.io/badge/EVO-%3E=3.1.3-blue.svg">

Модуль для создания формы пользовательских настроек. http://docs.evo.im/03_extras/clientsettings.html

```
php -d="memory_limit=-1" artisan package:installrequire mnoskov/evocms-module-example "*"

# создать события в базе данных
php artisan vendor:publish --provider=EvolutionCMS\ClientSettings\ClientSettingsServiceProvider

# создать папку core/custom/clientsettings и загрузить в нее примеры
php artisan db:seed --class=ClientSettingsItemsSeeder
```

Для начала работы нужно переименовать файлы конфигурации `core/custom/clientsettings/*.php.sample` в `*.php`.

<img src="https://monosnap.com/file/yCajIZTcbBAawiI582hhO4TkYjMqWC.png">
