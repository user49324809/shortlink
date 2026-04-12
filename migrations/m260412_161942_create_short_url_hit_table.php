<?php


use yii\db\Migration;

class m260412_190500_create_short_url_hit_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%short_url_hit}}', [
            'id' => $this->primaryKey(),
            'short_url_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'user_agent' => $this->text(),
            'referer' => $this->text(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-short_url_hit-short_url_id',
            '{{%short_url_hit}}',
            'short_url_id'
        );

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
        $this->dropIndex('idx-short_url_hit-short_url_id', '{{%short_url_hit}}');
        $this->dropTable('{{%short_url_hit}}');
    }
}