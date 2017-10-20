<?php

define('SERVER_ROOT', __DIR__);

require SERVER_ROOT . '/lib/App.php';
require SERVER_ROOT . '/lib/Component.php';
require SERVER_ROOT . '/lib/MySQL.php';
require SERVER_ROOT . '/lib/UserSession.php';
require SERVER_ROOT . '/lib/StringHelper.php';
require SERVER_ROOT . '/controllers/Controller.php';
require SERVER_ROOT . '/controllers/SiteController.php';
require SERVER_ROOT . '/controllers/FriendsController.php';
require SERVER_ROOT . '/controllers/CommentsController.php';
require SERVER_ROOT . '/controllers/WallController.php';
require SERVER_ROOT . '/models/Model.php';
require SERVER_ROOT . '/models/Comments.php';
require SERVER_ROOT . '/models/Users.php';
require SERVER_ROOT . '/models/Friends.php';
require SERVER_ROOT . '/models/FriendOffers.php';

$config = require SERVER_ROOT . '/config.php';

$app = \DemoSocial\App::getInstance();
$app->init($config);
$app->run();

