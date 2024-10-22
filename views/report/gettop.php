<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\models\Author;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "ТОП 10 авторов, выпустивших больше всего книг за $year год";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-top-authors">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'label' => 'Books Count',
                'value' => 'book_count'
            ],
            [
                'label' => 'Books',
                'class' => 'yii\grid\DataColumn',
                'format' => 'raw',
                'value' => function ($data) use ($year) {
                    $books = [];
                    foreach ($data['books'] ?? [] as $book) {
                        if ($book['year'] == $year) {
                            $books[] = $book;
                        }
                    }

                    $bookTitles = array_map(function ($book) {
                        return Html::a(Html::encode($book['title']), ['book/view', 'id' => $book['id']]);
                    }, $books);
                    return implode(', ', $bookTitles);
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

</div>