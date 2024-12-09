<?php

use yii\helpers\Html;
use yii\helpers\Url;

$recoveryLink = Url::toRoute(['/auth/auth/reset-password', 'login' => $username, 'fk' => $token->token], 'https');
?>

<h3 style="margin:22px 0px;font-family:Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;color:#2d3339;line-height:20px;">
    <?= \Yii::t('auth.main', 'Здравствуйте') ?>!
</h3>

<p style="line-height:22px;color:#727c84;padding-left: 20px;padding-right: 20px;">
<p><?= \Yii::t('auth.main', 'Вы запросили восстановление пароля. Для продолжения, перейдите по ссылке:'); ?></p>

<?= Html::a($recoveryLink, $recoveryLink) ?>
<p><?= \Yii::t('auth.main', 'Ссылка действительна в течении 24 часов.'); ?></p>

<br>
<em>
    <p><?= \Yii::t('auth.main', 'Желаем Вам приятной работы!'); ?></p>
</em>
</p>

