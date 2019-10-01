<?php

    $payload = array(
        'external_id' => $order_info['order_id'],
        'amount' => number_format($order_info['total'] * $this->currency->getvalue($order_info['currency_code']), 8, '.', ''),
        'currency_iso' => $order_info['currency_code'],
        'callback_url' => $this->url->link('extension/payment/netcents/success'),
        'first_name' => $order_info['firstname'],
        'last_name' => $order_info['lastname'],
        'email' => $order_info['email'],
        'webhook_url' => $this->url->link('extension/payment/netcents/callback'),
        'merchant_id' => $this->config->get('payment_netcents_api_key'),
        'data_encryption' => array(
            'external_id' => $order_info['order_id'],
            'amount' => number_format($order_info['total'] * $this->currency->getvalue($order_info['currency_code']), 8, '.', ''),
            'currency_iso' => $order_info['currency_code'],
            'callback_url' => $this->url->link('extension/payment/netcents/success'),
            'first_name' => $order_info['firstname'],
            'last_name' => $order_info['lastname'],
            'email' => $order_info['email'],
            'webhook_url' => $this->url->link('extension/payment/netcents/callback'),
            'merchant_id' => $this->config->get('payment_netcents_api_key'),
        )
    );
