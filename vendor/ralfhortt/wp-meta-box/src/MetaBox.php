<?php

namespace RalfHortt\MetaBoxes;

use Exception;

abstract class MetaBox
{
    /** @var string Meta box ID */
    protected $identifier = '';

    /** @var string Meta box title */
    protected $name = '';

    /** @var (string|array|WP_Screen) The screen or screens on which to show the box (such as a post type, 'link', or 'comment'). Accepts a single screen ID, WP_Screen object, or array of screen IDs. Default is the current screen. If you have used add_menu_page() or add_submenu_page() to create a new screen (and hence screen_id), make sure your menu slug conforms to the limits of sanitize_key() otherwise the 'screen' menu may not correctly render on your page. */
    protected $screen = '';

    /** @var The context within the screen where the boxes should display. Available contexts vary from screen to screen. Post edit screen contexts include 'normal', 'side', and 'advanced'. Comments screen contexts include 'normal' and 'side'. Menus meta boxes (accordion sections) all use the 'side' context. Global */
    protected $context = 'advanced';

    protected $validContext = [
        'normal',
        'side',
        'advanced',
    ];

    /** @var string The priority within the context where the boxes should show ('high', 'low'). */
    protected $priority = 'default';

    protected $validPriorities = [
        'default',
        'high',
        'low',
    ];

    /** @var array Data that should be set as the property of the box array (which is the second parameter passed to your callback). */
    protected $callbackArgs = [];

    /**
     * Boot meta box.
     *
     * @return void
     */
    public function register(): void
    {
        \add_action('save_post', [$this, 'savePost'], 10, 3);
        \add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
    }

    public function addMetaBoxes(): void
    {
        if (!in_array($this->context, $this->validContext)) {
            throw new Exception(sprintf('Invalid context `%s` for meta box `%s`. Valid values are `normal`, `side` and `advanced`.', $this->context, $this->name));
        }

        if (!in_array($this->priority, $this->validPriorities)) {
            throw new Exception(sprintf('Invalid priority `%s` for meta box `%s`. Valid values are `default`, `high` and `low`.', $this->priority, $this->name));
        }

        \add_meta_box($this->identifier, $this->name, [$this, 'metaBox'], $this->screen, $this->context, $this->priority, $this->callbackArgs);
    }

    public function metaBox(\WP_Post $post, array $callbackArgs): void
    {
        \wp_nonce_field('save-'.$this->identifier, $this->identifier.'-nonce');
        \do_action("before-meta-box-{$this->identifier}", $post, $callbackArgs);
        $this->render($post, $callbackArgs);
        \do_action("after-meta-box-{$this->identifier}", $post, $callbackArgs);
    }

    abstract protected function render(\WP_Post $post, array $callbackArgs): void;

    protected function save(int $postId, \WP_Post $post, bool $update): void
    {
    }

    public function savePost(int $postId, \WP_Post $post, bool $update): void
    {
        if (!isset($_POST[$this->identifier.'-nonce']) || !\wp_verify_nonce($_POST[$this->identifier.'-nonce'], 'save-'.$this->identifier)) {
            return;
        }

        $this->save($postId, $post, $update);
        \do_action("saved-meta-box-{$this->identifier}", $postId, $post, $update);
    }
}
