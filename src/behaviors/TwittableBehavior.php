<?php
/**
 * @link https://github.com/cybercog/yii2-twittable
 * @copyright Copyright (c) 2015 LLC CyberCog
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace cybercog\yii\twittable\behaviors;

use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use cybercog\yii\twittable\models\Twitter;
use yii\helpers\Url;

/**
 * Class TaggableBehavior
 * @package cybercog\yii\twittable\behaviors
 * @property ActiveRecord $owner
 * @author Anton Komarev <ell@cybercog.su>
 */
class TwittableBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive tweet identifier
     * Set this property to false if you do not want to record the tweet identifier.
     */
    public $tweetIdAttribute = 'tweet_id';
    /**
     * @var callable the value that will be assigned to the attributes. This should be a valid
     * PHP callable whose return value will be assigned to the current attribute(s).
     * The signature of the callable should be:
     *
     * ```php
     * function ($event) {
     *     // return value will be assigned to the attribute
     * }
     * ```
     *
     * If this property is not set, `null` value will be assigned to the attribute.
     */
    public $value;

    public $consumerKey;
    public $consumerSecret;
    public $accessToken;
    public $accessTokenSecret;

    /**
     * If setted to `true` will automatically tweet any created model
     * @var bool
     */
    public $autoPosting = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->tweetIdAttribute],
            ];
        }
    }

    /**
     * Evaluates the value of the tweet.
     * The return result of this method will be assigned to the current attribute(s).
     * @param Event $event
     * @return mixed the value of the tweet.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            if ($this->autoPosting) {
                $tweetId = $this->createTweet($event);
            }
            else {
                $tweetId = null;
            }
        } else {
            $tweetId = call_user_func($this->value, $event);
        }
        return $tweetId;
    }

    /**
     * Posting data to Twitter
     * @param $event
     * @return Int64
     */
    private function createTweet($event)
    {
        $message = $event->sender->title;
        $url = Url::to(['view', 'slug' => $event->sender->slug], true);
        $tags = $event->sender->tagNames;
        $twitter = new Twitter([
            'consumerKey' => $this->consumerKey,
            'consumerSecret' => $this->consumerSecret,
            'accessToken' => $this->accessToken,
            'accessTokenSecret' => $this->accessTokenSecret,
        ]);
        return $twitter->statusUpdate($message, $url, $tags);
    }
}