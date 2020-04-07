@extends('public.reset.master')
@section('content')
	@foreach ($users as $user)
    <p>Plexuss User Id = {{ $user->id }}, User Name = {{$user->fname}} {{$user->lname}}, User Email = {{$user->email}}, FaceBook ID = {{$user->fb_id}} </p>
	@endforeach

	{{Form::open(array('url' => 'reset'));}}
		{{Form::label('id', 'Plexuss User ID of user to delete');}}<br/>
			<select name="userid">
	  			@foreach ($users as $user) {
	    			<option value="{{$user->id}}">{{$user->id}}</option>
	  			}
	  			@endforeach
			</select>
		{{Form::submit('Submit!');}}
	{{Form::close();}}
@stop