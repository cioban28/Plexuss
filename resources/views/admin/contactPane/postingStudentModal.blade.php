<div id="posting-student-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    <div>
        <h4 class='posting-student-name'></h4>        
    </div>
    <div>
        <label for="posting-student-client-select">Select Posting Client</label>
        <select id='posting-student-client-select'>
            {{-- Ajax clients in here --}}
        </select>
    </div>

    <div>
        <label for="posting-student-college-select">Select Posting College</label>
        <select id='posting-student-college-select'>
            {{-- Ajax colleges in here --}}
        </select>
    </div>

    <div>
        <label for="posting-student-program-select">Select Posting Program</label>
        <select id='posting-student-program-select'>
            {{-- Ajax programs in here --}}
        </select>
    </div>

    <input type="hidden" id="posting-student-user_id" value="" />
    <div class='posting-student-errors'>
        {{-- Ajax clients in here --}}
    </div>

    <div class='row text-center'>
        <div class="columns small-1">
            <input type="checkbox" id= "gdpr_phone" name="gdpr_phone" required="required">    
        </div>
        <div class="columns small-1 end" style="font-size: 1px;">
            <label for='gdpr_phone' style="font-size: 11em;padding-top: 0.5em;padding-right: 8em;">Phone</label>
        </div>
    </div>

    <div class='row text-center'>
        <div class="columns small-1">
            <input type="checkbox" id= "gdpr_email" name="gdpr_email" required="required">    
        </div>
        <div class="columns small-1 end" style="font-size: 1px;">
            <label for='gdpr_email' style="font-size: 11em;padding-top: 0.5em;padding-right: 8em;">Email</label>
        </div>
    </div>

    <div class="posting-student-button disabled">Post Student</div>

    <div class="text-center posting-manage-students-ajax-loader">
        <svg width="70" height="20">
            <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
                <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
            </rect>
            <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
                <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
            </rect>
            <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
                <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
            </rect>
        </svg>
    </div>
</div>