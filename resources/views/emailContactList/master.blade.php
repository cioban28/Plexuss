<?php 
    // dd($contactList);
?>

<!doctype html>
<html class="no-js" lang="en">
    <head>
        @include('private.headers.header')
    </head>
    <body>
        <div id='contact-list' data-contact-list='{{ json_encode($contactList) }}'></div>
        @include('emailContactList.footer')
        <script src='/js/handleOneAppEmailContactList.js'></script>
    </body>
</html>