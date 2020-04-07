<!doctype html>
<html class="no-js" lang="en">
  <head>
    <title>Home page summits for Plexuss</title>


    <style type="text/css">
      th {
        text-align: left;
        padding-right: 10px;
        background-color: grey;
      }

      td {
        padding-right: 10px;
      }

      /*td:nth-child(odd) {background: #CCC}*/
      tbody tr:nth-child(odd) {background: #ccc}

      tfoot td {
        border: solid 1px #000;
      }

      h2 {
        margin-bottom: 0;
        padding-bottom: 0;
      }

      .box {
          display: inline-block;
          width: 350px;
          margin: 10px;
      }

    </style>
  </head>
  <body>
    

    <h1>Plexuss Site information</h1>

    <h2>Beta User that signed up on for Plexuss</h2>

    <div class="box">Total number of users: <b>{{ $total_users_count or ''}}</b></div>
    @if($show_ldy === true)
    <div class="box">Total LDY users: <b>{{ $total_users_ldy_count or ''}}</b></div> 
    @endif
    <br/>
    <div class="box">Today's users: <b>{{ $total_today_count or ''}}</b></div>
    @if($show_ldy === true)
    <div class="box">Today's users: <b>{{ $total_today_ldy_count or ''}}</b></div>
    @endif
    <br/>
    <div class="box">Yesterday's users: <b>{{ $total_yesterday_count or ''}}</b></div>
    @if($show_ldy === true)
    <div class="box">Yesterday's users: <b>{{ $total_yesterday_ldy_count or ''}}</b></div>
    @endif
    <br/>
    <div class="box">This week's users: <b>{{ $this_week_count or ''}}</b></div>
    @if($show_ldy === true)
    <div class="box">This week's users: <b>{{ $this_week_ldy_count or ''}}</b></div>
    @endif
    <br/>
    <div class="box">This month's users: <b>{{ $this_month_count or ''}}</b></div>
    @if($show_ldy === true)
    <div class="box">This month's users: <b>{{ $this_month_ldy_count or ''}}</b></div>
    @endif
    <br/>
    <div class="box">Last month's users: <b>{{ $last_month_count or ''}}</b></div>
    @if($show_ldy === true)
    <div class="box">Last month's users: <b>{{ $last_month_ldy_count or ''}}</b></div>
    @endif
    <br/>

		<table>
      <thead>
        <tr>
          <th>count</th>
          <th>first name</th>
          <th>last name</th>
          <th>email</th>
          <th>city</th>
          <th>state</th>
          <th>country</th>
          <th>profile % done</th>
          <th>User Type</th>
          <th>Phone</th>
          <th>Major</th>
          <th>in high school?</th>
          <th>Grad Year</th>
          <th>date created</th>
        </tr>
      </thead>

      <tbody>

          <?php 
          $cnt = 1;
          $betaUserCounter= $total_users_count +1; ?> 
            @foreach ($betaUsers as $dt) 
          <?php 

          $betaUserCounter--;
          $cnt++;

          if($cnt >= 1000){
            break;
          }
           ?>
        <tr>
          <td>{{ $betaUserCounter }}</td>
          <td>{{ $dt->fname }}</td>
          <td>{{ $dt->lname }}</td>
          <td>{{ $dt->email}}</td>
          <td>{{ $dt->city }}</td>
          <td>{{ $dt->state }}</td>
          <td>{{ $dt->country_name }}</td>
          <td>{{ $dt->profile_percent }}</td>
          <?php 
          $student_type ="";

          if($dt->is_student){
             $student_type .="Student ";
          }

          if($dt->is_intl_student){
             $student_type .="Intl Student ";
          }
          if($dt->is_alumni){
             $student_type .="Alumni ";
          }
          if($dt->is_parent){
             $student_type .="Parent ";
          }
          if($dt->is_counselor){
             $student_type .="Counselor ";
          }
           ?>
          <td>{{ $student_type or ''}}</td>
          <td>{{ $dt->phone }}</td>
          <td>{{ $dt->major }}</td>
          @if(isset($dt->in_college) && $dt->in_college == 1)
            <td>in college</td>
            <td>{{$dt->college_grad_year or ''}}</td>
          @else
            <td>in high school</td>
            <td>{{$dt->hs_grad_year or ''}}</td>
          @endif
          <td>{{ $dt->created_at }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=7>
            Total Beta User count is {{$betaUserCounter}}
          </td>
        </tr>
      </tfoot>
    </table>

    <h2>Advertising Submits for Plexuss</h2>
    <table>
      <thead>
        <tr>
          <th>count</th>
          <th>company</th>
          <th>title</th>
          <th>email</th>
          <th>phone</th>
          <th>tell us more</th>
          <th>created at</th>
        </tr>
      </thead>

      <tbody>
          <?php $advertisingSubmitsCount=0 ?> 
            @foreach ($advertisingSubmits as $as)
          <?php $advertisingSubmitsCount++ ?>
        <tr>
          <td>{{ $advertisingSubmitsCount }}</td>
          <td>{{ $as->company }}</td>
          <td>{{ $as->title }}</td>
          <td>{{ $as->email}}</td>
          <td>{{ $as->phone }}</td>
          <td>{{ $as->notes }}</td>
          <td>{{ $as->created_at }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=7>
            Total advertising submits count is {{$advertisingSubmitsCount}}
          </td>
        </tr>
      </tfoot>
    </table>

    <h2>Career Submits for Plexuss</h2>
    <table>
      <thead>
        <tr>
          <th>count</th>
          <th>position</th>
          <th>first name</th>
          <th>last name</th>
          <th>email</th>
          <th>phone</th>
          <th>zipcode</th>
          <th>school</th>
          <th>grade_level</th>
          <th>counselor</th>
          <th>gpa</th>
          <th>camid</th>
          <th>specid</th>
          <th>pixel</th>
          <th>created at</th>
        </tr>
      </thead>
      <tbody>
          <?php $CareersSubmitsCount=0 ?> 
            @foreach ($careersSubmits as $cs)
          <?php $CareersSubmitsCount++ ?>
        <tr>
          <td>{{ $CareersSubmitsCount }}</td>
          <td>{{ $cs->position }}</td>
          <td>{{ $cs->fname }}</td>
          <td>{{ $cs->lname}}</td>
          <td>{{ $cs->email }}</td>
          <td>{{ $cs->phone }}</td>
          <td>{{ $cs->zipcode }}</td>
          <td>{{ $cs->school }}</td>
          <td>{{ $cs->grade_level }}</td>
          <td>{{ $cs->counselor }}</td>
          <td>{{ $cs->gpa }}</td>
          <td>{{ $cs->camid }}</td>
          <td>{{ $cs->specid }}</td>
          <td>{{ $cs->pixel }}</td>
          <td>{{ $cs->created_at }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=12>
            Total Career submits count is {{$CareersSubmitsCount}}
          </td>
        </tr>
      </tfoot>
    </table>

    <h2>College Care Package notify signups</h2>
    <table>
      <thead>
        <tr>
          <th>count</th>
          <th>email</th>
          <th>ip</th>
          <th>user id</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
          <?php $ccpNotify=0 ?> 
            @foreach ($carepackage_signups as $ccpNotifyObj)
          <?php $ccpNotify++;
          ?>
        <tr>
          <td>{{ $ccpNotify }}</td>
          <td>{{ $ccpNotifyObj['email'] or '' }}</td>
          <td>{{ $ccpNotifyObj['ip'] or ''}}</td>
          <td>{{ $ccpNotifyObj['user_id'] or ''}}</td>
          <td>{{ $ccpNotifyObj['created_at'] or ''}}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=12>
            Total CCP Notify count is {{$ccpNotify}}
          </td>
        </tr>
      </tfoot>
    </table>
    <h2>College Submits for Plexuss</h2>
    <table>
      <thead>
        <tr>
          <th>count</th>
          <th>company</th>
          <th>contact</th>
          <th>title</th>
          <th>email</th>
          <th>phone</th>
          <th>notes</th>
          <th>created at</th>
        </tr>
      </thead>
      <tbody>
          <?php $collegeSubmissionsCount=0 ?> 
            @foreach ($collegeSubmissions as $cs)
          <?php $collegeSubmissionsCount++ ?>
        <tr>
          <td>{{ $collegeSubmissionsCount }}</td>
          <td>{{ $cs->company }}</td>
          <td>{{ $cs->contact }}</td>
          <td>{{ $cs->title}}</td>
          <td>{{ $cs->email }}</td>
          <td>{{ $cs->phone }}</td>
          <td>{{ $cs->notes }}</td>
          <td>{{ $cs->created_at }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=8>
            Total College Submits count is {{$collegeSubmissionsCount}}
          </td>
        </tr>
      </tfoot>
    </table>

    <h2>Contact us Submits for Plexuss</h2>
    <table>
      <thead>
        <tr>
          <th>count</th>
          <th>first name</th>
          <th>last name</th>
          <th>email</th>
          <th>phone</th>
          <th>company</th>
          <th>tell us more</th>
          <th>created at</th>
        </tr>
      </thead>
      <tbody>
          <?php $contactSubmissionsCount=0 ?> 
            @foreach ($contactus as $cs)
          <?php $contactSubmissionsCount++ ?>
        <tr>
          <td>{{ $contactSubmissionsCount }}</td>
          <td>{{ $cs->fname }}</td>
          <td>{{ $cs->lname }}</td>
          <td>{{ $cs->email }}</td>
          <td>{{ $cs->phone }}</td>
          <td>{{ $cs->company }}</td>
          <td>{{ $cs->tell_us_more }}</td>
          <td>{{ $cs->created_at }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=8>
            Total Contact Submits count is {{$contactSubmissionsCount}}
          </td>
        </tr>
      </tfoot>
    </table>

    <h2>Scholarship Submits for Plexuss</h2>
    <table>
      <thead>
        <tr>
          <th>count</th>
          <th>scholarship title</th>
          <th>contact</th>
          <th>phone</th>
          <th>fax</th>
          <th>email</th>
          <th>address</th>
          <th>address 2</th>
          <th>city</th>
          <th>state</th>
          <th>zip</th>
          <th>deadline</th>
          <th>number of awards</th>
          <th>max amount</th>
          <th>website</th>
          <th>scholarship description</th>
          <th>created at</th>
        </tr>
      </thead>
      <tbody>
          <?php $scholarshipSubmissionsCount=0 ?> 
            @foreach ($scholarshipSubmission as $cs)
          <?php $scholarshipSubmissionsCount++ ?>
        <tr>
          <td>{{ $scholarshipSubmissionsCount }}</td>
          <td>{{ $cs->scholarship_title }}</td>
          <td>{{ $cs->contact }}</td>
          <td>{{ $cs->phone }}</td>
          <td>{{ $cs->fax }}</td>
          <td>{{ $cs->email }}</td>
          <td>{{ $cs->address }}</td>
          <td>{{ $cs->address2 }}</td>
          <td>{{ $cs->city }}</td>
          <td>{{ $cs->state }}</td>
          <td>{{ $cs->zip }}</td>
          <td>{{ $cs->deadline }}</td>
          <td>{{ $cs->number_of_awards }}</td>
          <td>{{ $cs->max_amount }}</td>
          <td>{{ $cs->website }}</td>
          <td>{{ $cs->scholarship_description }}</td>
          <td>{{ $cs->created_at }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan=17>
            Total Scholarship Submits count is {{$scholarshipSubmissionsCount}}
          </td>
        </tr>
      </tfoot>
    </table>
	</body>
</html>