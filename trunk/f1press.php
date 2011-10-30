<?php

/*
Plugin Name: F1 Press
Description: Displays the latest Formula 1 official site news on your blog.
Version: 1.1
Author: pagepro.com.ua
License: GPL2
*/
define(f1_DESC_CHARS, '100');
define(f1_DESC, '');
define(f1_IMG, 'checked="checked"');
define(f1_DIR, basename(dirname(__FILE__)));
define(f1_RSS_items, '10');
define(f1_RSS, 'http://www.formula1.com/rss/news/latest.rss');
define(f1_TITLE, 'F1 Press');
include_once(ABSPATH . WPINC . '/rss.php');

function cut_f1text($str,$length)  {
  while(substr($str,$length,1) !== " ")  {
    substr($str,$length,1);
    $length = $length - 1;
  }
  $str = substr($str,0,$length);
  $str .= ' ...';
  return $str;
}

function widget_f1pwidget_init() {
	if(!function_exists('register_sidebar_widget'))
		return;
		
	function widget_f1pwidget($args) {
		extract($args);			
		$wpurl = get_bloginfo('wpurl');
  		$options = get_option('widget_f1pwidget');

  		$rss = fetch_rss(f1_RSS);
  		$img_url = $rss->image['url'];
  		$output = '';
  		if($img_url && $options['f1pwidget_image'])  {
    		$output = '<a href="'.$rss->image['link'].'" target="_blank"><img src="'.$img_url.'" border="0" style="border:1px solid #000"/><br/><strong>'.$rss->image['title'].'</strong></a><br/><br/>';
  		}
  		$items = count($rss->items);

		if($items != 0)  {
    		$output .= '<ul>';		
    		for($i=0; $i<$options['f1pwidget_items'] && $i<$items; $i++)  {
      			$output .= '<li>';
      			$output .= '<a href="'.$rss->items[$i]['link'].'" target="_blank"><strong>'.$rss->items[$i]['title'].'</strong></a></span>';
      			if($options['f1pwidget_desc'])  {  
        			$output.= '<br/>'.cut_f1text($rss->items[$i]['description'], $options['f1pwidget_chars']).'&nbsp;&nbsp;<a href="'.$rss->items[$i]['link'].'" target="_blank"><strong>read more &raquo;</strong></a>';
      			}
      			$output .= '</li>';
    		}
    		$output .= '</ul>';
  		}
  		$title = $options['f1pwidget_title'];
  		echo $before_widget;
  		echo $before_title . $title . $after_title;
  		echo $lightbox;
  		echo $output;
  		echo $after_widget;
	}
		
	function widget_f1pwidget_control() {
		$options = get_option('widget_f1pwidget');
  		if(!$options)  {
    		$options['f1pwidget_title'] = f1_TITLE;
    		$options['f1pwidget_chars'] = f1_DESC_CHARS;
    		$options['f1pwidget_items'] = f1_RSS_items;
    		$options['f1pwidget_desc'] = f1_DESC;
    		$options['f1pwidget_image'] = f1_IMG;
  		}
		if ( $_POST['f1pwidget-submit'] ) {
			$options['f1pwidget_title'] = strip_tags(stripslashes($_POST['f1pwidget-title']));
			$options['f1pwidget_items'] = strip_tags(stripslashes($_POST['f1pwidget-items']));
			$options['f1pwidget_image'] = strip_tags(stripslashes($_POST['f1pwidget-img'] ? 'checked="checked"' : ''));
			$options['f1pwidget_desc'] = strip_tags(stripslashes($_POST['f1pwidget-desc'] ? 'checked="checked"' : ''));
			$options['f1pwidget_chars'] = strip_tags(stripslashes($_POST['f1pwidget-chars']));
			update_option('widget_f1pwidget', $options);
		}
		
		$title = htmlspecialchars($options['f1pwidget_title'], ENT_QUOTES);
		$items = htmlspecialchars($options['f1pwidget_items'], ENT_QUOTES);
		$image = htmlspecialchars($options['f1pwidget_image'], ENT_QUOTES);
		$desc = htmlspecialchars($options['f1pwidget_desc'], ENT_QUOTES);
		$chars = htmlspecialchars($options['f1pwidget_chars'], ENT_QUOTES);
		
		echo '<p><label for="f1pwidget-title">'. _e('Title') .' <input style="width: 200px;" id="f1pwidget-title" name="f1pwidget-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p><label for="f1pwidget-items">'._e('Items').' <input style="width: 50px;" id="f1pwidget-items" name="f1pwidget-items" type="text" value="'.$items.'" /></label></p>';
		echo '<p><label for="f1pwidget-img">'._e('Show image').' <input id="f1pwidget-img" name="f1pwidget-img" type="checkbox" '. $image.' /></label></p>';
		echo '<p><label for="f1pwidget-desc">'. _e('Show description') .' <input id="f1pwidget-desc" name="f1pwidget-desc" type="checkbox" '. $desc .' /></label></p>';
		echo '<p><label for="f1pwidget-chars">'._e('Symbols in description').'<input id="f1pwidget-chars" name="f1pwidget-chars" size="3" maxlength="3" type="text" value="'. $chars .'" /></label></p>';
		echo '<input type="hidden" id="f1pwidget-submit" name="f1pwidget-submit" value="1" />';
}
	register_widget_control('F1 Press', 'widget_f1pwidget_control', 200, 200);		
	wp_register_sidebar_widget(sanitize_title('F1 Press'), 'F1 Press', 'widget_f1pwidget', array('description' => __('Formula 1 official site news')));
}
add_action('widgets_init', 'widget_f1pwidget_init');

?>
