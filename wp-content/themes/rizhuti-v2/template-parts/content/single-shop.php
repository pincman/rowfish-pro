<?php 
$_widget_wap_position = _cao('show_shop_widget_wap_position','bottom');
$format = get_post_format() ? get_post_format() : 'image';
$shop_thumbnail_url = _get_post_shop_thumbnail_url(null,'full');
$post_shop_type = _get_post_shop_type();
$is_comments_tab = !(post_password_required() || !comments_open() || !is_site_comments());
$is_faq_tab = true;
$faq_tab_data = _cao('single_shop_template_help',array());

?>

<?php if ( _cao('is_single_shop_template_img',true) && !empty($shop_thumbnail_url) && ($post_shop_type==3 || $post_shop_type==4) ) : ?>
<div class="single-download-thumbnail mb-4">    
  <img src="<?php echo $shop_thumbnail_url;?>" class="img-fluid wp-post-image" alt="<?php the_title();?>" />
</div>
<?php endif;?>


<div class="single-download-nav">
  <ul class="nav nav-pills" id="pills-tab" role="tablist">

    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="pills-details-tab" data-toggle="pill" href="#pills-details" role="tab" aria-controls="pills-details" aria-selected="true"><i class="far fa-file-alt mr-1"></i><?php _e( '详情介绍','rizhuti-v2' );?></a>
    </li>

    <?php if ( $is_comments_tab ) : ?>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="pills-comments-tab" data-toggle="pill" href="#pills-comments" role="tab" aria-controls="pills-comments" aria-selected="false"><i class="fa fa-comments-o mr-1"></i><?php _e( '评论建议','rizhuti-v2' );?></a>
    </li>
    <?php endif;?>

    <?php if ( $is_faq_tab ) : ?>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="pills-faq-tab" data-toggle="pill" href="#pills-faq" role="tab" aria-controls="pills-faq" aria-selected="false"><i class="far fa-question-circle mr-1"></i><?php _e( '常见问题','rizhuti-v2' );?></a>
    </li>
    <?php endif;?>
    
  </ul>
</div>

<div class="tab-content" id="pills-tabContent">

  <div class="tab-pane fade show active" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
    <article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
      <div class="container">
        <?php if (_cao('is_single_breadcrumb','1')) : ?>
        <div class="article-crumb"><?php rizhuti_v2_breadcrumb('breadcrumb'); ?></div>
        <?php endif; ?>

        <?php if ( !rizhuti_v2_show_hero() || $format != 'image' ){
          get_template_part( 'template-parts/content/entry-header' );
        }?>

        <?php if ($_widget_wap_position == 'top') { shop_widget_wap_position();}?>
      
        <div class="entry-wrapper">
          <div class="entry-content u-text-format u-clearfix">
            <?php 
              the_content();
              rizhuti_v2_pagination(5);
              if ($_widget_wap_position == 'bottom') { shop_widget_wap_position(); }
              if ($copyright = _cao('single_copyright')) { echo '<div class="post-note alert alert-info mt-2" role="alert">' . $copyright . '</div>'; }
              if (_cao('is_single_tags','1')) { get_template_part( 'template-parts/content/entry-tags'); }
              if (_cao('is_single_share','1')) { get_template_part( 'template-parts/content/entry-share'); }
            ?>
          </div>
        </div>
      </div>
    </article>
  </div>

  <?php if ( $is_comments_tab ) : ?>
  <div class="tab-pane fade" id="pills-comments" role="tabpanel" aria-labelledby="pills-comments-tab">
    <?php comments_template();?>
  </div>
  <?php endif;?>

  <?php if ( $is_faq_tab ) : ?>
  <div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab">

    <div class="accordion" id="accordionhelp">
    <?php foreach ($faq_tab_data as $key => $item) : ?>
      <div class="card">
        <div class="card-header" id="heading-<?php echo $key;?>">
          <h2 class="mb-0">
            <button class="btn btn-sm btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $key;?>" aria-expanded="false" aria-controls="collapse-<?php echo $key;?>">
              <?php echo $item['title'];?><span class="fa fa-plus"></span><span class="fa fa-minus"></span>
            </button>

          </h2>
        </div>
        <div id="collapse-<?php echo $key;?>" class="collapse" aria-labelledby="heading-<?php echo $key;?>" data-parent="#accordionhelp">
          <div class="card-body bg-primary text-white">
            <?php echo $item['desc'];?>
          </div>
        </div>
      </div>
    <?php endforeach;?>
    </div>
  </div>
  <?php endif;?>


</div>


<?php
if (_cao('is_single_entry_page',true)) {
  get_template_part( 'template-parts/content/entry-navigation' );
}
if (_cao('related_posts_item_style','list') != 'none') {
  get_template_part( 'template-parts/global/related-posts');
}
?>

