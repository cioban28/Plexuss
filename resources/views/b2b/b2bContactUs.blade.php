@extends('b2b.master')

@section('b2b-content')

  <div id="contact-us-wrapper">
    <div class="sub-page-header">
      <h1>Contact Us</h1>
    </div>
    <div class="row">
      <div class="contact-form">
        <div class="header">
          <h3 class="head-text">THANK YOU FOR YOUR INTEREST IN PLEXUSS</h3>
          <div class="content-text">
            Please provide us with the below information so we may keep you fully updated on exciting developments regarding our company or to learn more about our solutions please visit our <a href="/solutions/our-solutions" class="solution-page-link">Solutions Page</a>.
          </div>
        </div>
        <form class="form" id="b2b-contact-form">
          <div class="row">
            <div class="check-field columns large-6 medium-12 small-12">
              <input type="text" name="first_name" placeholder="First name" required>
              <i class="check-mark fa fa-check" aria-hidden="true"></i>
            </div>
            <div class="check-field columns large-6 medium-12 small-12">
              <input type="text" name="last_name" placeholder="Last name" required>
              <i class="check-mark fa fa-check" aria-hidden="true"></i>

            </div>
          </div>
          <div class="row">
            <div class="columns check-field large-6 medium-12 small-12">
              <input type="text" name="company" placeholder="Institute or Company name" required>
              <i class="check-mark fa fa-check" aria-hidden="true"></i>
            </div>
            <div class="columns large-6 check-field medium-12 small-12">
              <input type="text" name="title" placeholder="Position" required>
              <i class="check-mark fa fa-check" aria-hidden="true"></i>
            </div>
          </div>
          <div class="row">
            <div class="columns large-6 medium-12 check-field small-12">
              <input type="email" name="email" placeholder="Email address" required>
              <i class="check-mark fa fa-check" aria-hidden="true"></i>
            </div>
            <div class="columns large-6 medium-12 small-12 check-field ">
              <input type="text" name="phone" placeholder="Phone number" required>
              <i class="check-mark fa fa-check" aria-hidden="true"></i>
            </div>
          </div>
          <div class="row">
            <div class="columns large-12 medium-12 small-12">
              <select class="multi-select" name="client_type" multiple required>
                <option value="domestic_recruitment">Domestic Recruitment</option>
                <option value="intl_recruitment">International Recruitment</option>
                <option value="retention_student_success">Retention & Student Success</option>
                <option value="consulting">Consulting & Project Management</option>
                <option value="advertising">Advertising</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="columns large-12 medium-12 small-12">
              <button type="submit" class="submit-btn">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  @include('b2b.b2bFooter')
@stop
