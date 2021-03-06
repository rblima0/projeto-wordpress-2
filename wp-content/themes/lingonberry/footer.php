<?php if ( is_active_sidebar( 'footer-a' ) || is_active_sidebar( 'footer-b' ) || is_active_sidebar( 'footer-c' ) ) : ?>

	<div class="footer section">
		
		<div class="footer-inner section-inner">
		
			<?php if ( is_active_sidebar( 'footer-a' ) ) : ?>
			
				<div class="footer-a widgets">
			
					<?php dynamic_sidebar( 'footer-a' ); ?>
					
					<div class="clear"></div>
					
				</div>
				
			<?php endif; ?> <!-- /footer-a -->
				
			<?php if ( is_active_sidebar( 'footer-b' ) ) : ?>
			
				<div class="footer-b widgets">
			
					<?php dynamic_sidebar( 'footer-b' ); ?>
					
					<div class="clear"></div>
					
				</div>
							
			<?php endif; ?> <!-- /footer-b -->
								
			<?php if ( is_active_sidebar( 'footer-c' ) ) : ?>
			
				<div class="footer-c widgets">
			
					<?php dynamic_sidebar( 'footer-c' ); ?>
					
					<div class="clear"></div>
					
				</div>
				
			<?php endif; ?> <!-- /footer-c -->
			
			<div class="clear"></div>
		
		</div> <!-- /footer-inner -->
	
	</div> <!-- /footer -->

<?php endif; ?>

<div class="credits section">

	<div class="credits-inner section-inner">

		<p class="credits-left">
		
			<span><?php _e('Copyright', 'lingonberry'); ?></span> &copy; <?php echo date("Y") ?> <a href="<?php echo home_url(); ?>/" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
		
		</p>
		
		<p class="credits-right">
			
			<span><?php printf( __( 'Theme by <a href="%s">Anders Noren</a>', 'lingonberry'), 'http://www.andersnoren.se' ); ?> &mdash; </span><a title="<?php _e('To the top', 'lingonberry'); ?>" class="tothetop"><?php _e('Up', 'lingonberry' ); ?> &uarr;</a>
			
		</p>
		
		<div class="clear"></div>
	
	</div> <!-- /credits-inner -->
	
</div> <!-- /credits -->

<?php wp_footer(); ?>

</body>
</html>