<section class="newsletter bg-brand p-30 text-white wow fadeIn animated">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-md-3 mb-lg-0">
                <div class="row align-items-center">
                    <div class="col flex-horizontal-center title">
                        <img class="icon-email" src="{{ Theme::asset()->url('images/icons/icon-email.svg') }}" alt="icon">
                        <h4 class="font-size-20 mb-0 ml-3">{!! BaseHelper::clean($title) !!}</h4>
                    </div>
                    {{-- <div class="col my-4 my-md-0">
                        <h5 class="font-size-15 ml-4 mb-0">{!! BaseHelper::clean($description) !!}</h5>
                    </div> --}}
                </div>
            </div>
            <div class="col-lg-5">
                <!-- Subscribe Form -->
                <form class="newsletter-form" method="post" action="{{ route('public.newsletter.subscribe') }}">
                    @csrf
                    <div class="form-subcriber d-flex wow fadeIn animated ">
                        <input type="email" name="email" class="form-control bg-white font-small" placeholder="{{ __('Enter your email') }}">
                        <button class="btn bg-dark text-white desktop-button" type="submit" disabled>{{ __('Subscribe') }}</button>
                    </div>

                    <div class="chek-form">
                        <div class="custome-checkbox">
                            <input class="form-check-input" type="checkbox" name="privacy_policy" id="privacy_policy" required>
                            <label class="form-check-label" for="privacy_policy"><span>{!! __('I agree to the :[1]privacy policy:[/1]', ['[1]' => '<a href="/privacy-policy">', '[/1]' => '</a>']) !!}</span></label>
                        </div>
                    </div>

                    @if (setting('enable_captcha') && is_plugin_active('captcha'))
                        <div class="col-auto">
                            {!! Captcha::display() !!}
                        </div>
                    @endif

                    <button class="btn bg-dark text-white mobile-button" type="submit" disabled>{{ __('Subscribe') }}</button>
                </form>
                <!-- End Subscribe Form -->
            </div>
        </div>
    </div>
</section>
