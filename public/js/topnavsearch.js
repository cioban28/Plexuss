$(function() {
    var mDefault = 'default';
    if (window.location.pathname.split('/')[1] === 'admin') { mDefault = 'students';
        $('.top_search_txt').attr('placeholder', 'Search Students...'); }
    var cid = $('#topnavsearch').data('cid');
    getCollegeAutocomplete(mDefault, cid);
});
$(document).mouseup(function(e) { $('.closedivset').hide(); });

function expandDivContent(expandID, expandDiv) { $('#' + expandDiv).slideToggle(250, 'easeInOutExpo', function() { $('#' + expandID).toggleClass("run"); }); }

function ToggleDiv(DivID, expandDiv) { $('#' + DivID).click(function() { $('#' + expandDiv).toggle(); }); }
$("#filter-toggle-btn").click(function() { $(this).toggleClass("run");
    $(".filter-toggle").slideToggle(700); })

function ShowSearchMobile() { $('.new-mobile-search-plex-row').slideToggle(700); }
$(document).on('mouseenter', '.i-want-to-tab', function() { $('.i-want-to-dropdown').fadeIn(200); })
$(document).on('mouseleave', '.i-want-to-tab', function() { $('.i-want-to-dropdown').fadeOut(200); });
$(document).on('click', '.i-want-to-dropdown .dropdown-link, #i-want-to-modal .i-want-to-modal-content .i-want-to-link', function(event) {
    var href = $(this).data('href'),
        label = $(this).find('.content-label').html(),
        pathArray = null,
        slug_1 = '',
        slug_2 = '';
    pathArray = window.location.pathname.split('/');
    pathArray.shift();
    slug_1 = pathArray.shift();
    slug_2 = pathArray.join('/');
    if (typeof amplitude !== 'undefined') { amplitude.getInstance().logEvent('click_i_want_to', { 'Location 1': slug_1, 'Location 2': slug_2, 'Destination Slug': href.replace('/', ''), 'Destination Content': label }); }
    window.location.href = href;
});

function setSearch() {
    $('.top_search_filter').show();
    $('.top_search_filter ul li').click(function() {
        var ID = $(this).attr('id');
        var text = $(this).children('span').html();
        var imgClass = 'search-default-img';
        var cid = $('#topnavsearch').data('cid');
        switch (ID) {
            case 'college':
                imgClass = "search-college-img"; break;
            case 'news':
                imgClass = "search-news-img"; break;
            case 'students':
                imgClass = 'search-students-img'; break; }
        $('.search-default-txt').removeClass('search-hamburger-img search-default-img search-college-img search-news-img search-students-img');
        $('.search-default-txt').addClass(imgClass);
        if (ID === 'ranking' || ID === 'news' || ID === 'default') { $('.search-default-txt').css('top', '5px'); } else if (ID === 'students') { $('.search-default-txt').css('top', '8px'); } else { $('.search-default-txt').css('top', '10px'); }
        $('.top_search_txt').attr('placeholder', text);
        $('.top_search_type').val(ID);
        if (ID === 'students')
            getCollegeAutocomplete(ID, cid);
        else
            getCollegeAutocomplete(ID);
        $('.top_search_filter').hide();
    })
}

function redirectSearch() {
    var value = $('.top_search_txt').val();
    var type = $('.top_search_type').val();
    var mDefault = 'default';
    if (window.location.pathname.split('/')[0] === 'admin')
        mDefault = 'students';
    if (type == '') { window.location = '/search?type=' + mDefault + '&term=' + value; } else { window.location = '/search?type=' + type + '&term=' + value; }
}

function getCollegeAutocomplete(stype, cid) {
    var src = "/getTopSearchAutocomplete?type=" + stype;
    if (cid)
        src += "&cid=" + cid;
    $(".top_search_txt").autocomplete({
        minLength: 1,
        delay: 0,
        source: src,
        create: function() {
            $(this).data("ui-autocomplete")._renderMenu = function(ul, items) {
                this.widget().menu('option', "items", "> :not(.ui-autocomplete-category, .ui-autocomplete-all-results-btn)");
                if (typeof items[0]['category'] !== 'undefined') { var top_inner_html = '<!-- <li><img src="images/select-collge-logo.png" /><span class="c79 pl5 ">Search <span class="f-bold">Georgia</span> in <span class="f-bold c-98d">Colleges</span></span></li><li><img src="images/select-ranking-logo.png" /><span class="c79 pl5 ">Search <span class="f-bold">Georgia</span> in <span class="f-bold c-98d">Rankings</span></span></li><li><img src="images/select-news-logo.png" /><span class="c79 pl5 ">Search <span class="f-bold">Georgia</span> in <span class="f-bold c-98d">News</span></span></li>-->';
                    ul.append(top_inner_html); }
                var that = this,
                    currentCategory = "";
                $.each(items, function(index, item) {
                    if (typeof item.category !== 'undefined' && item.category != currentCategory) { ul.append("<li class='ui-autocomplete-category'>" + item.category + "<span><a href='/search?type=" + item.type + "&term=" + item.term + "'>More..</a></span></li>");
                        currentCategory = item.category; }
                    that._renderItemData(ul, item);
                });
                var searchIn = (stype === 'default') ? '' : ('in ' + stype);
                var bottom_inner_html = '';
                bottom_inner_html += '<div class="auto-bottom-div">';
                bottom_inner_html += '<a href="/search?type=' + items[0].searchtype + '&term=' + items[0].term + '&cid=' + items[0].cid + '">view all results' + '</a>';
                bottom_inner_html += '</div>';
                ul.append("<li class='text-center ui-autocomplete-all-results-btn'>" + bottom_inner_html + "</li>");
            }

            , $(this).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>").attr("data-value", item.value).append("<img height=32 width=32 src='" + item.image + "' />" + item.label).appendTo(ul);
            }},
        select: function(event, ui) {
            var item = ui.item;
            var newsurl = '';
            if (item.type == 'news') { newsurl = 'article/'; }
            if (item.searchtype === 'students') { window.location.href = '/admin/' + item.type; } else { window.location.href = '/' + item.type + '/' + newsurl + item.slug; }
        }
    });
    $(".top_search_txt").data("ui-autocomplete")._renderItem = function(ul, item) {
        var newsurl = '';
        if (item.type == 'news') { newsurl = 'article/'; }
        var inner_html = '';
        if (item.searchtype === 'students') { inner_html += '<a href="/admin/' + item.type + '">';
            inner_html += '<div class="list_item_container clearfix">';
            inner_html += '<div class="res-leftside">';
            inner_html += '<div class="list_item_image_container"><img class="image" src="' + item.image + '" alt=""/></div>';
            inner_html += '<div class="title">' + item.value + '</div>';
            inner_html += '<div class="res-email">' + (item.email || 'N/A') + '</div>';
            inner_html += '</div>';
            inner_html += '<div class="res-rightside"><div>' + item.tab + '</div> <div class="res-phone">' + (item.phone || 'N/A') + '</div></div>';
            inner_html += '</div></a>'; } else { inner_html += '<a href="/' + item.type + '/' + newsurl + item.slug + '">';
            inner_html += '<div class="list_item_container clearfix">';
            inner_html += '<div class="list_item_image_container"><img class="image" src="' + item.image + '" alt=""/></div>';
            inner_html += '<div style="padding-left: 64px;">';
            inner_html += '<div class="title">' + item.value + '</div>';
            inner_html += '<span class="description">' + item.desc + '</span>';
            inner_html += '</div>';
            inner_html += '</div>'; 
            inner_html += '</a>'; }
        return $("<li class='junk'>" + inner_html + "</li>").appendTo(ul);
    }
}
