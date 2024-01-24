<?php

use Kirby\Http\Remote;

return [
	'command' => function ($cli) {

		$kirby = $cli->kirby();
		$kirby->impersonate('kirby');

		$plugins = page('plugins')->grandChildren()->filter(function ($plugin) {
			return str_contains($plugin->repository()->value() ?? '', 'github') === true;
		});

		foreach ($plugins as $plugin) {
			$repo = $plugin->repository()->value();
			$api  = str_replace('https://github.com', 'https://api.github.com/repos', $repo);

			// prime the cache
			$stars = $plugin->stars();

			$cli->success($api);
			$cli->dump($stars->value());
		}

	}
];
