<?php

namespace App\Controller;

class InitController
{

	public function register()
	{
		$this->setup();
	}

	public function get_classes()
	{
		return [
			WPCustom::class,
			WPContent::class,
			Validation::class,
			Profile::class,
			//RoleManager::class,
			MailSender::class,
			Contact::class,
			//Comment::class,
			Shop::class,
			DisableVirtual::class,
			Wishlist::class,
			CustomPay::class,
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
