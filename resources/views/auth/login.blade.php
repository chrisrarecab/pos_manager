@extends('auth')
@section('style')
@endsection

@section('title', 'Login')

@section('content')
<div class="container">
    <session-component></session-component>
    @if(! session('userId'))
    <login></login>
    @endif
</div>

@endsection