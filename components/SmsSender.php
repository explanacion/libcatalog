<?php

namespace app\components;

use Yii;
use yii\base\Component;

class SmsSender extends Component
{
    public $apiKey;
    public $apiUrl = 'http://smspilot.ru/api.php';

    public function sendSms($phone, $text)
    {
        $url = $this->apiUrl . '?send=' . urlencode($text) . '&to=' . $phone . '&apikey=' . $this->apiKey;
        $result = file_get_contents($url);

        if (strpos($result, 'SUCCESS') === 0) {
            Yii::info('SMS на телефон ' . $phone . ' успешно отправлена.');
            return true;
        } else {
            Yii::error('Ошибка отправки SMS: ' . $result);
            return false;
        }
    }
}