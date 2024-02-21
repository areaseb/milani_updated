@if ($keys->count())
    <section class="banners pt-60">
        <div class="container">
            <div class="banners-row">
                @foreach ($keys as $key)
                    <div class="banners-item">
                        {!! display_ad($key) !!}
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
