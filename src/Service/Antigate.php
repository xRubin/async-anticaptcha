<?php
namespace React\Anticaptcha\Service;

use React\Anticaptcha\Captcha;
use React\Anticaptcha\ServiceAbstract;
use React\Promise;

/**
 * Class Antigate
 * @package React\Anticaptcha
 */
class Antigate extends ServiceAbstract
{
    /**
     * @return array
     */
    public function getErrorCodes()
    {
        return
            [
                'ERROR_CONNECTION' => 'Не удалось соединиться с сервером',

                'ERROR_WRONG_USER_KEY' => 'Неправильный формат ключа учетной записи (длина не равняется 32 байтам)',
                'ERROR_KEY_DOES_NOT_EXIST' => 'Вы использовали неверный captcha ключ в запросе',
                'ERROR_ZERO_BALANCE' => 'Нулевой либо отрицательный баланс',
                'ERROR_NO_SLOT_AVAILABLE' => 'Нет свободных работников в данный момент, попробуйте позже либо повысьте свою максимальную ставку',
                'ERROR_ZERO_CAPTCHA_FILESIZE' => 'Размер капчи которую вы загружаете менее 100 байт',
                'ERROR_TOO_BIG_CAPTCHA_FILESIZE' => 'Ваша капча имеет размер более 100 килобайт',
                'ERROR_WRONG_FILE_EXTENSION' => 'Ваша капча имеет неверное расширение, допустимые расширения jpg, jpeg, gif, png',
                'ERROR_IMAGE_TYPE_NOT_SUPPORTED' => 'Невозможно определить тип файла капчи, принимаются только форматы JPG, GIF, PNG',
                'ERROR_IP_NOT_ALLOWED' => 'Запрос с этого IP адреса с текущим ключом отклонен. Пожалуйста смотрите раздел управления доступом по IP',
                'ERROR_WRONG_ID_FORMAT' => 'Некорректный идентификатор капчи, принимаются только цифры',
                'ERROR_CAPTCHA_UNSOLVABLE' => 'Капчу не смогли разгадать 5 разных работников',
            ];
    }

    /**
     * @return string
     */
    public function getUploadMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return 'http://antigate.com/in.php';
    }

    /**
     * @param Captcha $captcha
     * @return string
     */
    public function getUploadQuery(Captcha $captcha)
    {
        return http_build_query(
            array_merge(
                $captcha->getOptions(),
                array(
                    'method' => 'base64',
                    'key' => $this->key,
                    'body' => base64_encode((string)$captcha),
                    'ext' => 'jpg'
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getResultMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getResultPath()
    {
        return 'http://antigate.com/res.php';
    }

    /**
     * @param string $captchaId
     * @return string
     */
    public function getResultQuery($captchaId)
    {
        return http_build_query(
            array(
                'key' => $this->key,
                'action' => 'get',
                'id' => $captchaId
            )
        );
    }
}
