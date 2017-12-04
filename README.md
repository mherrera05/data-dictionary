![Symfony Bundle](https://symfony.com/images/v5/pictos/home-main-illu.svg)

[![Packagist](https://img.shields.io/packagist/dt/mherrera05/data-dictionary.svg?style=for-the-badge)](https://packagist.org/packages/mherrera05/data-dictionary) [![GitHub release](https://img.shields.io/github/release/qubyte/rubidium.svg?style=for-the-badge)](https://github.com/mherrera05/data-dictionary) [![Packagist](https://img.shields.io/packagist/l/mherrera05/data-dictionary.svg?style=for-the-badge)](https://packagist.org/packages/mherrera05/data-dictionary)

# Introduction

[Data Dictionary](https://packagist.org/packages/mherrera05/data-dictionary) is a [Symfony Bundle](http://symfony.com/doc/current/bundles.html) that offers database composition of project rendered as HTML format, making it easy for developers to read field name, data type and comments.

# Installation & Usage
This is a Symfony Bundle with dependency on Doctrine, you can install it via composer.

## Requirements

 * PHP `5.3 or higher`
 * Doctrine Symfony Bundle `~1.4`
 * Doctrine ORM `^2.4.8`
 * Twig `^1.0||^2.0`

## Executable
Install data-dictionary bundle via composer. You need composer installed:

```bash
$ composer require mherrera05/data-dictionary "dev-master"
ó 
$ composer require mherrera05/data-dictionary ">=1.0"
```

## Setting
Add the bundle to `AppKernel.php` on your project.

```php
$bundles = array(
	...
    new DataDictionaryBundle\DataDictionaryBundle(),
);
```

Import bundle resources on `routing.yml`.
```yaml
data_dictionary:
    resource: "@DataDictionaryBundle/Resources/config/routing.yml"
    prefix:   /
```

## Usage
Once installed and setted up, you can run command:

```bash
$ php app/console data:dictionary:import {bundle-name}
```

Use the name of bundle where you want to put the json files. Example, Doctrine creates `orm.yml` from database on your base bundle.
