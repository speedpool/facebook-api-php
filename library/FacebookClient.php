<?php

require_once 'facebook-php-sdk/src/facebook.php';

class FacebookClient extends Facebook
{
    public function fql($query)
    {
        $params = array(
            'method' => 'fql.query',
            'query' => $query,
        );

        return $this->api($params);
    }
}
