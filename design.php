<?php 

//This displays a correct link to the page/post in the search results, title
add_filter('the_permalink',ti_permalink);
function ti_permalink($original) {
	global $post;
	if ($post->ID==null || strpos($post->guid,"#")>0) {
		return $post->guid;
	}
	else
		return $original;
}

//This replaces the empty href in "Continue reading", (in the search results)
add_filter('get_the_excerpt', 'ti_get_the_excerpt',100 );
function ti_get_the_excerpt($original) {
	global $post;
	if (!($post->ID==null)) {
		return $original;
	}
	
	$i=strpos($original,'<a href="');
	if ($i==false) {
		return $original;
	}
	$i+=9;
	//Edit or delete it, then start <span class="KeywordHighlight">blogging</span>! .<a href="">Continue reading <span class="meta-nav">&rarr;</span></a>
	$j=strpos($original,'">',$i);
	$result=substr($original,0,$i).$post->guid.substr($original,$j);	
	
	return $result;
}

//highlights the searched keywords in the search result snippet, using the
//text ranges received with the search result.
function ti_highLightSnippet($doc, $snippet)
{
	$options = get_option('ti_search_options');
	if ($options[highlightSearchOnOff]=='off')
		return $snippet;

	$list=Array();
	foreach ($doc->keywordRanges as $range) {
		array_push($list,$range->begin,$range->end);
	}
	
	if ($options[highlightTermOnOff]=='off') {
		$doc->termRanges=Array();
	}
	foreach ($doc->termRanges as $range) {
		array_push($list,$range->begin,$range->end);
	}
	sort($list);
	$SPAN_CLOSE= "</span>";

	mb_internal_encoding("UTF-8");
	$distance=0;
	for ($i=0; $i<count($list)-1; $i++) {
		if ($list[$i+1] > $list[$i]) {
			$type=ti_getHighlightType($doc,$list[$i],$list[$i+1],$distance);
			if ($type!=null) {
				//echo $list[$i]." - ".$list[$i+1]."<br>";
				$snippet= mb_substr($snippet,0,$list[$i+1]).$SPAN_CLOSE.mb_substr($snippet,$list[$i+1],mb_strlen($snippet));
				$snippet= mb_substr($snippet,0,$list[$i]).$type.mb_substr($snippet,$list[$i],mb_strlen($snippet));
				for ($j=$i+1; $j<count($list); $j++) {
					$list[$j] += strlen($type) + strlen($SPAN_CLOSE);
				}
				$distance += strlen($type) + strlen($SPAN_CLOSE);
			}
		}
	}
	return $snippet;
}

//the keywords and concepts are highlighted with different classes
//(edit or override in your theme if you want different colors/styles) 
function ti_getHighlightType($doc,$i,$j,$distance) {
	$i=$i-$distance;
	$j=$j-$distance;
	$SPAN_CLASS_Keyword = "<span class=\"KeywordHighlight\">";
	$SPAN_CLASS_Term = "<span class=\"TermHighlight\">";

	foreach ($doc->keywordRanges as $range) {
		if ($range->begin <= $i && $range->end >=$j)
			return $SPAN_CLASS_Keyword;
	}
	foreach ($doc->termRanges as $range) {
		if ($range->begin <= $i && $range->end >=$j)
			return $SPAN_CLASS_Term;
	}
	return null;
}

?>
