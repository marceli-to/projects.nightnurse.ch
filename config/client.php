<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Company name
  |--------------------------------------------------------------------------
  |
  */

  'company' => env('ER_NIGHTNURSE_COMPANY_NAME', 'ER Nightnurse'),

  /*
  |--------------------------------------------------------------------------
  | E-Mail settings
  |--------------------------------------------------------------------------
  |
  */

  'email' => [
    'from' => env('ER_NIGHTNURSE_MAIL_FROM', 'noreply@nightnurse.ch'),
    'recipient' => env('ER_NIGHTNURSE_MAIL_RECIPIENT', 'm@marceli.to'),
    'bcc' => env('ER_NIGHTNURSE_MAIL_BCC', 'm@marceli.to'),
    'recipient_test' => env('ER_NIGHTNURSE_MAIL_RECIPIENT_TEST', 'm@marceli.to')
  ],

  /*
  |--------------------------------------------------------------------------
  | Domain
  |--------------------------------------------------------------------------
  |
  */

  'domain' => env('ER_NIGHTNURSE_DOMAIN', 'https://er.nightnurse.ch'),

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

  'owner_id' => env('ER_NIGHTNURSE_OWNER_ID', 1)
]
?>