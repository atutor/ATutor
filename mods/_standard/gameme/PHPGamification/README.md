PHPGamification
===============

**PHPGamification** is a **Generic Gamification PHP Framework** that claims to be simple and objective.

Forked from [jfuentesa/gamification](https://github.com/jfuentesa/gengamification).

# Features

* Quickly integrate a **full gamification engine** to your projects
* Handle **Points**, **Levels** and **Bagdes**
* Access stored user **alerts** and **logs** to easy understand user scores
* Use your own **user database** with no need to change your tables structures
* Create Callbacks to use when user receives Points or Badges

# Sample code

```php
/** Instantiate **/
$gamification = new PHPGamification::getInstance();
$gamification->setDAO(new DAO('my_host', 'my_databse', 'my_user', 'my_pass'));

/** Badges definitions */
$gamification->addBadge('newbee', 'Newbee', 'You logged in, congratulations!');
$gamification->addBadge('addict', 'Addict', 'You have logged in 10 times');
$gamification->addBadge('professional_writer', 'Professional Writer', 'You must write a book! 50 posts!!');

/** Levels definitions */
$gamification->addLevel(0, 'No Star');
$gamification->addLevel(1000, 'Five stars', 'grant_five_stars_badge');// Execute event: grant_five_stars_badge
$gamification->addLevel(2000, '2K points!');

/** Events definitions */

// Welcome to our network! (disallow reachRequiredRepetitions)
$event = new Event();
$event->setAlias('join_network')
    ->setEachPointsGranted(10)
    ->setAllowRepetitions(false); // Just one time
$gamification->addEvent($event);

// Each Login/Logged in 10 times (25 points each time, 50 points when reach 10 times)
$event = new Event();
$event->setAlias('login')
    ->setEachPointsGranted(25)
    ->setEachBadgeGranted($gamification->getBadgeByAlias('newbee'))
    ->setReachRequiredRepetitions(10)
    ->setReachPointsGranted(50)
    ->setReachBadgeGranted($gamification->getBadgeByAlias('addict'));
$gamification->addEvent($event);

// Each post to blog/You wrote 5 post to your blog (100 points each + badge, 1000 points reach)
$event = new Event();
$event->setAlias('post_to_blog')
    ->setEachPointsGranted(150)
    ->setEachCallback("MyOtherClass::myPostToBlogCallBackFunction")
    ->setReachRequiredRepetitions(50)
    ->setReachBadgeGranted($gamification->getBadgeByAlias('professional_writer'));
$gamification->addEvent($event);

/** Using it */

$gamification->setUserId(1);
$gamification->executeEvent('join_network');
$gamification->executeEvent('login');
for ($i=0; $i<9; $i++)
    $gamification->executeEvent('login');
$gamification->executeEvent('post_to_blog', array('YourPostId'=>11));

/** Getting user data */
echo "<pre>";
var_dump($gamification->getUserAllData());
echo "</pre>";

/** Getting users ranking */
echo "<pre>";
var_dump($gamification->getUsersPointsRanking();
echo "</pre>";
```


# Installing

Clone, download or just add to your composer.json file:
```json
{
    "require": {
        "tiagogouvea/phpgamification": "*"
    }
}
```

Or call:

```shell
composer require tiagogouvea/phpgamification
```

# Using

In /sample/ folder you can see a simple intuitive code. It must be you start point to use PHPGamification.

## Setup your gamification rules

Get the PHPGamification engine to construct the object:

```php
$gamification = new PHPGamification::getInstance();
$gamification->setDAO(new DAO('my_host', 'my_databse', 'my_user', 'my_pass'));
```

You can set your own DAO: implement DAOInterface and set your instance with $gamification->setDAO();

### Levels and badges

Just tell what levels and badges have your game, like in sample file.

### Events

A event may occur a just time or many times. When creating a event you can setup Points and Badge to be granted **each** time it occurs and/or when user **reach** the required repetitions.

* Each: occurs every time a event is called
* Reach: occurs only when user reach the required reachRequiredRepetitions

* You can also use setAllowRepetitions(false) to tell the event can be called just one time by user.

### Callbacks

Use callback methods to improve the user interation with your gamification system or validate if a user really need receive points/badges. The callbacks can be called in two moments:

* Each Callback: will run every time and event are executed
* Reach Callback: will run just when user reach the required event repetions

When using your callback methods, remember to return **true** to the event continue, or **false** to event don't grant Points nor adges.
It can be used to cerify some data in your business logic before giving points or badges to the user.

## Running your gamification engine

Start the engine setting the **User Id** that you are working with:

```php
$gamification->setUserId($yourUserId);
```

Every time you want to something happen in your gamification enviroment you must **execute a event** calling:

```php
$gamification->executeEvent('login',array('more_data'=>'to_your_callback'));
```

All information you can need about the some user can be retrieved calling getUserScores(), getUserBadges(), showUserLog() and getUserEvents(), or $gamification->getUserAllData() to return all togheter:

```php
var_dump($gamification->getUserScores());
```

## Keep your users engajed

Take advantage of events callback to send emails to user user when some great happend, like, win a new badge or growing by the levels.

# Todo

If you want to colaborate, make a fork and do your pull requests!

* Allow Callbacks when your conquest a new level (move ReachCallback to badge and level?)
* Fix autoload to work just with composer
* Create a iframe call to people show their points and badges on their blogs

# Contact

Tiago Gouvêa

[Blog](http://www.tiagogouvea.com.br) | [Twitter](https://twitter.com/TiagoGouvea) | [Facebook](https://www.facebook.com/tiagogouvea)
