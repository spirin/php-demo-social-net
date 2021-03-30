<?php

return [
	'session' => [
		'cookie_lifetime' => 86400,
		'use_only_cookies' => true,
	],
	'mysql' => [
		'host' => 'localhost',
		'name' => 'demosocial',
		'username' => 'demosocial',
		'password' => 'demosocial',
	],
	'routes' => [
		'error404' => ['controller' => \Controller\SiteController::class, 'action' => 'error404', 'guest' => true],
		'error500' => ['controller' => \Controller\SiteController::class, 'action' => 'error500', 'guest' => true],
		'index' => ['controller' => \Controller\SiteController::class, 'action' => 'index', 'guest' => false],
		'register' => ['controller' => \Controller\SiteController::class, 'action' => 'register', 'guest' => true],
		'login' => ['controller' => \Controller\SiteController::class, 'action' => 'login', 'guest' => true],
		'logout' => ['controller' => \Controller\SiteController::class, 'action' => 'logout', 'guest' => false],
		'profile' => ['controller' => \Controller\SiteController::class, 'action' => 'profile', 'guest' => false],
		
		'wall' => ['controller' => \Controller\WallController::class, 'action' => 'index', 'guest' => false],
		'ajaxPost' => ['controller' => \Controller\WallController::class, 'action' => 'ajaxPost', 'guest' => false],
		'ajaxLike' => ['controller' => \Controller\WallController::class, 'action' => 'ajaxLike', 'guest' => false],
		'ajaxDisLike' => ['controller' => \Controller\WallController::class, 'action' => 'ajaxDisLike', 'guest' => false],
		
		'friends' => ['controller' => \Controller\FriendsController::class, 'action' => 'index', 'guest' => false],
		'search' => ['controller' => \Controller\FriendsController::class, 'action' => 'search', 'guest' => false],
		'ajaxSearch' => ['controller' => \Controller\FriendsController::class, 'action' => 'ajaxSearch', 'guest' => false],
		'ajaxOffers' => ['controller' => \Controller\FriendsController::class, 'action' => 'ajaxOffers', 'guest' => false],
		'ajaxRequest' => ['controller' => \Controller\FriendsController::class, 'action' => 'ajaxRequest', 'guest' => false],
		'ajaxConfirm' => ['controller' => \Controller\FriendsController::class, 'action' => 'ajaxConfirm', 'guest' => false],
		'ajaxReject' => ['controller' => \Controller\FriendsController::class, 'action' => 'ajaxReject', 'guest' => false],
		
		'messages' => ['controller' => \Controller\CommentsController::class, 'action' => 'index', 'guest' => false],
		'ajaxComment' => ['controller' => \Controller\CommentsController::class, 'action' => 'ajaxComment', 'guest' => false],
		'ajaxGetComments' => ['controller' => \Controller\CommentsController::class, 'action' => 'ajaxGetComments', 'guest' => false],
	],
];
