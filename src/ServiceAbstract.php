<?php
namespace React\Anticaptcha;

use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\Promise;

/**
 * Class ServiceAbstract
 * @package React\Anticaptcha
 */
abstract class ServiceAbstract implements ServiceInterface
{
    /** @var int */
    public $requestInterval = 3;

    /** @var string */
    protected $key;

    /** @var Client */
    private $client;
    /** @var LoopInterface */
    private $loop;

    /**
     * @param Client $client
     * @param LoopInterface $loop
     * @param string $key
     */
    public function __construct(Client $client, LoopInterface $loop, $key)
    {
        $this->client = $client;
        $this->loop = $loop;
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getErrorCodes()
    {
        return [];
    }

    /**
     * @return string
     */
    abstract public function getUploadMethod();

    /**
     * @return string
     */
    abstract public function getUploadPath();

    /**
     * @param Captcha $captcha
     * @return string
     */
    abstract public function getUploadQuery(Captcha $captcha);

    /**
     * @return string
     */
    abstract public function getResultMethod();

    /**
     * @return string
     */
    abstract public function getResultPath();

    /**
     * @param string $captchaId
     * @return string
     */
    abstract public function getResultQuery($captchaId);

    /**
     * @param Captcha $captcha
     * @return Promise\Promise
     */
    public function upload(Captcha $captcha)
    {
        $deferred = new Promise\Deferred();

        $request = $this->client->request(
            $this->getUploadMethod(),
            $this->getUploadPath(),
            [
                'Content-Length' => strlen($this->getUploadQuery($captcha)),
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        $request->on('response', function ($response) use ($request, $deferred) {
            /** @var \React\HttpClient\Response $response */

            $response->on('data', function ($data, $response) use ($request, $deferred) {
                /** @var \GuzzleHttp\Psr7\Stream $data */
                /** @var \React\HttpClient\Response $response */
                $response->handleEnd();
                $answer = $data->getContents(); // stream read

                if (!$answer) {
                    $deferred->reject(
                        new ServiceException('ERROR_CONNECTION')
                    );
                }

                if (substr($answer, 0, 2) === 'OK') {
                    $deferred->resolve(substr($answer, 3));
                } else {
                    $deferred->reject(
                        new ServiceException($answer)
                    );
                }
            });
        });

        $request->end(
            $this->getUploadQuery($captcha)
        );
        return $deferred->promise();
    }

    /**
     * @param string $captchaId
     * @return Promise\Promise
     */
    public function wait($captchaId)
    {
        $deferred = new Promise\Deferred();
        $this->loop->addPeriodicTimer($this->requestInterval, function ($timer) use ($deferred, $captchaId) {
            $request = $this->client->request(
                $this->getResultMethod(),
                $this->getResultPath(),
                [
                    'Content-Length' => strlen($this->getResultQuery($captchaId)),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            );

            $request->on('response', function ($response) use ($request, $deferred, $timer) {
                /** @var \React\HttpClient\Response $response */
                $response->on('data', function ($data, $response) use ($request, $deferred, $timer) {
                    /** @var \GuzzleHttp\Psr7\Stream $data */
                    /** @var \React\HttpClient\Response $response */
                    $response->handleEnd();
                    $answer = $data->getContents(); // stream read

                    if (!$answer) {
                        $this->loop->cancelTimer($timer);
                        $deferred->reject(
                            new ServiceException('ERROR_CONNECTION')
                        );
                    } elseif (substr($answer, 0, 2) === 'OK') {
                        $this->loop->cancelTimer($timer);
                        $deferred->resolve(substr($answer, 3));
                    } elseif ($answer !== 'CAPCHA_NOT_READY') {
                        $this->loop->cancelTimer($timer);
                        $deferred->reject(
                            new ServiceException($answer)
                        );
                    }
                });
            });

            $request->end(
                $this->getResultQuery($captchaId)
            );
        });

        return $deferred->promise();
    }
}
