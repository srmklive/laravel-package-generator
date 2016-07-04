# Laravel Package Generator

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)

<a name="introduction"></a>
## Introduction

By using this package, you can create custom packages for your laravel applications. 

**Only Laravel 5.1 or greater is supported in this package.**


<a name="installation"></a>
## Installation

* Use following command to install:

```
composer require srmklive/laravel-package-generator
```

* Add the service provider to your $providers array in config/app.php file like: 

```
Srmklive\PackageGenerator\Providers\PackageGeneratorServiceProvider::class
```

<a name="usage"></a>
## Usage

```
php artisan make:package --author=author --name="Test Author" --email=test@example.com --package=mypackage
```
