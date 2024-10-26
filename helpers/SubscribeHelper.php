<?php

namespace app\helpers;

use app\components\SmsSender;
use app\models\Author;
use app\models\Book;
use app\models\Subscription;
use Yii;

class SubscribeHelper {
    public static function notifySubscribers(int $authorId, int $bookId) {
        $book = Book::findOne($bookId);
        $author = Author::findOne($authorId);
        if ($book && $author) {
            $text = "В каталог добавлена книга автора $author с названием: $book->title";

            /** @var Subscription $subscriber */
            foreach ($author->subscribers ?? [] as $subscriber) {
                $phone = $subscriber->phone_number;
                /** @var SmsSender $smsSender */
                $smsSender = Yii::$app->smsSender;
                if ($smsSender->sendSms($phone, $text)) {
                    Yii::info($text . " и успешно отправлено СМС-оповещение на номер $phone");
                } else {
                    Yii::error("Произошла ошибка при отправке СМС-оповещение на номер $phone");
                }
            }
        }
    }
}