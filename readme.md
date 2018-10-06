#### Cara Menggunakan CakeTpl Di Dalam CakePHP 3

* Kemaskini 'composer.json'.
```json
    "autoload": {
        "psr-4": {
            "App\\": "src\/"
        },
        "files": [ "vendor/elmyrockers/CakeTpl/TplParser.class.php", "vendor/elmyrockers/CakeTpl/TplView.php" ]
    },
```

* Menerusi CLI, taip: cake/app composer dump-autoload

* Kemaskini 'AppView' di dalam direktori berikut: your-app/src/View/
```php
use elmyrockers\CakeTpl\TplView;

class AppView extends TplView // jadikan TplView sebagai parent class kepada AppView
```

*Keseluruhan kod di dalam AppView akan kelihatan seperti berikut:
```php
<?php
namespace App\View;
use elmyrockers\CakeTpl\TplView;


class AppView extends TplView
{
	public function initialize()
	{
		
	}
}
```