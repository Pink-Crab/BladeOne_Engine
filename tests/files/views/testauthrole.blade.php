@auth('administrator')
Administrator
@elseauth('editor')
Editor
@endauth

@guest('administrator')
(not administrator)
@elseguest('editor')
(not editor)
@endguest

@guest()
(neither administrator or editor)
@endguest