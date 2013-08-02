<?php

class Facebook_GraphApi
{
    /**
     * @var Facebook
     */
    protected $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @param array $params
     */
    public function me($params = array())
    {
        return $this->fetch('/me', $params);
    }

    /**
     * @param string $facebookId Numerical facebook id or username
     * @param array $params
     *
     * @return array
     */
    public function posts($facebookId, $params = array())
    {
        $path = "/${facebookId}/posts";

        return $this->fetch($path, 'GET', $params);
    }

    /**
     * @param string $facebookId Numerical facebook id or username
     * @param array $params
     */
    public function feed($facebookId, $params = array())
    {
        $path = "/${facebookId}/feed";

        return $this->fetch($path, 'GET', $params);
    }

    /**
     * @param string $endpoint
     * @param array $params
     *
     * @return mxied
     */
    public function fetch($path, $params = array())
    {
        try {
            $data = $this->facebook->api($path, $params);
        } catch (FacebookApiException $e) {
            $data = false;
        }

        return $data;
    }
}
