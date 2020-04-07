@extends('checkout.master')

@section('content')
  <div class="premium-method-block">
    <div class="premium-box">
      <div class="flex-container">
        <div class="gold-image">
          <img src="/images/checkout/Premium-Gif-White.gif">
        </div>
      </div>
      <div class="confirm-head">
        <h4>Are you interested in GUARANTEED ADMISSION to a University in the USA?</h4>
      </div>
      <div class="confirm-msg">
        <p>Become a Premium Member for expert 1-on-1 support, up to five free applications, and guaranteed I-20 to get your visa!</p>
      </div>
      <div class="row large-12 medium-12 hollow-button-confirm">
        <button class = "confirmation-btn btn-left" href = "#">Yes, I want to be accepted!</button>
        <button class = "confirmation-btn btn-right" href = "#">No, I am not ready yet.</button>
      </div>
    </div>
  </div>
@stop
