<?php

namespace app\components;

use app\models\ShortUrl;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Url;

class UrlShortenerService extends Component
{
    public function isUrlAvailable(string $url): bool
    {
        if (!$this->hasPublicHost($url)) {
            return false;
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => (int) Yii::$app->params['requestTimeout'],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'ShortLinkQrBot/1.0',
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);

        curl_exec($ch);
        $error = curl_errno($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $error === 0 && $httpCode >= 200 && $httpCode < 400;
    }

    private function hasPublicHost(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!is_string($host) || $host === '' || strtolower($host) === 'localhost') {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->isPublicIp($host);
        }

        $records = dns_get_record($host, DNS_A | DNS_AAAA);
        if ($records === false || $records === []) {
            return false;
        }

        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;
            if (!is_string($ip) || !$this->isPublicIp($ip)) {
                return false;
            }
        }

        return true;
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    public function getOrCreateShortUrl(string $originalUrl): ShortUrl
    {
        $model = ShortUrl::findOne(['original_url' => $originalUrl]);
        if ($model !== null) {
            return $model;
        }

        $model = new ShortUrl();
        $model->original_url = $originalUrl;
        $model->short_code = $this->generateUniqueShortCode();
        $model->hits_count = 0;

        if (!$model->save()) {
            throw new Exception('Не удалось сохранить ссылку: ' . json_encode($model->getFirstErrors(), JSON_UNESCAPED_UNICODE));
        }

        return $model;
    }

    public function buildShortUrl(string $code): string
    {
        return Url::to(['redirect/go', 'code' => $code], true);
    }

    public function buildQrCodeDataUri(string $url): string
    {
        $result = Builder::create()
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(260)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return $result->getDataUri();
    }

    protected function generateUniqueShortCode(): string
    {
        $length = (int) Yii::$app->params['shortCodeLength'];
        do {
            $code = rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
            $code = substr($code, 0, $length);
        } while (ShortUrl::find()->where(['short_code' => $code])->exists());

        return $code;
    }

    public function detectClientIp(): string
    {
        $headers = Yii::$app->params['trustProxyHeaders']
            ? ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR']
            : ['REMOTE_ADDR'];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $raw = explode(',', (string) $_SERVER[$header])[0];
                $ip = trim($raw);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }
}
