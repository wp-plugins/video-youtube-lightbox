<?php

/*
Plugin Name: Video Youtube Lightbox
Plugin URI: https://github.com/ManuDavila/wp-video-youtube-lightbox
Description: Video Youtube Lightbox Widget. You can add your favorites Youtube videos in a playlist from the admin panel widgets and display it in a responsive lightbox with a single click.
Version: 1.0
Author: Manuel J. Dávila González
Author URI: https://github.com/ManuDavila
License: GPL2
*/

/*  Copyright 2015 Video Youtube Lightbox  (email : manudg_1@msn.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Video_Youtube_Lightbox extends WP_Widget {

	// constructor
	function __construct() {
		parent::__construct(
			'Video_Youtube_Lightbox',
			__( 'Video Youtube Lightbox' , 'video-youtube-lightbox'),
			array( 'description' => __( 'Video Youtube Lightbox Widget. You can add your favorites Youtube videos in a playlist from the admin panel widgets and display it in a responsive lightbox with a single click' , 'video-youtube-lightbox'))
		);
	}

	// widget form creation
	function form($instance) {
	
	// Check values
	if( $instance) {
		
		$title = esc_attr($instance['title']);
		$youtubetitle = esc_attr($instance['youtubetitle']);
		$youtubeurl = esc_attr($instance['youtubeurl']);
		$youtubelimit = esc_attr($instance['youtubelimit']);
	} 
	else 
	{
		
		$title = 'Youtube Playlist';
		$youtubetitle = '';
		$youtubeurl = '';
		$youtubelimit = 10;
	}
	
	if (empty($youtubelimit))
	{
		$youtubelimit = 10;
	}
	
	?>

	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id('youtubelimit'); ?>"><?php _e('Limit:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('youtubelimit'); ?>" name="<?php echo $this->get_field_name('youtubelimit'); ?>" type="number" value="<?php echo $youtubelimit ?>" min="0" />
	</p>

	<p><h3>Add Youtube Video:</p>
	
	<p>
	<label for="<?php echo $this->get_field_id('youtubetitle'); ?>"><?php _e('Video Title:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('youtubetitle'); ?>" name="<?php echo $this->get_field_name('youtubetitle'); ?>" type="text" value="<?php if (isset($_SESSION['youtubetitle'])){echo $_SESSION['youtubetitle'];} ?>" placeholder="Video Title ..." />
	</p>

	<p>
	<label for="<?php echo $this->get_field_id('youtubeurl'); ?>"><?php _e('Video URL:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('youtubeurl'); ?>" name="<?php echo $this->get_field_name('youtubeurl'); ?>" type="text" value="<?php if (isset($_SESSION['youtubeurl'])){echo $_SESSION['youtubeurl'];} ?>" placeholder=" https://www.youtube.com/watch?v=uBkuU0B5BJg" />
	</p>
	<p><?php settings_errors(); ?></p>
	<?php
	}
	
	// update widget
	function update($new_instance, $old_instance) {
		
		$instance = $old_instance;
		
		$instance['title'] = htmlspecialchars(strip_tags($new_instance['title']));
		$instance['youtubetitle'] = htmlspecialchars(strip_tags($new_instance['youtubetitle']));
		$instance['youtubeurl'] = htmlspecialchars(strip_tags($new_instance['youtubeurl']));
		$instance['youtubelimit'] = $new_instance['youtubelimit'];
		
		//Show value in input file 
		$_SESSION['youtubetitle'] = $instance['youtubetitle'];
		$_SESSION['youtubeurl'] = $instance['youtubeurl'];
		
		// The widget title is independient is saved
		add_settings_error( 'fields_main_input', null, 'Yeah, Widget Title saved!', 'updated');
		
		
		if (!empty($instance['youtubetitle']) || !empty($instance['youtubeurl']))
		{
		// Validate URL
		$rx = '~
		^(?:https?://)?              # Optional protocol
		 (?:www\.)?                  # Optional subdomain
		 (?:youtube\.com|youtu\.be)  # Mandatory domain name
		 /watch\?v=([^&]+)           # URI with video id as capture group 1
		 ~x';
		 
		$has_match = preg_match($rx, $instance['youtubeurl'], $matches);
		
		if (empty($instance['youtubetitle']))
		{
			add_settings_error( 'fields_main_input', null, 'Video Title is required!', 'error');
		}
		
		else if (empty($instance['youtubeurl']))
		{
			add_settings_error( 'fields_main_input', null, 'Video URL is required!', 'error');
		}

		else if (!$has_match)
		{
			add_settings_error( 'fields_main_input', null, 'Incorrect url video!', 'error');
		}
		else
		{
			//Destroy session
			unset($_SESSION['youtubetitle']);
			unset($_SESSION['youtubeurl']);
			
			$v = explode("=", $instance['youtubeurl']);
			$v = $v[1];
			$instance['youtubeurl'] = "https://www.youtube.com/embed/$v";
			$instance['youtubeimage'] = "http://img.youtube.com/vi/$v/default.jpg";
			$create_record = true;
		}
		}
		
		
		global $table_prefix;
		global $wpdb;
		$table = $table_prefix."videoyoutubelightbox";
		
	    $query = "CREATE TABLE IF NOT EXISTS $table (
		  id int(11) AUTO_INCREMENT,
		  youtubetitle varchar(255) NOT NULL,
		  youtubeurl varchar(255) NOT NULL,
		  youtubeimage varchar(255) NOT NULL,
		  PRIMARY KEY  (id)
		  )";
		
		#Create table if not exist
		$wpdb->query($query);
		
		#insert data
		if ($create_record)
		{
			$query = "INSERT INTO $table(youtubetitle, youtubeurl, youtubeimage) VALUES (%s, %s, %s)";
			$wpdb->query($wpdb->prepare($query, $instance['youtubetitle'], $instance['youtubeurl'], $instance['youtubeimage']));
			add_settings_error( 'fields_main_input', null, 'Yeah, new video!', 'updated');
		}

		return $instance;
	}

	// display widget
	function widget($args, $instance) {
		
	    extract( $args );
				
	   	global $table_prefix;
		global $wpdb;
		
		$table = $table_prefix."videoyoutubelightbox";
		
		//If is admin set option delete videos
		if (current_user_can('manage_options'))
		{	
			if (isset($_POST["vyp_delete"]))
			{
				$vyp_delete = $_POST["vyp_delete"];
				if ((int) $vyp_delete)
				{
					$sql = "DELETE FROM $table WHERE id=$vyp_delete";
					$wpdb->query($sql);
				}
			}
		} 
		
		if ((int) $instance['youtubelimit'])
		{
			$limit = $instance['youtubelimit'];
		}
		else
		{
			$limit = 10;
		}
		
	    $query = "SELECT id, youtubetitle, youtubeurl, youtubeimage FROM $table ORDER BY id DESC LIMIT 0, $limit";
		$rows = $wpdb->get_results($query, OBJECT_K);
	   
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text wp_widget_plugin_box">';

	   // Check if title is set
	   if ($title) {
		  echo $before_title . $title . $after_title;
	   }
		
	?>		

	<div id="video-youtube-lightbox-back"></div>
	<div id="video-youtube-lightbox-box">
	<iframe id="video-youtube-lightbox-iframe" src="" frameborder="0" allowfullscreen></iframe>
	</div>
	
		<?php
		foreach($rows as $row):
		?>
			<p>
			 <a href="<?php echo $row->youtubeurl ?>" class="video-youtube-lightbox-link" style="float: left; margin: 5px; width: 45%;" title="<?php echo $row->youtubetitle ?>"><img class="video-youtube-playlist-img" src="<?php echo $row->youtubeimage ?>" alt="<?php echo $row->youtubetitle ?>" /></a>
			<a href="<?php echo $row->youtubeurl ?>" class="video-youtube-lightbox-link" title="<?php echo $row->youtubetitle ?>"><small><?php echo $row->youtubetitle ?></small></a>
			</p>
			<div style="clear: both;"></div>
			<?php 
			//If is admin show options delete videos
			if (current_user_can('manage_options'))
			{	
				?>
				<form method="post">
					<input type="hidden" name="vyp_delete" value="<?php echo $row->id ?>" />
					<button type="submit" title="Delete Video"><span class="dashicons dashicons-trash"></span></button>
				</form>
				<?php
			} 
			?>
			<hr />
	   <?php
	   endforeach;
	   ?>
	   <?php
	   echo '</div>';
	   echo $after_widget;
	}
	
	public function register($widget_class) {
		$this->widgets[$widget_class] = new $widget_class();
	}

}

function video_youtube_lightbox_register_widgets() {
	register_widget('Video_Youtube_Lightbox');
}

add_action('widgets_init', 'video_youtube_lightbox_register_widgets');


function theme_name_scripts() {
	$css = plugins_url( 'css/video-youtube-lightbox.css', __FILE__ );
	wp_enqueue_style( 'video-youtube-lightbox-css', $css);
	$js = plugins_url( 'js/video-youtube-lightbox.js', __FILE__ );
	wp_enqueue_script( 'video-youtube-lightbox-javascript', $js, array('jquery'));
}

add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

?>