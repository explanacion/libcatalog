<?php

namespace app\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use app\models\Author;

class ReportController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetTop()
    {
        $year = Yii::$app->request->post('year');
        if (!$year) {
            throw new BadRequestHttpException('Параметр "year" не передан.');
        }

        $authors = Author::find()
            ->select(['author.*', 'COUNT(book.id) AS book_count'])
            ->innerJoinWith('books')
            ->where(['book.year' => (int)$year])
            ->groupBy('author.id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $authors,
        ]);

        return $this->render('gettop', [
            'year' => $year,
            'dataProvider' => $dataProvider,
        ]);
    }
}
