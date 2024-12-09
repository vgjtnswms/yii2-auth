
<?php

use yii\db\Migration;
use vs\yii2\auth\models\AuthCredentials;

/**
 * Class m240710_153900_add_user_admin
 */
class m240710_153900_add_user_admin extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%users}}', [
            'auth_key' => Yii::$app->security->generateRandomString(),
            'ts' => new \yii\db\Expression('NOW()'),
            'is_deleted' => 0,
            'is_registered' => 1,
            'fio' => 'admin',
            'role' => 'SUPER',
            'type' => 1,
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'uuid' => new \yii\db\Expression('uuid_generate_v4()'),
        ]);

        $userId = $this->db->getLastInsertID();

        $this->insert('{{%auth_credentials}}', [
            'credential' => 'admin',
            'validation' => Yii::$app->security->generatePasswordHash('admin'),
            'type' => AuthCredentials::TYPE_LOGIN,
            'user_id' => $userId,
            'ts' => new \yii\db\Expression('NOW()'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%users}}', ['email' => 'admin@example.com']);
    }
}