<div class="col-3 panel panel-default">
    <div class="panel-title">
        <h3>{{ $config['name'] }}</h3>
    </div>
    <div class="panel-content">
        <p>{!! do_shortcode(BaseHelper::clean($config['content'])) !!}</p>
    </div>
</div>
