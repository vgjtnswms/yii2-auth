<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */

/** @var vs\yii2\auth\forms\PasswordRecoveryForm $model */
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use himiklab\yii2\recaptcha\ReCaptcha;

$this->title = 'Восстановление пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('auth.main', 'Пожалуйста, введите новый пароль:') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'request-password-reset-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-6 form-control'],
            'errorOptions' => ['class' => 'col-lg-9 invalid-feedback'],
        ],
    ]); ?>

        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password_duplicate')->passwordInput() ?>

        <!-- <?= $form->field($model, 'captcha')->label('')->widget(
            ReCaptcha::class,
            ['siteKey' => $model->siteKey]
        ) ?> -->

        <style>.alert-padding-fix ul{padding: 0px 14px 0px 14px;margin: 0px;}</style>

        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?= Html::errorSummary($model, ['header' => '', 'class' => 'alert-padding-fix']) ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <div>
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>