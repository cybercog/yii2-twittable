<?php
/**
 * @link https://github.com/cybercog/yii2-twittable
 * @copyright Copyright (c) 2015 Anton Komarev
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace cybercog\twittable\behaviors;

use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use cybercog\twittable\models\Twitter;

/**
 * TaggableBehavior
 *
 * @property ActiveRecord $owner
 *
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
     * Evaluates the value of the user.
     * The return result of this method will be assigned to the current attribute(s).
     * @param Event $event
     * @return mixed the value of the user.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            $message = $event->sender->title;
            $url = Url::to(['view', 'id' => $event->sender->id], true);
            $tags = $event->sender->tagNames;
            $twitter = new Twitter();
            $tweetId = $twitter->statusUpdate($message, $url, $tags);
            return $tweetId;
        } else {
            return call_user_func($this->value, $event);
        }
    }
}