<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

global $video;

$video_id = (int) get_query_var( 'post_id' );

if( $video_id )
{
	$video = new VM_video( $video_id );
	$story = LM_story::get_story( $video->parent_id );
}

if( false == $video_id || false == $video->parent_id )
{
	$video = @array_pop( VM_manager::get_latest_by_category( 0, 0, 1 ) );
	$story = LM_story::get_story( $video->parent_id );
}

// video permalink
$video_category_id   = get_category_by_slug( 'videos' )->cat_ID;
$video_category_link = get_category_link( $video_category_id );
$video_permalink     = $video_category_link . $video->id . '/';

get_header();
?>          
		<?php if( $video ) : ?>
		<div class="videos">
		<div class="story" id="id-<?php echo $video->parent_id; ?>">
			<h1 class="title">
				<a href='<?php echo $story->permalink; ?>'><?php echo $video->title;?></a>
			</h1>
			
			<?php $video->player(620,349); ?>

			<div class="social">
				<h4>Share this page</h4>
				 <div class="content clearfix">
					<div class="buttons">
						<?php
							exp_single_addthis_button( $video->id , $video_permalink , $video->title, false );
						?>
						<div class="fb-link">

<div class="fb-like" data-href="<?php echo $video_permalink; ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="true"></div>	
<!--
<fb:like colorscheme='evil' href='<?php echo $video_permalink; ?>' layout='button_count' show_faces='false' width='100px'></fb:like>
-->
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
			if( function_exists( 'exp_load_widget_file' ) )
			{
				$data = array( 'pagination' => true, 'limit' => 8 );
				exp_load_widget_file( "videos-gallery", $data );
			}
		?>
		</div>
</div>