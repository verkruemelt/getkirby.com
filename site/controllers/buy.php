<?php

use Kirby\Cms\Page;

return function (Page $page) {
	$sale = new Buy\Sale();

	// expire the cache when the sale banner/prices change
	$sale->expires();

	return [
		'basic'      => Buy\Product::Basic,
		'countries'  => option('countries'),
		'discounts'  => option('buy.volume'),
		'donation'   => option('buy.donation'),
		'enterprise' => Buy\Product::Enterprise,
		'sale'       => $sale,
		'questions'  => $page->find('answers')->children()
	];
};
