<?php
		// filter week 
		
		 if ( $instance['week'] ) :
		 
		 $week = date('W'); // affiche les articles de la semaine en cour
		
		endif;
			
		// filter date 7 days 
		
		 if ( $instance['7_days'] ) :
		 
		 function filter_where_spmw($where = '') {
    // Posts in the last 7 days
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
    return $where;
  }
add_filter('posts_where', 'filter_where_spmw');
		
		endif;
		// filter date 15 days 
		
		 if ( $instance['15_days'] ) :
		 
		 function filter_where_spmw($where = '') {
    // Posts in the last 15 days
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-15 days')) . "'";
    return $where;
  }
add_filter('posts_where', 'filter_where_spmw');
		
		endif;
// filter date 30 days 
		
		 if ( $instance['30_days'] ) :
		 
		 function filter_where_spmw($where = '') {
    // Posts in the last 30 days
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-30 days')) . "'";
    return $where;
  }
add_filter('posts_where', 'filter_where_spmw');
		
		endif;						
?>