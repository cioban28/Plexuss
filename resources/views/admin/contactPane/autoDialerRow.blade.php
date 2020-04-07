<?php
    // admin-inquiries uses $inquiries
    isset($inquiries) ? $auto_dialer_list = $inquiries : NULL;

    // admin-pending uses $auto_dialer_list
    isset($inquiry_list) ? $auto_dialer_list = $inquiry_list : NULL;

    !isset($auto_dialer_list) ? $auto_dialer_list = [] : NULL;

    $auto_dialer_black_list = isset($auto_dialer_black_list) ? $auto_dialer_black_list : [];
?>

<div class="row auto-dialer-row-container hidden" data-org_branch_id="{{$org_branch_id or NULL}}" data-admin_user_id="{{$user_id or NULL}}" data-autodialerblacklist='{{ json_encode($auto_dialer_black_list) }}' data-autodialerlist='{{ json_encode($auto_dialer_list) }}'>
    <div class='column auto-dialer-header-container'>
        <span class='auto-dialer-header-text'>Auto Dial</span>
        <label class="switch">
          <input class="auto-dial-button toggle-button" type="checkbox">
          <span class="slider round"></span>
        </label>
    </div>

    <div class='column auto-dialer-current-phone medium-8'></div>
    <div class='column auto-dialer-current-name medium-7' data-tooltip aria-haspopup="true" title=""></div>
    <div class='column medium-3'><div class='auto-dialer-call-action-button auto-dialer-call-button'></div></div>

    <div class='column auto-dialer-call-duration'></div>

    <div class='column medium-3'><div class='auto-dialer-call-action-button auto-dialer-end-call-button hidden'></div></div>

    <div class='column medium-4 auto-dialer-change-user-button auto-dialer-previous-button'>Previous</div>
    <div class='column medium-4 auto-dialer-change-user-button auto-dialer-next-button'>Next</div>
</div>