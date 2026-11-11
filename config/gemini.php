<?php

$apiKey = env('GEMINI_API_KEY');
$model = env('GEMINI_MODEL', 'gemini-2.5-flash');

return [
    'api_key' => $apiKey,
    'model' => $model,
];