<?php

/*
Plugin Name: F1 Press
Description: Displays the latest Formula1 news on your blog.
Version: 1.5
Author: Limeira Studio
Author URI: http://www.limeirastudio.com/
License: GPL2
Copyright: Limeira Studio
*/

function register_f1_press_widget()	{
	register_widget('F1_Press');
}
add_action('widgets_init', 'register_f1_press_widget');

class F1_Press extends WP_Widget {
			
	private $feed = 'http://feeds.bbci.co.uk/sport/0/formula1/rss.xml';
	
	function __construct()	{
		$options = array(
            'description'   =>  'Displays the latest Formula1 news on your blog.',
            'name'          =>  'F1 Press'
        );
		parent::__construct('f1_press', '', $options);
		$this->defaults = array(
			'title'				=> 'F1 Press',
			'items_per_page'	=> '5',
			//'view_type'			=> 2, //TODO
			'trim_description'	=> 30,
			'show_images'		=> 'on',
			'show_description'	=> 'on',
			'show_date'			=> '',
			'show_countdown'	=> ''
		);
	}
		
	public function form($instance)	{

		$instance = wp_parse_args((array)$instance, $this->defaults);
		$title = !empty($instance['title']) ? $instance['title'] : '';
		$items_per_page = !empty($instance['items_per_page']) ? $instance['items_per_page'] : '';
		$trim = !empty($instance['trim_description']) ? $instance['trim_description'] : '';
		$show_images = !empty($instance['show_images']) ? $instance['show_images'] : '';
		$show_date = !empty($instance['show_date']) ? $instance['show_date'] : '';
		$show_description = !empty($instance['show_description']) ? $instance['show_description'] : '';
		$show_countdown = !empty($instance['show_countdown']) ? $instance['show_countdown'] : '';
		?>

		<p>
			<label for="<?=$this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?=$this->get_field_id('title'); ?>" name="<?=$this->get_field_name('title'); ?>" type="text" value="<?=esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?=$this->get_field_id('items_per_page'); ?>"><?php _e('Items:'); ?></label> 
			<input class="widefat" id="<?=$this->get_field_id('items_per_page'); ?>" name="<?=$this->get_field_name('items_per_page'); ?>" type="text" value="<?=esc_attr($items_per_page); ?>">
		</p>
		<p>
		    <input class="checkbox" type="checkbox" <?php checked($show_images, 'on'); ?> id="<?=$this->get_field_id('show_images'); ?>" name="<?=$this->get_field_name('show_images'); ?>" /> 
		    <label for="<?=$this->get_field_id('show_images'); ?>"> Show Images</label>
		</p>
		<p>
		    <input class="checkbox" type="checkbox" <?php checked($show_date, 'on'); ?> id="<?=$this->get_field_id('show_date'); ?>" name="<?=$this->get_field_name('show_date'); ?>" /> 
		    <label for="<?=$this->get_field_id('show_date'); ?>"> Show Item Date</label>
		</p>
		<p>
		    <input class="checkbox" type="checkbox" <?php checked($show_description, 'on'); ?> id="<?=$this->get_field_id('show_description'); ?>" name="<?=$this->get_field_name('show_description'); ?>" /> 
		    <label for="<?=$this->get_field_id('show_description'); ?>"> Show Item Description</label>
		</p>
		<p>    
			<label for="<?=$this->get_field_id('trim_description'); ?>">
			<input id="<?=$this->get_field_id('trim_description'); ?>" name="<?=$this->get_field_name('trim_description'); ?>" size="3" maxlength="3" type="text" value="<?=esc_attr($trim); ?>" /> Trim Description</label>		
		</p>
		<p>
		    <input class="checkbox" type="checkbox" <?php checked($show_countdown, 'on'); ?> id="<?=$this->get_field_id('show_countdown'); ?>" name="<?=$this->get_field_name('show_countdown'); ?>" /> 
		    <label for="<?=$this->get_field_id('show_countdown'); ?>"> Show Countdown</label>
		</p>
			<?php 
	}

	public function widget($args, $instance)	{
		$title = $instance['title'];
		$perpage = $instance['items_per_page'];
		$trim = $instance['trim_description'];
		$show_images = $instance['show_images'];
		$show_date = $instance['show_date'];
		$show_description = $instance['show_description'];
		$show_countdown = $instance['show_countdown'];
		
		echo $args['before_widget'];?>

		<style>
		.f1press-item	{
			display: inline-block;
			padding: 0;
		}
		.f1press-item-image	{
		-moz-transition:-moz-transform 0.5s ease-in; 
		-webkit-transition:-webkit-transform 0.5s ease-in; 
		-o-transition:-o-transform 0.5s ease-in;
		float:left; 
		padding:5px
		}
		.f1press-item-image:hover	{
		-moz-transform:scale(1.1); 
		-webkit-transform:scale(1.1);
		-o-transform:scale(1.1);
		 filter: alpha(Opacity=80);
		opacity: 0.8;
		}
		.f1press-item-date	{
			font-size: 10px;
		}
		</style>
		
		<?php
		if($title)	{
			echo '<h3 class="f1press-widget-title">'.$title.'</h3>';
		}
		$rss = fetch_feed($this->feed);
		$maxitems = $rss->get_item_quantity($perpage); 
		$rss_items = $rss->get_items(0, $maxitems);
		foreach($rss_items as $item)	{
			$enc = $item->get_enclosures();?>
			<div class="f1press-item">
			<a target="_blank" title="<?=$item->get_title();?>" href="<?=$item->get_permalink();?>">
			<div class="f1press-item-title"><strong><?=$item->get_title();?></strong></div>
			<?php if($show_date): ?>
			<div class="f1press-item-date"><strong><?=$item->get_date('j F Y | g:i a');?></strong></div>
			<?php endif; ?>
			<?php if($show_images): ?>
			<img class="f1press-item-image" src="<?=$enc[0]->thumbnails[1];?>" alt="<?=$item->get_title();?>" />
			<?php endif; ?>
			<?php if($show_description): ?>
			<div class="f1press-item-description"><?=wp_trim_words($item->get_description(), $trim, $more = null);?></div>
			<?php endif; ?>
			</a>
			</div>
			<hr style="border: 1px dotted" />
		<?php
		}
		if($show_countdown)	{$this->countown_widget();}
		echo $args['after_widget'];
	}

	public function update($new_instance, $old_instance)	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['items_per_page'] = (!empty($new_instance['items_per_page'])) ? strip_tags($new_instance['items_per_page']) : '';
		$instance['trim_description'] = (isset($new_instance['trim_description'])) ? strip_tags($new_instance['trim_description']) : '';
		$instance['show_images'] = (isset($new_instance['show_images'])) ? strip_tags($new_instance['show_images']) : '';
		$instance['show_date'] = (isset($new_instance['show_date'])) ? strip_tags($new_instance['show_date']) : '';
		$instance['show_description'] = (isset($new_instance['show_description'])) ? strip_tags($new_instance['show_description']) : '';
		$instance['show_countdown'] = (isset($new_instance['show_countdown'])) ? strip_tags($new_instance['show_countdown']) : '';
		
		return $instance;
	}
	
	private function countown_widget()	{
		echo '
			<div id="f1-widget-container">
    			<script src="http://amitd.co/widgets/f1/f1-countdown.min.js"></script>
			</div>';
	}
	
}

?>
