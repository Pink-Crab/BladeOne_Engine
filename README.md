![logo](./docs/Perique%20BladeOne%20Card.jpg "Perique BladeOne Engine")
# BladeOne_Engine
A BladeOne Engine for the PinkCrab Renderable Interface.

[![Latest Stable Version](https://poser.pugx.org/pinkcrab/bladeone-engine/v)](https://packagist.org/packages/pinkcrab/bladeone-engine)
[![Total Downloads](https://poser.pugx.org/pinkcrab/bladeone-engine/downloads)](https://packagist.org/packages/pinkcrab/bladeone-engine)
[![License](https://poser.pugx.org/pinkcrab/bladeone-engine/license)](https://packagist.org/packages/pinkcrab/bladeone-engine)
[![PHP Version Require](https://poser.pugx.org/pinkcrab/bladeone-engine/require/php)](https://packagist.org/packages/pinkcrab/bladeone-engine)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/BladeOne_Engine?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/BladeOne_Engine)

[![WP6.3 [PHP7.4-8.2] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_3.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_3.yaml)
[![WP6.4 [PHP7.4-8.2] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_4.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_4.yaml)
[![WP6.5 [PHP7.4-8.2] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_5.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_5.yaml)
[![WP6.6 [PHP7.4-8.2] Tests](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_6.yaml/badge.svg)](https://github.com/Pink-Crab/BladeOne_Engine/actions/workflows/WP_6_6.yaml)

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

## Usage ##

Just like the native PHP_Engine found with Perique, you can inject the View service into any class and use it to render a view. 
   
```php
class Some_Class {
   
      public function __construct( View $view ) {
         $this->view = $view;
      }
   
      public function render_view() {
         return $this->view->render('some.path.view_name', ['data' => 'to pass']);
      }
}
```
The above would render the template `path/to/project/root/views/some/path/view_name.blade.php` with access to $data in the view which would be `to pass`.

```blade
<p>{{ $data }}</p>
```
Would render as
```html
<p>to pass</p>
```

It is fully possible to make use of the template inheritance and other blade features. 
```blade
<div class="wrap">
   @include('partials.header')
   @yield('content')
   @include('partials.footer')
</div>
```

```blade
@extends('layouts.default')
@section('content')
   Some content
@stop

```

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
      
      // Set the rendering mode.
      $blade->mode(  PinkCrab_BladeOne::MODE_DEBUG );

      // Set the comment mode.
      $blade->comment_mode( PinkCrab_BladeOne::COMMENT_RAW );

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
      ->mode(  PinkCrab_BladeOne::MODE_DEBUG )
      ->comment_mode( PinkCrab_BladeOne::COMMENT_RAW )
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
         // The setup.
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
* [comment_mode](docs/BladeOne-Module.md#public-function-comment_mode-int-mode)
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


## Included Components

Out of the box PinkCrab_BladeOne comes with the BladeOneHTML trait added, giving access all HTML components.
* [BladeOneHTML](https://github.com/EFTEC/BladeOneHtml)
* [viewModel](docs/Bladeone-Template-Useage.md#view-model)
* [viewComponent](docs/Bladeone-Template-Useage.md#component)
* [nonce](docs/Bladeone-Template-Useage.md#nonce)

---

## Magic Call Methods ##

The BladeOne class has a large selection of Static and regular methods, these can all be accessed from BladeOne_Engine. These can be called as follows.
```php
// None static
$this->view->engine()->some_method($data);

// As static 
BladeOne_Engine::some_method($data);
```

The can also be called in templates.
```php
{$this->some_method($data)}

// Or
{BladeOne_Engine::some_method($data)}
```

> For the complete list of methods, please visit https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class


## Static Access ##
```php 
// Using the App's View method to access none static methods on the fly.
App::view()->engine()->some_method($data);
```
> calling `engine()` on view, will return the underlying rendering engine used, in this case  `PinkCrab_BladeOne`. 

> Of course you can set the engine it self as a global variable using `$provider->share('view_helper', [App::view(), 'engine'])`. Then you can use `{$view_helper->some_method(\$data)}` in your view.

***

## Extending 

It is possible to extend BladeOne via other plugins, if you would like to add additional functionality by adding custom directives, or adding additional methods to the BladeOne_Engine class. You can do this by using the `PinkCrab_BladeOne::SETUP_CONFIG` action and add any additional configs such as directives.

```php
add_action( PinkCrab_BladeOne::SETUP_CONFIG, function( PinkCrab_BladeOne $engine ) {
    $engine->directive( 'my_directive', function( $expression ) {
        return "<?php echo 'Hello World'; ?>";
    } );
} );
```

## Dependencies ##
* [BladeOne V4](https://github.com/EFTEC/BladeOne)
* [BladeOne HTML V2](https://github.com/eftec/BladeOneHtml)

## Requires ##
* [PinkCrab Perique Framework V2 and above.](https://github.com/Pink-Crab/Perqiue-Framework)
* PHP7.4+

## License ##

### MIT License ###
http://www.opensource.org/licenses/mit-license.html  

## Previous Perique Support ##

* For support of all versions before Perique V2, please use the [BladeOne_Provider](https://github.com/Pink-Crab/Perique-BladeOne-Provider)

## Change Log ##
* 2.1.0 - Updated to support Perique V2.1.x
-- Please note version number jumped to match rest of Perique Framework --
* 1.1.0 - Provides BladeOne 4.12+ and BladeOneHTML 2.4+. With comment mode support.
* 1.0.1 - Last version to support pre 4.12 BladeOne (will be the last)
* 1.0.0 - Migrated over from the Perique V2 Prep branch of BladeOne_Provider.
  * New Features
  * Auth and Permissions now hooked up and based on the current user.
  * Perique V2 Module structure.
  * WP Nonce support. 

