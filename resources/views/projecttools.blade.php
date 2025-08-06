@extends('layout')
@section('style')
@endsection

@section('content')
<session-component></session-component>
@if(session('userId'))
<div class="container">
    <cancel-ptu-component></cancel-ptu-component>
</div>
@endif
@endsection