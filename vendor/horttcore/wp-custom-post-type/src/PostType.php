<?php
namespace Horttcore\CustomPostType;

abstract class PostType
{

    /**
     * Post type slug
     * 
     * @var string $slug
     */
    protected $slug = 'null';


    /**
     * Register hooks
     *
     * @since 1.0.0
     **/
    public function register()
    {
        add_action( 'init', [$this, 'registerPostType'] );
        add_action( 'post_updated_messages', [$this, 'postUpdateMessages'] );
    }


    /**
     * Get post type slug
     * 
     * @return string
     * @since 1.0.0
     **/
    protected function getPostTypeSlug(): string
    {
        return $this->slug;
    }


    /**
     * Register post type
     *
     * @param array $messages Post update messages
     * @return array
     **/
    public function postUpdateMessages( $messages ): array
    {
        $post = get_post();
        $postType = $this->getPostTypeSlug();
        $postTtypeObject = get_post_type_object($postType);
        $messages[$postType] = $this->getPostUpdateMessages( $post, $postType, $postTtypeObject );

        return $messages;
    }


    /**
     * Register post type
     *
     * @return WP_Post_Type|WP_Error
     **/
    public function registerPostType()
    {
        $args = $this->getConfig();
        $args['labels'] = $this->getLabels();

        return register_post_type( $this->getPostTypeSlug(), $args );
    }


    /**
     * Get configuration
     *
     * @return array
     **/
    abstract function getConfig(): array;


    /**
     * Get post type labels
     *
     * @return array
     **/
    abstract function getLabels(): array;


    /**
     * Update messages
     *
     * @param WP_Post $post Post object
     * @param string $postType Post type slug
     * @param WP_Post_Type $postType Post type slug
     * @return array Update messages
     **/
    abstract function getPostUpdateMessages(\WP_Post $post, string $postType, \WP_Post_Type $postTypeObjects) : array;


}