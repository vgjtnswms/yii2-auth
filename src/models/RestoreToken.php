<?php

namespace vs\yii2\auth\models;

use Yii;
use yii\db\ActiveRecord;
use vs\yii2\auth\models\User;
use vs\yii2\auth\models\RestoreTokenInterface;

/**
 * This is the model class for table "RestoreToken".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $ts timestampz
 * @property string $expiration_ts timestampz
 * @property string $used_ts timestampz
 * @property string $info
 *
 * @property User $user
 */
class RestoreToken extends ActiveRecord implements RestoreTokenInterface
{
    /**
     * @var UserInterface
     */
    private $_user = false;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $token;

    /**
     * @var array
     */
    public $info;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '"public"."restore_token"';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['user_id', 'token', 'expiration_ts'], 'required'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Yii::$app->user->identityClass::findIdentity($this->user_id);
        }

        return $this->_user;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param array $info
     * @return void
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }
}
