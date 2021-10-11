<?php
require_once(__DIR__.'/vendor/autoload.php');

// Using Podcast Scraper
use Tzsk\ScrapePod\ScrapePodcast;
$scraper = new ScrapePodcast();

if (isset($argv[1]) && $argv[1] != '') {
	$rss_url = $argv[1];
	if (filter_var($rss_url, FILTER_VALIDATE_URL) === false) {
        echo 'Invalid command line input. Must be a valid RSS';
        exit;
    }
} else {
	echo 'No command line input given. Exiting.';
	exit;
}

// Sample RSS feed: https://feeds.hubhopper.com/267e511c0ebe7a1bcd33f9bb74aeee08.rss
$data = $scraper->feed($rss_url);
$wanted_fields = ['title', 'mp3', 'size', 'duration', 'description', 'image', 'published_at'];

if ($data['status'] === true && 
	isset($data['data']) && 
	isset($data['data']['episodes']) && 
	count($data['data']['episodes']) > 0) {
	
	// echo count($data['data']['episodes']);
	
	$fp = fopen('podcast-episodes.csv', 'w');
	
	// add headers for wanted fields
	fputcsv($fp, $wanted_fields);

	foreach ($data['data']['episodes'] as $episode) {
		// var_dump($episode);
		// die;
		// extract wanted fields only
		$new_array = array_intersect_key($episode, array_flip($wanted_fields));
		@fputcsv($fp, $new_array);
	}

	fclose($fp);
}