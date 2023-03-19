{{ $foo }}
@form(method="post" action="testform")
    @input(type="text" name="myform" value=$foo)
    @textarea(name="description" value="default")
    @button(text="click me" type="submit" class="test" onclick='alert("ok")')
@endform