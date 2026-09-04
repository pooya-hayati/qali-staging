<?php

namespace App\PostTypes;


class InitPostTypes
{

	public function register()
	{
		$this->setup();
	}

	public function get_classes()
	{
		return [
			BlogPost::class,
			PagePost::class,
		];
	}

	public function setup()
	{
		foreach ($this->get_classes() as $class) {
			$service = new $class;
			if (method_exists($class, 'register')) {
				$service->register();
			}
		}
	}
}
