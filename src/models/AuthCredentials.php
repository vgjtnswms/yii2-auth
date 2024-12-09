<?php

namespace vs\yii2\auth\models;

use Yii;
use yii\base\InvalidConfigException;
// use common\components\validators\PhoneNumberValidator;

/**
 * This is the model class for table "auth_credentials".
 *
 * @property string $credential
 * @property string $validation
 * @property string $type
 * @property int    $user_id
 * @property string $ts
 */
class AuthCredentials extends \yii\db\ActiveRecord implements AuthCredentialsInterface
{
    /**
     * @var UserInterface
     */
    private $_user = false;

    public function init()
    {
        parent::init();

        // Проверка наличия компонента 'user'
        if (!Yii::$app->has('user')) {
            throw new InvalidConfigException('Component "user" is not configured.');
        }

        if (!Yii::$app->has('security')) {
            throw new InvalidConfigException('Component "security" is not configured.');
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '"public"."auth_credentials"';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'registration' => ['credential', 'validation', 'type', 'user_id'],
            'change_password' => ['validation']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['credential', 'validation', 'type', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['credential'], 'string', 'max' => 255],
            // [['credential'], PhoneNumberValidator::className(), 'when' => function ($model) {
            //     return $model->type == 'phone';
            // }],
            [['credential'], 'email', 'when' => function ($model) {
                return $model->type == 'email';
            }],
            [['validation'], 'string', 'max' => 1000],
            [['validation'], 'filter', 'on' => ['registration'], 'filter' => function ($value) {
                return Yii::$app->security->generatePasswordHash($value);
            }],
            [['validation'], 'filter', 'on' => ['change_password'], 'filter' => function ($value) {
                return Yii::$app->security->generatePasswordHash($value);
            }],
            [['type'], 'string', 'max' => 25],
            [['credential'], 'unique'],
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
     * @param string $validation Пароль.
     * @return void
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
    }
}
