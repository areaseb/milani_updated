<?php

return [
    'name'        => 'Ecommerce',
    'description' => 'Config email templates for Ecommerce',
    'templates'   => [
        'welcome'                 => [
            'title'       => 'Welcome',
            'description' => 'Send email to user when they registered an account on our site',
            'subject'     => 'Welcome to {{ site_title }}!',
            'can_off'     => true,
            'enabled'     => false,
            'variables'   => [
                'customer_name' => 'plugins/ecommerce::ecommerce.customer_name',
            ],
        ],
        'confirm-email'           => [
            'title'       => 'Confirm email',
            'description' => 'Send email to user when they register an account to verify their email',
            'subject'     => 'Confirm Email Notification',
            'can_off'     => false,
            'variables'   => [
                'verify_link' => 'Verify email link',
            ],
        ],
        'password-reminder'       => [
            'title'       => 'Reset password',
            'description' => 'Send email to user when requesting reset password',
            'subject'     => 'Reset Password',
            'can_off'     => false,
            'variables'   => [
                'reset_link' => 'Reset password link',
            ],
        ],
        'customer_new_order'      => [
            'title'       => 'plugins/ecommerce::email.customer_new_order_title',
            'description' => 'plugins/ecommerce::email.customer_new_order_description',
            'subject'     => 'New order(s) at {{ site_title }}',
            'can_off'     => true,
            'enabled'     => false,
            'variables'   => [
                'customer_name'    => 'plugins/ecommerce::ecommerce.customer_name',
                'customer_phone'   => 'plugins/ecommerce::ecommerce.customer_phone',
                'customer_address' => 'plugins/ecommerce::ecommerce.customer_address',
                'shipping_method'  => 'plugins/ecommerce::ecommerce.shipping_method',
                'payment_method'   => 'plugins/ecommerce::ecommerce.payment_method',
                'product_list'     => 'plugins/ecommerce::ecommerce.product_list',
                'order_id'         => 'plugins/ecommerce::ecommerce.order_id',
            ],
        ],
        'customer_cancel_order'   => [
            'title'       => 'plugins/ecommerce::email.order_cancellation_title',
            'description' => 'plugins/ecommerce::email.order_cancellation_description',
            'subject'     => 'Order cancelled {{ order_id }}',
            'can_off'     => true,
            'variables'   => [
                'customer_name' => 'plugins/ecommerce::ecommerce.customer_name',
                'order_id'      => 'plugins/ecommerce::ecommerce.order_id',
            ],
        ],
        'customer_delivery_order' => [
            'title'       => 'plugins/ecommerce::email.delivery_confirmation_title',
            'description' => 'plugins/ecommerce::email.delivery_confirmation_description',
            'subject'     => 'Order delivering {{ order_id }}',
            'can_off'     => true,
            'variables'   => [
                'customer_name'        => 'plugins/ecommerce::ecommerce.customer_name',
                'order_id'             => 'plugins/ecommerce::ecommerce.order_id',
                'order_delivery_notes' => 'Order delivery notes',
            ],
        ],
        'admin_new_order'         => [
            'title'       => 'plugins/ecommerce::email.admin_new_order_title',
            'description' => 'plugins/ecommerce::email.admin_new_order_description',
            'subject'     => 'New order(s) at {{ site_title }}',
            'can_off'     => true,
            'enabled'     => false,
            'variables'   => [
                'customer_name'    => 'plugins/ecommerce::ecommerce.customer_name',
                'customer_phone'   => 'plugins/ecommerce::ecommerce.customer_phone',
                'customer_address' => 'plugins/ecommerce::ecommerce.customer_address',
                'shipping_method'  => 'plugins/ecommerce::ecommerce.shipping_method',
                'payment_method'   => 'plugins/ecommerce::ecommerce.payment_method',
                'product_list'     => 'plugins/ecommerce::ecommerce.product_list',
                'order_id'         => 'plugins/ecommerce::ecommerce.order_id',
            ],
        ],
        'order_confirm'           => [
            'title'       => 'plugins/ecommerce::email.order_confirmation_title',
            'description' => 'plugins/ecommerce::email.order_confirmation_description',
            'subject'     => 'Order confirmed {{ order_id }}',
            'can_off'     => true,
            'variables'   => [
                'customer_name'    => 'plugins/ecommerce::ecommerce.customer_name',
                'customer_phone'   => 'plugins/ecommerce::ecommerce.customer_phone',
                'customer_address' => 'plugins/ecommerce::ecommerce.customer_address',
                'shipping_method'  => 'plugins/ecommerce::ecommerce.shipping_method',
                'payment_method'   => 'plugins/ecommerce::ecommerce.payment_method',
                'product_list'     => 'plugins/ecommerce::ecommerce.product_list',
                'order_id'         => 'plugins/ecommerce::ecommerce.order_id',
            ],
        ],
        'order_confirm_payment'   => [
            'title'       => 'plugins/ecommerce::email.payment_confirmation_title',
            'description' => 'plugins/ecommerce::email.payment_confirmation_description',
            'subject'     => 'Payment for order {{ order_id }} was confirmed',
            'can_off'     => true,
            'variables'   => [
                'customer_name'    => 'plugins/ecommerce::ecommerce.customer_name',
                'customer_phone'   => 'plugins/ecommerce::ecommerce.customer_phone',
                'customer_address' => 'plugins/ecommerce::ecommerce.customer_address',
                'shipping_method'  => 'plugins/ecommerce::ecommerce.shipping_method',
                'payment_method'   => 'plugins/ecommerce::ecommerce.payment_method',
                'product_list'     => 'plugins/ecommerce::ecommerce.product_list',
                'order_id'         => 'plugins/ecommerce::ecommerce.order_id',
            ],
        ],
        'order_recover'           => [
            'title'       => 'plugins/ecommerce::email.order_recover_title',
            'description' => 'plugins/ecommerce::email.order_recover_description',
            'subject'     => 'Incomplete order',
            'can_off'     => true,
            'variables'   => [
                'customer_name'    => 'plugins/ecommerce::ecommerce.customer_name',
                'customer_phone'   => 'plugins/ecommerce::ecommerce.customer_phone',
                'customer_address' => 'plugins/ecommerce::ecommerce.customer_address',
                'shipping_method'  => 'plugins/ecommerce::ecommerce.shipping_method',
                'payment_method'   => 'plugins/ecommerce::ecommerce.payment_method',
                'product_list'     => 'plugins/ecommerce::ecommerce.product_list',
                'order_id'         => 'plugins/ecommerce::ecommerce.order_id',
                'order_token'      => 'plugins/ecommerce::ecommerce.order_token',
            ],
        ],
        'order-return-request'    => [
            'title'       => 'plugins/ecommerce::email.order_return_request_title',
            'description' => 'plugins/ecommerce::email.order_return_request_description',
            'subject'     => 'Order return request',
            'can_off'     => true,
            'variables'   => [
                'customer_name'       => 'plugins/ecommerce::ecommerce.customer_name',
                'customer_phone'      => 'plugins/ecommerce::ecommerce.customer_phone',
                'customer_address'    => 'plugins/ecommerce::ecommerce.customer_address',
                'list_order_products' => 'List of order products',
                'order_id'            => 'plugins/ecommerce::ecommerce.order_id',
            ],
        ],
        'invoice-payment-created' => [
            'title'       => 'Invoice Payment Detail',
            'description' => 'Send a notification to the customer who makes order',
            'subject'     => 'Payment received from {{ customer_name }} on {{ site_title }}',
            'can_off'     => true,
            'enabled'     => false,
            'variables'   => [
                'customer_name' => 'Customer name',
                'invoice_code'  => 'Invoice Code',
                'invoice_link'  => 'Invoice Link',
            ],
        ],
    ],
];
