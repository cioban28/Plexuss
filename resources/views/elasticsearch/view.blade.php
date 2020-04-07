<!doctype html>
<html class="no-js" lang="en">
	<head>
	</head>
	<body>
		<h1>Elasticsearch Controller</h1>

		<h2>Tools you dont want to touch unless you know what they do.</h2>
		<div>
			<a href="/resetCollegeInfo">Delete the college Index.</a>
		</div>
		<div>
			<a href="/setmappingforcollege">Set College Mapping.</a>
		</div>
		<div>
			<a href="/getmappingforcollege">Get College Mapping.</a>
		</div>
		<div>
			<a href="/updatecollegelist">Update College Lists.</a>
		</div>
		<br/>
		<br/>
		<br/>
		<!--
		<h2>Search for stuff!</h2>
		<div>
			{{ Form::open(array('url' => '/searchcollegelist', 'method' => 'POST')); }}
				{{Form::label('searchBox', 'Type in a search result');}}
				{{Form::text('searchBox');}}
				{{Form::submit('Submit!', array('class' => 'name'));}}
			{{ Form::close(); }}
		</div>
	-->

	</body>
</html>
