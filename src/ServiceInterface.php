<?php

namespace React\Anticaptcha;

use React\Promise\Promise;

/**
 * Interface ServiceInterface
 * @package React\Anticaptcha
 */
interface ServiceInterface
{
    /**
     * @param Captcha $captcha
     * @return Promise
     */
    public function upload(Captcha $captcha);

    /**
     * @param string $captchaId
     * @return Promise
     */
    public function wait($captchaId);
}
