
@if(isset($articles)  && !empty($articles))
  @foreach($articles as $article)
    <div class="small-12 medium-6 large-4 columns">
      <div class="news-box article-box" id="{{$article->id}}" data-slug="{{$article->slug}}" data-title="{{$article->title}}">
        <img class="news-post-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$article->img_lg}}">
        <div class="news-content">
          <strong>{{$article->external_author}}</strong>
          @if (strlen($article->title) > 60)
            <h2>{{ strip_tags(substr($article->title, 0, 60)) . '...' }}</h2>
          @else
            <h2>{{ $article->title }}</h2>
          @endif
          @if (strlen($article->content) > 180)
            <p>{{ strip_tags(substr($article->content, 0, 180)) . '...' }} </p>
          @else
            <p>{{ $article->content }}</p>
          @endif
        </div>
      </div>
    </div>
  @endforeach
@endif
