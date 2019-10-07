<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model fcm\manager\models\FcmNotifications */

$this->title = Yii::t('app', 'Create Fcm Notifications');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fcm Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fcm-notifications-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
