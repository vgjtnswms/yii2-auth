<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m240710_153700_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'pgsql') {
            // Устанавливаем расширение uuid-ossp для работы с типом uuid
            $this->execute('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

            // Создаем таблицу
            $this->createTable('{{%users}}', [
                'id' => $this->primaryKey(),
                'auth_key' => $this->string(255),
                'ts' => 'timestamptz NOT NULL DEFAULT now()',
                'is_deleted' => $this->smallInteger()->defaultValue(0)->notNull(),
                'is_registered' => $this->smallInteger()->defaultValue(1)->notNull(),
                'photo' => $this->string(255),
                'fio' => $this->string(255),
                'system_role' => $this->string(50),
                'info' => $this->json(),
                'type' => $this->integer()->defaultValue(1)->notNull(),
                'uuid' => 'UUID DEFAULT uuid_generate_v4()',
                'email' => $this->string(180),
                'phone' => $this->string(256),
            ]);

            $this->createIndex('idx_unique_users_uuid', '{{%users}}', 'uuid', true);
            $this->createIndex('idx_users_uuid', '{{%users}}', 'uuid');
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
        $this->dropIndex('idx_users_uuid', '{{%users}}');

        $this->dropTable('{{%users}}');

        if ($this->db->driverName === 'pgsql') {
            $this->execute('DROP EXTENSION IF EXISTS "uuid-ossp"');
        }
    }
}