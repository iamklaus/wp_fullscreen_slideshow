<!doctype html>
<html lang="en">

<head>
    <title>VELUX Screens</title>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="600">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <style>

        body {
            font-size: 40px;
            text-shadow: black 1px 0 10px;
        }

        h1 {
            font-size: 90px;
        }
        
        .carousel-caption {
            text-align: center;
            vertical-align: middle;
        }

        .carousel-item {
            height: 100vh;
            background: no-repeat center center scroll;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .carousel-fade .carousel-item.active,
        .carousel-fade .carousel-item-next.carousel-item-left,
        .carousel-fade .carousel-item-prev.carousel-item-right {
            opacity: 1;
        }

        .carousel-fade .active.carousel-item-left,
        .carousel-fade .active.carousel-item-right {
            opacity: 0;
        }

        .carousel-fade .carousel-item-next,
        .carousel-fade .carousel-item-prev,
        .carousel-fade .carousel-item.active,
        .carousel-fade .active.carousel-item-left,
        .carousel-fade .active.carousel-item-prev {
            transform: translateX(0);
            transform: translate3d(0, 0, 0);
        }
    </style>
</head>

<body style="background-color: black;">

<div id="veluxScreenCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="3000">
    <div class="carousel-inner">

      <?php
      $first = true;

      if ( have_posts() ) :

        while ( have_posts() ) : the_post();

            $notext = false;
            $post_thumbnail_id = get_post_thumbnail_id( $post );
            if($post_thumbnail_id) $images = [$post_thumbnail_id];
            else $images = [];

            if($gallery = get_post_gallery( get_the_ID(), false )) {
                $gallery_ids = explode(",", $gallery['ids']);
                $images = array_merge($images, $gallery_ids);
            }
		
	        shuffle($images);
            $tags = get_the_tags();

            if($tags) {
                foreach($tags as $tag):
	                if($tag->name == "notext") { $notext = true; }
                endforeach;
            }
 		
	        foreach($images as $image):
            ?>
              <div class="carousel-item <?php if($first) { echo " active "; $first=false; }   ?>" style="background-image: url('<?php echo wp_get_attachment_image_url($image, $size = 'full'); ?>'); height: 100vh; background-position:center;">
              <?php if ( $notext == false ) : ?>
                  <div class="carousel-caption">
                      <h1><?php the_title(); ?> </h1>
                      <p><?php echo strip_shortcodes(wp_trim_words( get_the_content(), 80 )); ?></p>
                  </div>
              <?php endif; ?> 
              </div>

            <?php
            endforeach;
        endwhile;
      endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>
</html>
