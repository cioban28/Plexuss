@extends('private.admin.news.master')
@section('content')
<!doctype html>
<!-- Left Side Part -->
				<div class="news-header hidden-display"> 
					<div class="new-header-top">
						<p class="new-head-title"><span>News List</span>  </p>
					</div>
					<div class="new-header-bottom">
						
					</div>
				</div>
				<div class="new-description">
					<div class='row'>
						<div class="large-12 column">
							<a class="button expand" href="/addnews">Add News</a>
						</div>
						<div class="large-12 column listNewsRowContainer">
							<div class="row newsRow">
								<div class="large-1 column"><span><b>ID</b></span></div>
								<div class="large-3 column"><span><b>Category</b></span></div>
								<div class="large-4 column"><span><b>News Title</b></span></div>
								<div class="large-1 column"><span><b>SM</b></span></div>
								<div class="large-1 column"><span><b>LG</b></span></div>
								<div class="large-1 column"><span><b>Edit</b></span></div>
								<div class="large-1 column"><span><b>View</b></span></div>
							</div>
						@foreach ($data['news'] as $key => $value)
							<div class="row newsRow">
								<div class="large-1 column">{{ $value['id'] }}</div>
								<div class="large-3 column">{{ $value['news_subcategory_id'] }}</div>
								<div class="large-4 column">{{ $value['title'] }}</div>
								<div class="large-1 column">
									@if ($value['img_sm'] == '')
										{{ 'N' }}
									@else
										{{ 'Y' }}
									@endif
								</div>
								<div class="large-1 column">
									@if ($value['img_sm'] == '')
										{{ 'N' }}
									@else
										{{ 'Y' }}
									@endif
								</div>
								<div class="large-1 column"><a href={{ "/editnews/" . $value ['id'] }}>edit</a></div>
								<div class="large-1 column"><a href={{ "/news/article/" . $value ['slug'] }} target="_blank">view</a></div>
							</div>
						@endforeach
						</div>
					</div>
				</div>
@stop
