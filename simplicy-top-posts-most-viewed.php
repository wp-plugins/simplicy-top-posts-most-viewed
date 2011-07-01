<?php
/*
 * Plugin Name: Simplicy Top Posts Most Viewed
 * Version: 1.0
 * Plugin URI: http://www.naxialis.com
 * Description: Afficher vos article les plus consulter dans votre sidebar.
 * Author: Naxialis
 * Author URI: http://www.naxialis.com
 */
class Widget_simplicy_top_post_viewed extends WP_Widget  //class /!\
{
	function Widget_simplicy_top_post_viewed() 
	{		
		$widget_ops = array('classname' => 'Widget_simplicy_top_post_viewed', 'description' => __( "Afficher et personnaliser l&acute;affichage de vos articles dans la sidebar") );		
		$control_ops = array('width' => 350, 'height' => 300);		
		$this->WP_Widget('Widget_simplicy_top_post_viewed', __('Simplicy Top Posts Most Viewed'), $widget_ops, $control_ops);
	}

	function widget($args, $instance){
		extract($args);    
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']); 
		$vue = empty($instance['vue']) ? '&nbsp;' : $instance['vue'];
		$item = empty($instance['item']) ? '0' : $instance['item']; 
		$nb_posts = empty($instance['posts_nb']) ? '5' : $instance['posts_nb'] ; 
		$thumb_w = empty($instance['thumb_w']) ? null : $instance['thumb_w']; 
		$thumb_h = empty($instance['thumb_h']) ? null : $instance['thumb_h']; 
		$lenght = empty($instance['lenght']) ? null : $instance['lenght']; 
		
		
		// Find dropdown value categorie
    
       if(strpos($item, 'c:') !== FALSE) {
        $category = str_replace('c:', '', $item);
      } 
	 
      if($category != 0) {
        $data = spp_get_post_view('category', $category);
        $data = $data[0];
      } 
	  else {
        // If no post or category is selected, use the most recent post.
        $data = spp_get_post_view('0');
        $data = $data[0];
        if(!$data) {
          $title = "Simplicy Top Posts Most Viewed";
          $length = 100;
          $data = (object)array(
            'post_title' => 'Error!',
            'post_content' => 'This widget needs configuration',
          );
        }
    }
	
// fin categorie
		
		echo $before_widget;		
		if ( $title )
			echo $before_title . $title . $after_title;
						
		// Excerpt length filter
	$new_excerpt_length = create_function('$length', "return " . $instance["excerpt_length"] . ";");
	if ( $instance["excerpt_length"] > 0 )
		add_filter('excerpt_length', $new_excerpt_length);
		
		// affichage du widget
		
		if ($item != null) 
		{
			if (is_numeric($category))
			{
				query_posts(array(  'cat' => $category,  'orderby' => 'meta_value_num',  'order' => 'DESC',  'post_type' => 'post',  'post_status' => 'publish',  'posts_per_page' => $nb_posts,  'meta_key' => 'post_views_count', 'caller_get_posts'=> 1
));
			}
			
			else
			{
				query_posts(array(  'category_name' => $category,  'orderby' => 'meta_value_num',  'order' => 'DESC',  'post_type' => 'post',  'post_status' => 'publish',  'posts_per_page' => $nb_posts,  'meta_key' => 'post_views_count', 'caller_get_posts'=> 1
));
			}
			if (have_posts())
			{
				echo "<ul>" ;
				while (have_posts()) : the_post(); ?>
					<li style=" border-bottom:#CCCCCC dashed 1px;"> 
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(the_title()); ?>"><?php the_title(); ?> </a> <p><?php echo '<em>' ; ?><?php echo getPostViews (get_the_ID ());?> <?php echo $vue ; ?><?php echo '</em>' ; ?></p>
					
                    <!-- affichage de la miniature -->
                    <?php if ( $instance['view_thumbs'] ) : ?>
                    <a href="<?php the_permalink() ?>" >
						<?php global $post;
  						$thumb=vp_get_thumbs_url_view($post->post_content); 
  						if ($thumb!='') echo '<p style="float:left;"><img width="'.$thumb_w.'" height="'.$thumb_h.'" src="'.$thumb.'" alt="'. get_the_title().'" /></p>'; ?>
  					</a>
                    <?php endif; ?>
                    <!-- affichage de la miniature fin -->
                    <?php if ( $instance['excerpt'] ) : ?>
                    <?php the_excerpt(); ?> 
                    </li>
                    <?php endif; ?>
				<?php endwhile ;
				echo "</ul>" ;
			}
		}
		
		echo $after_widget;
	}
	
	

	function update($new_instance, $old_instance)
	{
		//on enregistre la variable 'titre'
		$instance = $old_instance; 		
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['vue'] = strip_tags(stripslashes($new_instance['vue']));
		//on enregistre la variable 'category'
		$instance['category'] = strip_tags(stripslashes($new_instance['category']));
		//on enregistre la variable 'posts'
		$instance['posts_nb'] = strip_tags(stripslashes($new_instance['posts_nb']));		
		$instance['thumb_w'] = strip_tags(stripslashes($new_instance['thumb_w']));		
		$instance['thumb_h'] = strip_tags(stripslashes($new_instance['thumb_h']));
		$instance['excerpt'] = strip_tags(stripslashes($new_instance['excerpt']));
		$instance['view_thumbs'] = strip_tags(stripslashes($new_instance['view_thumbs']));
		$instance['excerpt_length'] = strip_tags(stripslashes($new_instance['excerpt_length']));
		$instance['item'] = strip_tags(stripslashes($new_instance['item']));
		

		return $instance;
// the excerpt		
		remove_filter('excerpt_length', $new_excerpt_length);
	
	$post = $post_old; // Restore the post object.
	}
	

	function form($instance) {
		//les valeurs par défaut sont définies ici, par exemple 'posts'=>'5' défini le nombre de posts à afficher à 5 par défaut
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'category'=>'', 'posts'=>'5') );
		

		//on stocke les valeurs, en s'assurant qu'ils vont s'afficher correctement
		$title = htmlspecialchars($instance['title']);
		$vue = htmlspecialchars($instance['vue']);
		$category = htmlspecialchars($instance['category']);
		$posts_nb = htmlspecialchars($instance['posts_nb']);
		$thumb_w = htmlspecialchars($instance['thumb_w']);
		$thumb_h = htmlspecialchars($instance['thumb_h']);
		$item = htmlspecialchars($instance['item']);
		$post = htmlspecialchars($instance['post_id']);
	
		
		

		echo '<p style="text-align:left;"><label for="' . $this->get_field_name('title') . '">' . __('<p>Titre:</p>') . ' <input style="width: 250px;float:left;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>'; ?>
        
<?php // valeur a affivher pour les vues ?>
<?php
echo '<p style="text-align:left;"><label for="' . $this->get_field_name('vue') . '">' . __('<br /><p style="margin-bottom:10px;padding-bottom:0; padding-top:5px;">Nom de la valeur :</p><p style="font-size:9px;"><em>ex : vue(s) ,Visite(s), Lecture(s)</em></p>') . ' <input style="width: 250px;float:left;" id="' . $this->get_field_id('vue') . '" name="' . $this->get_field_name('vue') . '" type="text" value="' . $vue . '" /></label></p>'; ?>
        
		<?php //La catégorie ?>
<br /><br />           
     <p>
    <label style=" margin-bottom:0; padding-bottom:0;" for="<?php echo $this->get_field_name('item'); ?>"><?php echo __('Selectioner une catégorie: '); ?><p style="font-size:9px; padding-top:0; margin-top:0;"><em>Laissez le champ vide pour afficher toutes les catégories.</em></p></label>
    
    <select name="<?php echo $this->get_field_name('item'); ?>" id="<?php echo $this->get_field_id('item'); ?>">
          <option value="">  </option>
        <?php foreach(spp_get_dropdown_view() as $category) : ?>
          <option style="width: 225px;" <?php echo ('c:' . $category['category_id'] == $instance['item']) ? 'selected' : '' ?> value="c:<?php echo $category['category_id']; ?>">
          Catégorie: <?php echo $category['category_name']; ?> 
    
          </option>
      	<?php endforeach; ?>
    </select>
  </p>
  <?php
		//Le nombre de posts à montrer
		echo '<p style="text-align:left;"><label for="' . $this->get_field_name('posts_nb') . '">' . __('<p>Nombre d&acute;article à afficher:</p>') . ' <input style="width: 100px;float:left;" id="' . $this->get_field_id('posts') . '" name="' . $this->get_field_name('posts_nb') . '" type="text" value="' . $posts_nb . '" /></label></p><br /><br />'; ?>
	
	<?php // afficher extrait ?>    
<label for="<?php echo $this->get_field_id("excerpt"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("excerpt"); ?>" name="<?php echo $this->get_field_name("excerpt"); ?>"<?php checked( (bool) $instance["excerpt"], true ); ?> />
				<?php _e( 'Afficher un extrait' ); ?>
			</label>
		</p>
		<?php // longueur de l'article ?>
		<p>
			<label for="<?php echo $this->get_field_id("excerpt_length"); ?>">
				<?php _e( 'Longueur extrait (en mots):' ); ?>
			</label>
			<input style="text-align: center;" type="text" id="<?php echo $this->get_field_id("excerpt_length"); ?>" name="<?php echo $this->get_field_name("excerpt_length"); ?>" value="<?php echo $instance["excerpt_length"]; ?>" size="3" />
		</p>
        
<?php // afficher une vignette ?>    
<label for="<?php echo $this->get_field_id("view_thumbs"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("view_thumbs"); ?>" name="<?php echo $this->get_field_name("view_thumbs"); ?>"<?php checked( (bool) $instance["view_thumbs"], true ); ?> />
				<?php _e( 'Afficher une vignette' ); ?>
			</label>
    	
		<?php //dimention de la vignette ?>
		<p>
			<label>
				<?php _e('<p>Dimenssion de la vignette: </p>'); ?>
				<?php echo '<p style="text-align:left;"><label for="' . $this->get_field_name('thumb_w') . '">' . __('<p style="float:left;line-height:22px;">Largeur :</p>') . '<input style="width:20%;float:left;" id="' . $this->get_field_id('thumb_w') . '" name="' . $this->get_field_name('thumb_w') . '" type="text" value="' . $thumb_w . '" /></label></p>'; ?>
				</label>
           		<label>				
				<?php echo '<p style="text-align:left;"><label for="' . $this->get_field_name('thumb_h') . '">' . __('<p style="float:left;line-height:22px;padding-left:10px;">Hauteur :</p>') . '<input style="width:20%;float:left;" id="' . $this->get_field_id('thumb_h') . '" name="' . $this->get_field_name('thumb_h') . '" type="text" value="' . $thumb_h . '" /></label></p>'; ?>
				</label>
			
		</p>
        <br /> <br />

<?php

	}
}

// fonction compteur de visite 
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
	
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 Vue";
    }
	echo '<strong>' ;
    echo $count ;
	echo '</strong>' ;
}
function setSimplicyViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
// ******************************************************** fonction image ***************************************************************
 

function vp_get_thumbs_url_view($text)
{
  global $post;
 
  $imageurl="";        
 
  // extract the thumbnail from attached imaged
  $allimages =&get_children('post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );        
 
  foreach ($allimages as $img){                
     $img_src = wp_get_attachment_image_src($img->ID);
     break;                       
  }
 
  $imageurl=$img_src[0];
 
 
  // try to get any image
  if (!$imageurl)
  {
    preg_match('/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'>]*)/i' ,  $text, $matches);
    $imageurl=$matches[1];
  }
 
  // try to get youtube video thumbnail
  if (!$imageurl)
  {
    preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/watch(\?v\=|\/v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
 
    $youtubeurl = $matches2[0];
    if ($youtubeurl)
     $imageurl = "http://i.ytimg.com/vi/{$matches2[3]}/1.jpg"; 
   else preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/(v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
 
   $youtubeurl = $matches2[0];
   if ($youtubeurl)
     $imageurl = "http://i.ytimg.com/vi/{$matches2[3]}/1.jpg"; 
  }
 
 
return $imageurl;
}
// modification affichage excert (...)
function new_excerpt_more_view($excerpt) {
	return str_replace('[...]', '', $excerpt);
}
add_filter('wp_trim_excerpt', 'new_excerpt_more_view');


/**
 * Get all posts or all posts from a category
 */
function spp_get_all_posts_view($category = NULL) {
  global $wpdb;
  $query =
    "SELECT ID, post_title, post_content, post_date, post_status, guid, term_id
     FROM {$wpdb->posts}
     LEFT JOIN {$wpdb->term_relationships}
     ON object_id = ID
     LEFT JOIN {$wpdb->term_taxonomy}
     ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
     WHERE post_status = 'publish'";
     if($category != NULL) {
       $query .= " AND {$wpdb->term_taxonomy}.term_id = " . $category;
     }
     $query .= " AND post_type = 'post'
     GROUP BY ID
     ORDER BY post_date
     ;";
  $data = $wpdb->get_results($query);
  return $data;
}



/**
 * Select a specific post or the latest post from a category
 */
function spp_get_post_view($type, $selector = NULL) {
  global $wpdb;
  if($selector == NULL) {
    $data = $wpdb->get_results(
      "SELECT ID, post_title, post_content, post_excerpt, post_date, post_status, guid
       FROM {$wpdb->posts}
       LEFT JOIN {$wpdb->term_relationships}
       ON object_id = ID
       WHERE ID = (SELECT max(ID) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish')
       LIMIT 1;"
    );
  } else {
    switch($type) {
      case 'category':
        $data = $wpdb->get_results(
          "SELECT ID, post_title, post_content, post_excerpt, post_date, post_status, guid, term_id
           FROM {$wpdb->posts}
           LEFT JOIN {$wpdb->term_relationships}
           ON object_id = ID
           LEFT JOIN {$wpdb->term_taxonomy}
           ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
           WHERE term_id = $selector
           AND post_status = 'publish'
           ORDER BY post_date
           DESC LIMIT 1;"
        );
        break;

      case 'post':
        $data = $wpdb->get_results(
          "SELECT ID, post_title, post_content, post_excerpt, post_date, post_status, guid
           FROM {$wpdb->posts}
           LEFT JOIN {$wpdb->term_relationships}
           ON object_id = ID
           WHERE ID = $selector
           AND post_status = 'publish'
           LIMIT 1;"
        );
        break;
    }
  }
  return $data;
}

/**
 * Get all categories
 */
function spp_get_categories_view() {
  global $wpdb;
  $categories = $wpdb->get_results(
    "SELECT {$wpdb->terms}.term_id, name FROM {$wpdb->terms}
     LEFT JOIN {$wpdb->term_taxonomy}
     ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
     WHERE {$wpdb->term_taxonomy}.taxonomy = 'category'
     AND {$wpdb->term_taxonomy}.count > 0;"
  );
  return $categories;
}

function spp_get_dropdown_view() {
  $categories = spp_get_categories_view();
  $i = 0;
  foreach($categories as $category) {
    $posts = spp_get_all_posts_view($category->term_id);
    $select[$i]['category_name'] = $category->name;
    $select[$i]['category_id'] = $category->term_id;
    $j = 0;
    foreach($posts as $post) {
      $select[$i]['children'][$j]['post_name'] = $post->post_title;
      $select[$i]['children'][$j]['post_id'] = $post->ID;
      $j++;
    }
    $i++;
  }
  return $select;
}

function affichageCategorieInit_view() //donnez un nom qui vous parle -pas de prérequis
{
    register_widget('Widget_simplicy_top_post_viewed'); //le nom de la classe
}
add_action('widgets_init', 'affichageCategorieInit_view'); //le nom de la fonction définit juste au dessus
?>