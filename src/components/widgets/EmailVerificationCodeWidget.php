<?php

namespace vs\yii2\auth\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class EmailVerificationCodeWidget extends Widget
{
    public $model;
    public $attribute;
    public $emailAttribute;
    public $form;

    public $url = '/auth/auth/send-verification-code';

    public function run()
    {
        $id = Html::getInputId($this->model, $this->attribute);
        $emailId = Html::getInputId($this->model, $this->emailAttribute);
        $formId = $this->form->getId();

        $this->view->registerJs("
            $('#get-verification-code').on('click', function () {
                var email = $('#{$emailId}').val();
                if (email) {
                    $.ajax({
                        url: '" . Url::toRoute([$this->url]) . "',
                        type: 'POST',
                        data: { email: email },
                        success: function (data) {
                            if (data.success) {
                                var messageContainer = $('<div class=\"alert-success alert alert-dismissible\">' + data.message + '<button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">×</span></button></div>');
                                messageContainer.insertBefore('.registration');
                                setTimeout(function() {
                                    messageContainer.fadeOut('slow', function() {
                                        $(this).remove();
                                    });
                                }, 5000);
                                var input = $('#" . $id . "');
                                input.removeClass('is-invalid');
                                input.parent('.input-group').next('.invalid-feedback').text('');
                            } else if (data.message) {
                                var input = $('#" . $id . "');
                                input.addClass('is-invalid');
                                input.parent('.input-group').next('.invalid-feedback').text(data.message);
                            } else {
                                var messageContainer = $('<div class=\"alert alert-error\">Сервис временно недоступен, попробуйте позже.</div>');
                                $('body').append(messageContainer);
                                setTimeout(function() {
                                    messageContainer.fadeOut('slow', function() {
                                        $(this).remove();
                                    });
                                }, 5000);
                            }
                        }
                    });
                } else {
                    alert('Пожалуйста, введите корректный адрес электронной почты.');
                }
            });
        ");

        $input = Html::activeTextInput($this->model, $this->attribute, ['class' => 'form-control', 'id' => $id]); // 'placeholder' => 'XXX-XXX'
        $button = Html::button('Получить код', ['class' => 'btn btn-primary', 'id' => 'get-verification-code']);

        $field = $this->form->field($this->model, $this->attribute, [
            'template' => "{label}\n<div class=\"col-lg-6 input-group input-group-fix\">{$input}<div class=\"input-group-append\">{$button}</div></div>\n{error}",
            'labelOptions' => ['class' => 'col-lg-3 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'col-lg-9 invalid-feedback invalid-feedback-fix'],
        ])->textInput();

        return "
            <style>.input-group-fix {padding: 0px; margin: 0px;} .invalid-feedback-fix {display: block;}</style>
            {$field}
        ";
    }
}