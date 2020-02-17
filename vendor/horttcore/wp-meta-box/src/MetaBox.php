<?php
namespace Horttcore\MetaBoxes;

abstract class MetaBox
{


    /**
     * @var string $identifier Meta box ID
     */
    protected $identifier = '';


    /**
     * @var string $name Meta box title
     */
    protected $name = '';


    /**
     * @var (string|array|WP_Screen) $screen  The screen or screens on which to show the box (such as a post type, 'link', or 'comment'). Accepts a single screen ID, WP_Screen object, or array of screen IDs. Default is the current screen. If you have used add_menu_page() or add_submenu_page() to create a new screen (and hence screen_id), make sure your menu slug conforms to the limits of sanitize_key() otherwise the 'screen' menu may not correctly render on your page.
     */
    protected $screen = '';


    /**
     * @var $context The context within the screen where the boxes should display. Available contexts vary from screen to screen. Post edit screen contexts include 'normal', 'side', and 'advanced'. Comments screen contexts include 'normal' and 'side'. Menus meta boxes (accordion sections) all use the 'side' context. Global
     */
    protected $context = 'advanced';


    /**
     * @var string $priority The priority within the context where the boxes should show ('high', 'low').
     */
    protected $priority = 'default';


    /**
     * @var array $callback_args Data that should be set as the $args property of the box array (which is the second parameter passed to your callback).
     */
    protected $callbackArgs = [];


    /**
     * Boot meta box
     *
     * @return void
     */
    public function register()
    {
        add_action('save_post', [$this, 'savePost']);
        add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
    }


    /**
     * Register meta box
     *
     * @return void
     */
    public function addMetaBoxes()
    {
        add_meta_box($this->identifier, $this->name, [$this, 'metaBox'], $this->screen, $this->context, $this->priority, $this->callbackArgs);
    }

    /**
     * Meta box
     *
     * @param WP_Post $post Post object
     * @return void
     **/
    public function metaBox(\WP_Post $post)
    {
        wp_nonce_field( 'save-' . $this->identifier, $this->identifier . '-nonce' );
        $this->render( $post );
    }


    /**
     * Render function
     *
     * @param WP_Post $post Post object
     * @return void
     */
    abstract function render(\WP_Post $post);


    /**
     * Save function
     *
     * @param int $postId Post ID
     * @return void
     */
    abstract function save(int $postId);


    /**
     * Save post
     *
     * @param int $postId
     **/
    public function savePost(int $postId)
    {
        if ( !isset( $_POST[$this->identifier . '-nonce'] ) || !wp_verify_nonce( $_POST[$this->identifier . '-nonce'], 'save-' . $this->identifier ) )
            return;

        $this->save($postId);
    }


}
