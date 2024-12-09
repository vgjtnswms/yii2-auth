<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var vs\yii2\auth\forms\RegistrationForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use himiklab\yii2\recaptcha\ReCaptcha;
use vs\yii2\auth\components\widgets\EmailVerificationCodeWidget;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="registration">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните следующие поля для регистрации:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'col-lg-6 form-control'],
            'errorOptions' => ['class' => 'col-lg-9 invalid-feedback'],
        ],
    ]); ?>

    <?= $form->field($model, 'lastName')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'firstName')->textInput() ?>
    <?= $form->field($model, 'middleName')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() // ['placeholder' => 'example@mail.kz'] ?>
    <?= EmailVerificationCodeWidget::widget([
        'model' => $model,
        'attribute' => 'emailVerificationCode',
        'emailAttribute' => 'email',
        'form' => $form,
    ]) ?>

    <?= $form->field($model, 'phone')->textInput() // ['placeholder' => '+7 ___ ___-__-__'] ?>

    <style>.consent-list-fix {padding: 0px 14px 0px 14px; margin: 0px;}</style>

    <div class="form-group">
        <p><strong>Требуется ваше согласие по следующим пунктам:</strong></p>
        <ol class="consent-list-fix">
            <li>Я подтверждаю, что вся представленная информация является достоверной и точной;</li>
            <li>Я несу ответственность в соответствии с действующим законодательством РК за предоставление заведомо ложных или неполных сведений;</li>
            <li>Я выражаю свое согласие на необходимое использование и обработку своих персональных данных, в том числе в информационных системах;</li>
            <li>В случае обнаружения представленной пользователями неполной и/или недостоверной информации, услугодатель ответственности не несет.</li>
        </ol>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'agreeTerms')->checkbox([
            'template' => "<div style=\"margin-left: 14px;\" class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?>
    </div>

    <?php if ($model->userAgreementLink !== null) : ?>
    <div class="form-group">
        <?php $link = "<a href=\"" . $model->userAgreementLink . "\">" . Yii::t("auth.main", "С пользовательским соглашением") . "</a>"; ?>
        <?php $id = Html::getInputId($model, 'agreeUserAgreement'); ?>
        <?= $form->field($model, 'agreeUserAgreement')->checkbox([
            'template' => "<div style=\"margin-left: 14px;\" class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'label' => "<label class=\"custom-control-label\" for=\"{$id}\">{$link}&nbsp;" . Yii::t("auth.main", "ознакомлен(-а) и согласен(-на)") . "</label>"
        ]) ?>
    </div>
    <?php endif; ?>

    <?php if ($model->privacyPolicyLink !== null) : ?>
    <div class="form-group">
        <?php $link = "<a href=\"" . $model->privacyPolicyLink . "\">" . Yii::t("auth.main", "С политикой конфиденциальности") . "</a>"; ?>
        <?php $id = Html::getInputId($model, 'agreePrivacyPolicy'); ?>
        <?= $form->field($model, 'agreePrivacyPolicy')->checkbox([
            'template' => "<div style=\"margin-left: 14px;\" class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'label' => "<label class=\"custom-control-label\" for=\"{$id}\">{$link}&nbsp;" . Yii::t("auth.main", "ознакомлен(-а) и согласен(-на)") . "</label>"
        ]) ?>
    </div>
    <?php endif; ?>

    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>

    <?php if ($model->useCaptcha) : ?>
        <?= $form->field($model, 'captcha')->label('')->widget(
            ReCaptcha::class,
            ['siteKey' => $model->siteKey]
        ) ?>
    <?php endif; ?>

    <style>
        .alert-padding-fix ul {
            padding: 0px 14px 0px 14px;
            margin: 0px;
        }
    </style>

    <?php if ($model->hasErrors()) : ?>
        <div class="alert alert-danger">
            <?= Html::errorSummary($model, ['header' => '', 'class' => 'alert-padding-fix']) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div>
            <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'registration-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>