<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var vs\yii2\auth\forms\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use himiklab\yii2\recaptcha\ReCaptcha;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните следующие поля для входа:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-6 form-control'],
            'errorOptions' => ['class' => 'col-lg-9 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true,]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div style=\"margin-left: 14px;\" class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]) ?>
        </div>

        <?php if ($model->useCaptcha): ?>
        <?= $form->field($model, 'captcha')->widget(
            ReCaptcha::class,
            ['siteKey' => $model->siteKey]
        ) ?>
        <?php endif; ?>

        <div class="form-group">
            <a href="/auth/auth/restore" target="_full">Восстановить пароль</a>
        </div>

        <style>.alert-padding-fix ul{padding: 0px 14px 0px 14px;margin: 0px;}</style>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?= Html::errorSummary($model, ['header' => '', 'class' => 'alert-padding-fix']) ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <div>
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
