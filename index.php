<!doctype html>
<html lang="en">

<head>

    <title><?php echo get_bloginfo( 'name' ); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="600">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <?php wp_head(); ?>

</head>

<body style="background-color: black;">

<div id="veluxScreenCarousel" class="carousel slide carousel-fade">
    <div class="carousel-inner">

        <?php
        $first = true;
        $trigger_timeout = false;

        $args = array(
            'post_type' => 'post',
            'orderby' => 'rand');

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                
                $query->the_post();
                $medias = getMedia($post); 

                foreach($medias as $media) { 
                    if($media['type'] == 'image') {
                        if($first) $trigger_timeout = true; ?>
                        <div class="carousel-item <?php if($first) { echo " active "; $first=false; }   ?>" id="<?=$media['uuid']; ?>" style="background-image: url('<?=$media['url'];?>'); height: 100vh; background-position:center;"> <?php
                    } elseif($media['type'] == 'video') { ?>
                        <div class="carousel-item <?php if($first) { echo " active "; $first=false; }  ?>" id="<?=$media['uuid'];?>" data-interval="<?=$media['length'];?>">
                            <video id="<?=$media['uuid'].'video' ?>" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover;" onended="videoEnded()">
                                <source src="<?=$media['url'];?>" type="<?=$media['mimetype']?>">                    
                            </video> <?php
                    } ?>

                        <div class="carousel-caption">
                            <h1><?=$media['title']?></h1>
                            <p><?=$media['subtitle'];?></p>
                        </div>
                    </div> <?php
                }
            }
        } 
        wp_reset_postdata(); ?>
    </div>
</div>

<script>

function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}

function videoEnded() {
    $('.carousel').carousel('next');
    console.log('Next trigger by video end');
}

$('#veluxScreenCarousel').on('slide.bs.carousel', function () {
    var carousel = document.querySelector('.carousel');
    var activeItem = carousel.querySelector('.carousel-item.active');
    var nextItem = activeItem.nextElementSibling;
    if (!nextItem) var nextItem = carousel.querySelector('.carousel-item:first-child');
    var nextItemId = nextItem.getAttribute('id');

    if(nextItemId.startsWith('video')) {
        var video = document.querySelector(nextItemId + "video");
        console.log(nextItemId + "video");
        $('#' + nextItemId + 'video').get(0).currentTime = 0;
        $('#' + nextItemId + 'video').get(0).play();
    } else {
        setTimeout(nextSlide, <?=get_option( 'iamklaus_fullscreenslidertheme_options_field', 5000 ); ?> + getRandomInt(500));
    } 
})

function nextSlide() {
    $('.carousel').carousel('next');
}

<?php if($trigger_timeout) echo "setTimeout(nextSlide, ".get_option( 'iamklaus_fullscreenslidertheme_options_field', 5000 )." + getRandomInt(500));"; ?>

</script>

<?php wp_footer(); ?>

</body>
</html>