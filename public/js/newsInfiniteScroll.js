Plex.ytv = { owl: null, is_article: false, searched: null, resultsHTML: null, resultsCount: null };
$(document).ready(function() {
    var path = window.location.pathname;
    path = path.split('/');
    if (path.indexOf('article') === -1) { Plex.ytv.is_article = true; }
    Plex.ytv.owl = $(".news-owl-carousel").owlCarousel({ navigation: Plex.ytv.is_article, pagination: false });
    $(window).scroll(function() { if ($(window).scrollTop() + 5 >= $(document).height() - $(window).height()) { if (AjaxHold == 0 && PageNumber != "ss") { scrollinfinite(); } } });
});
$(document).on('click', '.owl-container .owl-nav-btn', function() { if ($(this).hasClass('nav-next')) { Plex.ytv.owl.trigger('owl.next'); } else if ($(this).hasClass('nav-prev')) { Plex.ytv.owl.trigger('owl.prev'); } });
$(document).on('click', '.layer-container', function() {
    var id = $(this).data('id'),
        src = '/lightbox/';
    if (id) {
        $('#lightbox iframe').attr('src', src + id);
        $('#lightbox').foundation('reveal', 'open');
    }
});
$(document).on('click', function(e) {
    if ($(e.target).hasClass('university-search-tab') || $(e.target).hasClass('search-articles-btn') || $(e.target).hasClass('college-recruit'))
        e.preventDefault();
    var container = $('.search-articles-container'),
        target = $(e.target);
    if (target.hasClass('search-articles-btn') || target.hasClass('university-search-tab') || target.hasClass('magnifier')) { container.toggle(); } else if (target.hasClass('iframe-container') || target.hasClass('close-lightbox') || target.hasClass('close-reveal-modal')) {
        $(document).foundation('reveal', 'close');
        $('.iframe-container iframe').remove();
        $('.iframe-container').prepend('<iframe src="" frameborder="0"></iframe>');
    } else if (target.closest('.search-articles-container').length === 0) {
        $('.university-search-tab').removeClass('open');
        container.hide();
    }
});
$(document).on('click', '.submit-search', function(e) {
    e.preventDefault();
    var searchBar = $(this).closest('.search-bar'),
        input = searchBar.find('input[name="search_articles"]').val();
    if (input) {
        Plex.ytv.searched = input;
        Plex.ytv.searchArticles(input);
    }
});
$(document).on('keyup', 'input[name="search_articles"]', function(e) {
    if (e.keyCode === 13) {
        var input = $('input[name="search_articles"]').val();
        if (input) {
            Plex.ytv.searched = input;
            Plex.ytv.searchArticles(input);
        }
    }
});
$("#srch_articles").keypress(function() {
    var input = $("#srch_articles").val();
    if (input.length < 0) { $('.results').addClass('hide'); }
    if (input === '') { $('.results').addClass('hide'); }
});
Plex.ytv.searchArticles = function(term) {
    $('.loader').removeClass('hide');
    $.ajax({
        url: '/news/search',
        type: 'POST',
        data: { search: term },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(data) {
            $('.loader').addClass('hide');
            if (term != '') {
                Plex.ytv.resultsHTML = Plex.ytv.buildResults(data);
                Plex.ytv.render();
            }
        },
        error: function(error) {
            $('.loader').addClass('hide');
            topAlert({ textColor: '#fff', bkg: '#797979', msg: 'Oops. Looks like something went wrong. Check spelling. Try capitalizing search', type: 'soft', dur: 10000 });
        }
    });
}
Plex.ytv.buildResults = function(results) {
    var html = '',
        source = null,
        url = '';
    Plex.ytv.resultsCount = results.length;
    html += '<div class="results-label">';
    html += '<b>We\'ve found <span class="result-count">' + Plex.ytv.resultsCount + '</span> results for <span class="searched"><i>' + Plex.ytv.searched + '</i></span>...</b>';
    html += '</div>';
    if (results.length === 0) { html += '<div>No results</div>'; return html; }
    _.each(results, function(obj) {
        url = '/news';
        if (obj.category_color === 'essays') { url = url + '/essay/' + obj.slug + '/essay'; } else { url = url + '/article/' + obj.slug + '/1'; }
        html += '<div class="row a-result" data-equalizer>';
        html += '<div class="column small-3 a-img" data-equalizer-watch>';
        if (obj.img_sm.indexOf('http') === -1) {
            html += '<a href="' + url + '">';
            html += '<img class="' + obj.category_color + '" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/' + obj.img_sm + '" alt="' + obj.title + '">';
            html += '</a>';
        } else {
            html += '<a href="' + url + '">';
            html += '<img class="has-vid ' + obj.category_color + '" src="' + obj.img_sm + '" alt="' + obj.title + '">';
            html += '<div class="layer">';
            html += '<div class="playbtn text-center">';
            html += '<div class="play-arrow"></div>';
            html += '</div>';
            html += '</div>';
            html += '</a>';
        }
        html += '</div>';
        html += '<div class="column small-9 a-details" data-equalizer-watch>';
        html += '<div class="a-title"><b>' + obj.title + '</b></div>';
        html += '<div class="a-time"><b>' + obj.time + '</b></div>';
        html += '<a href="' + url + '">';
        html += '<div class="descrip-layer">';
        html += '<div>' + obj.short_descrip + '</div>';
        html += '<div class="read-more text-right"><u>Read More</u></div>';
        html += '</div>';
        html += '</a>';
        html += '</div>';
        html += '</div>';
    });
    return html;
}
Plex.ytv.render = function() {
    $('.result-count').html(Plex.ytv.resultsCount);
    $('.searched').html(Plex.ytv.searched);
    $('.results-container .results').html(Plex.ytv.resultsHTML);
}
$(document).on('click', '.college-recruit', function() { Plex.ytv.searchFromRecruitedList($(this).text()); });
Plex.ytv.searchFromRecruitedList = function(input) {
    $('input[name="search_articles"]').val(input);
    Plex.ytv.searched = input;
    Plex.ytv.searchArticles(input);
}
$(document).on('click', '.university-search-tab', function() {
    var _this = $(this);
    if (_this.hasClass('open')) _this.removeClass('open')
    else _this.addClass('open');
});