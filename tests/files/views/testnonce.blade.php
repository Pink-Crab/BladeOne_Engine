@switch($type)
    @case('with_ref_nonce')
        @nonce('with_ref_nonce', 'with_referer')
        @break
    @case('without_ref_nonce')
        @nonce('without_ref_nonce', 'without_referer', false)
        @break
    @default
        @nonce('def_ref_nonce')
@endswitch






