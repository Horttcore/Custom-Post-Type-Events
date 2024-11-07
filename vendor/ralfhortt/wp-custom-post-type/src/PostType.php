<?php

namespace RalfHortt\CustomPostType;

abstract class PostType
{
    /**
     * Post type slug.
     */
    protected string $slug;

    /**
     * Register hooks.
     **/
    public function register(): void
    {
        \add_action('init', [$this, 'registerPostType']);
        \add_action('post_updated_messages', [$this, 'postUpdateMessages']);
    }

    /**
     * Get post type slug.
     **/
    protected function getPostTypeSlug(): string
    {
        return $this->slug;
    }

    public function postUpdateMessages(array $postUpdateMessages): array
    {
        $post = \get_post();
        $postType = $this->getPostTypeSlug();
        $postTtypeObject = \get_post_type_object($postType);
        $postUpdateMessages[$postType] = $this->getPostUpdateMessages($post, $postType, $postTtypeObject);

        return $postUpdateMessages;
    }

    /**
     * Register post type.
     **/
    public function registerPostType(): \WP_Post_Type|\WP_Error
    {
        $args = $this->getConfig();
        $args['labels'] = $this->getLabels();

        return \register_post_type($this->getPostTypeSlug(), $args);
    }

    /**
     * Get configuration.
     **/
    abstract public function getConfig(): array;

    /**
     * Get post type labels.
     **/
    abstract public function getLabels(): array;

    /**
     * Get post update messages.
     **/
    abstract public function getPostUpdateMessages(\WP_Post $post, string $postTypeSlug, \WP_Post_Type $postTypeObject): array;
}
