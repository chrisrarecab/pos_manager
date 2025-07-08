@extends('layout')

@section('style')
<style>
    span.card-header{
        border-bottom: none;
        font-weight: 600;
    }
    .form-select.select-terminal {
        border-radius: .45rem;
        line-height: 1;
    }

</style>
@endsection

@section('content')

<div class="container">
 <terminal-settings-component :detail="{{ json_encode($detail) }}"></terminal-settings-component>
</div>



@endsection
@section('js')

@endsection
