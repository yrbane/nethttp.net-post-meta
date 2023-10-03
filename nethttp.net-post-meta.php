<?php

/**
 * Plugin Name: nethttp.net-post-meta
 * Plugin URI: https://github.com/yrbane/nethttp.net-post-meta
 * Description: A wordpress plugin that is a simple way to store additional data for posts.
 * Version: 0.0.1
 * Author: Barney <yrbane@nethttp.net>
 * Author URI: https://github.com/yrbane
 * Requires PHP: 7.4
 * Text Domain: default
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /languages
 */

namespace nethttp;

use Post_meta_type;

include 'post_meta_type.php';

class Post_Meta
{
    // Plugin version
    private const PLUGIN_VERSION = '0.0.1';

    // Plugin name
    private const PLUGIN_NAME = 'nethttp.net-post-meta';

    /**
     * The absolute path to the directory containing this plugin file.
     */
    private const PLUGIN_PATH = __DIR__ . '/';

    /**
     * The name of the option that stores the selected net-post-meta.
     */
    private const OPTION = 'nethttp.net-post-meta';

    /**
     * Constructor function that sets up actions to be taken when the plugin is loaded.
     */
    public function __construct()
    {
        // Check if the net-post-meta option is not set and add it if it's not
        if (!get_option(self::OPTION)) {
            add_option(self::OPTION, []);
        }

        // Add a menu link in the WordPress admin panel
        add_action('admin_menu', [$this, 'admin_menu']);

        //Adding style and script
        add_action('admin_enqueue_scripts', [$this, 'styles_and_scripts']);

        // Add hooks to display and save post meta fields
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_post_meta']);
    }

    /**
     * Function to add meta boxes for post meta fields.
     */
    public function add_meta_boxes()
    {
        // Get the list of post meta fields from the option
        $metas = get_option(self::OPTION);

        // Add a meta box for each post meta field
        foreach ($metas as &$meta) {
            add_meta_box(
                $meta['slug'], // Meta box ID
                $meta['name'], // Meta box title
                [$this, 'display_meta_box'], // Callback function to display the meta box content
                'post', // Post type
                $meta['context'], // Meta box context (normal, side, advanced)
                'high', // Meta box priority (high, core, default, low)
                $meta // Additional data to be passed to the callback function
            );
        }
    }

    /**
     * Function to display the meta box content for post meta fields.
     * @param WP_Post $post The current post object.
     * @param array $meta The meta field data.
     */
    public function display_meta_box($post, $meta)
    {
        //var_dump($meta);
        //echo '<br/><br/>';

        // Get the list of user meta fields from the option
        //$metas = get_option(self::OPTION);
        //var_dump($metas);
        Post_meta_type::{$meta['args']['type']}($post, $meta['args']);
    }

    /**
     * Function to save the post meta fields when a post is saved.
     * @param int $post_id The ID of the post being saved.
     */
    public function save_post_meta($post_id)
    {
        // Check if the current user can edit the post
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Get the list of post meta fields from the option
        $metas = get_option(self::OPTION);

        // Loop through each post meta field and update the post meta value
        foreach ($metas as &$meta) {
            if (isset($_POST[$meta['slug']])) {
                // Update the post meta
                update_post_meta($post_id, $meta['slug'], $_POST[$meta['slug']]);
            }
        }
    }

    /**
     * Function to add styles and scripts to the WordPress admin panel.
     */
    public function styles_and_scripts($hook)
    {
        // Check if we are on the plugin settings page
        if ($hook === 'toplevel_page_nethttp.net-post-meta') {
            // Add CSS style
            wp_enqueue_style(
                self::PLUGIN_NAME . '-css',
                plugin_dir_url(__FILE__) . '/style.css',
                [],
                WP_DEBUG ? time() : self::PLUGIN_VERSION
            );

            // Enqueue JavaScript
            wp_enqueue_script(
                self::PLUGIN_NAME . '-js',
                plugin_dir_url(__FILE__) . '/script.js',
                ['jquery'],
                WP_DEBUG ? time() : self::PLUGIN_VERSION,
                true
            );
        }
    }

    /**
     * Adds a menu link and page to WordPress admin panel.
     */
    public function admin_menu()
    {
        add_menu_page(
            'nethttp.net post meta', // Title of the page
            'Post Meta', // Text to show on the menu link
            'administrator', // Capability requirement to see the link
            'nethttp.net-post-meta', // The 'slug' - file to display when clicking the link,
            [$this, 'settings'], // Callback function to generate page content
            'dashicons-admin-generic', // Icon to display next to the menu item
            99 // Position of the menu item
        );
    }

    /**
     * Display admin form setting to define post metas to add.
     */
    public function settings()
    {
        if (!empty($_POST)) {
            $this->save($_POST);
        }

        $metas = get_option(self::OPTION);
        $post_meta_types = get_class_methods('Post_meta_type');

?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Post Metas</h1>
            <button class="page-title-action" id="add-post-meta">Ajouter un groupe</button>
            <form method="post" action="<?php echo admin_url('admin.php?page=nethttp.net-post-meta'); ?>">
                <table class="wp-list-table widefat fixed striped table-view-list" id="post-metas"></table>
                <?php submit_button(__('Save Settings', 'textdomain')); ?>
            </form>
        </div>
        <script>
            var postMetas = <?php echo json_encode($metas) ?>;
            var postMetasType = <?php echo json_encode($post_meta_types) ?>;
        </script>

<?php
    }

    /**
     * Save settings.
     * @param array $data The data to save.
     */
    private function save($data)
    {
        $dataToSave = [];
        foreach ($data['name'] as $k => $value) {
            if (empty($value)) {
                continue;
            }
            if (!isset($data['type'][$k]) || !isset($data['description'][$k])) {
                continue;
            }
            $data['name'][$k] = stripslashes($data['name'][$k]);
            $dataToSave[] = [
                'slug' => sanitize_title($data['name'][$k], '_'),
                'name' => $data['name'][$k],
                'type' => $data['type'][$k],
                'context' => $data['context'][$k],
                'description' => stripslashes($data['description'][$k]),
            ];
        }

        if (update_option(self::OPTION, $dataToSave)) {
            $this->notice('Post metas settings saved!');
        } else {
            $this->error('Post metas settings not saved or unchanged!');
        }
    }

    /**
     * Outputs a notice message to the user.
     * @param string $msg The message to display.
     * @param string $type The type of notice to display, defaults to 'success'.
     * @return void
     */
    private function notice($msg, $type = 'success')
    {
        echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $msg . '</p></div>';
    }

    /**
     * Outputs an error message to the user.
     * @param string $msg The error message to display.
     * @return void
     */
    private function error($msg)
    {
        return $this->notice($msg, 'error');
    }
}

new Post_Meta();
