<?php
$json = file_get_contents('https://openrouter.ai/api/v1/models');
$data = json_decode($json, true);
$models = [];
foreach($data['data'] as $model) {
    if (strpos($model['id'], 'gemini') !== false) {
        $models[] = $model['id'];
    }
}
print_r($models);
