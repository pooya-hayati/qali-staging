<?php

/**
 *
 * This theme uses PSR-4 and OOP logic instead of procedural coding
 * Every function, hook and action is properly divided and organized inside related folders and files
 * Use the file `config/custom/custom.php` to write your custom functions
 *
 * @package awps
 */

namespace App;

final class Init
{

	public static function get_services()
	{
		return [
			Controller\InitController::class,
			PostTypes\InitPostTypes::class,
			Setup\AppSetup::class,
			Setup\AppEnqueue::class,
			Setup\AppMenu::class,
			Setup\AppSettings::class,
		];
	}

	public static function register_services()
	{
		foreach (self::get_services() as $class) {
			$service = self::instantiate($class);
			if (method_exists($service, 'register')) {
				$service->register();
			}
		}
	}


	private static function instantiate($class)
	{
		return new $class();
	}
}
