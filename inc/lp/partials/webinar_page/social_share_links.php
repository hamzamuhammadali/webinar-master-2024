<?php defined( 'ABSPATH' ) || exit; ?>
<div class="webinarShare">
		
			<div class="webinarShareCopy" style="color: <?php webinarignition_display( $webinar_data->webinar_invite_color, "#222" ); ?>;">
				<i class="icon-user"></i> <?php webinarignition_display( $webinar_data->webinar_invite, __( "Invite Your Friends To The Webinar:", "webinarignition") ); ?>
			</div>
			<div class="webinarShareIcons wi-block--sharing">

				<!-- Facebook Share	Button -->
				<?php if ( $webinar_data->webinar_fb_share != "off" ) : ?>
					<div style="position: relative; float: left; min-height: 20px; width: 60px; margin-right: 15px;">
						<div class="fb-like"
							 data-href="<?php echo $webinar_data->webinar_permalink; ?>"
							 style="position: absolute; top: -2px; left: 0;"
							 data-layout="button_count"
							 data-width="60"
							 data-show-faces="false">
						</div>
					</div>
				<?php endif ?>

				<!-- Twitter Share Button -->
				<?php if ( $webinar_data->webinar_tw_share != "off" ) : ?>
					<div style="position: relative; float: left; min-height: 20px; width: 60px; margin-right: 15px;">
						<a
							href="https://twitter.com/share"
							data-url="<?php echo $webinar_data->webinar_permalink; ?>"
						   	class="twitter-share-button"
						>Tweet
						</a>
					</div>
				<?php endif ?>

				<!-- Linkedin Share Button -->
				<?php if ( $webinar_data->webinar_ld_share != "off" ) : ?>
					<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
					<script type="IN/Share" data-url="<?php echo $webinar_data->webinar_permalink; ?>" data-counter="right"></script>
				<?php endif ?>
			</div>
		
	</div>