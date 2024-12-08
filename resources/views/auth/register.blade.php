@extends('auth')
@section('style')
@endsection

@section('title', 'Register')

@section('content')
<div class="container">
    <register :secret="{{ json_encode($secret) }}"></register>
</div>

@endsection