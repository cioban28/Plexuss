<div class="column small-12 large-3 show-for-large-up">
  <div class="small-12 large-9 column"> <a onclick="openmainTab(event,'list')" class="bck-button button expand @if(count($scholarships)> 0 && $page_type !='add') active  @endif">
    Scholarships
    </a> </div>
  <br />
  <div class="small-12 large-9 column"> <a class="bck-button button expand @if(count($scholarships)== 0 || $page_type =='add') active @endif" @if(isset($scholarship_info->id)) href="{{url('scholarshipadmin/add')}}" @else onclick="openmainTab(event,'add')" @endif>
   Add Scholarship
    </a> </div>
</div>
