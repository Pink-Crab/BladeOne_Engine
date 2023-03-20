
# Release Candidate 1.0.0-RC1
---

# BladeOne_Provider
A BladeOne Provider for the PinkCrab Renderable Interface.

[![Latest Stable Version](http://poser.pugx.org/pinkcrab/bladeone-engine/v)](https://packagist.org/packages/pinkcrab/bladeone-engine)
[![Total Downloads](http://poser.pugx.org/pinkcrab/bladeone-engine/downloads)](https://packagist.org/packages/pinkcrab/bladeone-engine)
[![License](http://poser.pugx.org/pinkcrab/bladeone-engine/license)](https://packagist.org/packages/pinkcrab/bladeone-engine)
[![PHP Version Require](http://poser.pugx.org/pinkcrab/bladeone-engine/require/php)](https://packagist.org/packages/pinkcrab/bladeone-engine)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/BladeOne_Engine?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/BladeOne_Engine)

[![WP5.9 [PHP7.4-8.1] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_5_9.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_5_9.yaml)
[![WP6.0 [PHP7.4-8.1] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_0.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_0.yaml)
[![WP6.1 [PHP7.4-8.1] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_1.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_1.yaml)

[![codecov](https://codecov.io/gh/Pink-Crab/BladeOne_Engine/branch/master/graph/badge.svg?token=jYyPg3FOSN)](https://codecov.io/gh/Pink-Crab/BladeOne_Engine)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/BladeOne_Engine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/BladeOne_Engine/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/9d4a3c4c0a3e97b8dc34/maintainability)](https://codeclimate.com/github/Pink-Crab/BladeOne_Engine/maintainability)


> Supports and tested with the PinkCrab Perique Framework versions 2.0.*


## Why? ##
The BladeOne implementation of the Renderable interface, allows the use of Blade within the PinkCrab Framework. 

## Setup ##

````bash 
$ composer require pinkcrab/bladeone-engine
````

Out of the box, you can just include the BladeOne module when you are booting Perique and you will have full access to the BladeOne engine. 

```php
// Bootstrap for Perique follows as normal.. 
$app = ( new App_Factory('path/to/project/root') )
   ->default_config()
   ->module(BladeOne::class)
   // Rest of setup
   ->boot();
```
By default the following are assumed
* `path/to/project/root/views` as the view path
* `path/wp-content/uploads/blade-cache` as the cache path
* `MODE_AUTO`


## Configuring BladeOne ##

As with all other modules, BladeOne can be configured by passing a `\Closure` as the 2nd argument to the `module()` method. 

```php
// Bootstrap for Perique follows as normal..
$app = ( new App_Factory('path/to/project/root') )
   ->default_config()
   ->module(BladeOne::class, function( BladeOne $blade ) {
      // Module config.
      $blade
         ->template_path('path/to/custom/views')
         ->compiled_path('path/to/custom/cache'); // Fluent API for chaining.
      
      $blade->mode( BladeOne::MODE_DEBUG );

      // BladeOne_Engine config.
      $blade->config( function( BladeOne_Engine $engine  {
         // See all methods below.
         $engine
            ->set_compiled_extension('.php')
            ->directive('test', fn($e) =>'test'); // Fluent API for chaining.
         
         $engine->allow_pipe( false ); 
      });

      // Ensure you return the instance.
      return $blade;
   })
   // Rest of setup
   ->boot();
```

<details>
  <summary>Compact BladeOne Config</summary>
  <p>It is possible to do the Module config in a much more concise fashion, using the fluent API and PHP Arrow functions</p>

```php
$app = ( new App_Factory('path/to/project/root') )
   ->default_config()
   ->module(BladeOne::class, fn( BladeOne $blade ) => $blade
      ->template_path('path/to/custom/views')
      ->compiled_path('path/to/custom/cache')
      ->mode( BladeOne::MODE_DEBUG )
      ->config( fn( BladeOne_Engine $engine ) => $engine
         ->set_compiled_extension('.php')
         ->directive('test', fn($e) =>'test')
         ->allow_pipe( false )
      )
   )
->boot();
```

You can also hold the config in its own class and use that.

```php
/** Some Class */
class BladeOneConfig {
   public function __invoke( BladeOne $blade ): BladeOne {
      return $blade
         ->template_path('path/to/custom/views')
         ->compiled_path('path/to/custom/cache')
         ->mode( BladeOne::MODE_DEBUG )
         ->config( fn( BladeOne_Engine $engine ) => $engine
            ->set_compiled_extension('.php')
            ->directive('test', fn($e) =>'test')
            ->allow_pipe( false )
         );
   }
}

$app = ( new App_Factory('path/to/project/root') )
   ->default_config()
   ->module(BladeOne::class, new BladeOneConfig() )
   ->boot();
```
</details>

## BladeOne_Module Config

You can call the following methods on the BladeOne Module to configure the BladeOne Module.
* [template_path](docs/BladeOne-Module.md#public-function-template_path-string-template_path-)
* [compiled_path](docs/BladeOne-Module.md#public-function-compiled_path-string-compiled_path-)
* [mode](docs/BladeOne-Module.md#public-function-mode-int-mode-)
* [config](docs/BladeOne-Module.md#public-function-configcallable-config)

## BladeOne_Engine Config

You can call the following methods on the BladeOne_Engine to configure the BladeOne_Engine.
* [allow_pipe](docs/BladeOne-Engine.md#public-function-allow_pipe-bool-bool--true-)
* [directive](docs/BladeOne-Engine.md#public-function-directive-string-name-callable-handler-)
* [directive_rt](/docs/BladeOne-Engine.md#public-function-directive_rt-string-name-callable-handler)
* [add_include](docs/BladeOne-Engine.md#public-function-add_include-view-alias--null-)
* [add_class_alias](docs/BladeOne-Engine.md#public-function-add_alias_classes-alias_name-class_with_namespace-)
* [share](docs/BladeOne-Engine.md#public-function-share-stringarray-var_name-value--null-)
* [set_file_extension](docs/BladeOne-Engine.md#public-function-set_file_extension-string-file_extension-)
* [set_compiled_extension](docs/BladeOne-Engine.md#public-function-set_compiled_extension-string-file_extension-)
* [set_esc_function](docs/BladeOne-Engine.md#public-function-set_esc_function-callable-esc-)

## Blade Templating

Most Blade features are present, to see the full docs please visit the [EFTEC/BladeOne wiki](https://github.com/EFTEC/BladeOne/wiki)

* Echo data [escaped](docs/Bladeone-Template-Useage.md#echo-string-escaped) or [unescaped](docs/Bladeone-Template-Useage.md#echo-string-unescaped)
* [Call PHP Function](docs/Bladeone-Template-Useage.md#calling-php-functions)
* [Running PHP Block](docs/Bladeone-Template-Useage.md#running-php-blocks)
* *Conditionals*
  * [if, elseif, else](docs/Bladeone-Template-Useage.md#if-statements)
  * [switch](docs/Bladeone-Template-Useage.md#switch-statements)
* *Loops*
  * [for](docs/Bladeone-Template-Useage.md#for-loops)
  * [foreach](docs/Bladeone-Template-Useage.md#foreach-loops)  
  * [forelse](docs/Bladeone-Template-Useage.md#forelse-loops)
  * [while](docs/Bladeone-Template-Useage.md#while-loops)
* [include](docs/Bladeone-Template-Useage.md#include)
* [form](docs/Bladeone-Template-Useage.md#form)
* [auth](docs/Bladeone-Template-Useage.md#auth)
* [permissions](docs/Bladeone-Template-Useage.md#permissions)
* [view models](docs/Bladeone-Template-Useage.md#view-model)
* [component](docs/Bladeone-Template-Useage.md#component)

## Included Components

Out of the box PinkCrab_BladeOne comes with the BladeOneHTML trait added, giving access all HTML components.
[BladeOneHTML](https://github.com/EFTEC/BladeOneHtml)
[viewModel]()
[viewComponent]()

---

## Magic Call Methods ##

The BladeOne class has a large selection of Static and regular methods, these can all be accessed from BladeOne_Engine. These can be called as follows.
```php
// None static
$this->view->engine()->some_method($data);

// As static 
BladeOne_Engine::some_method($data);
```
> For the complete list of methods, please visit https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class


**If you want access none static methods using a static means, you can use**
```php 
// Using the App's View method to access none static methods on the fly.
App::view()->engine()->some_method($data);
```
> calling` engine()` on view, will return the underlying rendering engine used, in this case the BladeOne_Provider. 

> Of course you can set the engine it self as a global variable using `$provider->share('view_helper', [App::view(), 'engine'])`. Then you can use `{$view_helper->some_method(\$data)}` in your view.

***

## Dependencies ##
* [BladeOne 4.1](https://github.com/EFTEC/BladeOne)
* [BladeOne HTML 2.0](https://github.com/eftec/BladeOneHtml)

## Requires ##
* [PinkCrab Perique Framework V2.0.0 and above.](https://github.com/Pink-Crab/Perqiue-Framework)
* PHP7.4+

## License ##

### MIT License ###
http://www.opensource.org/licenses/mit-license.html  

## Previous Perique Support ##

* For support of all versions befor Perique V2, please use the [BladeOne_Provider](https://github.com/Pink-Crab/Perique-BladeOne-Provider)

## Change Log ##
* 1.0.0 - Migrated over from the Perique V2 Prep branch of BladeOne_Provider.

