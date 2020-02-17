<?php
namespace Horttcore\CustomTaxonomy;

abstract class Taxonomy
{


    /**
     * Taxonomy slug
     * 
     * @var string $slug Taxonomy slug
     */
    protected $slug = '';


    /**
     * Attach taxonomy to this post types
     * 
     * @var array<string> postType Array of post types
     */
    protected $postTypes = [];


    /**
     * Register hooks
     *
     * @since 1.0.0
     **/
    public function register()
    {
        add_action('init', [$this, 'registerTaxonomy']);
    }


    /**
     * Get taxonomy slug
     * 
     * @return string
     * @since 1.0.0
     **/
    protected function getTaxonomySlug() : string
    {
        return $this->slug;
    }


    /**
     * Register taxonomy
     *
     * @return WP_Error|void
     **/
    public function registerTaxonomy()
    {
        $args = $this->getConfig();
        $args['labels'] = $this->getLabels();

        return register_taxonomy($this->getTaxonomySlug(), $this->postTypes, $args);
    }


    /**
     * Get taxonomy configuration
     *
     * @return array
     **/
    abstract function getConfig(): array;


    /**
     * Get taxonomy labels
     *
     * @return array
     **/
    abstract function getLabels(): array;


}