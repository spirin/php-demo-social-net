<?php

return array(
	'components' => array(
		'user' => array(
			'class' => '\DemoSocial\UserSession',
			'cookie_lifetime' => 86400,
			'use_only_cookies' => true
		),
		'mysql' => array(
			'class' => '\DemoSocial\MySQL',
			'host' => 'localhost',
			'name' => 'demosocial',
			'username' => 'demosocial',
			'password' => 'demosocial'
		)
	),
	'routes' => array(
		'index' => array('controller' => '\DemoSocial\SiteController', 'action' => 'index', 'guest' => false),
		'error404' => array('controller' => '\DemoSocial\SiteController', 'action' => 'error404'),
		'register' => array('controller' => '\DemoSocial\SiteController', 'action' => 'register', 'guest' => true),
		'login' => array('controller' => '\DemoSocial\SiteController', 'action' => 'login', 'guest' => true),
		'logout' => array('controller' => '\DemoSocial\SiteController', 'action' => 'logout', 'guest' => false),
		'profile' => array('controller' => '\DemoSocial\SiteController', 'action' => 'profile', 'guest' => false),
		
		'wall' => array('controller' => '\DemoSocial\WallController', 'action' => 'index', 'guest' => false),
		'ajaxPost' => array('controller' => '\DemoSocial\WallController', 'action' => 'ajaxPost', 'guest' => false),
		'ajaxLike' => array('controller' => '\DemoSocial\WallController', 'action' => 'ajaxLike', 'guest' => false),
		'ajaxDisLike' => array('controller' => '\DemoSocial\WallController', 'action' => 'ajaxDisLike', 'guest' => false),
		
		'friends' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'index', 'guest' => false),
		'search' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'search', 'guest' => false),
		'ajaxSearch' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'ajaxSearch', 'guest' => false),
		'ajaxOffers' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'ajaxOffers', 'guest' => false),
		'ajaxRequest' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'ajaxRequest', 'guest' => false),
		'ajaxConfirm' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'ajaxConfirm', 'guest' => false),
		'ajaxReject' => array('controller' => '\DemoSocial\FriendsController', 'action' => 'ajaxReject', 'guest' => false),
		
		'messages' => array('controller' => '\DemoSocial\CommentsController', 'action' => 'index', 'guest' => false),
		'ajaxComment' => array('controller' => '\DemoSocial\CommentsController', 'action' => 'ajaxComment', 'guest' => false),
		'ajaxGetComments' => array('controller' => '\DemoSocial\CommentsController', 'action' => 'ajaxGetComments', 'guest' => false),
	)
);
