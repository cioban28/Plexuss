<div class='floating-header-mobile hide-for-medium-up row collapse'>
    <div class="text-center column twenty @if( isset($type) && $type == 'yourlist') active @endif" onclick="loadPortalTabs('portal')">
        <img src="/images/nav-icons/list.png">
        <!-- <div class="badge">10</div> -->
    </div>
    <div class="text-center column twenty @if( isset($type) && $type == 'recommendationlist') active @endif" onclick="loadPortalTabs('recommendationlist')">
        <img src="/images/nav-icons/recommended.png">
    </div>
    <div class="text-center column twenty @if( isset($type) && $type == 'collegesrecruityou') active @endif" onclick="loadPortalTabs('collegesrecruityou')">
        <img src="/images/nav-icons/nav-college-hover.png">
    </div>

    <div class="text-center column twenty @if( isset($type) && $type == 'collegesviewedprofile') active @endif" onclick="loadPortalTabs('collegesviewedprofile')">
        <img src="/images/nav-icons/nav-colleges-viewing-you.png">
    </div>
    
    <div class="text-center column twenty @if( isset($type) && $type == 'trash') active @endif" onclick="loadPortalTabs('getTrashSchoolList')">
        <img src="/images/nav-icons/trash-big.png">  
    </div>
</div>
