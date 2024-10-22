<?php

namespace app\controllers;

use app\models\Book;
use app\models\BookAuthor;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
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
                            'actions' => ['index', 'view'],
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
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Book::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
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
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($this->request->isPost) {
            $postData = $this->request->post();
            if ($model->load($postData)) {
                $transaction = Yii::$app->db->beginTransaction();
                $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
                if ($model->uploadPhoto() && $model->save()) {
                    $authorIds = array_values(ArrayHelper::getValue($postData,'Book.authorIds'));
                    // Удаляем старые связи для данной книги
                    BookAuthor::deleteAll(['book_id' => $model->id]);

                    // Добавляем новые связи
                    foreach ($authorIds as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }

                    $transaction->commit();
                    // Отправка СМС после успешного сохранения книги
                    // TODO доделать нормальную авторизацию и хранить номер телефона в профиле пользователя
                    $phone = '79087964781'; // номер телефона пользователя
                    $text = 'Новая книга добавлена: ' . $model->title;

                    if (Yii::$app->smsSender->sendSms($phone, $text)) {
                        Yii::$app->session->setFlash('success', 'Книга успешно добавлена и СМС отправлена.');
                    } else {
                        Yii::$app->session->setFlash('error', 'Книга добавлена, но произошла ошибка при отправке СМС.');
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Произошла ошибка при добавлении книги/загрузке фото');
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($this->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->uploadPhoto() && $model->save()) {
                $postData = $this->request->post();
                $authorIds = array_values(ArrayHelper::getValue($postData,'Book.authorIds'));
                // Удаляем старые связи
                BookAuthor::deleteAll(['book_id' => $model->id]);

                // Добавляем новые связи
                foreach ($authorIds as $authorId) {
                    $bookAuthor = new BookAuthor();
                    $bookAuthor->book_id = $model->id;
                    $bookAuthor->author_id = $authorId;
                    $bookAuthor->save();
                }

                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $transaction->rollBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Book model.
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

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
