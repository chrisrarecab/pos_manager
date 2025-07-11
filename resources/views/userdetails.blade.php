@extends('layout')
@section('style')

@endsection

@section('content')
<div class="container">
    <!-- <h1 class="text-center"> Client Group</h1><br><br> -->

    <!-- <div style="position:relative;left:150px;">
        <h2>Users</h2>
    </div> -->
    
    <user-details-component :detail="{{ json_encode($detail) }}"></user-details-component>
</div>

@endsection