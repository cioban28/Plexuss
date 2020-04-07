@extends('sales.master') 
@section('content')

@php 
	$column_headers = array('Template Name','Category', 'Provider', 'Sent', 'Open', 'Clicks', 'OR%', 'CR%','Complete','1','5','P','$');
@endphp

<div id="Admin_Component">
	<div class='admin-reporting-header'>
		<div class="containerHeader">
			<input type="text" id="searchTemplate" name="searchTemplate" placeholder="Search Template/Provider">
			<select id="category_filter" onchange="category_filter(this.value);">
					<option value="">Select a category</option>
				@foreach($emailTemplateCategory as $option)
			  	<option value="{{$option['id']}}"> {{$option['category']}} </option>
			  @endforeach
			</select>
		</div>			
		<div class="right-side-container">
			<div class="upperDiv">
				<form action="">
						<ul>
							<li>
								<input id="start_date" type="date" name="start_date" required>
							</li>
							<li>
								<input id="end_date" type="date" name="end_date" required>
							</li>
							<li>
								<button id="email_template_fetch">Search</button>
							</li>
						</ul>
				</form>
			</div>
			<div id="exportDiv" class="upperDiv">
				<a id="export">Export</a>
				<a id="fetchMonth">This Month</a>
				<a id="fetchYesterday">Yesterday</a>
				<a id="fetchToday">Today</a>
			</div>
		</div>
	</div>
	<div  id="tableDiv" class="reporting-wrapper">
		<table id="example" class="sortable" role="grid">
	 		<thead id="myHeader">
	 			<tr>
	 				@foreach($column_headers as $column)
          	<th> {{$column}} </th>
          @endforeach
	 			</tr>
	 		</thead>
	 		<tbody id="email_templates">
	 			@php
	 				$sumTotal=0;$sumOpen=0;$sumClick=0;$sumConversion=0;$total_open_rate=0; $total_click_rate=0;
	 				$sumComplete=0;$sumSelected_1_4=0;$sumSelected_5_more=0;$sumPremium=0;
	 			@endphp
					@foreach($countReturn as $temps)
	 					<tr>
	 						<td>
	 							<a id="{{ $temps['template']	}}" onclick="tempHTML(this.id);">
	 						 	{{ $temps['template']	}} 
	 						 	</a>
	 						</td>
	 						<td>
	 							<select id="category: {{ $temps['template']	}}" class="js-select" name="category" onchange='category_modification(this.value, "<?php echo $temps['template']; ?>" );'>
								  @foreach($emailTemplateCategory as $option)
								  	<option data-id={{$option['id']}} data-value="{{$option['category']}}" value= "{{ $option['id'] }}" 
								  		@php
								  			if( $option['category'] == $temps['category']){
									  			echo 'selected';
									  		}
								  		@endphp		
								  	> {{$option['category']}} </option>
								  @endforeach
								</select>
	 						</td>
	 						<td> {{	$temps['provider']	}} </td>
	 						<td> {{	number_format($temps['total'])}} </td>
	 						<td> {{	number_format($temps['open'])}} </td>
	 						<td> {{	number_format($temps['click'])}} </td>
	 						<td> {{	number_format($temps['open_rate'],2) }} </td>
	 						<td> {{	number_format($temps['click_rate'],2)}} </td>
	 						
	 						<td> {{	number_format($temps['complete'])}} </td>
	 						<td> {{	number_format($temps['selected_1_4'])}} </td>
	 						<td> {{	number_format($temps['selected_5_more'])}} </td>
	 						<td> {{	number_format($temps['premium'])}} </td>
	 						<td> {{	number_format($temps['conversion'])}} </td>
	 					</tr>
	 					@php
			 				$sumTotal += $temps['total'];
			 				$sumOpen += $temps['open'];
			 				$sumClick += $temps['click'];
			 				$sumConversion += $temps['conversion'];
			 				$sumComplete += $temps['complete'];
			 				$sumSelected_1_4 += $temps['selected_1_4'];
			 				$sumSelected_5_more += $temps['selected_5_more'];
			 				$sumPremium += $temps['premium'];
			 			@endphp
					@endforeach
				@php
					$total_open_rate = ($sumOpen/$sumTotal)*100;
					$total_click_rate = ($sumClick/$sumOpen)*100;
				@endphp		
				</tbody>
				<tfoot id="email_templates_foot">
					<tr>
			 			<td>Template Name</td>
			 			<td>Category</td>
			 			<td>Provider</td>
			 			<td> {{number_format($sumTotal)}} </td>
			 			<td> {{number_format($sumOpen)}} </td>
			 			<td> {{number_format($sumClick)}} </td>
			 			<td> {{number_format($total_open_rate,2)}} </td>
			 			<td> {{number_format($total_click_rate,2)}} </td>
			 			
			 			<td> {{number_format($sumComplete)}} </td>
			 			<td> {{number_format($sumSelected_1_4)}} </td>
			 			<td> {{number_format($sumSelected_5_more)}} </td>
			 			<td> {{number_format($sumPremium)}} </td>
			 			<td> {{number_format($sumConversion)}} </td>
		 			</tr>
	 		</tfoot>
	 	</table>
	</div>
</div>

<!-- Template HTML Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<table style="width: 100%;border: none;">
        		<tr>
        			<td>
        				<span id="title"></span>
        				<span id="from_email"></span>
        			</td>
        			<td>
        					<div id="email_div" style="display: none">
        						<p id="email_message" style="font-size: 14px;color: #00C273;margin: 0px;padding-bottom: 4px; text-align: -webkit-left;">Send a test to:
        						</p>
        							<p id="error_msg" style="text-align: left; display: none;font-size: 12px;font-weight: 600;"></p>
        						<div id="email_input">
        							<input type="text" name="test_email" id="test_email" placeholder="use commas to separate multiple emails">
	        						<button id="send_test">Send Test</button>
        						</div>
        						
        					</div>
        			</td>
        			<td> <a type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </a></td>
        		</tr>
        	</table>
        	
         
          </h5>
      </div>
      <div class="modal-body" id="modal-content">
      	
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
@stop