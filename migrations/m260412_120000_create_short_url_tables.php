<?php

use yii\db\Migration;

class m260412_120000_create_short_url_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%short_url}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->string(2048)->notNull(),
            'short_code' => $this->string(32)->notNull()->unique(),
            'hits_count' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-short_url-original_url', '{{%short_url}}', 'original_url');

        $this->createTable('{{%short_url_hit}}', [
            'id' => $this->primaryKey(),
            'short_url_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(64),
            'user_agent' => $this->string(2048),
            'referer' => $this->string(2048),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-short_url_hit-short_url_id', '{{%short_url_hit}}', 'short_url_id');
        $this->addForeignKey(
            'fk-short_url_hit-short_url_id',
            '{{%short_url_hit}}',
            'short_url_id',
            '{{%short_url}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-short_url_hit-short_url_id', '{{%short_url_hit}}');
        $this->dropTable('{{%short_url_hit}}');
        $this->dropTable('{{%short_url}}');
    }
}
