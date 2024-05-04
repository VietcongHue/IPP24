<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

ini_set('display_errors', 'stderr');

require __DIR__ . '/vendor/autoload.php';

$engine = new IPP\Core\Engine;
$status = $engine->run();
exit($status);
