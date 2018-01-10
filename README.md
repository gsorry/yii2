Pine Yii2 Helpers
==============
Various behaviors, helpers, widgets and classes

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist pine/yii2 "*"
```

or manually add

```
"require": {
    ...
    "pine/yii2": "*",
    ...
},
```

to the require section of your `composer.json` file.

and

```
"repositories":[
    ...
    {
        "type": "git",
        "url": "https://github.com/gsorry/yii2.git"
    },
    ...
]
```

to the repositories section of your `composer.json` file.

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \pine\yii2\AutoloadExample::widget(); ?>
```

Gii
---

```php
if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
        'generators' => [ //here
            'crud' => [ // generator name
                'class' => 'yii\gii\generators\crud\Generator', // generator class
                'templates' => [ //setting for out templates
                    'pine' => '@app/vendor/pine/yii2/gii/pine', // template name => path to template
                ]
            ]
        ],
    ];
}
```