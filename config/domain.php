<?php

return [
  'env_stub' => '.env',
  'storage_dirs' => [
    'app' => [
      'public' => [
      ],
    ],
    'framework' => [
      'cache' => [
      ],
      'testing' => [
      ],
      'sessions' => [
      ],
      'views' => [
      ],
    ],
    'logs' => [
    ],
  ],
  'domains' => [
    'my.nightnurse.ch.local' => 'my.nightnurse.ch.local',
    'staging.my.nightnurse.ch' => 'staging.my.nightnurse.ch',
    'my.nightnurse.ch' => [
      'storage_path' => 'storage'
    ]
  ],
 ];