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
					<span class="date"><?php echo get_the_time( get_option( 'date_format' ), $lastpost->ID ); ?></span>
					<h2> <?php echo $lastpost->post_title;?></h2>
					<div class="excerpt">
						<p>
							<?php sgc_excerpt($lastpost->ID, 14);?>
						</p>						
					</div>
				</a>	
			</article> 

	<?php } ?>