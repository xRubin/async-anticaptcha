<?php
namespace React\Anticaptcha;

use React\Promise;

/**
 * Class Resolver
 * @package React\Anticaptcha
 */
class Resolver
{
    /** @var ServiceInterface */
    private $connector;

    /**
     * @param ServiceInterface $connector
     */
    public function __construct(ServiceInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param Captcha $captcha
     * @return Promise\Promise
     */
    public function resolve(Captcha $captcha)
    {
        return
            $this->connector->upload($captcha)
            ->then([$this->connector, 'wait']);
    }
}
