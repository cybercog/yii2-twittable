<?php

namespace cybercog\twittable\models;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class Twitter
 * @package cybercog\twittable\models
 */
class Twitter
{

    public $consumerKey = '';
    public $consumerSecret = '';
    public $accessToken = '';
    public $accessTokenSecret = '';

    private $connection;

    public function init()
    {
        parent::init();
        $this->connection = new TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $this->accessToken,
            $this->accessTokenSecret
        );
    }

    private function getConfig()
    {
        return $this->connection->get('help/configuration');
    }

    public function statusUpdate($message, $url, $tags)
    {
        $tagList = explode(',', $tags);
        $tags = '';
        foreach ($tagList as $tag) {
            $tag = trim($tag);
            $tag = str_replace(array(' ', '-'), '_', $tag);
            $tags .= ' #' . $tag;
        }
        // Чтобы @mention не парсилось (но так адрес почты сломает, а надо ли вообще почту в заголовках?)
        $message = str_replace('@', '@ ', $message);

        $twitterConfig = $this->getConfig();
        $urlLength = intval($twitterConfig->short_url_length_https);
        $messageLength = strlen($message);
        $tagsLength = strlen($tags);
        if ($messageLength + $urlLength + $tagsLength > 140) {
            // :TODO: Write an error log (shorten tags)
            return false;
        }

        $status = $message . ' ' . $url . $tags;

        $response = $this->connection->post('statuses/update', array('status' => $status));
        // :TODO: Handle errors
        return $response->id;
    }

    public function statusDelete($id)
    {
        return $this->connection->post('statuses/destroy/' . $id);
    }
}