<?php

/*
Plugin Name: F1 Press
Description: Displays the latest Formula1 news on your blog.
Version: 2.0
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
	
	private $plugname;
	private $version;
			
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
		$this->plugname = 'F1Press';
		$this->version = '2.0';
		$this->load_style();
		add_shortcode('f1press',array(&$this,'f1press_shortcode'));
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
		
		echo $args['before_widget'];
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
		if($show_countdown)	{$this->countdown_widget();}
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
	
	private function countdown_widget()	{
		echo '
			<div id="f1-widget-container">
    			<script src="http://amitd.co/widgets/f1/f1-countdown.min.js"></script>
			</div>';
	}
	
	public function f1press_shortcode($atts)	{
		
		require_once(plugin_dir_path( __FILE__ ).'html_table.class.php');
		$tbl = new HTML_Table('', 'f1-results');
		
		if(isset($_GET['sc-replace']))	{
			$atts['type'] = $_GET['sc-replace-type'];
			$atts['season'] = $_GET['sc-replace-season'];
			$atts['round'] = $_GET['sc-replace-round'];
		}
		
		$tbl->addRow();
		
		if(!isset($atts['season']))	{
			$atts['season'] = date('Y');
		}
		
		if(!isset($atts['round']))	{
			$atts['round'] = 1;
		}
		
		switch($atts['type'])	{
			
			case 'race_results':
				
			if(!isset($atts['season']))	{
				$url = 'http://ergast.com/api/f1/current/last/results.json';
			}	elseif(!isset($atts['round'])) {
				$url = 'http://ergast.com/api/f1/'.$atts['season'].'/results.json';
			}	else{
				$url = 'http://ergast.com/api/f1/'.$atts['season'].'/'.$atts['round'].'/results.json';
			}

			$data = $this->get_API($url)->MRData->RaceTable->Races[0]->Results;
			
			$tbl->addCell('Pos', 'first', 'header');
		    $tbl->addCell('No', '', 'header');
		    $tbl->addCell('Driver', '', 'header');
			$tbl->addCell('Constructor', '', 'header');
			$tbl->addCell('Laps', '', 'header');
			$tbl->addCell('Grid', '', 'header');
			$tbl->addCell('Fastest Lap', '', 'header');
			$tbl->addCell('Time', '', 'header');
			$tbl->addCell('Status', '', 'header');
			$tbl->addCell('Points', '', 'header');

		    foreach($data as $row)	{
		    	$tbl->addRow();

				@$tbl->addCell($row->position);
				@$tbl->addCell($row->number);
				@$tbl->addCell('<a href="'.$row->Driver->url.'" target="_blank">'.$row->Driver->givenName.' '.$row->Driver->familyName.'</a>');
				@$tbl->addCell('<a href="'.$row->Constructor->url.'" target="_blank">'.$row->Constructor->name.'</a>');
				@$tbl->addCell($row->laps);
				@$tbl->addCell($row->grid);
				@$tbl->addCell($row->FastestLap->Time->time);
				@$tbl->addCell($row->Time->time);
				@$tbl->addCell($row->status);
				@$tbl->addCell($row->points);

			}
	
			break;
			
			case 'season_list':
			
			$url = 'http://ergast.com/api/f1/'.$atts['season'].'.json';
			$data = $this->get_API($url)->MRData->RaceTable->Races;
				
			$tbl->addCell('Race', 'first', 'header');
		    $tbl->addCell('Circuit', '', 'header');
		    $tbl->addCell('Date', '', 'header');
			$tbl->addCell('Time', '', 'header');
			$tbl->addCell('Location', '', 'header');
			$tbl->addCell('Information', '', 'header');
			
		    
		    foreach($data as $row)	{
				$tbl->addRow();
				
				@$tbl->addCell($row->raceName);
				@$tbl->addCell($row->Circuit->circuitName);
				@$tbl->addCell($row->date);
				@$tbl->addCell($row->time);
				@$tbl->addCell($row->Circuit->Location->locality . '('.$row->Circuit->Location->country.')');
				@$tbl->addCell('<a href="?sc-replace=true&sc-replace-type=qualifying_results&sc-replace-season='.$atts['season'].'&sc-replace-round='.$row->round.'"><strong>Q</strong></a> <a href="?sc-replace=true&sc-replace-type=race_results&sc-replace-season='.$atts['season'].'&sc-replace-round='.$row->round.'"><strong>R</strong></a> <a href="'.$row->url.'" target="_blank"><img style="width:16px; height:16px;" src="'.plugin_dir_url( __FILE__ ) . 'info.png'.'" alt="'.$row->Circuit->circuitName.' on Wikipedia" /></a> <a href="http://maps.google.com/maps?q='.$row->Circuit->Location->lat.',+'.$row->Circuit->Location->long.'" target="_blank"><img style="width:16px; height:16px;" src="'.plugin_dir_url( __FILE__ ) . 'map.png'.'" alt="'.$row->Circuit->circuitName.' on Wikipedia" /></a>');
		    }
				
			break;
			
			case 'driver_info':
			$drivers = preg_split("/[\s,]+/", $atts['id']);
			if(isset($atts['mode']) && $atts['mode'] == 'table')	{				
				$tbl->addCell('Name', '', 'header');
				$tbl->addCell('Number', 'first', 'header');
				$tbl->addCell('Code', '', 'header');
				$tbl->addCell('Date Of Birth', '', 'header');				
				$tbl->addCell('Nationality', '', 'header');
				$tbl->addCell('Information', '', 'header');
			}
				
			foreach($drivers as $driver)	{

				$url = 'http://ergast.com/api/f1/drivers/'.$driver.'.json';
				$data = $this->get_API($url)->MRData->DriverTable->Drivers;		
					
				foreach($data as $row)	{
						
					if(isset($atts['mode']) && $atts['mode'] == 'table')	{
						
						$tbl->addRow();
							
						@$tbl->addCell('<a href="'.$row->url.'" target="_blank">'.$row->givenName.' '.$row->familyName.'</a>');
						@$tbl->addCell($row->permanentNumber);
						@$tbl->addCell($row->code);
						@$tbl->addCell($row->dateOfBirth);
						@$tbl->addCell($row->nationality);
						@$tbl->addCell('<a href="'.$row->url.'" target="_blank"><img style="width:16px; height:16px;" src="'.plugin_dir_url( __FILE__ ) . 'info.png'.'" alt="'.$row->givenName.' '.$row->familyName.' on Wikipedia" /></a>');
						}	else {
								
							echo '<div style="display:inline-block; padding:10px">';
							echo '<h4><a href="'.$row->url.'" target="_blank">'.$row->givenName.' '.$row->familyName.'</a></h4>';	
							echo '<img style="float:left" src="'.$this->get_wiki_image($row->url).'" />';
							echo '</div>';
						}
				}
			}
				
			break;
			
			case 'qualifying_results':
				
			$url = 'http://ergast.com/api/f1/'.$atts['season'].'/'.$atts['round'].'/qualifying.json';		
			$data = $this->get_API($url)->MRData->RaceTable->Races[0]->QualifyingResults;
				
			$tbl->addCell('Pos', 'first', 'header');
		    $tbl->addCell('No', '', 'header');
		    $tbl->addCell('Driver', '', 'header');
			$tbl->addCell('Constructor', '', 'header');
			$tbl->addCell('Q1', '', 'header');
			$tbl->addCell('Q2', '', 'header');
			$tbl->addCell('Q3', '', 'header');
			
			foreach($data as $row)	{
				$tbl->addRow();
				@$tbl->addCell($row->position);
				@$tbl->addCell($row->number);
				@$tbl->addCell('<a href="'.$row->Driver->url.'" target="_blank">'.$row->Driver->givenName.' '.$row->Driver->familyName.'</a>');
				@$tbl->addCell('<a href="'.$row->Constructor->url.'" target="_blank">'.$row->Constructor->name.'</a>');
				@$tbl->addCell($row->Q1);
				@$tbl->addCell($row->Q2);
				@$tbl->addCell($row->Q3);
			}
			
			break;			
		}
		echo $tbl->display();	
	}
	
	private function load_style()	{
		if(!is_admin())	{
			wp_enqueue_style($this->plugname,plugin_dir_url( __FILE__ ).'style.css', array(), $this->version, 'all');
		}
	}
	
	private function get_wiki_image($wiki_url)	{
		$pathComponents = explode('/', parse_url($wiki_url, PHP_URL_PATH));
		$pageTitle = array_pop($pathComponents);
		$imagesQuery = "http://en.wikipedia.org/w/api.php?action=query&titles={$pageTitle}&prop=pageimages&format=json&pithumbsize=200";
		$imageKey = key($this->get_API($imagesQuery)->query->pages);
		return $this->get_API($imagesQuery)->query->pages->$imageKey->thumbnail->source;
	}
	
	private function get_API($url)	{
		return (json_decode(@file_get_contents($url))) ? json_decode(@file_get_contents($url)) : false;
	}

}

?>
