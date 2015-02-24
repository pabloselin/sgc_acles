<?php 
//single-sginsc_psu2014.php
?>
<?php get_header();?>
<section id="content">	

	<div class="container">

		<h2>Inscripciones Preuniversitario SGC 2014</h2>


		<div class="text-content intropsu2014">
		<?php 
			$intropost = 26194;
			$getintropost = get_post($intropost);
			echo apply_filters('the_content', $getintropost->post_content );
		?>

		<br/>
		<br/>
		</div>

		<div class="row">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>	

	<a class="blockinsc span3" href="<?php the_permalink();?>">				
		<div class="padbox">					
			<i class="icon-file-text"></i>
			<h2><?php the_title();?></h2>
		</div>	
	</a>

	<?php endwhile;
		else: ?>
	<p>
		<?php _e('Sorry, no posts matched your criteria.'); ?>
	</p>
	<?php endif; ?>

	<aside class="related span3 offset1">			
		 
	</aside>

</div>
</div>

</section>
<?php get_footer();?>