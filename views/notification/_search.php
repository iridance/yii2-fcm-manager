<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model fcm\manager\models\search\FcmNotifications */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fcm-notifications-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'body') ?>

    <?= $form->field($model, 'target') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'delay_time') ?>

    <?php // echo $form->field($model, 'extra_data') ?>

    <?php // echo $form->field($model, 'create_datetime') ?>

    <?php // echo $form->field($model, 'update_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
