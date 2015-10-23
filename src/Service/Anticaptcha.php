<?php
namespace React\Anticaptcha\Service;

use React\Anticaptcha\Captcha;
use React\Anticaptcha\ServiceAbstract;
use React\Promise;

/**
 * Class Anticaptcha
 * @package React\Anticaptcha
 */
class Anticaptcha extends ServiceAbstract
{
    /**
     * @return array
     */
    public function getErrorCodes()
    {
        return [
            'ERROR_CONNECTION' => 'Не удалось соединиться с сервером',

            'ERROR_WRONG_USER_KEY' => 'Неправильный формат ключа учетной записи (длина не равняется 32 байтам)',
            'ERROR_KEY_DOES_NOT_EXIST' => 'Авторизационный ключ не существует в системе',
            'ERROR_ZERO_BALANCE' => 'Баланс учетной записи ниже или равен нулю',
            'ERROR_NO_SLOT_AVAILABLE' => 'Нет свободных работников в данный момент, попробуйте позже либо повысьте свою максимальную ставку',
            'ERROR_ZERO_CAPTCHA_FILESIZE' => 'Размер капчи которую вы загружаете менее 100 байт',
            'ERROR_IMAGE_TYPE_NOT_SUPPORTED' => 'Формат капчи не распознан по EXIF заголовку либо не поддерживается. Допустимые форматы: JPG, GIF, PNG',
            'ERROR_IP_NOT_ALLOWED' => 'Запрос с этого IP адреса с текущим ключом отклонен',
            'ERROR_WRONG_ID_FORMAT' => 'ID капчи не является числом',
            'ERROR_NO_SUCH_CAPCHA_ID' => 'Капча с таким ID не была найдена в системе',
            'ERROR_CAPTCHA_UNSOLVABLE' => '5 разных работников не смогли разгадать капчу',
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
        return 'http://anti-captcha.com/in.php';
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
        return 'http://anti-captcha.com/res.php';
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
