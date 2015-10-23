<?php

namespace React\Anticaptcha;

/**
 * Class Captcha
 * @package React\Anticaptcha
 */
class Captcha
{
    /** @var string */
    protected $data;

    /** @var array */
    protected $options = [];

    /**
     * @param string $data
     * @param array $options
     */
    public function __construct($data, array $options = [])
    {
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->data;
    }
}
