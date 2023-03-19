
## Blade Basics

----
**Echo String \[Escaped\]**

```blade
{{$name}}

// Parses to
<?php echo esc_html($name); ?>
```
> The above will be escaped using `esc_html` by default. This can be changed using the `set_esc_function` method when creating the module.

----

**Echo String \[Unescaped\]**

```blade
{!!$name!!}

// Parses to
<?php echo $name; ?>
```
> The above will not be escaped, useful for outputting HTML or when piping the output through a function that will escape the output. `{!! $foo | esc_attr !!}`

**[Read more about Template Variables at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-variables)**

You can also define a default value for a variable if it is not set.

```blade
{{$name or 'Default Name'}}

// Parses to
<?php echo \esc_html(isset($name) ? $name : 'Default Name'); ?>
```

----

**Calling PHP Functions**

```blade
{{time()}}

// Parses to
<?php echo time(); ?>
```
> The above will call the `time()` function and output the result.

----

**Running PHP Blocks**

```blade
@php
    $foo = 'bar';
    printf('Hello %s', $foo);
@endphp
```
> The above will run the PHP code between the `@php` and `@endphp` tags.

----

**If Statements**

```blade
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

**[Read more about IF and Template Logic at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-logic)**

----

**Switch Statements**

```blade
@switch($i)
    @case(1)
        <p>One</p>
        @break
    @case(2)
        <p>Two</p>
        @break
    @default
        <p>Something else</p>
@endswitch

// Parses to
<?php switch($i): case(1): ?>
    <p>One</p>
    <?php break; ?>
<?php case(2): ?>
    <p>Two</p>
    <?php break; ?>
<?php default: ?>   
    <p>Something else</p>
<?php endswitch; ?>

```
> The above will output the correct block based on the value of `$i`.

**[Read more about Switch Template Logic at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-logic)**

----

**For Loops**

```blade
@for($i = 0; $i < 10; $i++)
    <p>Looping {{$i}}</p>
@endfor

// Parses to
<?php for($i = 0; $i < 10; $i++): ?>
    <p>Looping <?php echo $i; ?></p>
<?php endfor; ?>
```
> The above will loop 10 times and output the current loop number.

**[Read more about For & Loops at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-loop#loop)**

----

**Foreach Loops**

```blade
@foreach($users as $user)
    <p>{{$user->name}}</p>
@endforeach

// Parses to
<?php foreach($users as $user): ?>
    <p><?php echo $user->name; ?></p>
<?php endforeach; ?>
```

> The above will loop through the `$users` array and output the name of each user.

**[Read more about Foreach & Loops at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-loop#foreacharray-as-alias--endforeach)**

----

**Forelse Loops**

```blade
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

**[Read more about Forelse & Loops at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-loop#forelsearray-as-alias--empty--endforelse)**

----

**While Loops**

```blade
@while($i < 10)
    <p>Looping {{$i}}</p>
    <?php $i++; ?>
@endwhile

// Parses to
<?php while($i < 10): ?>
    <p>Looping <?php echo $i; ?></p>
    <?php $i++; ?>
<?php endwhile; ?>

```
> The above will loop 10 times and output the current loop number.

**[Read more about While & Loops at the BladeOne Wiki](https://github.com/EFTEC/BladeOne/wiki/Template-loop#whilecondition--endwhile)**

**Include**

```blade
@include('path.to.view')

// Parses to
<?php include 'base/path/to/view.php'; ?>
```
> The above will include the view file at `base/path/to/view.php`. The `base` path is set when creating the module.

**[See the BladeOne Docs for more info on include and associated methods(@includeif(), @includefast())](https://github.com/EFTEC/BladeOne/wiki/Template-inheritance#include)**

----

### Full Method Docs

> You can find the full method docs on the [EFTEC/BladeOne](https://github.com/EFTEC/BladeOne/wiki/Methods-of-the-class) wiki.

## BladeOne HTML

Out of the box, Perique BladeOne comes bundled with a selection of HTML helpers which make it much easier to generate HTML such as Forms, Links, Images, and more.

**Form**

```blade
@form(method="post" action="testform")
    @input(type="text" name="myform" value=$value)
    @textarea(name="description" value="default")
    @button(text="click me" type="submit" class="test" onclick='alert("ok")')
@endform

// Parses to
<form  method="post" action="testform">
  <input type="text" name="myform" value="<?php echo $this->e($value);?>" />
  <textarea  name="description" >default</textarea>
  <button type="submit" class="test" onclick='alert("ok")' >click me</button>
</form>

```
> The above will generate a form with a text input, textarea, and submit button.

**[See the BladeOne HTML Docs for more on form and form fields](https://github.com/EFTEC/BladeOneHtml#usage)**

## Custom Directives

There are a number built in BladeOne Directives which have been adapted from the original BladeOne package, to work better with Perique and WordPress.

**Auth**

```blade
@auth
    <p>Authenticated</p>
@else
    <p>Not Authenticated</p>
@endauth

// Parses to
<?php if(is_user_logged_in()): ?>
    <p>Authenticated</p>
<?php else: ?>
    <p>Not Authenticated</p>
<?php endif; ?>
```
> The above will output the correct block based on the current user's authentication status.

### It is also possible to use the `@auth` directive to check for specific roles.

```blade
@auth(role="administrator")
    <p>Administrator</p>
@elseauth(role="editor")
    <p>Editor</p>
@elseauth(role="author")
    <p>Author</p>
@endauth
```

### You can also check if in reverse

```blade
@guest('administrator')
    (not administrator)
@elseguest('editor')
    (not editor)
@endguest
```

> The above will output `(not administrator)` if the current user is not an administrator.
----

**Permissions**

```blade
@can(permission="edit_posts")
    <p>Can Edit Posts</p>
@elsecan(permission="manage_options")
    <p>Can Manage Options</p>
@endcan
```

> This works using WP User Capabilities, so you can use any of the [built in capabilities](https://codex.wordpress.org/Roles_and_Capabilities#Capabilities) or [create your own](https://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table).

## Auth & Permissions

----