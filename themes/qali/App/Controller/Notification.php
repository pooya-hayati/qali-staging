<?php

namespace App\Controller;

use App\Controller\Profile;

class Notification
{

	private $db_table;
	private $log_table;
	private $queue_table;

	private $sms_username;
	private $sms_password;

	private $notice_manager;
	private $notice_status;

	public function __construct()
	{

		global $wpdb;
		$this->db_table = $wpdb->prefix . 'notifications';
		$this->log_table = $wpdb->prefix . 'notification_logs';
		$this->queue_table = $wpdb->prefix . 'notification_queue';

		// مقداردهی متغیرهای مرتبط با SMS Gateway
		$this->sms_username = '9128158821';
		$this->sms_password = defined('QALI_SMS_PASSWORD') ? QALI_SMS_PASSWORD : '';

		$this->notice_manager = [
			'email' => [
				'emohammad1992@gmail.com',
			],
			'mobile' => [
				'09191678798',
				'09381512022',
			],
		];

		$this->notice_status =  [
			// پیام‌های وضعیت سفارش
			'pending' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} is pending payment. Total: {price}. Customer: {b_first_name} {b_last_name}.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} pending payment. Total: {price}.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} is awaiting payment. Total: {price}.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} awaiting payment. Total: {price}.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'on-hold' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} is on hold. Please verify the payment.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} on hold. Verify payment.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} is on hold. Please complete your payment.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} on hold. Complete payment.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'processing' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} is now processing. Items: {all_items_qty}.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} processing. Items: {all_items_qty}.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} is now being processed. Items: {all_items_qty}.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} being processed. Items: {all_items_qty}.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'completed' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} has been completed. Total: {price}.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} completed. Total: {price}.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} has been completed. Thank you!',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} completed. Thank you!',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'cancelled' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} has been cancelled. Reason: {description}.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} cancelled. Reason: {description}.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} has been cancelled. Please contact us for details.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} cancelled. Contact us for details.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'refunded' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} has been refunded. Amount: {price}.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} refunded. Amount: {price}.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} has been refunded. Amount: {price}.',
					],
					'sms' => [
						'enabled' => true,
						//'message' => 'Order #{order_id} refunded. Amount: {price}.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'failed' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} has failed. Please check the payment gateway.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} failed. Check payment gateway.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} has failed. Please try again or contact us.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} failed. Try again or contact us.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],
			'draft' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Order #{order_id} is saved as a draft.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} saved as draft.',
						'message' => '@283085@{order_id};{status};{all_items_qty};{price}',
					],
				],
				'customer' => [
					'email' => [
						'enabled' => false,
						'message' => 'Your order #{order_id} is currently a draft. Please finalize your order.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Order #{order_id} is a draft. Finalize it.',
						'message' => '@283086@{b_first_name};{b_last_name};{order_id};{status};{all_items_qty};{price};{transaction_id}',
					],
				],
			],

			// پیام‌های انبارداری
			'low_stock' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Product "{product_title}" is running low on stock. Current stock: {stock}.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Low stock: {product_title}. Stock: {stock}.',
						'message' => '@283076@{product_title}',
					],
				],
			],
			'no_stock' => [
				'manager' => [
					'email' => [
						'enabled' => false,
						'message' => 'Product "{product_title}" is out of stock. SKU: {sku}.',
					],
					'sms' => [
						'enabled' => false,
						//'message' => 'Out of stock: {product_title}. SKU: {sku}.',
						'message' => '@283078@{product_title}',
					],
				],
			],
		];
		$this->register();
	}
	/**
	 * Register WordPress hooks.
	 */
	public function register()
	{
		add_action('init', [$this, 'create_database_tables']);

		//WP Automate Email
		add_filter('password_change_email', [$this, 'system_password_change_email'], 10, 3);

		add_action('admin_menu', [$this, 'add_admin_menu']);
		add_action('user_register', [$this, 'handle_user_registration']);
		add_action('send_scheduled_notifications', [$this, 'send_scheduled_notifications']);
		add_action('process_notification_queue', [$this, 'process_notification_queue']);
		add_action('woocommerce_order_status_changed', [$this, 'handle_order_status_change'], 10, 4);

		if (!wp_next_scheduled('send_scheduled_notifications')) {
			wp_schedule_event(time(), 'hourly', 'send_scheduled_notifications');
		}

		if (!wp_next_scheduled('process_notification_queue')) {
			wp_schedule_event(time(), 'minute', 'process_notification_queue');
		}
		// ثبت اکشن AJAX برای جستجوی کاربران
		add_action('wp_ajax_custom_user_search', [$this, 'custom_user_search_ajax']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

		// Notices
		add_action('woocommerce_order_status_changed', [$this, 'send_status_change_notifications'], 10, 4);
		add_action('woocommerce_low_stock', [$this, 'notify_manager_on_low_stock']);
		add_action('woocommerce_no_stock', [$this, 'notify_manager_on_no_stock']);
	}
	/**
	 * Create the database tables for notifications, logs, and queue.
	 */
	public function create_database_tables()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Notifications table
		$sql1 = "CREATE TABLE IF NOT EXISTS {$this->db_table} (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) DEFAULT NULL,
        type VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('pending', 'sent', 'failed', 'scheduled', 'delayed', 'abandoned') DEFAULT 'pending',
        priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
        schedule_at TIMESTAMP NULL DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        sent_at TIMESTAMP NULL DEFAULT NULL,
        attachments LONGTEXT DEFAULT NULL,
        data LONGTEXT DEFAULT NULL,
        KEY user_id_index (user_id),
        KEY status_index (status),
        KEY schedule_at_index (schedule_at)
    ) $charset_collate;";

		// Logs table
		$sql2 = "CREATE TABLE IF NOT EXISTS {$this->log_table} (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        notification_id BIGINT(20) NOT NULL,
        log_message TEXT NOT NULL,
        log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY notification_id_index (notification_id)
    ) $charset_collate;";

		// Queue table
		$sql3 = "CREATE TABLE IF NOT EXISTS {$this->queue_table} (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        notification_id BIGINT(20) NOT NULL,
        user_id BIGINT(20) DEFAULT NULL,
        type VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
        status ENUM('pending', 'processed', 'failed', 'abandoned', 'scheduled', 'delayed') DEFAULT 'pending',
        schedule_at TIMESTAMP NULL DEFAULT NULL,
        retry_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        processed_at TIMESTAMP NULL DEFAULT NULL,
        attachments LONGTEXT DEFAULT NULL,
        data LONGTEXT DEFAULT NULL,
        KEY notification_id_index (notification_id),
        KEY status_index (status),
        KEY schedule_at_index (schedule_at)
    ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql1);
		dbDelta($sql2);
		dbDelta($sql3);

		// Adding foreign keys manually (not supported by dbDelta)
		$wpdb->query("ALTER TABLE {$this->log_table} 
                  ADD CONSTRAINT fk_notification_logs 
                  FOREIGN KEY (notification_id) REFERENCES {$this->db_table}(id) 
                  ON DELETE CASCADE");

		$wpdb->query("ALTER TABLE {$this->queue_table} 
                  ADD CONSTRAINT fk_notification_queue 
                  FOREIGN KEY (notification_id) REFERENCES {$this->db_table}(id) 
                  ON DELETE CASCADE");
	}

	/**
	 * Add admin menu for notification management.
	 */
	public function add_admin_menu()
	{
		add_menu_page(
			__('Notification System', LANG_STRING),
			__('Notifications', LANG_STRING),
			'manage_options',
			'notification-system',
			[$this, 'render_admin_page'],
			'dashicons-megaphone'
		);

		$submenus = [
			[__('Send Notification', LANG_STRING), 'notification-send', [$this, 'render_send_notification_page']],
			[__('Queue', LANG_STRING), 'notification-queue', [$this, 'render_queue_page']],
			[__('Logs', LANG_STRING), 'notification-logs', [$this, 'render_logs_page']],
			[__('Analytics', LANG_STRING), 'notification-analytics', [$this, 'render_analytics_page']],
			[__('Settings', LANG_STRING), 'notification-settings', [$this, 'render_settings_page']],
			[__('Message Templates', LANG_STRING), 'notification-templates', [$this, 'render_templates_page']],
			[__('Web Notifications', LANG_STRING), 'notification-web', [$this, 'render_web_notifications_page']],
		];

		foreach ($submenus as $submenu) {
			add_submenu_page(
				'notification-system',
				$submenu[0], // عنوان صفحه
				$submenu[0], // عنوان منو
				'manage_options',
				$submenu[1], // slug
				$submenu[2]  // callback
			);
		}
	}
	/**
	 * Render admin page for notifications.
	 */
	public function render_admin_page()
	{
		global $wpdb;

		// تعداد آیتم‌ها در هر صفحه
		$items_per_page = 10;

		// دریافت صفحه فعلی از URL
		$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

		// محاسبه آفست
		$offset = ($current_page - 1) * $items_per_page;

		// دریافت تعداد کل نوتیفیکیشن‌ها
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$this->db_table}");

		// دریافت نوتیفیکیشن‌ها برای صفحه جاری
		$query = $wpdb->prepare(
			"SELECT * FROM {$this->db_table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
			$items_per_page,
			$offset
		);
		$notifications = $wpdb->get_results($query);

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Notifications', LANG_STRING) . '</h1>';

		if (empty($notifications)) {
			echo '<p>' . esc_html__('No notifications found.', LANG_STRING) . '</p>';
		} else {
			echo '<table class="widefat fixed striped">';
			echo '<thead><tr>';
			echo '<th>' . esc_html__('ID', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('User', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Type', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Priority', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Message', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Status', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Created At', LANG_STRING) . '</th>';
			echo '</tr></thead><tbody>';

			foreach ($notifications as $notification) {
				$user = get_userdata($notification->user_id);
				echo '<tr>';
				echo '<td>' . esc_html($notification->id) . '</td>';
				echo '<td>' . esc_html($user ? $user->user_login : __('Unknown', LANG_STRING)) . '</td>';
				echo '<td>' . esc_html($notification->type) . '</td>';
				echo '<td>' . esc_html(ucfirst($notification->priority)) . '</td>';
				echo '<td>' . esc_html($notification->message) . '</td>';
				echo '<td>' . esc_html(ucfirst($notification->status)) . '</td>';
				echo '<td>' . esc_html($notification->created_at) . '</td>';
				echo '</tr>';
			}

			echo '</tbody></table>';

			// نمایش لینک‌های صفحه‌بندی
			$pagination_args = [
				'base'    => add_query_arg('paged', '%#%'),
				'format'  => '',
				'current' => $current_page,
				'total'   => ceil($total_items / $items_per_page),
			];

			echo '<div class="tablenav"><div class="tablenav-pages">';
			echo paginate_links($pagination_args);
			echo '</div></div>';
		}

		echo '</div>';
	}
	/**
	 * Render send notification page.
	 */
	public function render_send_notification_page()
	{
		// بررسی ارسال فرم
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'send_notification')) {
			$recipient = sanitize_text_field($_POST['recipient']);
			$message_content = wp_kses_post($_POST['message_content']);
			$schedule_at = !empty($_POST['schedule_at']) ? sanitize_text_field($_POST['schedule_at']) : null;
			$types = !empty($_POST['types']) ? array_map('sanitize_text_field', $_POST['types']) : [];
			$priority = sanitize_text_field($_POST['priority']);
			$specific_user = !empty($_POST['specific_user']) ? intval($_POST['specific_user']) : null;
			$group_name = !empty($_POST['group_name']) ? sanitize_text_field($_POST['group_name']) : null;

			if (!empty($types) && !empty($message_content)) {
				// تعیین زمان‌بندی
				$schedule_datetime = $schedule_at ? date('Y-m-d H:i:s', strtotime($schedule_at)) : null;

				// ارسال نوتیفیکیشن برای هر نوع انتخاب‌شده
				foreach ($types as $type) {
					$result = $this->enqueue_notification(
						$specific_user ? $specific_user : $recipient,
						$message_content,
						$type,
						$priority,
						$schedule_datetime
					);

					if (is_wp_error($result)) {
						echo '<div class="notice notice-error"><p>' . esc_html__('Failed to schedule notification: ', LANG_STRING) . esc_html($result->get_error_message()) . '</p></div>';
						break;
					}
				}

				echo '<div class="notice notice-success"><p>' . esc_html__('Notification(s) scheduled successfully.', LANG_STRING) . '</p></div>';
			} else {
				echo '<div class="notice notice-error"><p>' . esc_html__('Please fill in all required fields.', LANG_STRING) . '</p></div>';
			}
		}

		// فرم نمایش
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Send Notification', LANG_STRING) . '</h1>';
		echo '<p>' . esc_html__('Send notifications to specific users, user roles, or groups.', LANG_STRING) . '</p>';
		echo '<form method="post" id="notification-form">';
		echo wp_nonce_field('send_notification', '_wpnonce', true, false); // توکن CSRF

		// گیرنده
		echo '<label>' . esc_html__('Recipient:', LANG_STRING) . '</label><br>';
		echo '<select name="recipient" id="recipient" style="width: 100%;">';
		echo '<option value="all">' . esc_html__('All Users', LANG_STRING) . '</option>';
		foreach (wp_roles()->roles as $role_key => $role) {
			echo '<option value="role_' . esc_attr($role_key) . '">' . esc_html__('Role: ', LANG_STRING) . esc_html($role['name']) . '</option>';
		}
		echo '<option value="group">' . esc_html__('Group', LANG_STRING) . '</option>';
		echo '<option value="specific_user">' . esc_html__('Specific User', LANG_STRING) . '</option>';
		echo '</select><br><br>';

		// انتخاب کاربر خاص (پنهان به‌صورت پیش‌فرض)
		echo '<div id="specific-user-wrapper" style="display: none;">';
		echo '<label>' . esc_html__('Specific User:', LANG_STRING) . '</label><br>';
		echo '<select name="specific_user" id="specific-user" style="width: 100%;" data-placeholder="' . esc_attr__('Select a user...', LANG_STRING) . '" class="ajax-user-search"></select><br><br>';
		echo '</div>';

		// گروه (پنهان به‌صورت پیش‌فرض)
		echo '<div id="group-wrapper" style="display: none;">';
		echo '<label>' . esc_html__('Target Group:', LANG_STRING) . '</label><br>';
		echo '<input type="text" name="group_name" style="width: 100%;" value="' . esc_attr($_POST['group_name'] ?? '') . '"><br><br>';
		echo '</div>';

		// محتوای پیام
		echo '<label>' . esc_html__('Message Content:', LANG_STRING) . '</label><br>';
		echo '<textarea name="message_content" rows="10" style="width: 100%;">' . esc_textarea($_POST['message_content'] ?? '') . '</textarea><br><br>';

		// زمان‌بندی
		echo '<label>' . esc_html__('Schedule At (optional):', LANG_STRING) . '</label><br>';
		echo '<input type="datetime-local" name="schedule_at" style="width: 100%;" value="' . esc_attr($_POST['schedule_at'] ?? '') . '"><br><br>';

		// نوع پیام
		echo '<label>' . esc_html__('Types of Notification:', LANG_STRING) . '</label><br>';
		echo '<input type="checkbox" name="types[]" value="email"> ' . esc_html__('Email', LANG_STRING) . '<br>';
		echo '<input type="checkbox" name="types[]" value="sms"> ' . esc_html__('SMS', LANG_STRING) . '<br>';
		echo '<input type="checkbox" name="types[]" value="telegram"> ' . esc_html__('Telegram', LANG_STRING) . '<br>';
		echo '<input type="checkbox" name="types[]" value="whatsapp"> ' . esc_html__('WhatsApp', LANG_STRING) . '<br><br>';

		// اولویت
		echo '<label>' . esc_html__('Priority:', LANG_STRING) . '</label><br>';
		echo '<select name="priority" style="width: 100%;">';
		echo '<option value="low">' . esc_html__('Low', LANG_STRING) . '</option>';
		echo '<option value="normal" selected>' . esc_html__('Normal', LANG_STRING) . '</option>';
		echo '<option value="high">' . esc_html__('High', LANG_STRING) . '</option>';
		echo '</select><br><br>';

		// دکمه ارسال
		echo '<button type="submit" class="button button-primary">' . esc_html__('Send Notification', LANG_STRING) . '</button>';
		echo '</form>';
		echo '</div>';
	}
	// متد جستجوی آژاکسی کاربران
	public function custom_user_search_ajax()
	{
		check_ajax_referer('custom_user_search', 'security');

		$term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';

		if (empty($term)) {
			wp_send_json([]);
		}

		$args = [
			'search' => '*' . esc_attr($term) . '*',
			'search_columns' => ['user_login', 'user_email', 'display_name'],
			'number' => 10,
		];

		$user_query = new \WP_User_Query($args);

		$users = [];
		foreach ($user_query->get_results() as $user) {
			$users[] = [
				'id' => $user->ID,
				'text' => sprintf('%s (%s)', $user->display_name, $user->user_email),
			];
		}

		wp_send_json($users);
	}

	// افزودن اسکریپت‌های جاوااسکریپت و CSS
	public function enqueue_scripts()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);
		wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');

		// افزودن اسکریپت اینلاین برای مدیریت جستجوی آژاکسی
		wp_add_inline_script(
			'select2',
			'jQuery(document).ready(function ($) {
			// مدیریت نمایش و مخفی کردن فیلدها
			$("#recipient").on("change", function () {
				const value = $(this).val();
				$("#specific-user-wrapper").toggle(value === "specific_user");
				$("#group-wrapper").toggle(value === "group");
			});
			$(".ajax-user-search").select2({
				ajax: {
					url: "' . esc_url(admin_url('admin-ajax.php')) . '",
					dataType: "json",
					delay: 250,
					data: function (params) {
						return {
							term: params.term,
							action: "custom_user_search",
							security: "' . wp_create_nonce('custom_user_search') . '"
						};
					},
					processResults: function (data) {
						return {
							results: data
						};
					},
					cache: true
				},
				minimumInputLength: 3
			});
		});'
		);
	}
	/**
	 * Render queue management page.
	 */
	public function render_queue_page()
	{
		global $wpdb;

		// تعداد آیتم‌ها در هر صفحه
		$items_per_page = 10;

		// دریافت صفحه فعلی از URL
		$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

		// محاسبه آفست
		$offset = ($current_page - 1) * $items_per_page;

		// دریافت تعداد کل آیتم‌های صف
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$this->queue_table}");

		// دریافت آیتم‌های صف برای صفحه جاری
		$query = $wpdb->prepare(
			"SELECT * FROM {$this->queue_table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
			$items_per_page,
			$offset
		);
		$queue_items = $wpdb->get_results($query);

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Notification Queue', LANG_STRING) . '</h1>';

		if (empty($queue_items)) {
			echo '<p>' . esc_html__('No items found in the queue.', LANG_STRING) . '</p>';
		} else {
			echo '<table class="widefat fixed striped">';
			echo '<thead><tr>';
			echo '<th>' . esc_html__('ID', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('User ID', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Type', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Message', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Priority', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Status', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Scheduled At', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Created At', LANG_STRING) . '</th>';
			echo '</tr></thead><tbody>';

			foreach ($queue_items as $item) {
				echo '<tr>';
				echo '<td>' . esc_html($item->id) . '</td>';
				echo '<td>' . esc_html($item->user_id) . '</td>';
				echo '<td>' . esc_html($item->type) . '</td>';
				echo '<td>' . esc_html($item->message) . '</td>';
				echo '<td>' . esc_html(ucfirst($item->priority)) . '</td>';
				echo '<td>' . esc_html(ucfirst($item->status)) . '</td>';
				echo '<td>' . esc_html($item->schedule_at ? $item->schedule_at : __('Not Scheduled', LANG_STRING)) . '</td>';
				echo '<td>' . esc_html($item->created_at) . '</td>';
				echo '</tr>';
			}

			echo '</tbody></table>';

			// نمایش لینک‌های صفحه‌بندی
			$pagination_args = [
				'base'    => add_query_arg('paged', '%#%'),
				'format'  => '',
				'current' => $current_page,
				'total'   => ceil($total_items / $items_per_page),
			];

			echo '<div class="tablenav"><div class="tablenav-pages">';
			echo paginate_links($pagination_args);
			echo '</div></div>';
		}

		echo '</div>';
	}
	/**
	 * Render logs page.
	 */
	public function render_logs_page()
	{
		global $wpdb;

		// تعداد آیتم‌ها در هر صفحه
		$items_per_page = 10;

		// دریافت صفحه فعلی از URL
		$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

		// محاسبه آفست
		$offset = ($current_page - 1) * $items_per_page;

		// دریافت تعداد کل لاگ‌ها
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$this->log_table}");

		// دریافت لاگ‌ها برای صفحه جاری
		$query = $wpdb->prepare(
			"SELECT * FROM {$this->log_table} ORDER BY log_time DESC LIMIT %d OFFSET %d",
			$items_per_page,
			$offset
		);
		$logs = $wpdb->get_results($query);

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Notification Logs', LANG_STRING) . '</h1>';

		if (empty($logs)) {
			echo '<p>' . esc_html__('No logs found.', LANG_STRING) . '</p>';
		} else {
			echo '<table class="widefat fixed striped">';
			echo '<thead><tr>';
			echo '<th>' . esc_html__('Log ID', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Notification ID', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Message', LANG_STRING) . '</th>';
			echo '<th>' . esc_html__('Time', LANG_STRING) . '</th>';
			echo '</tr></thead><tbody>';

			foreach ($logs as $log) {
				echo '<tr>';
				echo '<td>' . esc_html($log->id) . '</td>';
				echo '<td>' . esc_html($log->notification_id) . '</td>';
				echo '<td>' . esc_html($log->log_message) . '</td>';
				echo '<td>' . esc_html($log->log_time) . '</td>';
				echo '</tr>';
			}

			echo '</tbody></table>';

			// نمایش لینک‌های صفحه‌بندی
			$pagination_args = [
				'base'    => add_query_arg('paged', '%#%'),
				'format'  => '',
				'current' => $current_page,
				'total'   => ceil($total_items / $items_per_page),
			];

			echo '<div class="tablenav"><div class="tablenav-pages">';
			echo paginate_links($pagination_args);
			echo '</div></div>';
		}

		echo '</div>';
	}
	/**
	 * Render analytics page.
	 */
	public function render_analytics_page()
	{
		global $wpdb;

		// استفاده از کوئری‌های ایمن
		$total_notifications = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->db_table}"));
		$sent_notifications = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->db_table} WHERE status = %s", 'sent'));
		$pending_notifications = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->db_table} WHERE status = %s", 'pending'));
		$failed_notifications = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->db_table} WHERE status = %s", 'failed'));
		$abandoned_notifications = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->queue_table} WHERE status = %s", 'abandoned'));
		$delayed_notifications = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->queue_table} WHERE status = %s", 'delayed'));

		// دسته‌بندی پیام‌ها بر اساس type
		$types = $wpdb->get_results("
			SELECT type, COUNT(*) AS count
			FROM {$this->db_table}
			GROUP BY type
		");

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Notification Analytics', LANG_STRING) . '</h1>';

		echo '<table class="widefat fixed striped">';
		echo '<thead><tr>';
		echo '<th>' . esc_html__('Metric', LANG_STRING) . '</th>';
		echo '<th>' . esc_html__('Count', LANG_STRING) . '</th>';
		echo '</tr></thead><tbody>';

		echo '<tr>';
		echo '<td>' . esc_html__('Total Notifications', LANG_STRING) . '</td>';
		echo '<td>' . esc_html($total_notifications) . '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>' . esc_html__('Sent Notifications', LANG_STRING) . '</td>';
		echo '<td>' . esc_html($sent_notifications) . '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>' . esc_html__('Pending Notifications', LANG_STRING) . '</td>';
		echo '<td>' . esc_html($pending_notifications) . '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>' . esc_html__('Failed Notifications', LANG_STRING) . '</td>';
		echo '<td>' . esc_html($failed_notifications) . '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>' . esc_html__('Abandoned Notifications', LANG_STRING) . '</td>';
		echo '<td>' . esc_html($abandoned_notifications) . '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>' . esc_html__('Delayed Notifications', LANG_STRING) . '</td>';
		echo '<td>' . esc_html($delayed_notifications) . '</td>';
		echo '</tr>';

		foreach ($types as $type) {
			echo '<tr>';
			echo '<td>' . esc_html(ucfirst($type->type)) . ' ' . esc_html__('Notifications', LANG_STRING) . '</td>';
			echo '<td>' . esc_html($type->count) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';

		// افزودن نمودار
		echo '<div id="analytics-chart" style="width: 100%; height: 400px;"></div>';
		echo '<script type="text/javascript">
			document.addEventListener("DOMContentLoaded", function() {
				const data = [
					{ label: "Sent", count: ' . esc_js($sent_notifications) . ' },
					{ label: "Pending", count: ' . esc_js($pending_notifications) . ' },
					{ label: "Failed", count: ' . esc_js($failed_notifications) . ' },
					{ label: "Abandoned", count: ' . esc_js($abandoned_notifications) . ' },
					{ label: "Delayed", count: ' . esc_js($delayed_notifications) . ' },
				];
	
				const types = ' . json_encode(array_map(function ($type) {
			return ['label' => ucfirst($type->type), 'count' => $type->count];
		}, $types)) . ';
	
				const allData = [...data, ...types];
				const chartContainer = document.getElementById("analytics-chart");
				const chartData = allData.map(item => `<div style="width: ${item.count / ' . esc_js($total_notifications) . ' * 100}%; background-color: #0073aa; color: #fff; padding: 10px; margin: 5px 0;">${item.label}: ${item.count}</div>`).join("");
	
				chartContainer.innerHTML = chartData;
			});
		</script>';
		echo '</div>';
	}
	/**
	 * Render settings page.
	 */
	public function render_settings_page()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'save_notification_settings')) {
			update_option('notification_email_enabled', isset($_POST['email_enabled']));
			update_option('notification_sms_enabled', isset($_POST['sms_enabled']));
			update_option('notification_email_template', sanitize_textarea_field($_POST['email_template']));
			update_option('notification_sms_template', sanitize_textarea_field($_POST['sms_template']));

			// پیام موفقیت
			echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully.', LANG_STRING) . '</p></div>';
		}

		$email_enabled = get_option('notification_email_enabled', true);
		$sms_enabled = get_option('notification_sms_enabled', true);
		$email_template = get_option('notification_email_template', __('Hello {user_name}, you have a new notification: {message}', LANG_STRING));
		$sms_template = get_option('notification_sms_template', __('You have a new notification: {message}', LANG_STRING));

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Notification Settings', LANG_STRING) . '</h1>';
		echo '<form method="post">';
		echo wp_nonce_field('save_notification_settings', '_wpnonce', true, false); // اضافه کردن CSRF Token
		echo '<label><input type="checkbox" name="email_enabled" ' . checked($email_enabled, true, false) . '> ' . esc_html__('Enable Email Notifications', LANG_STRING) . '</label><br>';
		echo '<label><input type="checkbox" name="sms_enabled" ' . checked($sms_enabled, true, false) . '> ' . esc_html__('Enable SMS Notifications', LANG_STRING) . '</label><br>';
		echo '<label>' . esc_html__('Email Template', LANG_STRING) . '</label><br>';
		echo '<textarea name="email_template" rows="5" style="width: 100%;">' . esc_textarea($email_template) . '</textarea><br>';
		echo '<label>' . esc_html__('SMS Template', LANG_STRING) . '</label><br>';
		echo '<textarea name="sms_template" rows="3" style="width: 100%;">' . esc_textarea($sms_template) . '</textarea><br>';
		echo '<button type="submit" class="button button-primary" name="save_settings">' . esc_html__('Save Settings', LANG_STRING) . '</button>';
		echo '</form>';
		echo '</div>';
	}
	/**
	 * Render web notifications management page.
	 */
	public function render_web_notifications_page()
	{
		// بررسی ارسال فرم
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'save_web_notifications_settings')) {
			$enable_notifications = isset($_POST['enable_notifications']) ? 'yes' : 'no';
			$notification_title = sanitize_text_field($_POST['notification_title']);
			$notification_body = sanitize_textarea_field($_POST['notification_body']);

			// ذخیره تنظیمات
			update_option('web_notifications_enabled', $enable_notifications);
			update_option('web_notification_title', $notification_title);
			update_option('web_notification_body', $notification_body);

			echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully.', LANG_STRING) . '</p></div>';
		}

		// دریافت مقادیر ذخیره‌شده
		$enable_notifications = get_option('web_notifications_enabled', 'no');
		$notification_title = get_option('web_notification_title', '');
		$notification_body = get_option('web_notification_body', '');

		// فرم نمایش
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Web Notifications', LANG_STRING) . '</h1>';
		echo '<p>' . esc_html__('Manage and configure web notifications here.', LANG_STRING) . '</p>';
		echo '<form method="post">';
		echo wp_nonce_field('save_web_notifications_settings', '_wpnonce', true, false);
		echo '<label>';
		echo '<input type="checkbox" name="enable_notifications" ' . checked($enable_notifications, 'yes', false) . '> ';
		echo esc_html__('Enable Web Notifications', LANG_STRING);
		echo '</label><br><br>';
		echo '<label>' . esc_html__('Notification Title:', LANG_STRING) . '</label><br>';
		echo '<input type="text" name="notification_title" style="width: 100%;" value="' . esc_attr($notification_title) . '"><br><br>';
		echo '<label>' . esc_html__('Notification Body:', LANG_STRING) . '</label><br>';
		echo '<textarea name="notification_body" rows="5" style="width: 100%;">' . esc_textarea($notification_body) . '</textarea><br><br>';
		echo '<button type="submit" class="button button-primary">' . esc_html__('Save Settings', LANG_STRING) . '</button>';
		echo '</form>';
		echo '</div>';
	}
	/**
	 * Enqueue a notification.
	 */
	public function enqueue_notification($user_id, $message, $type, $priority = 'normal', $schedule_at = null, $attachments = [])
	{
		global $wpdb;

		// اعتبارسنجی ورودی‌ها
		if (empty($message) || empty($type)) {
			return new \WP_Error('invalid_input', __('Message and type are required.', LANG_STRING));
		}

		if (!empty($schedule_at) && !strtotime($schedule_at)) {
			return new \WP_Error('invalid_schedule', __('Invalid schedule date.', LANG_STRING));
		}

		if (!empty($user_id) && !is_numeric($user_id)) {
			return new \WP_Error('invalid_input', __('Invalid user ID.', LANG_STRING));
		}

		// لیست اولویت‌های معتبر
		$valid_priorities = ['low', 'normal', 'high'];
		if (!in_array($priority, $valid_priorities, true)) {
			$priority = 'normal';
		}

		// سریالایز کردن فایل‌های پیوست
		$serialized_attachments = !empty($attachments) ? maybe_serialize(array_map('sanitize_text_field', $attachments)) : null;

		// درج پیام در جدول queue
		$result = $wpdb->insert(
			$this->queue_table,
			[
				'user_id'    => $user_id ? intval($user_id) : null,
				'message'    => wp_kses_post($message),
				'type'       => sanitize_text_field($type),
				'priority'   => $priority,
				'status'     => 'pending',
				'created_at' => current_time('mysql'),
				'schedule_at' => $schedule_at ? sanitize_text_field($schedule_at) : null,
				'attachments' => $serialized_attachments,
			],
			['%d', '%s', '%s', '%s', '%s', '%s', '%s']
		);

		if (false === $result) {
			return new \WP_Error('db_error', __('Failed to enqueue notification.', LANG_STRING));
		}

		return $wpdb->insert_id;
	}

	public function add_notification_to_database($user_id, $type, $message, $priority = 'normal', $schedule_at = null, $attachments = [])
	{
		global $wpdb;

		// اعتبارسنجی ورودی‌ها
		if (!is_numeric($user_id) || empty($message) || empty($type)) {
			return new \WP_Error('invalid_input', __('Invalid input parameters.', LANG_STRING));
		}

		// لیست اولویت‌های معتبر
		$valid_priorities = ['low', 'normal', 'high'];
		if (!in_array($priority, $valid_priorities, true)) {
			$priority = 'normal';
		}

		// درج پیام در جدول notifications
		$result = $wpdb->insert(
			$this->db_table,
			[
				'user_id'    => intval($user_id),
				'type'       => sanitize_text_field($type),
				'message'    => sanitize_textarea_field($message),
				'priority'   => $priority,
				'status'     => 'pending',
				'schedule_at' => $schedule_at ? sanitize_text_field($schedule_at) : null,
				'created_at' => current_time('mysql'),
				'attachments' => !empty($attachments) ? maybe_serialize($attachments) : null,
			],
			['%d', '%s', '%s', '%s', '%s', '%s', '%s']
		);

		if (false === $result) {
			return new \WP_Error('db_error', __('Failed to add notification to database.', LANG_STRING));
		}

		return $wpdb->insert_id; // بازگشت ID درج شده
	}

	public function send_scheduled_notifications()
	{
		global $wpdb;

		// تعداد پیام‌ها برای پردازش در هر دسته
		$batch_size = 10;

		do {
			// انتخاب پیام‌های زمان‌بندی‌شده که باید ارسال شوند
			$scheduled_notifications = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$this->queue_table} 
                 WHERE status = %s AND schedule_at <= NOW()
                 LIMIT %d",
					'pending',
					$batch_size
				)
			);

			if (empty($scheduled_notifications)) {
				break;
			}

			foreach ($scheduled_notifications as $notification) {
				// فراخوانی متد ارسال
				$this->process_notification_queue($notification);
			}
		} while (count($scheduled_notifications) === $batch_size); // ادامه تا زمانی که پیام‌ها وجود دارند
	}
	/**
	 * Process the notification queue.
	 */
	public function process_notification_queue($notification)
	{
		global $wpdb;

		// تعداد تلاش‌های مجاز
		$max_retries = 3;

		// مقداردهی پیش‌فرض برای retry_count
		$retry_count = isset($notification->retry_count) ? intval($notification->retry_count) : 0;

		// بازیابی فایل‌های پیوست
		$attachments = !empty($notification->attachments) ? maybe_unserialize($notification->attachments) : [];

		$processed = false;

		// ارسال به کاربر خاص
		if (!empty($notification->user_id)) {
			$processed = $this->send_to_user($notification->user_id, $notification->type, $notification->message, $attachments);
		}
		// ارسال به نقش خاص
		elseif (!empty($notification->target_role)) {
			$processed = $this->send_to_role($notification->target_role, $notification->type, $notification->message, $attachments);
		}
		// ارسال به گروه خاص
		elseif (!empty($notification->target_group)) {
			$processed = $this->send_to_group($notification->target_group, $notification->type, $notification->message, $attachments);
		}

		// به‌روزرسانی وضعیت و تعداد تلاش‌ها
		if ($processed) {
			$wpdb->update(
				$this->queue_table,
				[
					'status'       => 'processed',
					'processed_at' => current_time('mysql'),
				],
				['id' => $notification->id],
				['%s', '%s'],
				['%d']
			);
		} else {
			$retry_count++;
			$status = ($retry_count >= $max_retries) ? 'abandoned' : 'failed';

			$wpdb->update(
				$this->queue_table,
				[
					'status'       => $status,
					'retry_count'  => $retry_count,
					'last_retry_at' => current_time('mysql'),
				],
				['id' => $notification->id],
				['%s', '%d', '%s'],
				['%d']
			);

			// ذخیره گزارش برای پیام‌های abandoned
			if ($status === 'abandoned') {
				$wpdb->insert(
					$this->log_table,
					[
						'notification_id' => $notification->id,
						'log_message'     => sprintf(
							__('Notification abandoned after %d retries. Message: %s', LANG_STRING),
							$max_retries,
							$notification->message
						),
						'log_time'        => current_time('mysql'),
					],
					['%d', '%s', '%s']
				);
			}
		}
	}

	private function send_to_user($user_id, $type, $message, $attachments)
	{
		if ($type === 'email') {
			$user = get_userdata($user_id);
			if ($user && is_email($user->user_email)) {
				$result = $this->send_email_notification($user->user_email, $message, null, $attachments);
				if (!$result) {
					error_log("Failed to send email to user ID: {$user_id}");
				}
				return $result;
			}
		} elseif ($type === 'sms') {
			$phone_number = get_user_meta($user_id, 'phone_number', true);
			if (!empty($phone_number)) {
				$result = $this->send_sms_notification($phone_number, $message);
				if (!$result) {
					error_log("Failed to send SMS to user ID: {$user_id}");
				}
				return $result;
			}
		} elseif ($type === 'telegram') {
			$telegram_id = get_user_meta($user_id, 'telegram_id', true);
			if (!empty($telegram_id)) {
				$result = $this->send_telegram_notification($telegram_id, $message);
				if (!$result) {
					error_log("Failed to send Telegram message to user ID: {$user_id}");
				}
				return $result;
			}
		} elseif ($type === 'whatsapp') {
			$phone_number = get_user_meta($user_id, 'phone_number', true);
			if (!empty($phone_number)) {
				$result = $this->send_whatsapp_notification($phone_number, $message);
				if (!$result) {
					error_log("Failed to send WhatsApp message to user ID: {$user_id}");
				}
				return $result;
			}
		}
		return false;
	}

	private function send_to_group($group, $type, $message, $attachments)
	{
		$batch_size = 50; // تعداد کاربران در هر دسته
		$offset = 0;

		do {
			// دریافت کاربران با توجه به گروه
			$users = get_users([
				'meta_key'   => 'group',
				'meta_value' => sanitize_text_field($group),
				'number'     => $batch_size,
				'offset'     => $offset,
			]);

			if (empty($users)) {
				// اگر هیچ کاربری پیدا نشد، پایان دهید
				if ($offset === 0) {
					error_log("No users found for group: {$group}");
					return false;
				}
				break;
			}

			foreach ($users as $user) {
				// ارسال پیام به هر کاربر
				$result = $this->send_to_user($user->ID, $type, $message, $attachments);

				// لاگ‌گیری در صورت شکست ارسال
				if (!$result) {
					error_log("Failed to send notification to user ID: {$user->ID} in group: {$group}");
				}
			}

			// افزایش آفست برای دریافت دسته بعدی کاربران
			$offset += $batch_size;
		} while (count($users) === $batch_size);

		return true;
	}

	/**
	 * Generates the email header template.
	 */
	public static function mail_template_header($user, $email, $auto = true)
	{
		// دریافت نام کامل کاربر
		$user_full_name = isset($user) ? esc_html(Profile::get_user_full_name($user)) : esc_html__('User', LANG_STRING);

		// ایمن‌سازی ایمیل
		$email = sanitize_email($email);

		// تولید هدر ایمیل
		$header = [];
		$header[] = '<strong>' . sprintf(esc_html__('Dear %s,', LANG_STRING), $user_full_name) . '</strong>';

		if ($auto) {
			$header[] = '<p>' . sprintf(
				esc_html__('This email has been automatically sent to %s. Do NOT respond, it is not monitored.', LANG_STRING),
				esc_html($email)
			) . '</p>';
		}

		$header[] = '<br>';

		// بازگشت پیام
		return implode('', $header);
	}

	/**
	 * Generates the email footer template.
	 */
	public static function mail_template_footer()
	{
		$footer = [];
		$footer[] = '<br>';
		$footer[] = '<strong>' . esc_html__('Best regards,', LANG_STRING) . '</strong>';
		$footer[] = '<br>';
		$footer[] = '<strong>' . esc_html__('Support Team', LANG_STRING) . '</strong>';
		$footer[] = '<strong>' . esc_html(get_bloginfo('name')) . '</strong>';

		// بازگشت پیام
		return implode('<br>', $footer);
	}

	/**
	 * Send email notification.
	 */
	public function send_email_notification($to, $message, $subject = null, $attachments = [])
	{
		// بررسی معتبر بودن ایمیل
		if (!is_email($to)) {
			error_log(__('Invalid email address provided for notification.', LANG_STRING));
			return false;
		}

		// تنظیم موضوع پیش‌فرض در صورت عدم ارائه
		if (empty($subject)) {
			$subject = __('New Notification', LANG_STRING);
		}

		// ایجاد قالب HTML برای ایمیل
		$email_template = '
			<html>
			<body>
				<h2>' . esc_html__('You have a new notification!', LANG_STRING) . '</h2>
				<p>' . nl2br(esc_html($message)) . '</p>
			</body>
			</html>
		';

		// تنظیم هدرها
		$headers = ['Content-Type: text/html; charset=UTF-8'];

		// ارسال ایمیل
		$sent = wp_mail($to, $subject, $email_template, $headers, $attachments);

		// مدیریت خطا در صورت عدم موفقیت
		if (!$sent) {
			error_log(__('Failed to send email notification to: ', LANG_STRING) . $to);
			return false;
		}

		return true;
	}


	/**
	 * Send SMS notification.
	 */
	/*public function send_sms_notification($phone_number, $message)
	{
		// بررسی معتبر بودن شماره تلفن
		if (empty($phone_number) || !preg_match('/^\+?[1-9]\d{1,14}$/', $phone_number)) {
			error_log(__('Invalid phone number provided for SMS notification.', LANG_STRING));
			return false;
		}

		// تنظیمات API (از تنظیمات ذخیره‌شده دریافت کنید)
		$api_url = 'https://sms-api.example.com/send'; // جایگزین کنید
		$api_key = get_option('sms_api_key', 'your-api-key'); // کلید API را از تنظیمات دریافت کنید

		// ارسال درخواست به API
		$response = wp_remote_post($api_url, [
			'body' => [
				'api_key' => sanitize_text_field($api_key),
				'number'  => sanitize_text_field($phone_number),
				'message' => sanitize_textarea_field($message),
			],
		]);

		// بررسی وضعیت پاسخ
		if (is_wp_error($response)) {
			error_log(__('Failed to send SMS notification: ', LANG_STRING) . $response->get_error_message());
			return false;
		}

		// دریافت و بررسی پاسخ API
		$response_body = wp_remote_retrieve_body($response);
		$response_code = wp_remote_retrieve_response_code($response);

		if ($response_code !== 200) {
			error_log(__('SMS API returned an error: ', LANG_STRING) . $response_body);
			return false;
		}

		return true; // موفقیت
	}*/
	public function send_sms_notification($mobile, $message)
	{
		try {
			// پاکسازی ورودی
			$mobile = sanitize_text_field($mobile);

			// بررسی معتبر بودن شماره موبایل
			if (empty($mobile)) {
				error_log(__('Invalid mobile number.', LANG_STRING));
				return false;
			}


			// غیرفعال‌کردن کش WSDL
			ini_set("soap.wsdl_cache_enabled", "0");

			// اتصال به سرویس پیامک
			$client = new \SoapClient(
				"http://api.payamak-panel.com/post/send.asmx?wsdl",
				[
					"encoding" => "UTF-8",
					"connection_timeout" => 5, // تایم‌اوت برای جلوگیری از قفل‌شدن
				]
			);

			// تنظیم پارامترهای API
			$params = [
				"username" => $this->sms_username,
				"password" => $this->sms_password,
				"text"     => $message,
				"to"       => $mobile,
			];

			// ارسال درخواست به API
			$response = $client->SendByBaseNumber3($params);

			// بررسی نتیجه API
			if (isset($response->SendByBaseNumber3Result) && strlen($response->SendByBaseNumber3Result) > 15) {
				error_log(__('OTP sent successfully', LANG_STRING));
				return true;
			} else {
				error_log(__('Failed to send OTP', LANG_STRING));
				return false;
			}
		} catch (SoapFault $fault) {
			error_log(__('Connection error with SMS service: ', LANG_STRING) . $fault->getMessage());
			return false;
		} catch (Exception $e) {
			error_log(__('Unexpected error: ', LANG_STRING) . $e->getMessage());
			return false;
		}
	}

	/**
	 * Send Telegram notification.
	 */
	public function send_telegram_notification($chat_id, $message, $attachments = [])
	{
		$api_key = get_option('telegram_api_key');

		if (empty($api_key)) {
			error_log(__('Telegram API key is missing.', LANG_STRING));
			return false;
		}

		$api_url = 'https://api.telegram.org/bot' . $api_key . '/sendMessage';

		// ارسال پیام متنی
		$response = wp_remote_post($api_url, [
			'body' => [
				'chat_id' => sanitize_text_field($chat_id),
				'text'    => sanitize_text_field($message),
			],
		]);

		if (is_wp_error($response)) {
			error_log(__('Failed to send Telegram notification: ', LANG_STRING) . $response->get_error_message());
			return false;
		}

		// ارسال فایل‌های پیوست
		foreach ($attachments as $file_path) {
			$api_url = 'https://api.telegram.org/bot' . $api_key . '/sendDocument';
			$file_response = wp_remote_post($api_url, [
				'body' => [
					'chat_id' => sanitize_text_field($chat_id),
					'document' => curl_file_create($file_path),
				],
			]);

			if (is_wp_error($file_response)) {
				error_log(__('Failed to send Telegram attachment: ', LANG_STRING) . $file_path);
			}
		}

		return true;
	}


	/**
	 * Send WhatsApp notification.
	 */
	public function send_whatsapp_notification($phone_number, $message)
	{
		$account_sid = get_option('twilio_account_sid');
		$auth_token  = get_option('twilio_auth_token');
		$from_number = get_option('twilio_whatsapp_number');

		if (empty($account_sid) || empty($auth_token) || empty($from_number)) {
			error_log(__('Twilio credentials are missing.', LANG_STRING));
			return false;
		}

		$api_url = 'https://api.twilio.com/2010-04-01/Accounts/' . $account_sid . '/Messages.json';

		// ارسال درخواست
		$response = wp_remote_post($api_url, [
			'headers' => [
				'Authorization' => 'Basic ' . base64_encode($account_sid . ':' . $auth_token),
			],
			'body'    => [
				'From' => 'whatsapp:' . sanitize_text_field($from_number),
				'To'   => 'whatsapp:' . sanitize_text_field($phone_number),
				'Body' => sanitize_text_field($message),
			],
		]);

		// بررسی موفقیت ارسال
		if (is_wp_error($response)) {
			error_log(__('Failed to send WhatsApp notification: ', LANG_STRING) . $response->get_error_message());
			return false;
		}

		$response_data = json_decode(wp_remote_retrieve_body($response), true);

		return isset($response_data['sid']);
	}



	/**
	 * Handle user registration notification.
	 */
	public function handle_user_registration($user_id)
	{
		// دریافت اطلاعات کاربر
		$user = get_userdata($user_id);
		if (!$user) {
			error_log(__('User not found for registration notification.', LANG_STRING));
			return;
		}

		// تعریف پیام خوشامدگویی
		$message = sprintf(
			__('Welcome to our site, %s!', LANG_STRING),
			$user->display_name
		);

		// افزودن نوتیفیکیشن به پایگاه داده
		$notification_result = $this->add_notification_to_database($user_id, 'email', $message, 'high');
		if (is_wp_error($notification_result)) {
			error_log(__('Failed to add notification to database: ', LANG_STRING) . $notification_result->get_error_message());
			return;
		}

		// افزودن به صف نوتیفیکیشن
		$enqueue_result = $this->enqueue_notification($user_id, $message, 'email', 'high');
		if (is_wp_error($enqueue_result)) {
			error_log(__('Failed to enqueue notification: ', LANG_STRING) . $enqueue_result->get_error_message());
		}
	}

	/**
	 * Handle WooCommerce order status change notifications.
	 */
	public function handle_order_status_change($order_id, $old_status, $new_status, $order)
	{
		// بررسی صحت داده‌ها
		if (empty($order_id) || empty($old_status) || empty($new_status) || !$order || !$order->get_user_id()) {
			error_log(__('Invalid data provided for order status change notification.', LANG_STRING));
			return;
		}

		$user_id = $order->get_user_id();

		// تولید پیام
		$message = sprintf(
			__('Your order #%d has been updated from %s to %s.', LANG_STRING),
			intval($order_id),
			sanitize_text_field($old_status),
			sanitize_text_field($new_status)
		);

		// افزودن نوتیفیکیشن به پایگاه داده
		$notification_result = $this->enqueue_notification($user_id, 'email', $message, 'normal');
		if (is_wp_error($notification_result)) {
			error_log(__('Failed to add notification to database: ', LANG_STRING) . $notification_result->get_error_message());
			return;
		}

		// افزودن به صف نوتیفیکیشن
		$enqueue_result = $this->enqueue_notification($user_id, $message, 'email', 'normal');
		if (is_wp_error($enqueue_result)) {
			error_log(__('Failed to enqueue notification: ', LANG_STRING) . $enqueue_result->get_error_message());
		}

		// ارسال پیامک (در صورت نیاز)
		$phone_number = get_user_meta($user_id, 'phone_number', true);
		if (!empty($phone_number)) {
			$sms_message = sprintf(
				__('Order #%d updated to %s.', LANG_STRING),
				intval($order_id),
				sanitize_text_field($new_status)
			);
			$sms_result = $this->enqueue_notification($user_id, $sms_message, 'sms', 'normal');
			if (is_wp_error($sms_result)) {
				error_log(__('Failed to enqueue SMS notification: ', LANG_STRING) . $sms_result->get_error_message());
			}
		}
	}

	public function get_email_manager()
	{
		// دریافت تنظیمات
		$settings = get_option('settings', []);

		// بررسی و بازگشت ایمیل مدیر
		return isset($settings['contact']['manager']) ? sanitize_email($settings['contact']['manager']) : '';
	}

	public function system_password_change_email($pass_change_email, $user, $userdata)
	{
		// دریافت تنظیمات
		$settings = get_option('settings', []);
		$contact_email = isset($settings['contact']['email']) ? sanitize_email($settings['contact']['email']) : '';

		// تولید پیام
		$username = isset($user->display_name) ? sanitize_text_field($user->display_name) : __('User', LANG_STRING);
		$message  = [];
		$message[] = '<strong>' . sprintf(esc_html__('Dear %s,', LANG_STRING), $username) . '</strong>';
		$message[] = '<p>' . esc_html__('You have recently changed your account password.', LANG_STRING) . '</p>';
		$message[] = '<p>' . esc_html__('If this is incorrect, and you did not change your password, please contact us at:', LANG_STRING) . '</p>';
		$message[] = '<p><a href="mailto:' . esc_attr($contact_email) . '">' . esc_html($contact_email) . '</a></p>';
		$message[] = self::mail_template_footer();

		// تنظیم پیام ایمیل
		$pass_change_email['message'] = implode('', $message);

		return $pass_change_email;
	}

	// Notices
	public function send_status_change_notifications($order_id, $old_status, $new_status, $order)
	{
		$order_data = $this->get_order_data($order);
		// پیام به مدیر
		if (isset($this->notice_status[$new_status]['manager'])) {
			foreach (['email', 'sms'] as $type) {
				if ($this->notice_status[$new_status]['manager'][$type]['enabled']) {
					$message = $this->replace_placeholders($this->notice_status[$new_status]['manager'][$type]['message'], $order_data);
					if ($type === 'email') {
						$this->send_email_to_managers($message);
					} elseif ($type === 'sms') {
						$this->send_sms_to_managers($message);
					}
				}
			}
		}

		// پیام به مشتری
		if (isset($this->notice_status[$new_status]['customer'])) {
			foreach (['email', 'sms'] as $type) {
				if ($this->notice_status[$new_status]['customer'][$type]['enabled']) {
					$message = $this->replace_placeholders($this->notice_status[$new_status]['customer'][$type]['message'], $order_data);
					if ($type === 'email') {
						$this->send_email_to_user($order_data['{email}'], $message);
					} elseif ($type === 'sms') {
						$this->send_sms_to_user($order_data['{mobile}'], $message);
					}
				}
			}
		}
	}

	public function notify_manager_on_low_stock($product)
	{
		if (isset($this->notice_status['low_stock']['manager'])) {
			$product_data = [
				'{product_id}' => $product->get_id(),
				'{sku}' => $product->get_sku(),
				'{product_title}' => $product->get_name(),
				'{stock}' => $product->get_stock_quantity(),
			];
			foreach (['email', 'sms'] as $type) {
				if ($this->notice_status['low_stock']['manager'][$type]['enabled']) {
					$message = $this->replace_placeholders($this->notice_status['low_stock']['manager'][$type]['message'], $product_data);
					if ($type === 'email') {
						$this->send_email_to_managers($message);
					} elseif ($type === 'sms') {
						$this->send_sms_to_managers($message);
					}
				}
			}
		}
	}

	public function notify_manager_on_no_stock($product)
	{
		if (isset($this->notice_status['no_stock']['manager'])) {
			$product_data = [
				'{product_id}' => $product->get_id(),
				'{sku}' => $product->get_sku(),
				'{product_title}' => $product->get_name(),
			];
			foreach (['email', 'sms'] as $type) {
				if ($this->notice_status['no_stock']['manager'][$type]['enabled']) {
					$message = $this->replace_placeholders($this->notice_status['no_stock']['manager'][$type]['message'], $product_data);
					if ($type === 'email') {
						$this->send_email_to_managers($message);
					} elseif ($type === 'sms') {
						$this->send_sms_to_managers($message);
					}
				}
			}
		}
	}

	private function replace_placeholders($message, $data)
	{
		foreach ($data as $key => $value) {
			$message = str_replace($key, $value, $message);
		}
		return $message;
	}

	private function get_order_data($order)
	{

		return [
			'{mobile}' => $order->get_billing_phone(),
			'{phone}' => $order->get_billing_phone(),
			'{email}' => $order->get_billing_email(),
			'{status}' => wc_get_order_status_name($order->get_status()),
			'{all_items}' => implode("\n", wp_list_pluck($order->get_items(), 'name')),
			'{all_items_qty}' => implode("\n", array_map(function ($item) {
				return $item->get_name() . ' (' . $item->get_quantity() . ')';
			}, $order->get_items())),
			'{count_items}' => $order->get_item_count(),
			'{price}' => show_price($order->get_total()),
			'{post_id}' => $order->get_id(),
			'{order_id}' => $order->get_order_number(),
			'{transaction_id}' => $order->get_transaction_id(),
			'{date}' => $order->get_date_created()->date('Y-m-d H:i:s'),
			'{description}' => $order->get_customer_note(),
			'{payment_method}' => $order->get_payment_method_title(),
			'{shipping_method}' => $order->get_shipping_method(),
			'{b_first_name}' => $order->get_billing_first_name(),
			'{b_last_name}' => $order->get_billing_last_name(),
			'{b_company}' => $order->get_billing_company(),
			'{b_country}' => $order->get_billing_country(),
			'{b_state}' => $order->get_billing_state(),
			'{b_city}' => $order->get_billing_city(),
			'{b_address_1}' => $order->get_billing_address_1(),
			'{b_address_2}' => $order->get_billing_address_2(),
			'{b_postcode}' => $order->get_billing_postcode(),
			'{sh_first_name}' => $order->get_shipping_first_name(),
			'{sh_last_name}' => $order->get_shipping_last_name(),
			'{sh_company}' => $order->get_shipping_company(),
			'{sh_country}' => $order->get_shipping_country(),
			'{sh_state}' => $order->get_shipping_state(),
			'{sh_city}' => $order->get_shipping_city(),
			'{sh_address_1}' => $order->get_shipping_address_1(),
			'{sh_address_2}' => $order->get_shipping_address_2(),
			'{sh_postcode}' => $order->get_shipping_postcode(),
		];
	}

	private function send_email_to_managers($message)
	{
		if (!empty($this->notice_manager['email'])) {
			foreach ($this->notice_manager['email'] as $email) {
				wp_mail($email, 'Notification', $message);
			}
		}
	}

	private function send_sms_to_managers($message)
	{
		if (!empty($this->notice_manager['mobile'])) {
			foreach ($this->notice_manager['mobile'] as $mobile) {
				$this->send_sms_notification($mobile, $message);
			}
		}
	}

	private function send_email_to_user($email, $message)
	{
		wp_mail($email, 'Notification', $message);
	}
	private function send_sms_to_user($mobile, $message)
	{
		$this->send_sms_notification($mobile, $message);
	}
}
