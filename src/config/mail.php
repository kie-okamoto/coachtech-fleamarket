<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | The default mailer used by your application.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Configure all of the mailers used by your application plus their
    | respective settings.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            // MailHog をデフォルトに（.envで上書き可）
            'host'       => env('MAIL_HOST', 'mailhog'),
            'port'       => env('MAIL_PORT', 1025),
            // MailHog は暗号化不要（null を推奨。文字列 'null' は不可）
            'encryption' => env('MAIL_ENCRYPTION', null),
            'username'   => env('MAIL_USERNAME'),
            'password'   => env('MAIL_PASSWORD'),
            'timeout'    => null,
            'auth_mode'  => null,
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -t -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers'   => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | All e-mails sent by your application will be sent from this address/name.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | Configure theme and component paths for Markdown based emails.
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            // 既定のMarkdownコンポーネント置き場
            resource_path('views/vendor/mail'),

            // ※必要に応じて独自パスを追加（例：emails ディレクトリを解決させたい場合）
            // resource_path('views/emails'),
        ],
    ],

];
