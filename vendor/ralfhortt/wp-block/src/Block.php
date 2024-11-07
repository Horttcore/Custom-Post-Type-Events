<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

abstract class Block implements ServiceContract
{
    // Block Title
    protected string $title;

    // Block name
    protected string $name;

    // Block attributes
    protected array $attributes = [];

    // Path to block.json
    protected string $blockJson;

    /**
     * Register.
     */
    public function register(): void
    {
        $attrs = [];
        if (! $this->hasBlockJson()) {
            $attrs['attributes'] = $this->getAttributes();
            $attrs['title'] = $this->getTitle();
        }

        add_action('init', function () use ($attrs) {
            if (is_admin()) {
                return;
            }
            \register_block_type(
                $this->hasBlockJson() ? $this->getBlockJson() : $this->getName(),
                array_merge([
                    'render_callback' => [$this, 'callback'],
                ], $attrs, $this->args()),
            );
        });
    }

    /**
     * Has block json.
     */
    protected function hasBlockJson(): string
    {
        return isset($this->blockJson) && $this->blockJson;
    }

    /**
     * Get block json.
     */
    protected function getBlockJson(): string
    {
        $path = plugin_dir_path(__FILE__);
        $path = str_replace(WP_PLUGIN_DIR, '', $path);
        $path = array_filter(explode(DIRECTORY_SEPARATOR, $path));
        $path = array_shift($path);

        return WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$this->blockJson;
    }

    /**
     * Get block name.
     */
    protected function getName(): string
    {
        return $this->name;
    }

    /**
     * Get block title.
     */
    protected function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Register block type args.
     */
    protected function args(): array
    {
        return [];
    }

    /**
     * Get block attributes.
     */
    protected function getAttributes(): array
    {
        return \apply_filters($this->getName().'/attributes', $this->attributes);
    }

    /**
     * Render callback.
     */
    public function callback(array $atts = [], string $content = ''): string
    {
        ob_start();

        \do_action($this->getName().'/before', $atts, $content);
        echo \apply_filters($this->getName().'/render', $this->render($atts, $content), $atts, $content);
        \do_action($this->getName().'/after', $atts, $content);

        return ob_get_clean();
    }

    /**
     * Output.
     */
    abstract protected function render(array $atts, string $content): void;
}
