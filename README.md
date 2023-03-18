
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
> You can have as many of these config classes as you want, allowing you to break up any custom directives, globals values and aliases etc.

## Included Components

Out of the box PinkCrab_BladeOne comes with the BladeOneHTML trait added, giving access all HTML components.
[BladeOneHTML Docs](https://github.com/EFTEC/BladeOneHtml)

## Public Methods For BladeOne##
The BladeOne Module has the following methods which can be used to configure the base BladeOne Module. 

> PLEASE ENSURE YOU RETURN BACK THE INSTANCE OF THE MODULE WHEN USING THE CONFIGURATION CLOSURE.

---

### **template_path** ###
```php
/**
 * Sets the template path.
 *
 * @param string $path
 * @return self
 */
public function template_path( string $path ): self{}
```

Sets the template path. This is the path where the views are stored.

---

### **compiled_path** ###
```php
/**
 * Sets the compiled path.
 *
 * @param string $path
 * @return self
 */
public function compiled_path( string $path ): self{}
```

Sets the compiled path. This is the path where the compiled views are stored.

---

### **mode** ###
```php
/**
 * Sets the mode.
 *
 * @param int $mode
 * @return self
 */
public function mode( int $mode ): self{}
```

Sets the mode. This can be one of the following:
* `BladeOne::MODE_DEBUG` - This will compile the template every time it is called.
* `BladeOne::MODE_AUTO` - This will compile the template if it does not exist, or if the template has been modified since the last compile.
* `BladeOne::MODE_SLOW` - This will compile the template if it does not exist.
* `BladeOne::MODE_FAST` - This will compile the template if it does not exist, or if the template has been modified since the last compile, and the template is older than 1 hour.
  
---

### **config** ###
```php
/**
 * Sets the config.
 *
 * @param \Closure(BladeOne_Engine $engine) $config
 * @return self
 */
public function config( \Closure $config ): self{}
```

Sets the config. This is a closure which is passed the BladeOne_Engine instance, allowing you to configure the underlying BladeOne implementation.


## Public Methods For BladeOne_Engine##
The BladeOne_Engine class has a number of methods which can be used to configure the underlying BladeOne implementation. This can be done using the `config()` method as part of the [module definition](#configuring-bladeone).

---

### **allow_pipe** ###
```php
/**
 * Sets if piping is enabled in templates.
 *
 * @param bool $bool
 * @return self
 */
public function allow_pipe( bool $bool = true ): self{}
```
Calling this will allow you toggle piping `{{ $var | esc_html }}` on or off. By default this is enabled.  
*Details*: https://github.com/EFTEC/BladeOne/wiki/Template-Pipes-\(Filter\)

---

### **directive** ###
```php
/**
 * Register a handler for custom directives.
 *
 * @param string   $name
 * @param callable $handler
 * @return self
 */
public function directive( string $name, callable $handler ): self{}
```
Calling this will allow you to create custom directives
```php
// Directive Example
$provider->directive('datetime', function ($expression) {
	// Return a valid PHP expression in php tags
    return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
});
```
```html
<!-- Called like so in your views. -->
<p class="date">@datetime($now)</p> 

<!-- Rendered as. -->
<p class="date">01/24/2021 14:34</p>
```
```php
// You will need to pass $now to your view
$class->render('path.to.view', ['now' => new DateTime()]);
```

*Details*: https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class#directive

> Don't forget our config class is loaded via the DI Container, so you can encapsulate your Directive callbacks into a class, with dependencies injected using the DI Container. (See above example)
---

### **directive_rt** ###
```php
/**
 * Register a handler for custom directives at runtime only.
 *
 * @param string   $name
 * @param callable $handler
 * @return self
 */
public function directive_rt( string $name, callable $handler ): self{}
```
Calling this will allow you to create custom directives
```php
// Directive at Run Time Example
$provider->directive_rt('datetime', function ($expression) {
	// Just print/echo the value.
	return "echo $expression->format('m/d/Y H:i');";
});
```
```html
<!-- Called like so in your views. -->
<p class="date">@datetime($now)</p> 

<!-- Rendered as. -->
<p class="date">01/24/2021 14:34</p>
```
```php
// You will need to pass $now to your view
$class->render('path.to.view', ['now' => new DateTime()]);
```
---

### **add_include** ###
```php
/**
 * Define a template alias
 *
 * @param string      $view  example "folder.template"
 * @param string|null $alias example "mynewop". If null then it uses the name of the template.
 * @return self
 */
public function add_include( $view, $alias = null ): self{}
```
This will allow you to set alias for your templates, this is ideal for global variables (share()).
```php
// Directive at Run Time Example
$provider->add_include('some.long.path.no.one.wants.to.type', 'longpath');

// This can then be used when rendering.
$class->render('longpath', ['data' => $data]);
```
---

### **add_alias_classes** ###
```php
/**
 * Define a class with a namespace
 *
 * @param string $alias_name
 * @param string $class_with_namespace
 * @return self
 */
public function add_alias_classes( $alias_name, $class_with_namespace ): self{}
```
This allows for the creation of simpler and short class names for use in templates.
```php
$provider->add_alias_classes('MyClass', 'Namespace\\For\\Class');
```
```html
<!-- Called like so in your views. -->
{{MyClass::some_method()}}
{!! MyClass::some_method() !!}
@MyClass::some_method()
```

---

### **share** ###
```php
/**
 * Adds a global variable. If <b>$var_name</b> is an array then it merges all the values.
 * <b>Example:</b>
 * <pre>
 * $this->share('variable',10.5);
 * $this->share('variable2','hello');
 * // or we could add the two variables as:
 * $this->share(['variable'=>10.5,'variable2'=>'hello']);
 * </pre>
 *
 * @param string|array<string, mixed> $var_name It is the name of the variable or it is an associative array
 * @param mixed        $value
 * @return self
 */
public function share( $var_name, $value = null ): self{}
```
Allows fore the creation of globals variable. This is best set in the Config class (detailed above) as you can pass in dependencies.
```php
$provider->share('GLOBAL_foo', [$this->injected_dep, 'method']);
```
```html
<!-- Called like so in your views. -->
{{ $GLOBAL_foo }}
@include('some.path') <!-- Where some.path uses GLOBAL_foo, ideal for dynamic components like nav menus >
```
> You do not need to defined \$GLOBAL_foo when you are passing values to render `\$foo->render('template.path', [])`

*Details*: https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class#share

---

### **set_file_extension** ###
```php
/**
 * Set the file extension for the template files.
 * It must includes the leading dot e.g. .blade.php
 *
 * @param string $file_extension Example: .prefix.ext
 * @return self
 */
public function set_file_extension( string $file_extension ): self{}
```
Allows you to define a custom extension for your blade templates.
```php
$provider->set_file_extension('.view.php');

// Can then be used to pass my.view.php as
$foo->render('my', ['data'=>'foo']);
```

---

### **set_compiled_extension** ###
```php
/**
 * Set the file extension for the compiled files.
 * Including the leading dot for the extension is required, e.g. .bladec
 *
 * @param string $file_extension
 * @return self
 */
public function set_compiled_extension(( string $file_extension ): self{}
```
Allows you to define a custom extension for your compiled views.
```php
$provider->set_file_extension('.view_cache');
```
---

### **set_esc** ###
```php
/**
 * Sets the esc function.
 * 
 * @param callable(mixed):string $esc
 * @return self
 */
public function set_esc_function( callable $esc ): self {}
```
Allows you to define a custom esc function for your views. By default this is set to `esc_html`.
```php
$provider->set_esc_function('esc_attr');
```
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

## View Models ##
Inside your templates it is possible to render viewModels in your templates by using either of the following methods.

```php
// @file /views/template.blade.php

// Using the $this->view_models() method.
{!! $this->view_modes(new View_Model('path.template', ['key' => 'value'])) !!}

// Using the directive
@viewModel(new View_Model('path.template', ['key' => 'value']))
```

## Components ##
Inside your templates it is possible to render components in your templates by using either of the following methods.

```php
// @file /views/template.blade.php

// Using the $this->component() method.
{!! $this->component(new SomeComponent()) !!}

// Using the directive
@component(new SomeComponent())
```
> Please note `@component` is not the same as regular BLADE components. BladeOne does not support these and this is the Perique Frameworks own implementation.


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

