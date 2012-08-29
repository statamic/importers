<?php
// USAGE: php convert.php

// Edit the details below to your neds
$wp_file = 'data/mb.xml';
$export_folder = 'content/'; // existing files will be over-written use with care

if (file_exists($wp_file)) {
  $xml = simplexml_load_file($wp_file);
  $count = 0;
  foreach ($xml->channel->item as $item) {
    $count ++;

    print "Exporting: (".$count.") " . $item->title."\n";
    $title = $item->title;
    $tags = array();
    $categories = array();
    $item_date = strtotime($item->pubDate);
    $file_name = $export_folder.date("Y-m-d-Hi", $item_date)."-".slugify($title).".md";

    if ($title == '') {
        $title = 'untitled post';
    }

    foreach($item->category as $taxonomy) {
        if ($taxonomy['domain'] == 'post_tag') {
            $tags[] = "'".$taxonomy['nicename']."'";
        }
        if ($taxonomy['domain'] == 'category') {
            $categories[] = "'".$taxonomy['nicename']."'";
        }
    }

    print "  -- filename: ".$file_name;

    $markdown  = "---\n";
    $markdown .= "title: '" . $title ."'\n";
    if (sizeof($tags)) {
      $markdown .= "tags: [".implode(", ", $tags)."]\n";
    }
    if (sizeof($categories)) {
      $markdown .= "categories: [".implode(", ", $categories)."]\n";
    }
    $markdown .= "---\n";
    $markdown .= $item->children('content', true)->encoded;
    $markdown .= "\n";

    file_put_contents($file_name, $markdown);

    print "\n";
  }
}

// credit: http://sourcecookbook.com/en/recipes/8/function-to-slugify-strings-in-php
function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
 
    // transliterate
    if (function_exists('iconv'))
    {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
 
    // lowercase
    $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text))
    {
        return 'n-a';
    }
 
    return $text;
}
