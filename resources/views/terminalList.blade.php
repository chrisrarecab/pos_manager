@extends('layout')
@section('style')
@endsection

@section('content')
<div class="container">
    <terminal-list-component :detail="{{ json_encode($detail) }}"></terminal-list-component>
</div>

@endsection