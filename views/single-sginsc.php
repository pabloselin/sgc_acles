<?php 
//single-sginsc_psu2014.php
?>
<?php get_header();?>
<section id="content">	

	<div class="container">
		<div class="row">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	

	<article id="article-<?php echo $post->ID;?>" <?php post_class('span6 offset1');?> >		
				<?php 
				if(has_post_thumbnail( )){
					$bgheader = get_post_thumbnail_id( );
					$bgheadersrc= wp_get_attachment_image_src( $bgheader, 'full');
					}
				?>
				<header>
				<?php 
						$ptype = get_post_type_object( get_post_type() );
					?>
					<span class="ptype"><?php echo $ptype->labels->name; ?></span> 											
					<h1><?php the_title();?></h1>
				</header>
				
				<div class="container">
					<div class="row">
						<div class="text-content span7">
							<?php the_content();?>

							<?php if(get_post_meta($post->ID, 'sgcinsc_urlpsu', true)):?>							
								<iframe src="<?php echo get_post_meta($post->ID, 'sgcinsc_urlpsu', true);?>?embedded=true" width="100%" height="1200" frameborder="0" marginheight="0" marginwidth="0">Cargando...</iframe>
							<?php endif;?>
						</div>
					</div>
				</div>
			</article>

	<?php endwhile;
		else: ?>
	<p>
		<?php _e('Sorry, no posts matched your criteria.'); ?>
	</p>
	<?php endif; ?>

	<aside class="related span3 offset1">			
		 <?php 
//Sidebar PSU 2014
?>
<h4><i class="icon-angle-right"></i> Más Preuniversitario PSU 2014</h4>
<?php
	$lastposts = get_posts('post_type=sginsc_psu2014&numberposts=10');
	foreach($lastposts as $lastpost){	
?>

			<article class="noticia-aside block-article">				
				<a href="<?php echo get_permalink($lastpost->ID);?>">					
					<h2> <?php echo $lastpost->post_title;?></h2>					
				</a>	
			</article> 

	<?php } ?>
	</aside>

</div>
</div>

</section>
<?php get_footer();?>