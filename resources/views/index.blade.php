@include('components.head')
@if (Auth::check())
    @include('components.loged')
@else
    @include('components.notloged')
@endif
@include('components.footer')