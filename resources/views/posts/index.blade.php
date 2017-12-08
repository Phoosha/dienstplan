<div class="news">

    @forelse ($posts as $post)
        <section class="news-item">
            <header class="news-header">
                <h3 class="news-title">{{ $post->title }}</h3>
                <p class="news-meta">von Blub</p>
            </header>
            <div class="news-description">
                <p>{!! $post->body !!}</p>
            </div>
        </section>
    @empty
        <section class="news-item">
            <p> - Es gibt keine Neuigkeiten - </p>
        </section>
    @endforelse

</div>
