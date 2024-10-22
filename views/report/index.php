<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use \yii\widgets\ActiveForm;
use app\models\Author;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ТОП 10 авторов, выпустивших больше всего книг за указанный год';
$this->params['breadcrumbs'][] = $this->title;
$currentYear = date('Y');
?>
<div class="year-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, введите год:</p>
    <?= $form = Html::beginForm(Url::to(['report/get-top']), 'post', ['id' => 'year-form']) ?>

    <?= Html::input('number', 'year', $currentYear, ['class' => 'form-control', 'max' => 9999, 'placeholder' => $currentYear]) ?>
    <div class="form-group">
        <?= Html::submitInput('Показать', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
    </div>

    <?= Html::endForm() ?>

</div>