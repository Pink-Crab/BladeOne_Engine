

## Methods

THe following methods can be used to configure the BladeOne_Engine. 


**public function allow_pipe( bool $bool = true )**
> @param bool $bool Default true  
> @return self  

Calling this will allow you toggle piping `{{ $var | esc_html }}` on or off. By default this is enabled.  

*Details*: https://github.com/EFTEC/BladeOne/wiki/Template-Pipes-\(Filter\)

---

**public function directive( string $name, callable $handler )**
> @param string   $name  
> @param callable $handler  
> @return self  

Calling this will allow you to create custom directives
```php
// Directive Example
$bladeone_engine->directive('datetime', function ($expression) {
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

**directive_rt**
```php
/**
 * Register a handler for custom directives at runtime only.
 *
 * @param string   $name
 * @param callable $handler
 * @return self
 */
public function directive_rt( string $name, callable $handler ): self
```
Calling this will allow you to create custom directives
```php
// Directive at Run Time Example
$bladeone_engine->directive_rt('datetime', function ($expression) {
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

**add_include**
```php
/**
 * Define a template alias
 *
 * @param string      $view  example "folder.template"
 * @param string|null $alias example "mynewop". If null then it uses the name of the template.
 * @return self
 */
public function add_include( $view, $alias = null ): self
```
This will allow you to set alias for your templates, this is ideal for global variables (share()).
```php
// Directive at Run Time Example
$bladeone_engine->add_include('some.long.path.no.one.wants.to.type', 'longpath');

// This can then be used when rendering.
$class->render('longpath', ['data' => $data]);
```
---

**add_alias_classes**
```php
/**
 * Define a class with a namespace
 *
 * @param string $alias_name
 * @param string $class_with_namespace
 * @return self
 */
public function add_alias_classes( $alias_name, $class_with_namespace ): self
```
This allows for the creation of simpler and short class names for use in templates.
```php
$bladeone_engine->add_alias_classes('MyClass', 'Namespace\\For\\Class');
```
```html
<!-- Called like so in your views. -->
{{MyClass::some_method()}}
{!! MyClass::some_method() !!}
@MyClass::some_method()
```

---

**share**
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
public function share( $var_name, $value = null ): self
```
Allows fore the creation of globals variable. This is best set in the Config class (detailed above) as you can pass in dependencies.
```php
$bladeone_engine->share('GLOBAL_foo', [$this->injected_dep, 'method']);
```
```html
<!-- Called like so in your views. -->
{{ $GLOBAL_foo }}
@include('some.path') 
<!-- Where some.path uses GLOBAL_foo, ideal for dynamic components like nav menus >
```
> You do not need to defined \$GLOBAL_foo when you are passing values to render `\$foo->render('template.path', [])`

*Details*: https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class#share

---

**set_file_extension**
```php
/**
 * Set the file extension for the template files.
 * It must includes the leading dot e.g. .blade.php
 *
 * @param string $file_extension Example: .prefix.ext
 * @return self
 */
public function set_file_extension( string $file_extension ): self
```
Allows you to define a custom extension for your blade templates.
```php
$bladeone_engine->set_file_extension('.view.php');

// Can then be used to pass my.view.php as
$foo->render('my', ['data'=>'foo']);
```

---

**set_compiled_extension**
```php
/**
 * Set the file extension for the compiled files.
 * Including the leading dot for the extension is required, e.g. .bladec
 *
 * @param string $file_extension
 * @return self
 */
public function set_compiled_extension(( string $file_extension ): self
```
Allows you to define a custom extension for your compiled views.
```php
$bladeone_engine->set_file_extension('.view_cache');
```
---

**set_esc**
```php
/**
 * Sets the esc function.
 * 
 * @param callable(mixed):string $esc
 * @return self
 */
public function set_esc_function( callable $esc ): self
```
Allows you to define a custom esc function for your views. By default this is set to `esc_html`.
```php
$bladeone_engine->set_esc_function('esc_attr');
```
---
