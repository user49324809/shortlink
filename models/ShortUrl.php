<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property int $hits_count
 * @property int $created_at
 * @property int $updated_at
 */
class ShortUrl extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%short_url}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['original_url', 'short_code'], 'required'],
            [['original_url'], 'string', 'max' => 2048],
            [['short_code'], 'string', 'max' => 32],
            [['hits_count', 'created_at', 'updated_at'], 'integer'],
            [['short_code'], 'unique'],
            [['original_url'], 'url', 'validSchemes' => ['http', 'https']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'original_url' => 'Исходная ссылка',
            'short_code' => 'Короткий код',
            'hits_count' => 'Количество переходов',
        ];
    }

    public function getHits()
    {
        return $this->hasMany(ShortUrlHit::class, ['short_url_id' => 'id']);
    }
}
