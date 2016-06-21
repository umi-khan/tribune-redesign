<?php
/*
 * This script shall only be run if there is a need to shift from layout manager to layout management.
 */

if(current_user_can('level_10'))
{
	set_time_limit(0);
	
	global $wpdb;

	$table_name = $wpdb->prefix.'layout_management';
	
	$templates_to_groups = array(
	
		1	=>	array(
			'group' => 0,
			'position' => 0
			),
		2	=>	array(
			'group' => 0,
			'position' => 1
			),
		3	=>	array(
			'group' => 0,
			'position' => 2
			),
		4	=>	array(
			'group' => 0,
			'position' => 3
			),
		5	=>	array(
			'group' => 0,
			'position' => 4
			),
		6	=>	array(
			'group' => 0,
			'position' => 5
			),
		7	=>	array(
			'group' => 0,
			'position' => 6
			),
		8	=>	array(
			'group' => 0,
			'position' => 7
			),
		9	=>	array(
			'group' => 0,
			'position' => 8
			),
		10	=>	array(
			'group' => 0,
			'position' => 9
			),
		11	=>	array(
			'group' => 0,
			'position' => 10
			),
		12	=>	array(
			'group' => 0,
			'position' => 11
			),
		13	=>	array(
			'group' => 0,
			'position' => 12
			),
		14	=>	array(
			'group' => 0,
			'position' => 13
			),
		15	=>	array(
			'group' => 0,
			'position' => 14
			),
		30	=>	array(
			'group' => 1,
			'position' => 0
			),
		31	=>	array(
			'group' => 1,
			'position' => 1
			),
		32	=>	array(
			'group' => 1,
			'position' => 2
			),
		33	=>	array(
			'group' => 1,
			'position' => 3
			),
		34	=>	array(
			'group' => 1,
			'position' => 4
			),
	
	);
	
	$categories_args = array('hide_empty' => false);
	$all_categories	=	get_categories($categories_args);
	$home_category = new stdClass();
	$home_category->cat_ID = 0;
	array_unshift($all_categories, $home_category );

	$num_records = 0;
	$num_inserted_records = 0;
	$num_failed_records = 0;
	$failures = array();
	
	if(is_array($all_categories))
	{
		foreach($all_categories as $category)
		{
			$category_Id = $category->cat_ID;
			$get_layout_query = 'SELECT * FROM '.$wpdb->prefix.'lm_layout WHERE category_id='.$category_Id ;
			$layouts = $wpdb->get_results($get_layout_query);
			
			if(is_array($layouts))
			{
				foreach($layouts as $layout)
				{
					$num_records++;

					$group_id	=	$templates_to_groups[$layout->template_id]['group'];
					$position	=	$templates_to_groups[$layout->template_id]['position'];
					$post_id		=	$layout->postid;
	
					$insert_query = 'INSERT into '.$table_name.' (post_id, category_id, group_id, `position`)
											VALUES (%d, %d, %d, %d)';
	
					$insert_query = $wpdb->prepare($insert_query, $post_id, $category_Id, $group_id,	$position);
	
					if($wpdb->query($insert_query))
					{
						$num_inserted_records++;
					}
					else
					{
						$num_failed_records++;
						$failures[] = $insert_query;
					}
				}
			}
		}
	}
	
}

?>

<html>
	<head>
		<title>Layout Management Transfer Records Script</title>
	</head>

	<body>
		<h2>Statistics:</h2>
		<p>
			Total records to transfer: <?php echo $num_records; ?> <br />
			Number of records transfered: <?php echo $num_inserted_records; ?> <br />
			Number of records failed: <?php echo $num_failed_records; ?> <br />
		</p>

		<?php if(is_array($failures) && !empty($failures)) : ?>
		<h3>Failures:</h3>

		<?php foreach($failures as $failure) : ?>

			<p style="color:red;">
				<?php echo $failure; ?>
			</p>

		<?php endforeach; ?>

		<?php endif; ?>
	</body>
</html>