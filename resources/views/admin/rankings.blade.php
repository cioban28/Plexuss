@extends('admin.master')
@section('content')

<div class='row'>
	<div class='small-4 column'>
		<a href="/admin/add/ranking" class="button">Add Ranking List</a>
	</div>
</div>
<div class='row'>
	<div class='small-12 column'>
		<table id="admin_ranking_list" class="admin_table display">
			<thead>
				<tr>
					<td>Date</td>
					<td>Title</td>
					<td>Source</td>
					<td>Meta</td>
					<td>QA</td>
					<td>Live</td>
				</tr>
			</thead>
			<tbody>
				@foreach($ranking_list as $list)
					<tr>
						<td>{{ $list->date }}</td>
						<td class='admin_hover_container'>
							<a href="/news/article/{{ $list->slug }}">{{ $list->title  }}</a>
							<div class='admin_hover_controls'>
								<span><a href='/admin/edit/ranking/{{ $list->id }}'>Edit</a> | </span>
								<span><a href='/ranking/list/{{ $list->id }}'>View</a> | </span>
								<span><a href='/admin/delete/ranking/{{ $list->id }}'>Delete</a></span>
							</div>
						</td>
						<td>{{ $list->source  }}</td>
						<td>
							@if(
								!is_null($list->page_title) &&
								!is_null($list->meta_keywords) &&
								!is_null($list->meta_description) &&
								!is_null($list->slug)
							)
								<img src='/images/admin/check-gray.png'/>
							@endif
						</td>
						<td>
							@if(!is_null($list->who_qa))
								<img src='/images/admin/check-gray.png'/>
							@endif
						</td>
						<td>
							@if($list->live_status == 1)
								<img src='/images/admin/check-green.png'/>
							@elseif($list->live_status == -1)
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
