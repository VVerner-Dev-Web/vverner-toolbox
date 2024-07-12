<?php

namespace VVerner\Core;

defined('ABSPATH') || exit('No direct script access allowed');

class JumpStart
{
  public function __construct()
  {
  }

  public static function availableJobs(): array
  {
    return [
      'posts' => 'Criar posts demonstrativos no blog',
      'pages' => 'Criar páginas padrões (Início, Contato, Sobre, Notícias)',
      'comments' => 'Apagar comentários padrões',
      'configs' => 'Configurar opções iniciais'
    ];
  }

  public function posts(): void
  {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $minimumPostsCount  = 3;
    $total              = 0;
    $uploadDir          = wp_upload_dir();
    $uploadPath         = $uploadDir['path'];

    while ($total < $minimumPostsCount) :
      $postId   = wp_insert_post([
        'post_type'     => 'post',
        'post_status'   => 'publish',
        'post_title'    => 'Demonstrativo 0' . $total,
        'post_content'  => '
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Nihil consequatur aspernatur sint, incidunt labore magni nesciunt modi assumenda veritatis deserunt neque ea. Natus, temporibus iste quia ex quod dolor doloremque?</p>
        <p>Libero incidunt animi eaque ad deleniti sunt eligendi at, excepturi asperiores ex velit fuga. Iste nihil neque cum ea officia labore dignissimos repellendus, minima dolorum? Voluptatibus magni temporibus aspernatur labore?</p>
        <p>Beatae, perspiciatis. Ab soluta facilis ad tempora consequuntur voluptatibus praesentium quasi nam doloribus adipisci reiciendis optio, ratione delectus commodi a repellendus eius. Dicta nisi suscipit sequi porro minima autem maiores!</p>'
      ]);

      $placeholder = VVERNER_TOOLBOX . '/assets/imgs/placeholder-' . $total . '.jpeg';
      $name        = basename($placeholder);
      $file        = $uploadPath . '/' . $name;
      $fileType    = wp_check_filetype($name, null);

      copy($placeholder, $file);

      $imageId  = wp_insert_attachment([
        'post_mime_type' => $fileType['type'],
        'post_title'     => sanitize_file_name($name),
        'post_content'   => '',
        'post_status'    => 'inherit'
      ], $file, $postId);

      $meta = wp_generate_attachment_metadata($imageId, $file);
      wp_update_attachment_metadata($imageId, $meta);
      set_post_thumbnail($postId, $imageId);

      $total++;
    endwhile;
  }

  public function pages(): void
  {
    $requiredPages = [
      'home'      => 'Início',
      'contact'   => 'Contato',
      'about'     => 'Sobre',
      'blog'      => 'Notícias'
    ];

    foreach ($requiredPages as $key => $page) :
      if ($this->getPostIdForKey($key)) :
        continue;
      endif;

      wp_insert_post([
        'post_type'     => 'page',
        'post_title'    => $page,
        'post_status'   => 'publish',
        'meta_input'    => [
          'vverner_key'   =>  $key
        ]
      ]);
    endforeach;
  }

  public function comments(): void
  {
    wp_delete_comment(1);
  }

  public function configs(): void
  {
    update_option('blogdescription', '');
    update_option('timezone_string', 'America/Sao_Paulo');
    update_option('date_format', 'd/m/Y');
    update_option('time_format', 'H:i');
    update_option('posts_per_page', 12);

    $home = $this->getPostIdForKey('home');
    $blog = $this->getPostIdForKey('blog');

    if ($home) :
      update_option('show_on_front', 'page');
      update_option('page_on_front', $home);
    endif;

    if ($blog) :
      update_option('page_for_posts', $blog);
    endif;
  }

  private function getPostIdForKey(string $key): int
  {
    $posts = get_posts([
      'posts_per_page' => 1,
      'fields' => 'ids',
      'post_type' => 'page',
      'post_status' => 'publish',
      'meta_query' => [
        [
          'key' => 'vverner_key',
          'value' => $key
        ]
      ]
    ]);

    return $posts ? array_shift($posts) : 0;
  }
}
