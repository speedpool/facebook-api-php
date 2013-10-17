<?php

class FacebookGraph
{
    const CACHE_USER_FILENAME = 'user';

    const CACHE_USER_AGE = 600;

    const CACHE_FEED_FILENAME = 'feed';

    const CACHE_FEED_AGE = 600;

    const CACHE_POSTS_FILENAME = 'posts';

    const CACHE_POSTS_AGE = 600;

    const CACHE_STREAM_FILENAME = 'stream';

    const CACHE_STREAM_AGE = 600;

    protected $id;

    protected $facebook;

    public function __construct($id, Facebook $facebook)
    {
        $this->id = $id;
        $this->facebook = $facebook;
    }

    public $cache = true;

    protected $cacheDir = 'cache/';

    public function getUser()
    {
        $cacheFilename = md5(self::CACHE_USER_FILENAME) . '.json';

        if ($this->isCached($cacheFilename, self::CACHE_USER_AGE)) {
            $json = $this->getCached($cacheFilename);
        } else {
            $params = array();
            $json = $this->request('', $params);

            $this->cache($cacheFilename, $json);
        }

        return $json;
    }

    public function getFeed($limit = 10, $name = '')
    {
        $prefix = preg_replace('/[^a-z0-9]/', '-', strtolower($name)) . '-';
        $prefix .= $limit . '-';

        $cacheFilename = md5(trim($prefix . self::CACHE_FEED_FILENAME, '-'))
            . '.json';

        if ($this->isCached($cacheFilename, self::CACHE_FEED_AGE)) {
            $json = $this->getCached($cacheFilename);
        } else {
            $params = array(
                'limit' => $limit,
            );

            if ($name != '') {
                $params['name'] = $name;
                $this->id = $name;
            }

            $json = $this->request('feed/', $params);

            $this->cache($cacheFilename, $json);
        }

        return $json;
    }

    public function getPosts($limit = 10, $name = '')
    {
        $prefix = preg_replace('/[^a-z0-9]/', '-', strtolower($name)) . '-';
        $prefix .= $limit . '-';

        $cacheFilename = md5(trim($prefix . self::CACHE_POSTS_FILENAME, '-'))
            . '.json';

        if ($this->isCached($cacheFilename, self::CACHE_POSTS_AGE)) {
            $json = $this->getCached($cacheFilename);
        } else {
            $params = array(
                'limit' => $limit,
                'fields' => 'from,message,name,picture,link,likes,comments.summary(true).filter(toplevel),description,actions,created_time',
                'with' => 'message',
            );

            if ($name != '') {
                $params['name'] = $name;
                $this->id = $name;
            }

            $json = $this->request('posts', $params);
            $this->cache($cacheFilename, $json);
        }

        return $json;
    }

    public function getStream($limit = 10, $id = null)
    {
        $prefix = preg_replace('/[^a-z0-9]/', '-', $id) . '-';
        $prefix .=  $limit . '-';

        $cacheFilename = md5(trim($prefix . self::CACHE_STREAM_FILENAME, '-'))
            . '.json';

        if (null === $id) {
            $id = $this->id;
        }

        if ($this->isCached($cacheFilename, self::CACHE_STREAM_AGE)) {
            $json = $this->getCached($cacheFilename);
        } else {
            $query = "SELECT post_id, permalink, created_time, message, comments, attachment, likes, is_hidden, is_published FROM stream WHERE source_id = {$id} AND message != '' AND filter_key = 'owner' LIMIT {$limit}";
            $json = $this->request('fql', $query);

            $this->cache($cacheFilename, $json);
        }

        return $json;
    }

    public function request($endpoint, $params)
    {
        switch ($endpoint) {
            case 'fql':
                $data = $this->facebook->fql($params);
                break;
            case 'api':
            default:
                $data = $this->facebook->api($this->id . '/' . $endpoint, 'GET', $params);
                break;
        }

        return json_encode($data);
    }

    public function cache($filename, $json)
    {
        if ($this->cache) {
            file_put_contents($this->cacheDir . $filename, $json);
        }
    }

    public function getCached($filename)
    {
        return file_get_contents($this->cacheDir . $filename);
    }

    public function isCached($filename, $age)
    {
        return $this->cache
            && file_exists($this->cacheDir . $filename)
            && filectime($this->cacheDir . $filename) > (time() - $age);
    }
}
