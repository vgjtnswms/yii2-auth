<?php

namespace vs\yii2\auth\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord implements UserInterface
{
    /**
     * @var string
     */
    public $authorization_token;

    /**
     * @var int
     */
    public $is_deleted;

    /**
     * @var mixed
     */
    public $organizations;

    /**
     * @param mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'username') {
            return $this->fio;
        }

        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '"public"."users"';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($token) {
            $authCredentials = AuthCredentials::findOne([
                'credential' => $token,
                'type' => 'token'
            ]);

            if ($token && $authCredentials->user_id) {
                $user = self::find()->byPk($authCredentials->user_id)->one();
                if ($user) {
                    $user->authorization_token = $authCredentials->credential;
                    return $user;
                }
                return null;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return (int)$this->is_deleted === 1;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return true;
    }

}