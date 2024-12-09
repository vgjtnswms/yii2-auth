
<?php

use yii\db\Migration;

/**
 * Class m240710_154000_create_restore_token_table
 */
class m240710_154000_create_restore_token_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'pgsql') {
            $this->createTable('{{%restore_token}}', [
                'id' => $this->primaryKey(),
                'token' => $this->string(255)->notNull(),
                'user_id' => $this->integer()->notNull(),
                'ts' => 'timestamptz(6) NOT NULL DEFAULT now()',
                'expiration_ts' => 'timestamptz(6) NOT NULL',
                'used_ts' => 'timestamptz(6) NULL DEFAULT NULL',
                'info' => $this->string(255)->null(),
            ]);

            $this->createIndex('{{%idx_unique_restore_token_token}}', '{{%restore_token}}', 'token', true);
            $this->createIndex('{{%idx_restore_token_token}}', '{{%restore_token}}', 'token');

            $this->addForeignKey(
                '{{%fk_restore_token_user_id}}',
                '{{%restore_token}}',
                'user_id',
                '{{%users}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        if ($this->db->driverName !== 'pgsql') {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk_restore_token_user_id}}',
            '{{%restore_token}}'
        );

        $this->dropIndex(
            '{{%idx_restore_token_token}}',
            '{{%restore_token}}'
        );

        $this->dropIndex(
            '{{%idx_unique_restore_token_token}}',
            '{{%restore_token}}'
        );

        $this->dropTable('{{%restore_token}}');
    }
}