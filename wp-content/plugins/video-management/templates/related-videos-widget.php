<?php
for ( $counter = 0 ; $counter < $post_videos_count ; $counter++ ) :
    if ($post_videos->videos[$counter]->id != $video_to_hide) :
        $video_link = $video_category_link . $post_videos->videos[$counter]->id . '/';
        $video_title = esc_html(VM_manager::word_break(VM_manager::trim_content(html_entity_decode($post_videos->videos[$counter]->title, ENT_QUOTES), 56)));
                    
        $class = $counter == 0 ? ' first' : ( $counter == $post_videos_count-1 ? ' last' : '' );?>
        <div class="video<?php echo $class; ?>">
            <a class="image" href="<?php echo $video_link; ?>">
                <img src="<?php echo $post_videos->videos[$counter]->thumbnail->url ?>" width="<?php echo $thumbnail_width;?>" height="<?php echo $thumbnail_height;?>" alt="" />
            </a>
            <p class="excerpt">
                <a href="<?php echo $video_link; ?>"><?php echo $video_title; ?></a>
                <?php edit_post_link('<img class="video_edit_img" src="' . VM_PLUGIN_URL . 'images/edit.png"  alt="" />', '', '', $video->parent_id); ?>               
            </p>
        </div>
        <?php
    endif;
endfor;