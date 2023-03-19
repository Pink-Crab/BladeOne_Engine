
## Blade Basics

----
**Echo String \[Escaped\]**

```php
{{$name}}

// Parses to
<?php echo esc_html($name); ?>
```
> The above will be escaped using `esc_html` by default. This can be changed using the `set_esc_function` method when creating the module.

----

**Echo String \[Unescaped\]**

```php
{!!$name!!}

// Parses to
<?php echo $name; ?>
```
> The above will not be escaped, useful for outputting HTML or when piping the output through a function that will escape the output. `{!! $foo | esc_attr !!}`

----

**Calling PHP Functions**

```php
{{time()}}

// Parses to
<?php echo time(); ?>
```
> The above will call the `time()` function and output the result.

----

**Running PHP Blocks**

```php
@php
    $foo = 'bar';
    printf('Hello %s', $foo);
@endphp
```
> The above will run the PHP code between the `@php` and `@endphp` tags.

----

**If Statements**

```php
@if($foo)
    <p>Foo is true</p>
@elseif($bar)
    <p>Bar is true</p>
@else
    <p>Foo and Bar are false</p>
@endif

// Parses to
<?php if($foo): ?>
    <p>Foo is true</p>
<?php elseif($bar): ?>
    <p>Bar is true</p>
<?php else: ?>
    <p>Foo and Bar are false</p>
<?php endif; ?>

```
> The above will output the correct block based on the value of `$foo` and `$bar`.

----

**For Loops**

```php
@for($i = 0; $i < 10; $i++)
    <p>Looping {{$i}}</p>
@endfor

// Parses to
<?php for($i = 0; $i < 10; $i++): ?>
    <p>Looping <?php echo $i; ?></p>
<?php endfor; ?>
```
> The above will loop 10 times and output the current loop number.

----

**Foreach Loops**

```php
@forelse($users as $user)
    <p>{{$user->name}}</p>
@empty
    <p>No users found</p>
@endforelse

// Parses to
<?php foreach($users as $user): ?>
    <p><?php echo $user->name; ?></p>
<?php endforeach; ?>
<?php if(empty($users)): ?>
    <p>No users found</p>
<?php endif; ?>
```
> The above will loop through the `$users` array and output the name of each user. If the array is empty, it will output the `No users found` message.

----

**Include**

```php
@include('path.to.view')

// Parses to
<?php include 'base/path/to/view.php'; ?>
```
> The above will include the view file at `base/path/to/view.php`. The `base` path is set when creating the module.

----

### Full Method Docs

> You can find the full method docs on the [EFTEC/BladeOne](https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class) wiki.

## Custom Directives

----

## Auth & Permissions

----