<?php

namespace Core;

/**
 * Extras.
 */

class PostType
{

	public $post_name = '';
	public $tax_name = '';
	public $custom_column = false;
	public $hide_title = false;
	public $meta = [];
	public $post_names = [];

	public function register()
	{
		add_action('init', [$this, 'register_post_type']);
		add_action('init', [$this, 'register_tax']);

		add_filter('rwmb_meta_boxes', [$this, 'register_meta']);
		add_action('admin_menu', [$this, 'post_type_admin_menu']);

		add_filter('posts_results', [$this, 'posts_results']);

		add_action('after_switch_theme', [$this, 'rewrite_flush']);
		//add_action( 'save_post', [$this, 'save_book_meta'], 10, 3 );

		if ($this->custom_column) {
			add_action("manage_{$this->post_name}_posts_custom_column",  [$this, 'admin_column_content'], 10, 2);
			add_filter("manage_{$this->post_name}_posts_columns", [$this, 'admin_column'], 10);
			add_action(
				'admin_head-edit.php',
				[$this, 'edit_post_change_title_in_list']
			);
		}
	}

	function edit_post_change_title_in_list()
	{
		global $current_screen;
		if ($this->hide_title && $current_screen->post_type == $this->post_name) {
			add_filter(
				'the_title',
				[$this, 'construct_new_title'],
				100,
				2
			);
		}
	}

	function construct_new_title($title, $id)
	{
		return $id;
	}

	function save_book_meta($post_id, $post, $update)
	{

		delete_post_meta($post_id, 'post_author_override');
	}


	public function post_type_admin_menu() {}

	public function post_type_tax_args()
	{
		return [];
	}

	public function post_type_args()
	{
		return [];
	}

	public function post_type_meta_args()
	{
		return [];
	}

	public function all_tax()
	{
		return [];
	}


	public function register_post_type()
	{
		if ($this->post_name != '') {
			register_post_type($this->post_name, $this->post_type_args());
		}
	}

	public function register_tax()
	{
		//var_dump($this);
		if (count($this->all_tax()) > 0) {
			foreach ($this->all_tax() as $tax_key => $tax_array) {
				register_taxonomy($tax_key, $this->post_name, $tax_array);
			}
		} else {
			if ($this->tax_name != '') {
				register_taxonomy($this->tax_name, $this->post_name, $this->post_type_tax_args());
			}
		}
	}

	public function register_meta($meta_boxes)
	{

		$new_meta = array_merge($meta_boxes, $this->post_type_meta_args());

		return $new_meta;
	}

	function posts_results($posts)
	{
		foreach ($posts as $key => $_post) {
			$posts[$key]->meta = get_post_meta_all($_post->ID);
		}

		return $posts;
	}
	public function rewrite_flush()
	{
		// Flush the rewrite rules only on theme activation
		flush_rewrite_rules();
	}
}
