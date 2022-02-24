<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Company name
  |--------------------------------------------------------------------------
  |
  */

  'company' => env('NIGHTNURSE_COMPANY_NAME', 'Projects Nightnurse'),

  /*
  |--------------------------------------------------------------------------
  | E-Mail settings
  |--------------------------------------------------------------------------
  |
  */

  'email' => [
    'from' => env('NIGHTNURSE_MAIL_FROM', 'noreply@nightnurse.ch'),
    'recipient' => env('NIGHTNURSE_MAIL_RECIPIENT', 'm@marceli.to'),
    'bcc' => env('NIGHTNURSE_MAIL_BCC', 'm@marceli.to'),
    'recipient_test' => env('NIGHTNURSE_MAIL_RECIPIENT_TEST', 'm@marceli.to')
  ],

  /*
  |--------------------------------------------------------------------------
  | Domain
  |--------------------------------------------------------------------------
  |
  */

  'domain' => env('NIGHTNURSE_DOMAIN', 'https://projects.nightnurse.ch'),

  /*
  |--------------------------------------------------------------------------
  | Chunk size for cron jobs
  |--------------------------------------------------------------------------
  |
  */

  'cron_chunk_size' => 3,

  /*
  |--------------------------------------------------------------------------
  | Owner ID (Table: companies)
  |--------------------------------------------------------------------------
  |
  */

  'owner_id' => env('NIGHTNURSE_OWNER_ID', 1)
]
?>