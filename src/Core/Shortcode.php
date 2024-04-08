<?php

namespace VVerner\Core;

defined('ABSPATH') || exit('No direct script access allowed');

class Shortcode
{
  private ?string $uxBuilderName = null;
  private array $atts = [];
  private array $options = [];

  public function __construct(
    private readonly string $name,
    private readonly string $view
  ) {
    $this->addDefaultAttributes();

    add_action('init', $this->addShortcode(...));

    if ($this->enqueueUxBuilder()) :
      add_action('ux_builder_setup', $this->uxBuilderSetup(...));
    endif;
  }

  public function setUxBuilderName(string $uxBuilderName): void
  {
    $this->uxBuilderName = $uxBuilderName;
  }

  private function addShortcode(): void
  {
    add_shortcode('vverner_' . $this->name, function ($args): string|false {
      ob_start();

      $args = shortcode_atts($this->atts, $args);

      if (!file_exists($this->view)) :
        file_put_contents($this->view, 'auto generated');
      endif;

      require_once $this->view;

      return ob_get_clean();
    });
  }

  private function enqueueUxBuilder(): bool
  {
    $filename = get_template_directory() . '/inc/builder/helpers.php';

    if (!file_exists($filename)) :
      return false;
    endif;

    require_once $filename;
    return true;
  }

  private function uxBuilderSetup(): void
  {
    add_ux_builder_shortcode('vverner_' . $this->name, [
      'name'              => $this->uxBuilderName ?: $this->name,
      'category'          => 'VVerner',
      'options'           => $this->options
    ]);
  }

  public function addAttribute(string $heading, string $key, $defaultValue = '', array $options = []): void
  {
    $this->atts[$key] = $defaultValue;
    $this->options[$key] = [
      'type'       => $options !== [] ? 'select' : 'textfield',
      'heading'    => $heading,
      'default'    => $defaultValue,
      'options'    => $options,
      'full_width' => true,
    ];
  }

  private function addDefaultAttributes(): void
  {
    $this->addAttribute('Classe extra de CSS', 'class', '');
    $this->addAttribute('ID', 'id', '');
  }
}
