# Twittable Behavior for Yii2

This extension provides behavior functions for tweeting.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require cybercog/yii2-twittable "*"
```

or add

```json
"cybercog/yii2-twittable": "*"
```

to the require section of your `composer.json` file.

## Migrations

If you want to auto-post News to twitter, run the following command

```bash
$ yii migrate/create news_tweet_link
```

Open the `/path/to/migrations/m_xxxxxx_xxxxxx_news_tweet_link.php` file,
inside the `up()` method add the following

```php
$sql = "ALTER TABLE news
        ADD COLUMN tweet_id BIGINT(64) NULL DEFAULT NULL";
$this->execute($sql);
```

inside the `down()` method add the following

```php
$sql = "ALTER TABLE news
        DROP COLUMN tweet_id";
$this->execute($sql);
```

## Configuring

```php
use cybercog\twittable\behaviors\TwittableBehavior;

/**
 * ...
 * @property string $tagNames
 */
class Post extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TwittableBehavior::className(),
                'consumerKey' => 'TWITTER_CONSUMER_KEY',
                'consumerSecret' => 'TWITTER_SECRET_KEY',
                'accessToken' => 'TWITTER_ACCESS_TOKEN',
                'accessTokenSecret' => 'TWITTER_SECRET_TOKEN',
            ],
        ];
    }
}
```

## Usage

Everything is working automatically right now. When you are creating new model - it's instantly adding a tweet.

## Todo

[] Configuring what to post: message, tags, url
[] Handle twitter errors
[] Manual posting of tweets if tweet isn't exist
[] Tweets deletion
[] Support Twitter cards
