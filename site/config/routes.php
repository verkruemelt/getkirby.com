<?php

use Buy\Paddle;
use Buy\Passthrough;
use Buy\Product;
use Kirby\Cms\Page;

return [
	[
		'pattern' => '.well-known/security.txt',
		'action'  => function () {
			go('security.txt');
		}
	],
	[
		'pattern' => 'hooks/clean',
		'method'  => 'GET|POST',
		'action'  => function () {
			$key = option('keys.hooks');

			if (empty($key) === false && get('key') === $key) {
				kirby()->cache('diffs')->flush();
				kirby()->cache('meet')->flush();
				kirby()->cache('pages')->flush();
				kirby()->cache('plugins')->flush();
				kirby()->cache('reference')->flush();
			}

			go();
		}
	],
	[
		'pattern' => 'releases/(:num)\-(:any)',
		'action'  => function ($generation, $major) {
			return go('releases/' . $generation . '.' . $major);
		}
	],
	[
		'pattern' => 'releases/(:num)\.(:any)',
		'action'  => function ($generation, $major) {
			return page('releases/' . $generation . '-' . $major);
		}
	],
	[
		'pattern' => 'releases/(:num)\.(:any)/(:all?)',
		'action'  => function ($generation, $major, $path) {
			return page('releases/' . $generation . '-' . $major . '/' . $path);
		}
	],
	[
		'pattern' => 'buy/prices',
		'action' => function () {
			// uncomment to test a specific country
			// Buy\Paddle::visitor(country: 'US');

			$basic      = Product::Basic;
			$enterprise = Product::Enterprise;
			$visitor    = Paddle::visitor();

			return json_encode([
				'basic-regular'         => $basic->price()->regular(),
				'basic-sale'            => $basic->price()->sale(),
				'enterprise-regular'    => $enterprise->price()->regular(),
				'enterprise-sale'       => $enterprise->price()->sale(),
				'country'               => $visitor->country(),
				'currency-sign'         => $visitor->currencySign(),
				'currency-sign-trimmed' => rtrim($visitor->currencySign(), ' '),
				'revenue-limit'         => $visitor->currency() !== 'EUR' ? ' (' . $visitor->revenueLimit(1000000) . ')' : '',
				'vat-rate'              => $visitor->vatRate(),
				'status'                => $visitor->error() ?? 'OK'
			], JSON_UNESCAPED_UNICODE);
		}
	],
	[
		'pattern' => 'buy',
		'method'  => 'POST',
		'action' => function () {
			// TODO: Use all dynamic form values
			$city       = get('city');
			$company    = get('company');
			$country    = get('country');
			$donate     = get('donate') === 'on';
			$email      = get('email');
			$productId  = get('product');
			$newsletter = get('newsletter') === 'on';
			$postalCode = get('postalCode');
			$state      = get('state');
			$street     = get('street');
			$quantity   = (int)get('quantity', 1);
			$vatId      = get('vatId');

			try {
				$product     = Product::from($productId);
				$price       = $product->price();
				$message     = $product->revenueLimit();
				$passthrough = new Passthrough(teamDonation: option('buy.donation.teamAmount') * $quantity);

				$eurPrice       = $product->price('EUR')->volume($quantity);
				$localizedPrice = $price->volume($quantity);

				if ($donate === true) {
					// prices per license
					$customerDonation = option('buy.donation.customerAmount');
					$eurPrice       += $customerDonation;
					$localizedPrice += $price->convert($customerDonation);

					// donation overall
					$customerDonation *= $quantity;
					$passthrough->customerDonation = $customerDonation;

					$message .= ' We will donate an additional €' . $customerDonation . ' to ' . option('buy.donation.charity') . '. Thank you for your donation!';
				}

				$prices  = [
					'EUR:' . $eurPrice,
					$price->currency . ':' . $localizedPrice,
				];

				go($product->checkout('buy', [
					'custom_message'    => $message,
					'customer_country'  => $country,
					'customer_email'    => $email,
					'customer_postcode' => $postalCode,
					'marketing_consent' => $newsletter ? 1 : 0,
					'passthrough'       => $passthrough,
					'prices'            => $prices,
					'quantity'          => $quantity,
					'quantity_variable' => 0,
					'vat_city'          => $city,
					'vat_country'       => $country,
					'vat_company_name'  => $company,
					'vat_number'        => $vatId,
					'vat_postcode'      => $postalCode,
					'vat_state'         => $state,
					'vat_street'        => $street,
				]));
			} catch (Throwable $e) {
				die($e->getMessage() . '<br>Please contact us: support@getkirby.com');
			}
		},
	],
	[
		'pattern' => 'pixels',
		'action'  => function () {
			return new Page([
				'slug'     => 'pixels',
				'template' => 'pixels',
				'content'  => [
					'title' => 'Pixels'
				]
			]);
		}
	],
	[
		'pattern' => 'plugins/k4',
		'action'  => function () {
			return page('plugins')->render(['filter' => 'k4']);
		}
	],
	[
		'pattern' => 'plugins/new',
		'action'  => function () {
			return page('plugins')->render(['filter' => 'published']);
		}
	],
];
