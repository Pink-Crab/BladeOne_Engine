@can('manage_options')
can_manage_options
@elsecan('edit_posts')
can_edit_posts
@endcan
@cannot('manage_options')
cannot_manage_options
@endcannot