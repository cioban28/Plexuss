	<div class='small-3 column'>
		<div class='row'>
			<div id ='admin_manage'class='small-12 column text-center no-padding'>
				<div class='row'>
					<div class='small-12 column'>
						<span class='admin_heading'>Manage</span>
					</div>
				</div>
				<div class='row' data-equalizer>
					<div class='small-3 column admin_manage_img text-center admin_flyout' data-equalizer-watch>
						<img src='/images/admin/plus.png'>
					</div>
					<div class='small-9 column admin_manage_text admin_flyout' data-equalizer-watch>
						<a href="/admin/add">Add New...</a>
							<ul>
								@if($admin['news'] == 'w')
								<li>
									<div class='row'>
										<div class='small-3 column'>
											<img src='/images/admin/white-plus.png'>
										</div>
										<div class='small-9 column'>
											<a href="/admin/add/news">News Article</a>
										</div>
									</div>
								</li>
								@endif
								@if($admin['rankings'] == 'w')
								<li>
									<div class='row'>
										<div class='small-3 column'>
											<img src='/images/admin/white-plus.png'>
										</div>
										<div class='small-9 column'>
											<a href="/admin/add/ranking">Ranking List</a>
										</div>
									</div>
								</li>
								@endif
								@if($admin['careers'] == 'w')
								<li>
									<div class='row'>
										<div class='small-3 column'>
											<img src='/images/admin/white-plus.png'>
										</div>
										<div class='small-9 column'>
											<a href="/admin/add/career">Job Posting</a>
										</div>
									</div>
								</li>
								@endif
								@if($admin['careers'] == 'w')
								<li>
									<div class='row'>
										<div class='small-3 column'>
											<img src='/images/admin/white-plus.png'>
										</div>
										<div class='small-9 column'>
											<a href="/admin/add/internship">Internship</a>
										</div>
									</div>
								</li>
								@endif
								@if($admin['scholarships'] == 'w')
								<li>
									<div class='row'>
										<div class='small-3 column'>
											<img src='/images/admin/white-plus.png'>
										</div>
										<div class='small-9 column'>
											<a href="/admin/add/scholarship">Scholarship</a>
										</div>
									</div>
								</li>
								@endif
							</ul>
					</div>
				</div>
				@if($admin['colleges'] == 'r' || $admin['colleges'] == 'w')
				<div class='row'data-equalizer>
					<a href='/admin/colleges/'>
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/college.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Colleges</span>
						</div>
					</a>
					<!-- COLLEGE FLYOUT SIDEBAR SECTION -->
					<div class='row'>
						<div class='small-12 column text-left admin_college_section_list'>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/stats" :
										"/admin/add/college/new/stats" }}
								'>
									<div class='small-9 column no-padding' style='font-weight:bold;'>
										<span>Stats</span>
									</div>
									<div class='small-2 column left'>
										<span class='admin_college_num_bubble'>
											3
										</span>
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/value" :
										"/admin/add/college/new/value" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Value</span>
									</div>
									<div class='small-2 column left'>
										<span class='admin_college_num_bubble' style='width:30px;'>
											46
										</span>
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/admissions" :
										"/admin/add/college/new/admissions" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Admissions</span>
									</div>
									<div class='small-2 column left'>
										<span class='admin_college_num_bubble' style='width:40px;'>
											117
										</span>
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/notables" :
										"/admin/add/college/new/notables" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Notables</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/campus_life" :
										"/admin/add/college/new/campus_life" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Campus Life</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/athletics" :
										"/admin/add/college/new/athletics" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Athletics</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/tuition" :
										"/admin/add/college/new/tuition" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Tuition</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/financial_aid" :
										"/admin/add/college/new/financial_aid" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Financial Aid</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/enrollment" :
										"/admin/add/college/new/enrollment" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Enrollment</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
							<div class='row'>
								<a href='
									{{ isset($college->id) ? 
										"/admin/edit/college/" . $college->id . "/calendar" :
										"/admin/add/college/new/calendar" }}
								'>
									<div class='small-9 column no-padding'>
										<span>Calendar</span>
									</div>
									<div class='small-2 column left'>
										<!-- IMAGE GOES HERE -->
									</div>
								</a>
							</div>
						</div>
					</div>
					<!-- END COLLEGE FLYOUT SIDEBAR -->
				</div>
				@endif
				@if($admin['news'] == 'r' || $admin['news'] == 'w')
				<div class='row' data-equalizer>
					<a href="/admin/news">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/news.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>News</span>
						</div>
					</a>
				</div>
				@endif
				@if($admin['rankings'] == 'r' || $admin['rankings'] == 'w')
				<div class='row'data-equalizer>
					<a href="/admin/rankings">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/ranking.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Ranking Lists</span>
						</div>
					</a>
				</div>
				@endif
				@if($admin['careers'] == 'r' || $admin['careers'] == 'w')
				<div class='row'data-equalizer>
					<a href="/admin/careers">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/jobs-intern.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Jobs/Internships</span>
						</div>
					</a>
				</div>
				@endif
				@if($admin['scholarships'] == 'r' || $admin['scholarships'] == 'w')
				<div class='row'data-equalizer>
					<a href="/admin/scholarships">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/scholarships.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Scholarships</span>
						</div>
					</a>
				</div>
				@endif
				@if($admin['right_col'] == 'r' || $admin['right_col'] == 'w')
				<div class='row'data-equalizer>
					<a href="/admin/right_col">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/right-side-column.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Right Column</span>
						</div>
					</a>
				</div>
				@endif
				@if($admin['feed'] == 'r' || $admin['feed'] == 'w')
				<div class='row'data-equalizer>
					<a href="/admin/feed">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/feed.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Feed</span>
						</div>
					</a>
				</div>
				@endif
				@if($admin['user_privilege'] == 'r' || $admin['user_privilege'] == 'w')
				<div class='row'data-equalizer>
					<a href="/admin/users">
						<div class='small-3 column admin_manage_img text-center' data-equalizer-watch>
							<img src='/images/admin/users.png'>
						</div>
						<div class='small-9 column admin_manage_text' data-equalizer-watch>
							<span>Users</span>
						</div>
					</a>
				</div>
				@endif
			</div>
		</div>
	</div>
