<?php

use Botble\Widget\AbstractWidget;

class NewsletterWidget extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $frontendTemplate = 'frontend';

    /**
     * @var string
     */
    protected $backendTemplate = 'backend';

    /**
     * @var string
     */
    protected $widgetDirectory = 'newsletter';

    /**
     * Newsletter constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'name' => __('Newsletter'),
            'subtitle' => __('Subtitle'),
            'description' => __('Widget description'),
        ]);
    }
}
