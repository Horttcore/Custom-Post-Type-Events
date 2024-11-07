<?php

namespace RalfHortt\CustomTaxonomy;

use RalfHortt\TranslatorService\Translator;

abstract class Taxonomy
{
    protected $slug = '';
    protected $postTypes = [];
    protected $useFilter = true;

    public function __construct()
    {
        (new Translator('wp-custom-taxonomy', dirname(plugin_basename(__FILE__)).'/../languages/'))->register();
    }

    public function register()
    {
        \add_action('init', [$this, 'registerTaxonomy']);

        if ($this->useFilter) {
            \add_action('restrict_manage_posts', [$this, 'taxonomyFilter']);
        }
    }

    protected function getTaxonomySlug(): string
    {
        return $this->slug;
    }

    public function registerTaxonomy()
    {
        $args = $this->getConfig();
        $args['labels'] = $this->getLabels();

        return \register_taxonomy($this->getTaxonomySlug(), $this->postTypes, $args);
    }

    abstract public function getConfig(): array;

    abstract public function getLabels(): array;

    public function taxonomyFilter()
    {
        global $typenow;
        if (in_array($typenow, $this->postTypes)) {
            $labels = $this->getLabels();
            \wp_dropdown_categories([
                'show_option_all' => sprintf(_x('Show all %s', 'Show all terms', 'wp-custom-taxonomy'), $labels['name']),
                'taxonomy'        => $this->slug,
                'name'            => $this->slug,
                'orderby'         => 'name',
                'selected'        => isset($_GET[$this->slug]) ? $_GET[$this->slug] : '',
                'show_count'      => true,
                'hide_empty'      => true,
                'hide_if_empty'   => true,
                'value_field'     => 'slug',
            ]);
        }
    }
}
