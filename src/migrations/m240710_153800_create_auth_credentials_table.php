<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%auth_credentials}}`.
 */
class m240710_153800_create_auth_credentials_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'pgsql') {
            $this->createTable('{{%auth_credentials}}', [
                'credential' => $this->string(255)->notNull(),
                'validation' => $this->string(1000)->notNull(),
                'type' => $this->string(25)->notNull(),
                'user_id' => $this->integer()->notNull(),
                'ts' => 'timestamptz NOT NULL DEFAULT now()',
            ]);

            $this->addPrimaryKey('pk_auth_credentials_credential', '{{%auth_credentials}}', 'credential');
            $this->createIndex('{{%idx_auth_credentials_user_id}}', '{{%auth_credentials}}', 'user_id');
            $this->addForeignKey('{{%fk_auth_credentials_user_id}}', '{{%auth_credentials}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
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
            '{{%fk_auth_credentials_user_id}}',
            '{{%auth_credentials}}'
        );

        $this->dropIndex(
            '{{%idx_auth_credentials_user_id}}',
            '{{%auth_credentials}}'
        );

        $this->dropPrimaryKey('pk_auth_credentials_credential', '{{%auth_credentials}}');

        $this->dropTable('{{%auth_credentials}}');
    }
}