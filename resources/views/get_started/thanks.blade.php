<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body background="{{$company['edx']['background']}}">
<div class="padding-1"></div>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="logo">
                <img src="{{$company['edx']['logo']}}" class="logo-img"/>
                <p class="logo-content">Qualification Questions</p>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-back">
                <div class="clearfix"></div>
                <div class="text-center">
                    <p class="skip-para">Thank for completing your profile.</p>
                </div>
            </div>
            <div class="padding-2"></div>
            <div class="text-center">
                <a class="plexuss-link" href="https://plexuss.com">Stay On Plexuss</a>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<div class="padding-1"></div>
<div class="clearfix"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>

