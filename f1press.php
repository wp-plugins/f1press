<?php

/*
Plugin Name: F1Press
Description: Displays the latest Formula 1 official site news on your blog.
Version: 1.0
Author: junatik
License: GPL2
*/

define(f1_TITLE, 'F1Press');
define(f1_DESC_CHARS, '300');
define(f1_RSS, 'http://www.formula1.com/rss/news/latest.rss');
define(f1_RSS_items, '10');
define(f1_DESC, false);
define(f1_IMG, true);
define(f1_DIR, basename(dirname(__FILE__)));
include_once(ABSPATH . WPINC . '/rss.php');

function f1_GetRSS($args)  {
  $wpurl = get_bloginfo('wpurl');
  $options = get_option('f1_widget');
  if($options == false)  {
    $options['f1_widget_url_title'] = f1_TITLE;
    $options['f1_desc_chars'] = f1_DESC_CHARS;
    $options['f1_RSS_items'] = f1_RSS_items;
    $options['f1_desc'] = f1_DESC;
    $options['f1_img'] = f1_IMG;
  }
  $rss = fetch_rss(f1_RSS);
  $img_url = $rss->image['url'];
  $output = '';
  if($img_url && $options['f1_img'])  {
    $output = '<a href="'.$rss->image['link'].'" target="_blank"><img src="'.$img_url.'" border="0" style="border:1px solid #000"/><br/><strong>'.$rss->image['title'].'</strong></a><br/><br/>';
  }
  $items = count($rss->items);
  if($items != 0)  {
    $output .= '<ul>';		
    for($i=0; $i<$options['f1_RSS_items'] && $i<$items; $i++)  {	
      $output .= '<li>';
      $output .= '<a href="'.$rss->items[$i]['link'].'" target="_blank"><strong>'.$rss->items[$i]['title'].'</strong></a></span>';
      if($options['f1_desc'])  {  
        $output.= '<br/>'.cut_f1text($rss->items[$i]['description'], $options['f1_desc_chars']).'&nbsp;&nbsp;<a href="'.$rss->items[$i]['link'].'" target="_blank"><strong>read more &raquo;</strong></a>';
      }
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  $title = $options['f1_widget_url_title'];
  extract($args);	
  echo $before_widget;
  echo $before_title . $title . $after_title;
  echo $lightbox;
  echo $output;
  echo $after_widget;
}

function cut_f1text($str,$length)  {
  while(substr($str,$length,1) !== " ")  {
    substr($str,$length,1);
    $length = $length - 1;
  }
  $str = substr($str,0,$length);
  $str .= ' ...';
  return $str;
}

function f1_widget_Admin()  {
  $options = $newoptions = get_option('f1_widget');	
  if($options == false)  {
    $newoptions['f1_widget_url_title'] = f1_TITLE;
    $newoptions['f1_desc_chars'] = f1_DESC_CHARS;
    $newoptions['f1_RSS_items'] = f1_RSS_items;
    $newoptions['f1_desc'] =  $options['f1_desc'] ? 'checked="checked"' : '';
    $newoptions['f1_img'] =  $options['f1_img'] ? 'checked="checked"' : '';
  }
  if($_POST['f1_widget-submit'])  {
    $newoptions['f1_widget_url_title'] = strip_tags(stripslashes($_POST['f1_widget_url_title']));
    $newoptions['f1_desc_chars'] = $_POST['f1_desc_chars'];
    $newoptions['f1_img'] = $_POST['f1_img'] ? 'checked="checked"' : '';
    $newoptions['f1_desc'] = $_POST['f1_desc'] ? 'checked="checked"' : '';
    $newoptions['f1_RSS_items'] = $_POST['f1_RSS_items'];
  }	
  if($options != $newoptions)  {
    $options = $newoptions;		
    update_option('f1_widget', $options);
  }
  $f1_widget_url_title = wp_specialchars($options['f1_widget_url_title']);
  $f1_desc_chars = $options['f1_desc_chars'];
  $f1_img = $options['f1_img'];
  $f1_desc = $options['f1_desc'];
  $f1_RSS_items = $options['f1_RSS_items'];

?>
<form method="post" action="">	
<p><label for="f1_widget_url_title"><?php _e('Title'); ?>: <input style="width: 180px;" id="f1_widget_url_title" name="f1_widget_url_title" type="text" value="<?php echo $f1_widget_url_title; ?>" /></label></p>
<p><label for="f1_RSS_items"><?php _e('Items'); ?>: <input id="f1_RSS_items" name="f1_RSS_items" size="2" maxlength="2" type="text" value="<?php echo $f1_RSS_items?>" /></label></p>
<p><label for="f1_img"><?php _e('Show image'); ?>: <input id="f1_img" name="f1_img" type="checkbox" <?php echo $f1_img?> /></label></p>
<p><label for="f1_desc"><?php _e('Show description'); ?>: <input id="f1_desc" name="f1_desc" type="checkbox" <?php echo $f1_desc?> /></label></p>
<p><label for="f1_desc_chars"><?php _e('Symbols in description'); ?>: <input id="f1_desc_chars" name="f1_desc_chars" size="3" maxlength="3" type="text" value="<?php echo $f1_desc_chars?>" /></label></p>
<br clear='all'></p>
<input type="hidden" id="f1_widget-submit" name="f1_widget-submit" value="1" />	
</form>
<?php
}
function f1_Init()  {
  register_sidebar_widget(__(f1_TITLE), 'f1_GetRSS');
  register_widget_control(__(f1_TITLE), 'f1_widget_Admin', 250, 250);
}
add_action("plugins_loaded", "f1_Init");

?>
