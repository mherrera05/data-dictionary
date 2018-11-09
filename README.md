![Symfony Bundle](https://www.forcelibre.com/wp-content/uploads/2017/10/logo-sf.png?x73297)

[![Packagist](https://img.shields.io/packagist/dt/mherrera05/data-dictionary.svg?style=for-the-badge)](https://packagist.org/packages/mherrera05/data-dictionary) [![GitHub tag](https://img.shields.io/github/tag/mherrera05/data-dictionary.svg?style=for-the-badge)](https://github.com/mherrera05/data-dictionary/tags) [![Codacy grade](https://img.shields.io/codacy/grade/fc5ba48f0fac49ab87357b5575cbb965.svg?style=for-the-badge)]() [![Packagist](https://img.shields.io/packagist/l/mherrera05/data-dictionary.svg?style=for-the-badge)](https://packagist.org/packages/mherrera05/data-dictionary) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/27857897-b592-4d7c-b415-a5609a3636ec/small.png)](https://insight.sensiolabs.com/projects/27857897-b592-4d7c-b415-a5609a3636ec)
[![knpbundles.com](http://knpbundles.com/mherrera05/data-dictionary/badge-short)](http://knpbundles.com/mherrera05/data-dictionary)


# Introduction

[Data Dictionary](https://packagist.org/packages/mherrera05/data-dictionary) is a [Symfony Bundle](http://symfony.com/doc/current/bundles.html) that offers database composition of project rendered as HTML format, making it easy for developers to read field name, data type and comments.


# Requirements

 * PHP `5.3 or higher`
 * Symfony `2.x or 3.x`
 * Doctrine Symfony Bundle `~1.4`
 * Doctrine ORM `^2.4.8`
 * Twig `^1.0||^2.0`

# Installation & Usage
This is a Symfony Bundle with dependency on Doctrine, you can install it via composer.

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```console
$ composer require mherrera05/data-dictionary "dev-master"
ó
$ composer require mherrera05/data-dictionary ">=1.0"
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new DataDictionaryBundle\DataDictionaryBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Import resources from Bundle

Import bundle resources adding resources in `routing.yml`.

```yaml
data_dictionary:
    resource: "@DataDictionaryBundle/Resources/config/routing.yml"
    prefix:   /
```

If you prefer, you can add a prefix for URL.

### Step 4: Add vendor templates

Add vendor templates directory as path in twig configuration `config.yml`.

```yaml
twig:
    ...
    paths:
        '%kernel.root_dir%/../vendor/mherrera05/data-dictionary/Resources/views': 'DataDictionaryBundle'
```

## Usage

### Step 1: Import model to json files

Once installed and enabled the Bundle, you can execute the command:

```bash
$ php app/console data:dictionary:import {bundle-name}
```

This command will export database tables and fields composition to `.json` files.

Use the name of bundle where you want to put the json files. Example, Doctrine creates `orm.yml` from database on your base bundle.

### Step 2: Call URL

Once files have been imported, just call the URL on web browser.

```bash
/app.php/data-dictionary
```

# Maintainer

Miguel Herrera [https://github.com/mherrera05/](https://github.com/mherrera05/)
