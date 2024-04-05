<?php

namespace VVerner\Core;

use VVerner\Controllers\Images;
use WP_Error;

defined('ABSPATH') || exit('No direct script access allowed');

class ImageOptimizer
{
  private Images $controller;

  private function __construct()
  {
    $this->controller = new Images;
  }

  public static function attach(): void
  {
    $cls = new self();
    add_filter('wp_handle_upload', $cls->resize(...));
  }

  private function resize(WP_Error|array $upload): WP_Error|array
  {
    $types = [
      'image/jpeg',
      'image/jpg',
      'image/webp',
      'image/png'
    ];

    if (
      is_wp_error($upload) ||
      !in_array($upload['type'], $types)
      || filesize($upload['file']) <= 0
    ) :
      return $upload;
    endif;

    $editor = wp_get_image_editor($upload['file']);
    $imageSize = $editor->get_size();

    if (isset($imageSize['width']) && $this->controller->maxWidth > 0 && $imageSize['width'] > $this->controller->maxWidth) :
      $editor->resize($this->controller->maxWidth, null, false);
    endif;

    $imageSize = $editor->get_size();

    if (isset($imageSize['height']) && $this->controller->maxHeight > 0 && $imageSize['height'] > $this->controller->maxHeight) :
      $editor->resize(null, $this->controller->maxHeight, false);
    endif;

    if ($this->controller->quality) :
      $editor->set_quality($this->controller->quality);
    endif;

    $editor->save($upload['file']);

    return $upload;
  }
}
