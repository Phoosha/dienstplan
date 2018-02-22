<h2 class="content-subhead">
    Ankündigungen
    @can('edit', App\Post::class)
    @include('layouts.editable', [ 'editUrl' => url('posts/edit'), 'viewUrl' => url('/') ])
    @endcan
</h2>
@include('layouts.status', [ 'errors' => null, 'statusKey' => 'posts-status' ])
<div class="news">
    @forelse ($posts as $post)
    <section class="news-item">
        <header class="news-header">

            <h3 class="news-title">{{ $post->title }}</h3>
            <p class="news-meta">von {{ $post->user->getFullName() }}</p>

            @if ($edit)
            <form method="post" action="{{ url('posts', $post->id) }}">
                <p class="news-meta">
                    Veröffentlichung: {{ $post->release_on->diffForHumans() }}
                    &emsp;
                    Ablauf: {{ isset($post->expire_on) ? $post->expire_on->diffForHumans() : 'nie' }}

                    @if (Auth::user()->can('delete', App\Post::class))
                    &ensp;
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <button type="submit" title="Löschen" class="pure-button icon-button secondary-button danger-button" onclick="return confirm('Ankündigung wirklich löschen?')">
                        <i class="fa-fw fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                    @endif

                </p>
            </form>
            @endif
        </header>

        <div class="news-description">
            <p>{!! preg_replace('#<br\s*/>\s*<br\s*/>#m', '</p><p>', nl2br(e($post->body))) !!}</p>
        </div>
    </section>
    @empty
    <section class="news-item">
        <p> - Es gibt keine Neuigkeiten - </p>
    </section>
    @endforelse
</div>

@if ($edit && Auth::user()->can('store', App\Post::class))
    <h2 class="content-subhead">Neue Ankündigung</h2>
    <form method="post" action="{{ url('posts') }}" class="pure-form" id="create-news">
        {{ csrf_field() }}
        <section class="news-item">
            <fieldset class="pure-group">
                <input type="text" id="title" name="title" placeholder="Titel der Ankündigung" value="{{ old('title') }}" required/>
                <textarea id="body" name="body" placeholder="Inhalt der Ankündigung" rows="7" spellcheck="true" required>{{ old('body') }}</textarea>
            </fieldset>
            <fieldset>
                <label for="release_on">
                    Veröffentlichung:
                    <input type="text" id="start-date" name="release_on" class="date start-date" size="9" value="{{ old('release_on') ?? now()->format(config('dienstplan.date_format')) }}"/>
                </label>
                <label for="expire_on">
                    Ablauf:
                    <input type="text" id="end-date" name="expire_on" class="date end-date" size="9" value="nie"/>
                </label>
            </fieldset>
            <button type="submit" class="pure-button primary-button">
                <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Speichern
            </button>
        </section>
        <input type="hidden" id="min-date" value="0" disabled/>
        <input type="hidden" id="max-date" value="{{ Carbon\Carbon::instance(config('dienstplan.max_date'))->format('d.m.Y') }}" disabled/>
    </form>
@endif

@push('late')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/datepicker.js') }}"></script>
@endpush
