<?php

namespace app\models;

use yii\base\Model;

class ShortenForm extends Model
{
    public string $url = '';

    public function rules(): array
    {
        return [
            [['url'], 'required'],
            [['url'], 'trim'],
            [['url'], 'string', 'max' => 2048],
            [['url'], 'url', 'validSchemes' => ['http', 'https'], 'message' => 'Введите корректный URL (http/https).'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'url' => 'URL',
        ];
    }
}
