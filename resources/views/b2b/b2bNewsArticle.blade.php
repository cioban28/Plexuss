  <div class="news-container">
    <div class="share-article-left-container">
      <span class="share-text">SHARE</span>
      <span class="share-social" onclick="linkedInShareClick('{{ $news_details->title }}')"><img src="../../../images/b2b/LinkedIn.svg"></span>
      <span class="share-social" onclick="fbShareClick('{{ $news_details->title }}')"><img src="../../../images/b2b/facebook-f.svg"></span>
      <span class="share-social" onclick="twitterShareClick('{{ $news_details->title }}')"><img src="../../../images/b2b/twitter.svg"></span>
    </div>

    <div class="row">
      <div class="news-container-inner">
        <h1>Plexuss News</h1>
        <div class="article-block">
          <div class="row">
            <section class="news-post">
              <img class="news-post-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_details->img_lg}}">
              <div class="news-post-content">
                <h2>{{$news_details->title}}</h2>
                <div class="row">
                  <div class="wrapper columns large-6 medium-6 small-6">
                    <img class="avatar" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_details->img_lg}}" alt="avatar">
                    <span class="source-name">{{$news_details->external_author}}</span>
                    <br>
                    <span class="publish-date">{{ $news_details->created_at->format('M, d, Y') }}</span>
                  </div>
                  <div class="share-article-container columns large-6 medium-6 small-6">
                    <span class="share-text">SHARE</span>
                    <span class="share-social" onclick="linkedInShareClick('{{ $news_details->title }}');"><img src="../../../images/b2b/LinkedIn.svg"></span>
                    <span class="share-social" onclick="fbShareClick('{{ $news_details->title }}');"><img src="../../../images/b2b/facebook-f.svg"></span>
                    <span class="share-social"  onclick="twitterShareClick('{{ $news_details->title }}');"><img src="../../../images/b2b/twitter.svg"></span>
                  </div>
                </div>
                <p>{!! $news_details->content !!}</p>
              </div>
            </section>
          </div>
          <div class="row related-articles"><h3>RELATED ARTICLES</h3></div>
          <div class="row">
            @if(isset($related_news)  && !empty($related_news))
              @for($i=0; $i<=2; $i++)
                <div class="small-12 medium-6 large-4 columns">
                  <div class="news-box article-box" id="{{$related_news[$i]->id}}" data-slug="{{$related_news[$i]->slug}}" data-title="{{$related_news[$i]->title}}">
                    <img class="news-post-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$related_news[$i]->img_lg}}">
                    <div class="news-content">
                      <strong>{{ $related_news[$i]->external_author }}</strong>
                      @if (strlen($related_news[$i]->title) > 60)
                        <h2>{{ strip_tags(substr($related_news[$i]->title, 0, 60)) . '...' }}</h2>
                      @else
                        <h2>{{ $related_news[$i]->title }}</h2>
                      @endif
                      @if (strlen($related_news[$i]->content) > 180)
                        <p>{{ strip_tags(substr($related_news[$i]->content, 0, 180)) . '...' }} </p>
                      @else
                        <p>{{ $related_news[$i]->content }}</p>
                      @endif
                    </div>
                  </div>
                </div>
              @endfor
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

@include('b2b.b2bFooter')
