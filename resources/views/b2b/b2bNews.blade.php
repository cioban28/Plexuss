@extends('b2b.master')

@section('b2b-content')

    <div class="news-container">
      <div class="row">
        <div class="news-container-inner">
          <h1>Plexuss News</h1>
          <div class="news-block">
            <div class="row">
              <section class="news-carousel slider">
                @for($i=0; $i<=3; $i++)
                  <div>
                    <img class="headline-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$articles[$i]->img_lg}}">
                      <div class="caption">
                        <div class="slider-head">
                          <div class="title-caption">
                            <img class="avatar" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$articles[$i]->img_sm}}">
                            <span>{{$articles[$i]->external_author}}</span>
                         </div>
                      </div>
                      <div class="caption-inner">
                        <h2 class="news-slider-heading" data-title="{{$articles[$i]->title}}" data-slug="{{$articles[$i]->slug}}">{{$articles[$i]->title}}</h2>
                        <p>{{strip_tags(substr($articles[$i]->content, 0, 250))}}</p>
                       </div>
                     </div>
                   </div>
                @endfor
              </section>
              <div class="article-boxes">
                <div id="container-box" class="blog-box-container js-masonry row"  data-masonry-options='{ "itemSelector": ".newsitem" }' data-page="1"  data-pageTotal="data['total']">
                  @include('b2b.b2bNewsArticleBox')
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @include('b2b.b2bFooter')
@stop
