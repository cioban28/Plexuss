@extends('admin.master')
@section('content')

<div class='row'>
	<div class='small-4 column'>
		<a href="/admin/add/news" class="button">Add News</a>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<table id="admin_news_list" class="admin_table display">
			<thead>
				<tr>
					<td>Date</td>
					<td>Category</td>
					<td>Subcategory</td>
					<td>Title</td>
					<td>Author</td>
					<td>Meta</td>
					<td>QA</td>
					<td>Live</td>
				</tr>
			</thead>
			<tbody>
				@foreach($news_list as $article)
					<tr>
						<td>{{ $article->date }}</td>
						<td>{{ $article->category }}</td>
						<td>{{ $article->subcategory }}</td>
						<td class='admin_hover_container'>
							<a href="/news/article/{{ $article->slug }}">{{ $article->title  }}</a>
							<div class='admin_hover_controls'>
								<span><a href='/admin/edit/news/{{ $article->id }}'>Edit</a> | </span>
								<span><a href='/news/article/{{ $article->id }}'>View</a> | </span>
								<span><a href='/admin/delete/news/{{ $article->id }}'>Delete</a></span>
							</div>
						</td>
						<td>{{ $article->author  }}</td>
						<td>
							@if(
								!is_null($article->page_title) &&
								!is_null($article->meta_keywords) &&
								!is_null($article->meta_description) &&
								!is_null($article->slug)
							)
								<img src='/images/admin/check-gray.png'/>
							@endif
						</td>
						<td>
							@if(!is_null($article->who_qa))
								<img src='/images/admin/check-gray.png'/>
							@endif
						</td>
						<td>
							@if($article->live_status == 1)
								<img src='/images/admin/check-green.png'/>
							@elseif($article->live_status == -1)
								<span style='color: #797979;font-size:20px;'>--</span>
								<!--
								<img src='/images/admin/old.png'/>
								-->
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
