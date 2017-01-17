Yii2 User Extension
===================
An user extension for yii2

# Installation

## Step 1

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer atans/yii2-user "*"
```

or add

```
"atans/yii2-user": "*"
```

to the require section of your `composer.json` file.

## Step 2

Update database schema

```
$ php yii migrate/up --migrationPath=@vendor/atans/yii2-user/migrations
```


## Step 3 

Add this module



```php
    // config/main.php

    'modules' => [
        'rbac' => 'atans\user\Module',
    ],
```