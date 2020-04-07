Plex.yt = Plex.yt || {};

$(document).ready(function(){

        //getting youvisit institution id to pass into api below
        var youvisit_university_id = $('#virtual-tour').data('universityid');

        //youvisit script/api
        var youvisit_script = '<a style="position: relative; height: 100px" href="http://www.youvisit.com" class="virtualtour_embed" title="Virtual Tour" data-inst="'+youvisit_university_id+'" data-link-type="image" data-image-width="98%" data-image-height="337" data-platform="plexuss">Virtual Tour</a>';
        youvisit_script += '<script async="async" src="https://www.youvisit.com/tour/Embed/js2"><\/script>';

        //storing the array size of college media array and virtual tour array on college overview page
        var imageCount = $('.default-overview-img').data('imagecount');
        var tourCount = $('.default-overview-img').data('tourcount');

        $("#owl-youtube").owlCarousel({
            navigation : false, // Show next and prev buttons
            navigationText : ["Prev","Next"],
            slideSpeed : 800,
            pagination : false,
            paginationSpeed : 400,
            singleItem:true,
            afterMove: function(elem){
                var visibleItems = this.visibleItems,
                    currentItem = this.$owlItems[visibleItems[0]],
                    item = $(currentItem).find('.item');

                if( item.hasClass('is-youniversity') ){
                    Plex.yt.renderYouniversity(item);
                }else{
                    if( $('.is-youniversity iframe').attr('src') !== '' ){
                        Plex.yt.destroyYouniversity(item);
                    }
                }
            }
        });//end of owlCarousel

        /***** check for redirect from homepage to determine if we need to show tour on load - start ****/
        $(window).on('load', function(){

            if(getRedirectParamFromFrontpage('showtour') == 'true'){
                $('#virtual-tour-tab').delay(3000).trigger('click');
            }
        });
        /***** check for redirect from homepage to determine if we need to show tour on load - end ****/

        //hide youtube carousel at page load
        $('#youtube-vid-carousel').hide();

        /*check if college media array is empty or not- if empty and school has a virtual tour, then show tour first, otherwise
        hide the tour and just show the default image*/
        if( imageCount == 0 && tourCount > 0 ){
            $('#college-carousel').hide();
            $('#college-pic').removeClass('active-tab');
            $('#virtual-tour-tab').addClass('active-tab');
            //show tour before default image on load
            $('#virtual-tour').html(youvisit_script);
        }else{
            $('#virtual-tour').hide();
        }

        //if VIDEO button clicked, then show youtube carousel and hide pics and tour
        $('#college-yt-vid').click(function(){
            var yt_item = $('#owl-youtube .item');
            $('#youtube-vid-carousel').show();
            $('#college-carousel').hide();
            $('#virtual-tour').hide();
            $('#college-pic').removeClass('active-tab');
            $('#virtual-tour-tab').removeClass('active-tab');
            $(this).addClass('active-tab');

            // if there's only one video and it's a youniversity vid, then render vid
            if( yt_item.length === 1 && yt_item.hasClass('is-youniversity') ){
                Plex.yt.renderYouniversity($('#owl-youtube .item.is-youniversity'));
            }
        });
        
        //if PICS button clicked then show pics and hide vids and tour
        $('#college-pic').click(function(){
            $('#youtube-vid-carousel').hide();
            $('#college-carousel').show();
            $('#virtual-tour').hide();
            $('#college-yt-vid').removeClass('active-tab');
            $('#virtual-tour-tab').removeClass('active-tab');
            $(this).addClass('active-tab');

            //if youniversity video is still on page, destroy it
            if( $('.is-youniversity iframe').attr('src') !== '' ){
                Plex.yt.destroyYouniversity($('.is-youniversity'));
            }
        });

        //if TOUR button clicked then show virtual tour and hide pics and video
        $('#virtual-tour-tab').click(function(){
            $('#virtual-tour').show();
            $('#youtube-vid-carousel').hide();
            $('#college-carousel').hide();
            $('#college-yt-vid').removeClass('active-tab');
            $('#college-pic').removeClass('active-tab');
            $(this).addClass('active-tab');
            //everytime virtual tour tab is clicked, embed the api in its div - work around for weird width percentage on tours
            $('#virtual-tour').html(youvisit_script);

            //if youniversity video is still on page, destroy it
            if( $('.is-youniversity iframe').attr('src') !== '' ){
                Plex.yt.destroyYouniversity($('.is-youniversity'));
            }
        });

});//end of document ready

function getRedirectParamFromFrontpage( arg ) {

    arg = arg.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

    var regex = new RegExp("[\\?&]" + arg + "=([^&#]*)"),
        results = regex.exec(location.search);
        
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

Plex.yt.renderYouniversity = function(item){
    var id = item.data('id'), src = '/college-youniversity/';

    //update iframe src
    if( id ){
        item.find('iframe').attr('src', src+id);
    }
}

Plex.yt.destroyYouniversity = function(){
    var youVid = $('.is-youniversity'), 
        resetHTML = '<iframe src="" frameborder="0" style="width:100%; height:553px;"></iframe><div class="loader"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="loading gif"></div>';

    youVid.html(resetHTML);
}