<?php

return [
    'app_id' => env('FXZB_APP_ID'),
    'services' => [
        // 电影票
        'movie' => [
            'secret' => env('FXZB_MOVIE_SECRET', ''),
            'base_url' => env('FXZB_MOVIE_BASE_URL', 'https://dev.movie-v2.fxzb.vip'),
        ],
        // 大牌点餐
        'brand' => [
            'secret' => env('FXZB_BRAND_SECRET', ''),
            'base_url' => env('FXZB_BRAND_BASE_URL', 'https://dev.bigbrand.fxzb.vip'),
        ],
    ],
];
