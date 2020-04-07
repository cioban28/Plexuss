<div id="incoming-call-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click:false;close_on_esc:false;" data-animate="slide-in-up slide-in-down">
    <div class='incoming-modal-actions-container'>
        <div class='minimize-modal-action-button close-incoming-modal-button'>Close</div>
        <div class='minimize-modal-action-button minimize-incoming-modal-button'>Minimize</div>
    </div>
    <div class='incoming-call-request-container'>
        <h4 class="modal-header">You have an incoming call</h4>
        <h3 class='incoming-phone-number'></h3>
        <div class='incoming-call-button answer-call-button'>Answer</div>
        <div class='incoming-call-button disconnect-call-button'>Disconnect</div>
    </div>
    <div class='incoming-call-active-container hidden'>
        <div class='left-side-active-call'>
            <div class='incoming-phone-number'>+1 (707) 123-4567</div>
            <div class='incoming-call-timer'>00:00</div>
            <div class='incoming-call-mute-toggle-button'>
                <div class='incoming-call-mute-icon'></div>
                <div class='incoming-call-mute-text'>Mute</div>
            </div>

        </div>
        <div class='right-side-active-call'>
            <div class='active-incoming-call-button incoming-call-end-button'>
                <div class='incoming-end-call-icon'></div>
                <div class='incoming-end-call-text'>End Call</div>
            </div>
            <div class='active-incoming-call-button incoming-call-record-button'>
                <div class='incoming-record-call-icon'></div>
                <div class='incoming-record-call-status'>Call is recording</div>
            </div>
            <div class='incoming-previously-called-log-container'>
                <div class='incoming-previously-called-header'>Previously Called</div>
                <div class='incoming-previously-called-log'></div>         
            </div>
        </div>
    </div>
</div>

<!-- Minimized incoming call, should only show if incoming-call was minimized -->
<div class='minimized-incoming-call hidden'>
    <div class='minimized-incoming-phone-number'>+1 (707) 123-4567</div>
    <div class='minimized-incoming-timer-number'>00:00:00</div>
    <div class='minimized-end-button'></div>
</div>