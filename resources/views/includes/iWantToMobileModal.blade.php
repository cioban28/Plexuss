<div id="i-want-to-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="i-want-to-header-container">
        <div style="font-size: 30px">&nbsp;</div>
        <h2 class="i-want-to-modal-header">
            I want to
        </h2>
        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
    <div class="i-want-to-modal-content">
        <div class="i-want-to-link" data-href="/college">
            <div class="i-want-to-image research-universities"></div>
            <div class='content-label'>Research Universities</div>
        </div>

        <div class="i-want-to-link" data-href="/college-application">
            <div class="i-want-to-image apply-universities"></div>
            <div class='content-label'>Apply to Universities</div>
        </div>

        <div class="i-want-to-link" data-href="/college-application">
            <div class="i-want-to-image find-scholarships"></div>
            <div class='content-label'>Find Scholarships</div>
        </div>

        @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
            <div class="i-want-to-link" data-href="/agency-search">
                <div class="i-want-to-image talk-to-advisor"></div>
                <div class='content-label'>Talk to an Advisor</div>
            </div>
        @endif
    </div>
</div>