@extends('auth')
@section('style')
@endsection

@section('title', 'Register')

@section('content')

<div class="container">
    @if ($software =='POS-CORE')  
        <register-core :secret="{{ json_encode($secret) }}"></register-core>
    @else
        <register-cirms></register-cirms>
    @endif
</div>

@endsection