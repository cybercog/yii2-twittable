<?php

namespace cybercog\twittable\models;

use Abraham\TwitterOAuth\TwitterOAuth;
use yii\base\Model;

/**
 * Class Twitter
 * @package cybercog\twittable\models
 */
class Twitter extends Model
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

    /**
     * @return mixed
     */
    private function getConfig()
    {
        return $this->connection->get('help/configuration');
    }

    /**
     * @param $message
     * @param $url
     * @param $tags
     * @return bool
     */
    public function statusUpdate($message, $url, $tags)
    {
        $tags = $this->prepareTags($tags);
        // Prevent @mention parsing
        $message = str_replace('@', '@ ', $message);

        $twitterConfig = $this->getConfig();
        // :TODO: Make check of current scheme
        // :TODO: What will be if we will try to post some uncommon url schemes (will they convert in t.co links?)
        $urlLength = intval($twitterConfig->short_url_length_https);
        $messageLength = strlen($message);
        $tagsLength = strlen($tags);
        if ($messageLength + $urlLength + $tagsLength > 140) {
            // :TODO: Write an error log (shorten tags)
            return false;
        }

        // :TODO: Make configurable posts data (only message for example or link + tags)
        $status = $message . ' ' . $url . ' ' . $tags;
        $status = trim($status);
        $response = $this->connection->post('statuses/update', array('status' => $status));
        // :TODO: Handle errors
        return $response->id;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function statusDelete($id)
    {
        return $this->connection->post('statuses/destroy/' . $id);
    }

    /**
     * @param $tagsString
     * @return string
     */
    private function prepareTags($tags)
    {
        $tagsArray = explode(', ', $tags);
        foreach ($tagsArray as &$tag) {
            // spaces & dashes are restricted
            $tag = str_replace(array(' ', '-'), '_', $tag);
            $tag = '#' . $tag;
        }
        $tags = implode(' ', $tagsArray);
        return $tags;
    }

}