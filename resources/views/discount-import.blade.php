@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    @notifyCss
    <form
        action="{{ route('ecommerce.discount-import.import') }}"
        method="POST"
        enctype="multipart/form-data">
        @csrf
    <div class="row justify-content-center">
        <div class="col-xxl-6 col-xl-8 col-lg-10 col-12">
            <div class="widget meta-boxes">
                <div class="widget-title pl-2">
                    <h4>{{ trans('plugins/ecommerce::bulk-import.menu'). ' discount' }}</h4>
                </div>
                <div class="widget-body">
                    <div class="form-group mb-3 @if ($errors->has('file')) has-error @endif">
                        <label class="control-label required" for="input-group-file">
                            {{ trans('plugins/ecommerce::bulk-import.choose_file')}}
                        </label>
                        {!! Form::file('discount', [
                             'required'         => true,
                             'class'            => 'form-control',
                             'id'               => 'input-group-file',
                             'aria-describedby' => 'input-group-addon',
                         ]) !!}
                        <label class="d-block mt-1 help-block" for="input-group-file">
                            {{ trans('plugins/ecommerce::bulk-import.choose_file_with_mime', ['types' =>  'xls'])}}
                        </label>
                    </div>
                    <div class="form-group mb-3 d-grid">
                        <button type="submit" class="btn btn-info"
                        >
                            {{ trans('plugins/ecommerce::bulk-import.start_import') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <div style="margin-top: 100px;">
        <x:notify-messages />
    </div>
    @notifyJs
    <script>
        (function(){
        let notify = document.querySelector('.notify')
            notify ? notify.classList.add('mt-5') : ''
        })();
    </script>
@stop

