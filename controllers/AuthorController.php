<?php

namespace app\controllers;

use app\models\Author;
use app\models\AuthorSearch;
use app\models\Subscription;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthorController implements the CRUD actions for Author model.
 */
class AuthorController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'subscribe', 'create-subscribe'],
                            'allow' => true,
                            'roles' => ['?', '@'],
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['@'],
                        ]
                    ]
                ]
            ],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'create-subscribe' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Author models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Author model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Author model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Author();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Author model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Author model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // создание подписки на новые книги автора
    public function actionCreateSubscribe($id)
    {
        if (!$id) {
            throw new BadRequestHttpException('Не указан идентификатор автора.');
        }
        if (!$this->findModel($id)) {
            throw new BadRequestHttpException('Автор не найден.');
        }

        $phone_number = $this->request->post('phone_number');
        if (!$phone_number) {
            throw new BadRequestHttpException('Не указан номер телефона.');
        }
        if (!preg_match('/^\d{7,15}$/', $phone_number)) {
            \Yii::$app->session->setFlash('error', 'Неверный номер телефона. Пример корректного номера: 79087964781');
            return $this->redirect(['index', 'id' => $id]);
        }

        $exists = Subscription::find()->where(['author_id' => $id, 'phone_number' => $phone_number])->exists();
        if ($exists) {
            \Yii::$app->session->setFlash('error', 'Вы уже подписаны на рассылку автора.');
        } else {
            $subscription = new Subscription();
            $subscription->author_id = $id;
            $subscription->phone_number = $phone_number;
            if ($subscription->save()) {
                \Yii::$app->session->setFlash('success', 'Вы успешно подписались на рассылку новых книг автора.');
            } else {
                \Yii::$app->session->setFlash('error', 'При подписке на рассылку возникли ошибки.');
            }
        }

        // Возвращаемся на страницу автора
        return $this->redirect(['view', 'id' => $id]);
    }

    // инициировать подписку
    public function actionSubscribe($id)
    {
        $model = $this->findModel($id);
        return $this->render('subscribe', ['model' => $model]);
    }

    /**
     * Finds the Author model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Author the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
