<?php
/**
 * Configurações do sistema
 */

/**
 * Configurações de pagamento
 */
$config['payment'] = [
    'mercadopago' => [
        'enabled' => true,
        'test_mode' => true,
        'public_key' => 'TEST-123456789',
        'access_token' => 'TEST-987654321'
    ],
    'paypal' => [
        'enabled' => false,
        'test_mode' => true,
        'client_id' => '',
        'client_secret' => ''
    ],
    'pix' => [
        'enabled' => true
    ]
];

/**
 * Configurações de email
 */
$config['email'] = [
    'smtp_host' => 'smtp.example.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'no-reply@example.com',
    'smtp_password' => 'password',
    'from_email' => 'no-reply@example.com',
    'from_name' => 'SteveShop'
];

/**
 * Configurações de afiliados
 */
$config['affiliate'] = [
    'enabled' => true,
    'commission_rate' => 5, // percentage
    'min_payout' => 50.00,
    'cookie_days' => 30
];

/**
 * Tempo de expiração da sessão (em segundos)
 */
$config['session_expire'] = 3600; // 1 hora 