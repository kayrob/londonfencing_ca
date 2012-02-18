<?php
namespace RoyalYork\Apps\RSS;

class RSS{
	/**
	* Sort an mutltidimensional array by a specific subkey
	* Can be used to sort multiple rss feeds by date
	* @access public
	* @param array $list
	* @param string $keySort
	* @return array
	*/
	public function multi_array_subval_sort($list,$keySort){
		foreach($list as $key=>$val) {
			$sorted[$key] = strtolower($val[$keySort]);
		}
		asort($sorted);
		foreach($sorted as $key=>$val) {
			$final[] = $list[$key];
		}
		return $final;
	}
	/**
	* Replace entities with decimal format for xml parsing (rss)
	* @access protected
	* @param string $string
	* @return string
	*/
	protected function replace_entities($string){
		$entities = array("&trade"=>"&#8482;","&ldquo;"=>"&#34;","&rdquo;"=>"&#34;","&nbsp;"=>"&#160;","&rsquo;"=>"&#8217;","&lsquo;"=>"&#8216;");
		foreach ($entities as $search => $replace){
			$string = str_replace($search,$replace,$string);
		}
		return $string;
	}
	/**
	* Output valid RSS feed based on items, title and description
	* @access public
	* @param string $title
	* @param string $description
	* @param array $items
	* @see multi_array_subval_sort()
	*/
	public function create_rss_feed($title,$description,$items){
		$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$output .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">";
		$output .= "<channel>";
		$output .= "<title>".$title."</title>";
		$output .= "<description>".$description."</description>";
		$output .= "<link>http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."</link>";
		$output .= "<atom:link href=\"http://" .$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."\" rel=\"self\" type=\"application/rss+xml\" />";

		$output .= "<copyright>Copyright (C) ".date("Y")." ".basename($_SERVER['SERVER_NAME'])."</copyright>";
		if (count($items) > 0){
			$items = $this->multi_array_subval_sort($items,"sortBy");
			foreach ($items as $item){
			$output .= "<item>";
			$output .= "<title><![CDATA[".$this->replace_entities($item["title"])."]]></title>";
			$output .= "<description><![CDATA[".$this->replace_entities($item["description"])."]]></description>";
			$output .= "<link>".htmlspecialchars($this->replace_entities($item["link"]))."</link>";
			$output .= "<guid>".htmlspecialchars($this->replace_entities($item["link"]))."</guid>";
			$output .= '<source url="'.$item["source"]."\"><![CDATA[".$this->replace_entities($item["title"])."]]></source>";
			$output .= "<pubDate>".$item["pubDate"]."</pubDate>";
			$output .= "</item>";
			}
		}
		$output .= "</channel>";
		$output .= "</rss>";
		echo($output);
	}
}