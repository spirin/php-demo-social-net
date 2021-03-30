<?php

define('SERVER_ROOT', __DIR__);

spl_autoload_register(static function(string $name) {
	require_once sprintf('%s/%s.php', SERVER_ROOT, str_replace('\\', '/', $name));
});

\Lib\Config::load(require SERVER_ROOT . '/config.php');
