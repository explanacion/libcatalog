<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Подписка на новые книги автора';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>Вы собираетесь подписаться на новые книги автора "<?= $model->name ?>".</p>

<p>Введите ваш номер телефона. При появлении новых книг вам придёт СМС</p>

<?= $form = Html::beginForm(Url::to(['create-subscribe', 'id' => $model->id]), 'post', ['id' => 'phone-form']) ?>

<div class="form-group">
    <?= Html::textInput('phone_number', '', ['class' => 'phone-form', 'max' => 256, 'placeholder' => '79087964781']) ?>
    <?= Html::submitInput('Подписаться', ['class' => 'btn btn-secondary', 'name' => 'submit-button']) ?>
</div>

<?= Html::endForm() ?>
