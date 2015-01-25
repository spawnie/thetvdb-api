<?php

// Load composer dependencies
require_once realpath(__DIR__.'/../vendor/autoload.php');

// Instantiate Config-object
$config = new Choi\TheTvDbApi\Config(require realpath(__DIR__.'/config.php'));

// Instantiate Manager-object
$thetvdb = new Choi\TheTvDbApi\Manager($config);

// Search series by name
$search_results = $thetvdb->searchSeries('Arrow');

// Retrieve Series-object
$series = $thetvdb->getSeriesById($search_results->first()['seriesid']);

// Fetch base series details
$base_details = $series->getBaseDetails();

// Fetch full series details
$full_details = $series->getFullDetails();

// Fetch series banners
$banners = $series->getBanners();

dd($banners);
