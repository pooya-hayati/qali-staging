<?php

namespace App\Setup;

use Core\Settings;

class AppSettings extends Settings
{

	public function register()
	{
		parent::register();
	}

	public function general_settings()
	{
		$meta_boxes = [
			'id'             => 'general',
			'title'          => __('General', LANG_STRING),
			'settings_pages' => 'settings',
			'tab'            => 'general',
			'fields'         => [
				[
					'name'   => __('General', LANG_STRING),
					'id'     => 'general',
					'type'   => 'group',
					'clone'  => false,
					'fields' => [
						[
							'name' => __('Logo', LANG_STRING),
							'id'   => 'logo',
							'type' => 'single_image',
						],
						[
							'name' => __('Logomark', LANG_STRING),
							'id'   => 'logomark',
							'type' => 'single_image',
						],
						[
							'name' => __('Logo Footer', LANG_STRING),
							'id'   => 'footer_logo',
							'type' => 'single_image',
						],
						[
							'name'    => __('About', LANG_STRING),
							'id'      => 'about',
							'type'    => 'wysiwyg',
							'options' => [
								'textarea_rows' => 3,
								'media_buttons' => false,
								'teeny'         => true,
							],
						],
						[
							'name' => __('Copyright', LANG_STRING),
							'id'   => 'copyright',
							'type' => 'textarea',
							'rows' => 1,
						],
					]
				],
			],
		];

		return $meta_boxes;
	}

	public function contact_settings()
	{
		$meta_boxes = [
			'id'             => 'contact',
			'title'          => __('Contact Information', LANG_STRING),
			'settings_pages' => 'settings',
			'tab'            => 'contact',
			'fields'         => [
				[
					'name'   => __('Contact Information', LANG_STRING),
					'id'     => 'contact',
					'type'   => 'group',
					'clone'  => false,
					'fields' => [
						[
							'name' => __('Manager', LANG_STRING),
							'id'   => 'manager',
							'type' => 'email',
						],
						[
							'name' => __('Address', LANG_STRING),
							'id'   => 'address',
							'type' => 'textarea',
							'rows' => 2,
						],
						[
							'name' => __('Phone', LANG_STRING),
							'id'   => 'phone',
							'type' => 'text',
						],
						[
							'name' => __('Postal Code', LANG_STRING),
							'id'   => 'pcode',
							'type' => 'text',
						],
						[
							'name' => __('Fax', LANG_STRING),
							'id'   => 'fax',
							'type' => 'text',
						],
						[
							'name' => __('Email', LANG_STRING),
							'id'   => 'email',
							'type' => 'text',
						],
					]
				],
			],
		];

		return $meta_boxes;
	}

	public function social_settings()
	{
		$meta_boxes = [
			'id'             => 'social',
			'title'          => __('Social Networks', LANG_STRING),
			'settings_pages' => 'settings',
			'tab'            => 'social',
			'fields'         => [
				[
					'name'   => __('Social Networks', LANG_STRING),
					'id'     => 'social',
					'type'   => 'group',
					'clone'  => false,
					'fields' => [
						[
							'name' => __('Instagram', LANG_STRING),
							'id'   => 'instagram',
							'type' => 'url',
						],
						[
							'name' => __('Telegram', LANG_STRING),
							'id'   => 'telegram',
							'type' => 'url',
						],
						[
							'name' => __('Whatsapp', LANG_STRING),
							'id'   => 'whatsapp',
							'type' => 'url',
						],
						[
							'name' => __('Facebook', LANG_STRING),
							'id'   => 'facebook',
							'type' => 'url',
						],
						[
							'name' => __('X', LANG_STRING),
							'id'   => 'x',
							'type' => 'url',
						],
						[
							'name' => __('Pinterest', LANG_STRING),
							'id'   => 'pinterest',
							'type' => 'url',
						],
						[
							'name' => __('Linkedin', LANG_STRING),
							'id'   => 'linkedin',
							'type' => 'url',
						],
						[
							'name' => __('Youtube', LANG_STRING),
							'id'   => 'youtube',
							'type' => 'url',
						],
						[
							'name' => __('Reddit', LANG_STRING),
							'id'   => 'reddit',
							'type' => 'url',
						],
						[
							'name' => __('Tik Tok', LANG_STRING),
							'id'   => 'tiktok',
							'type' => 'url',
						],
					]
				],
			],
		];

		return $meta_boxes;
	}

	public function child_setting()
	{
		return [
			$this->general_settings(),
			$this->contact_settings(),
			$this->social_settings(),
		];
	}

	public function parent_setting()
	{
		return [
			'id'          => 'settings',
			'option_name' => 'settings',
			'menu_title'  => __('Theme Settings', LANG_STRING),
			'icon_url'    => 'dashicons-admin-settings',
			'style'       => 'no-boxes',
			'columns'     => 1,
			'tabs'        => [
				'general' => __('General', LANG_STRING),
				'contact' => __('Contact info', LANG_STRING),
				'social'  => __('Social Networks', LANG_STRING),
			],
			'position' => 68,
		];
	}
}
