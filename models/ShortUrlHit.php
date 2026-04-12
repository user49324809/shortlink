<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $short_url_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referer
 * @property int $created_at
 */
class ShortUrlHit extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%short_url_hit}}';
    }

    public function rules(): array
    {
        return [
            [['short_url_id', 'created_at'], 'integer'],
            [['short_url_id'], 'required'],
            [['user_agent', 'referer'], 'string', 'max' => 2048],
            [['ip_address'], 'string', 'max' => 64],
        ];
    }

    public function getShortUrl()
    {
        return $this->hasOne(ShortUrl::class, ['id' => 'short_url_id']);
    }
}
