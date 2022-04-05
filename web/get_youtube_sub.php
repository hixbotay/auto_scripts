<?php
define('BREAK_LINE','<br>');

function get_url_data($link){
	
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $link,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    
  ),
));

$html = curl_exec($curl);
curl_close($curl);
return $html;
}

function debug($match){
	echo '<pre>';print_r($match);echo '</pre>';
}

function getAllsub($seg){
	$r = '';
	foreach($seg as $s){
		$r .= "{$s->utf8} ";
	}
return "<p>{$r}</p>";
}

function get_youtube_link($link){
	$video_id = '';
	$link = trim($link);
	$link = trim($link,'/');
	if (strpos($link, 'youtu.be') !== false) {		
		$video_id = end(explode('/',$link));
	}
	if (strpos($link, 'www.youtube.com') !== false) {		
		$video_id = end(explode('?v=',$link));
	}
	if($video_id){
		return "https://www.youtube.com/watch?v={$video_id}";
	}else{
		return false;
	}
}

function get_youtube_cap_html($link){		
	$link = get_youtube_link($link);
	if(!$link){
		return '';
	}

	$html = get_url_data($link);

	preg_match('/https:\/\/www.youtube.com\/api\/timedtext(.*?)u0026key=yt8/', $html, $match);

	$raw_url = str_replace('\u0026','&',$match[0]);
debug($raw_url);
	preg_match('/&hl=(.*?)&/', $raw_url, $hl);
debug($hl);
	$lang = $hl[1];
	
	$url = $raw_url."&lang={$lang}&fmt=json3&xorb=2&xobt=3&xovt=3&tlang=en";
		debug($url);
	$sub = get_url_data($url);
	$sub = json_decode($sub);
	if(!$sub){
		$url = $raw_url."&kind=asr&lang=en&fmt=json3&xorb=2&xobt=3&xovt=3";
		$sub = get_url_data($url);
		$sub = json_decode($sub);
	}
	if(!$sub){
		$url = $raw_url."&lang=en&fmt=json3&xorb=2&xobt=3&xovt=3";
		debug($url);
		$sub = get_url_data($url);
		$sub = json_decode($sub);
	}

	if($sub){
		$result = "<!--start youtube caption {$link}-->
		<div class='youtube-caption=list'>
		<p class='youtube-time-title'><a class='youtube-time-link' href='{$link}&t=0m0s'>Watch video at 00:00</a></p>";
		$block = 5*60*1000;
		$point = $block;	
		$p = 1;
		foreach($sub->events as $s){
			if($s->tStartMs < $point){
				
			}else{
				$point += $block;
				$m_html = sprintf('%02d',5*$p);
				$m = 5*$p;
				$m_next = 5*($p+1);
				$result .= "<p class='youtube-time-title'><a class='youtube-time-link' href='{$link}&t={$m}m0s'>Watch video from {$m_html}:00 - {$m_next}:00</a></p>";
				$p++;
			}
			$result .= getAllsub($s->segs);
		}
		$result .= '</div><!--end youtube caption-->';
	}
	return $result;
}

//start

function add_youtube_caption_to_post($post_id,$post,$update){
	$link = get_post_meta($post_id,'tm_video_url');
	$html = $post->post_content;
	$link = get_youtube_link($link);
	if($link){
		if(strpos($content,"<!--start youtube caption {$link}-->")){
			return;
		}
		$caption_html = get_youtube_cap_html($link);
		if(strpos($content,"<!--start youtube caption")){
			$html = substr($html, 0, strpos($content,"<!--start youtube caption"));
		}
		$html = $html.$caption_html;
		
		 // unhook this function so it doesn't loop infinitely
		remove_action('save_post', 'add_youtube_caption_to_post');

		// Update post
		$my_post = [
		  'ID'           => $post_id,
		  'post_content' => $html,
		];

		// Update the post into the database
		wp_update_post( $my_post );

		// re-hook this function
		add_action('save_post', 'add_youtube_caption_to_post');
	}
	
}
//add_action('save_post','add_youtube_caption_to_post');


$link = 'https://youtu.be/vTJdVE_gjI0';
$link = 'https://www.youtube.com/watch?v=psZ1g9fMfeo';
$link = 'https://www.youtube.com/watch?v=MiXWvCWmsFc';
echo get_youtube_cap_html($link);

