@extends('checkout.master')

@section('content')
   <div class="congrats-method-block">
     <div class="congrats-box">
      <div class="flex-container">
         <div class="gold-images">
           <img src="/images/checkout/Plexuss premium badge gold.png">
         </div>
      </div>
       <div class="congrats-head">
          <h1>Congrats!</h1>
         <p>You are a Plexuss Premium Member now!</p>
       </div>
      <div class="congrats-msg">
        <p>We will start looking at your profile so we can begin the application process.
          Within the next few days, a member of our team will contact you directly. 
          Please check your inbox periodically and expect a call from us soon.
        </p>
      </div>
      <p>In the meantime, feel free to explore Plexuss.</p>
      <div class="row hollow-button">
        <button class = "btn" href = "#">Continue to Plexuss</button>
      </div>
     </div>
   </div>
   <div id="confetti-wrapper"></div>
@stop
