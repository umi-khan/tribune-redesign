<?php

interface IDisplayable_control
{
	public function display($args = array());
}

class Control_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_image_large;
	protected $_video;
	protected $_author_link;

	public function __construct(LM_story $story)
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );
		$this->_image  = $image_manager->default_image->thumbnail;
		$this->_image_large  = $image_manager->default_image->large;
		
		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id, false );
			$this->_video  = $video_manager->default_video;
		}		
		$this->_author_link = SM_author_manager::get_author_posts_link( $this->_story->id );
	}

	protected function populate_data($args = array())
	{
		$default_args = array(
			'main_story'   => false,
			'show_related' => false,
			'is_first'     => false,
			'is_last'      => false
		);

		$args = wp_parse_args( $args, $default_args );

		$data = array(
			'story'           => $this->_story,
			'author'          => $this->_author_link,
			'image'           => $this->_image,
			'image_large'     => $this->_image_large,
			'arguments'       => $args
		);

		if( $this->_video ) $data['video'] = $this->_video;

		if( $args['show_related'] )
			$data['related_stories'] = Express_related_posts::get_related_posts( $this->_story->id, 2 );

		return $data;
	}

	public function display($args = array())
	{
		$data = $this->populate_data( $args );


		get_control( 'story', $data );
	}
}

class Control_special_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_video;
	protected $_author_link;

	public function __construct(LM_story $story)
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );
		$this->_image  = $image_manager->default_image->large;
		
		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id, false );
			$this->_video  = $video_manager->default_video;
		}			

		$this->_author_link = SM_author_manager::get_author_posts_link( $this->_story->id );
	}

	protected function populate_data($args = array())
	{
		$default_args = array( 'show_related' => true );

		$args = wp_parse_args( $args, $default_args );

		$data = array(
			'story'           => $this->_story,
			'author'          => $this->_author_link,
			'image'           => $this->_image,
			'arguments'       => $args
		);

		if( $this->_video ) $data['video'] = $this->_video;
		
		if( $args['show_related'] )
			$data['related_stories'] = Express_related_posts::get_related_posts( $this->_story->id, 4 );

		return $data;
	}

	public function display($args = array())
	{
		$data = $this->populate_data( $args );

		get_control( 'special-story', $data );
	}
}

class Control_top_story extends Control_story
{
	public function __construct(LM_story $story)
	{
		parent::__construct( $story );
	}

	public function display($args = array())
	{
		$args = wp_parse_args( $args, array( 'show_related' => true ) );

		parent::display( $args );
	}
}

class Control_main_story extends Control_story
{
	public function __construct(LM_story $story)
	{
		parent::__construct( $story );
	}

	public function display($args = array())
	{
		$args = wp_parse_args( $args, array( 'main_story' => true, 'show_related' => true ) );

		parent::display( $args );
	}
}

class Control_picture_story implements IDisplayable_control
{
	protected $_story;
	protected $_category;
	protected $_image;
	protected $_video;
	protected $_author;

	protected $_show_excerpt;
	protected $_show_meta;

	public function __construct(LM_story $story, $show_excerpt = true, $show_meta = true)
	{
		$this->_story = $story;

		if( is_category() )
		{
			global $wp_query;
			$this->_category = $wp_query->get_queried_object();
		}
		else
		{
			$this->_category = array_shift( get_the_category( $this->_story->id ) );
			if($this->_category->parent) $this->_category->parent = get_category($this->_category->parent);
		}

		$image_manager = new IM_Manager( $this->_story->id, false );		
		$this->_image  = $image_manager->default_image->large;		
		
		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id, false );
			$this->_video  = $video_manager->default_video;
		}
		
		$this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );

		$this->_show_excerpt = $show_excerpt;
		$this->_show_meta    = $show_meta;
	}

	public function display($args = array(), $isreturn=0, $slideshowdata= array())
	{
		$args = wp_parse_args( $args, array( 'show_excerpt' => $this->_show_excerpt, 'show_meta' => $this->_show_meta ) );
		$data = array(
			'story'     => $this->_story,
			'category'  => $this->_category,
			'author'    => $this->_author,
			'image'     => $this->_image,
			'arguments' => $args
		);
		
		if( $this->_video ) $data['video'] = $this->_video;

		if ( $isreturn==0 ) 
			 get_control( 'picture-story', $data );
		if ( $isreturn==1)
			 return $data; 
		if ( $isreturn==2 ) 
			 get_control( 'picture-story',null, $slideshowdata );
	}
}

class Control_small_picture_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_video;
	protected $_category;

	protected $_show_category_title;
	protected $_show_title_on_top;
	protected $_show_title_on_bottom;
	
	public function __construct(LM_story $story, $category, $show_category_title = false, $show_title_on_top = true,
			  $show_title_on_bottom = false)
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );
		$this->_image  = $image_manager->default_image->thumbnail;

		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id, false );
			$this->_video  = $video_manager->default_video;
		}
		$this->_category = $category;

		$this->_show_category_title  = $show_category_title;
		$this->_show_title_on_top    = $show_title_on_top;
		$this->_show_title_on_bottom = $show_title_on_bottom;
	}

	public function display($args = array())
	{
		$args = array(
			'show_category_title'  => $this->_show_category_title,
			'show_title_on_top'    => $this->_show_title_on_top,
			'show_title_on_bottom' => $this->_show_title_on_bottom
		);

		$data = array(
			'story'     => $this->_story,
			'image'     => $this->_image,
			'category'  => $this->_category,
			'arguments' => $args
		);

		if( false !== $this->_video ) $data['video'] = $this->_video;

		get_control( 'small-picture-story', $data );
	}
}

class Control_long_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_video;
    protected $_author;
   
    protected $_show_excerpt;
    protected $_show_meta;
    protected $_show_time;
    protected $_show_author;
   
   public function __construct(LM_story $story, $show_excerpt = false, $show_meta = false, $show_time = false, $show_author = false )
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );      
		$this->_image  = $image_manager->default_image->thumbnail;		
		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id );         
			$this->_video  = $video_manager->default_video;
		}
                  
		$this->_show_excerpt = $show_excerpt;
		$this->_show_meta    = $show_meta;
      $this->_show_author  = $show_author;
      if( false !== $this->_show_meta && false !== $this->_show_author ) $this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );      
      $this->_show_time    = $show_time;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );

		$args['show_excerpt'] = $this->_show_excerpt;
		$args['show_meta']    = $this->_show_meta;
      $args['show_time']    = $this->_show_time;
      $args['show_author']  = $this->_show_author;
      
		$data = array(
			'story'     => $this->_story,
			'image'     => $this->_image,
			'arguments' => $args
		);
		if( false !== $this->_show_meta ) $data['author'] = $this->_author;        
		if( false !== $this->_video ) $data['video'] = $this->_video;
		get_control( 'long-story', $data );
	}
}


class Control_long_pic_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_image_thumb;
	protected $_video;
    protected $_author;
   
    protected $_show_excerpt;
    protected $_show_meta;
    protected $_show_time;
    protected $_show_author;
   
   public function __construct(LM_story $story, $show_excerpt = false, $show_meta = false, $show_time = false, $show_author = false )
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );      
		$this->_image  = $image_manager->default_image->large;		
		$this->_image_thumb  = $image_manager->default_image->thumbnail;	
		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id );         
			$this->_video  = $video_manager->default_video;
		}
                  
		$this->_show_excerpt = $show_excerpt;
		$this->_show_meta    = $show_meta;
      $this->_show_author  = $show_author;
      if( false !== $this->_show_meta && false !== $this->_show_author ) $this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );      
      $this->_show_time    = $show_time;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );

		$args['show_excerpt'] = $this->_show_excerpt;
		$args['show_meta']    = $this->_show_meta;
      $args['show_time']    = $this->_show_time;
      $args['show_author']  = $this->_show_author;
      
		$data = array(
			'story'     	=> $this->_story,
			'image'     	=> $this->_image,
			'image_thumb' 	=> $this->_image_thumb,
			'arguments' 	=> $args
		);
		if( false !== $this->_show_meta ) $data['author'] = $this->_author;        
		if( false !== $this->_video ) $data['video'] = $this->_video;
		get_control( 'long-pic-story', $data );
	}
}

class Control_trend_pic_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_image_thumb;
	protected $_video;
    protected $_author;
   
    protected $_show_excerpt;
    protected $_show_meta;
    protected $_show_time;
    protected $_show_author;
   
   public function __construct(LM_story $story, $show_excerpt = false, $show_meta = false, $show_time = false, $show_author = false )
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );      
		$this->_image  = $image_manager->default_image->large;		
		$this->_image_thumb  = $image_manager->default_image->thumbnail;	
		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id );         
			$this->_video  = $video_manager->default_video;
		}
                  
		$this->_show_excerpt = $show_excerpt;
		$this->_show_meta    = $show_meta;
      $this->_show_author  = $show_author;
      if( false !== $this->_show_meta && false !== $this->_show_author ) $this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );      
      $this->_show_time    = $show_time;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );

		$args['show_excerpt'] = $this->_show_excerpt;
		$args['show_meta']    = $this->_show_meta;
      $args['show_time']    = $this->_show_time;
      $args['show_author']  = $this->_show_author;
      
		$data = array(
			'story'     	=> $this->_story,
			'image'     	=> $this->_image,
			'image_thumb' 	=> $this->_image_thumb,
			'arguments' 	=> $args
		);
		if( false !== $this->_show_meta ) $data['author'] = $this->_author;        
		if( false !== $this->_video ) $data['video'] = $this->_video;
		get_control( 'trend-pic-story', $data );
	}
}
class Control_trending_small_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_video;
	protected $_author;

	protected $_show_excerpt;

	public function __construct(LM_story $story, $show_excerpt = false)
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );
		$this->_image  = $image_manager->default_image->thumbnail;

		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id );
			$this->_video  = $video_manager->default_video;
		}
		
		$this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );

		$this->_show_excerpt = $show_excerpt;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );
		
		$args['show_excerpt'] = $this->_show_excerpt;

		$data = array(
			'story'     => $this->_story,
			'author'    => $this->_author,
			'image'     => $this->_image,
			'arguments' => $args
		);

		if( $this->_video ) $data['video'] = $this->_video;
		
		get_control( 'trend-small-story', $data );
	}
}

class Control_opinion_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_video;
	protected $_author;

	protected $_show_excerpt;

	public function __construct(LM_story $story, $show_excerpt = false)
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );
		$this->_image  = $image_manager->default_image->thumbnail;

		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id );
			$this->_video  = $video_manager->default_video;
		}
		
		$this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );

		$this->_show_excerpt = $show_excerpt;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );
		
		$args['show_excerpt'] = $this->_show_excerpt;

		$data = array(
			'story'     => $this->_story,
			'author'    => $this->_author,
			'image'     => $this->_image,
			'arguments' => $args
		);

		if( $this->_video ) $data['video'] = $this->_video;
		
		get_control( 'opinion-story', $data );
	}
}

class Control_small_story implements IDisplayable_control
{
	protected $_story;
	protected $_image;
	protected $_video;
	protected $_author;

	protected $_show_excerpt;

	public function __construct(LM_story $story, $show_excerpt = false)
	{
		$this->_story = $story;

		$image_manager = new IM_Manager( $this->_story->id, false );
		$this->_image  = $image_manager->default_image->thumbnail;

		if( false === $image_manager->has_images() )
		{
			$video_manager = new VM_Manager( $this->_story->id );
			$this->_video  = $video_manager->default_video;
		}
		
		$this->_author = SM_author_manager::get_author_posts_link( $this->_story->id );

		$this->_show_excerpt = $show_excerpt;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );
		
		$args['show_excerpt'] = $this->_show_excerpt;

		$data = array(
			'story'     => $this->_story,
			'author'    => $this->_author,
			'image'     => $this->_image,
			'arguments' => $args
		);

		if( $this->_video ) $data['video'] = $this->_video;
		
		get_control( 'small-story', $data );
	}
}

class Control_author_story implements IDisplayable_control
{
	protected $_story;
	protected $_author;
	protected $_author_link;

	protected $_show_excerpt;
	protected $_show_comments;

	public function __construct(LM_story $story, $show_excerpt = true, $show_comments = false)
	{
		$this->_story = $story;

		$author_manager = new SM_author_manager( $this->_story->id );
		$this->_author  = array_shift( $author_manager->authors );

		$this->_author_link = SM_author_manager::get_author_posts_link( $this->_story->id );

		$this->_show_excerpt = $show_excerpt;
		$this->_show_comments = $show_comments;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );
		
		$args['show_excerpt'] = $this->_show_excerpt;
		$args['show_comments'] = $this->_show_comments;

		$data = array(
			'story'       => $this->_story,
			'author'      => $this->_author,
			'author_link' => $this->_author_link,
			'arguments'   => $args
		);

		get_control( 'author-story', $data );
	}
}

class Control_text_story implements IDisplayable_control
{
	protected $_story;
	protected $_show_excerpt;

	public function __construct(LM_story $story, $show_excerpt = false)
	{
		$this->_story        = $story;
		$this->_show_excerpt = $show_excerpt;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );
		
		$args['show_excerpt'] = $this->_show_excerpt;

		$data = array(
			'story'     => $this->_story,
			'arguments' => $args
		);

		get_control( 'text-story', $data );
	}
}


class Control_more_story implements IDisplayable_control
{
	protected $_story;
	protected $_show_excerpt;

	public function __construct(LM_story $story, $show_excerpt = false)
	{
		$this->_story        = $story;
		$this->_show_excerpt = $show_excerpt;
	}

	public function display($args = array())
	{
		$default_args = array( 'is_first' => false, 'is_last' => false );
		$args         = wp_parse_args( $args, $default_args );
		
		$args['show_excerpt'] = $this->_show_excerpt;

		$data = array(
			'story'     => $this->_story,
			'arguments' => $args
		);

		get_control( 'more-text-story', $data );
	}
}

class Control_category_story extends Control_story
{
	protected $_category;

	public function __construct(LM_story $story, $category)
	{
		$this->_category = $category;

		parent::__construct( $story );
	}

	protected function populate_data($args = array())
	{
		$args['show_related'] = true;

		$data = parent::populate_data( $args );

		$data['category'] = $this->_category;

		return $data;
	}

	public function display($args = array())
	{
		$data = $this->populate_data( $args );

		get_control( 'category-story', $data );
	}
}

class Control_comments implements IDisplayable_control
{
	private $_comment;
	private $_story;
	private $_author;
	
	public function __construct($comment)
	{
		$this->_comment = $comment;

		$this->_comment->comment_content = LM_story::trim_content( $this->_comment->comment_content,
				  LM_config::STORY_EXCERPT_LENGTH );

		$this->_story   = LM_story::get_story( $this->_comment->comment_post_ID );
		$this->_author  = get_comment_author_link( $this->_comment->comment_ID );
	}

	protected function populate_data($args = array())
	{
		$default_args = array(
			'is_first'     => false,
			'is_last'      => false,
			'show_excerpt' => false
		);

		$args = wp_parse_args( $args, $default_args );

		$data = array(
			'story'     => $this->_story,
			'comment'   => $this->_comment,
			'author'    => $this->_author,
			'arguments' => $args
		);

		return $data;
	}

	public function display($args = array())
	{
		$data = $this->populate_data( $args );

		get_control( 'comment', $data );
	}
}

class Control_popular_stories implements IDisplayable_control
{
	const DEFAULT_NUM_STORIES = 5;

	protected $_stories;

	public function __construct($category, $num_stories = self::DEFAULT_NUM_STORIES)
	{
		$popular_stories = pp_get_popular_posts( $category, $num_stories );
		foreach( (array)$popular_stories as $story )
			$this->_stories[] = new Control_text_story( LM_story::get_story( $story ) );
	}

	protected function populate_data($args = array())
	{
		$default_args = array(
			'is_first'     => false,
			'is_last'      => false
		);

		$args = wp_parse_args( $args, $default_args );

		$data = array(
			'stories'   => $this->_stories,
			'arguments' => $args
		);

		return $data;
	}

	public function display($args = array())
	{
		$data = $this->populate_data( $args );

		get_control( 'popular-stories', $data );
	}
}