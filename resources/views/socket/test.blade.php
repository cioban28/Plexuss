@extends('socket.master')

@section('content')
    <ul>
        <li v-repeat="user: users">@{{ user }}</li>
    </ul>
@stop