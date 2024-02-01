<?php layout() ?>

<style>
.checklist {
	font-size: var(--text-sm);
}
.checklist li {
	display: flex;
	align-items: center;
	gap: .5rem;
}
.checklist li + li {
	margin-top: .25rem;
}

.revenue {
	position: relative;
	font-size: var(--text-sm);
	margin-top: 1.25rem;
	margin-bottom: 3rem;
}
.revenue summary {
	background: var(--color-yellow-300);
	color: var(--color-yellow-900);
	border-radius: 2rem;
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: .25rem .75rem;
	padding-right: .5rem;
}
.revenue summary::-webkit-details-marker {
	display: none;
}
.revenue summary svg {
	color: var(--color-yellow-700);
	margin-top: 1px;
}
.revenue div {
	position: absolute;
	top: 100%;
	left: 50%;
	width: 20rem;
	transform: translateX(-50%);
	background: black;
	color: white;
	margin-top: 1rem;
	padding: 1rem;
	border-radius: var(--rounded);
	box-shadow: var(--shadow-xl);
}
.revenue div::after {
	position: absolute;
	top: -4px;
	left: 50%;
	content: "";
	border-left: 4px solid transparent;
	border-bottom: 4px solid black;
	border-right: 4px solid transparent;
}
.revenue div p + p {
	margin-top: .75rem;
}
.revenue div strong {
	font-weight: var(--font-normal);
	color: var(--color-yellow-500);
}

.sale {
	color: var(--color-purple-600);
}
.strikethrough {
	text-decoration: line-through;
}
.currency-sign {
	font-size: var(--text-xl);
	padding-right: 0.25em;
}
k-price-info:not(.loaded) {
	color: var(--color-gray-600);
}

@media (max-width: 40rem) {
	.causes li:not(:first-child) {
		display: none;
	}
}

.volume-toggles {
	display: flex;
	align-items: center;
	gap: 1.5rem;
}
.volume-toggles label {
	display: flex;
	align-items: center;
	gap: .5rem;
	cursor: pointer;
}

</style>

<article v-scope @mounted="mounted">
	<div class="columns mb-42" style="--columns-sm: 1; --columns-md: 1; --columns-lg: 2; --gap: var(--spacing-6)">

		<div>
			<h1 class="h1 max-w-xl mb-12">
				The transparency of <a href="https://github.com/getkirby">open&#8209;source</a> meets a fair pricing&nbsp;model
			</h1>

			<?php if ($sale->isActive()): ?>
				<div class="h3 sale mb-6">
					<?= $sale->text() ?>
				</div>
			<?php endif ?>
		</div>

		<div class="columns" style="--columns: 2; --gap: var(--spacing-6)">
			<div class="pricing p-6 bg-white shadow-xl rounded flex flex-column justify-between">
				<header>
					<h2>
						Basic

						<?php if ($sale->isActive()): ?>
						<span class="px-1 color-gray-700 strikethrough">
							<k-price-info key="currency-sign">€</k-price-info><!--
							--><k-price-info key="basic-regular"><?= Buy\Product::Basic->price('EUR')->regular() ?></k-price-info>
						</span>
						<?php endif ?>
					</h2>

					<a href="/buy/basic" @click.prevent="openCheckout('basic')" target="_blank" class="buy-link h2 block mb-3">
						<span class="sale currency-sign">{{ currencySignTrimmed }}</span><!--
						--><span class="sale">{{ prices.basic }}</span>
						per site
					</a>

					<p class="text-sm color-gray-700">A discounted license for individuals, small teams and side projects</p>
				</header>

				<details class="revenue">
					<summary><span>Revenue limit: <strong>€1M / year</strong></span> <?= icon('info') ?></summary>
					<div>
						<p>Your revenue or funding is less than <strong>€1&nbsp;million<k-price-info key="revenue-limit"></k-price-info></strong> in the <strong>last 12 months</strong>.</p>
						<p>If you build a website for a client, the limit has to fit the revenue of your client.</p>
					</div>
				</details>

				<?php snippet('templates/buy/checklist') ?>

				<footer>
					<p>
						<a href="/buy/basic" @click.prevent="openCheckout('basic')" target="_blank" class="buy-link btn btn--filled mb-1 w-100%">
							<?= icon('cart') ?>
							Buy Basic
						</a>
					</p>
				</footer>
			</div>

			<div class="pricing p-6 bg-white shadow-xl rounded flex flex-column justify-between">
				<header>
					<h2>
						Enterprise

						<?php if ($sale->isActive()): ?>
						<span class="px-1 color-gray-700 strikethrough">
							<k-price-info key="currency-sign">€</k-price-info><!--
							--><k-price-info key="enterprise-regular"><?= Buy\Product::Enterprise->price('EUR')->regular() ?></k-price-info>
						</span>
						<?php endif ?>
					</h2>

					<a href="/buy/enterprise" @click.prevent="openCheckout('enterprise')" target="_blank" class="buy-link h2 block mb-3">
						<span class="sale currency-sign">{{ currencySignTrimmed }}</span><!--
						--><span class="sale">{{ prices.enterprise }}</span>
						per site
					</a>

					<p class="text-sm color-gray-700">Suitable for larger companies and organizations</p>
				</header>

				<details class="revenue">
					<summary><span>Revenue limit: <strong>No limit</strong></span> <?= icon('info') ?></summary>
					<div>
						This license does not have a revenue limit.
					</div>
				</details>

				<?php snippet('templates/buy/checklist') ?>

				<footer>
					<p>
						<a href="/buy/enterprise" @click.prevent="openCheckout('enterprise')" target="_blank" class="buy-link btn btn--filled mb-1 w-100%">
							<?= icon('cart') ?>
							Buy Enterprise
						</a>
					</p>
				</footer>
			</div>
			<p class="text-xs text-center mb-6 color-gray-700" style="--span: 2">Prices + VAT if applicable. With your purchase you agree to our <a class="underline" href="<?= url('license') ?>">License terms</a></p>
		</div>
	</div>

	<section class="mb-42">
		<form class="volume-discounts" method="POST" target="_blank" action="<?= url('buy/volume') ?>">
			<input type="hidden" name="donate">
			<header class="flex items-baseline justify-between mb-6">
				<h2 class="h2">Volume discounts</h2>
				<fieldset>
					<legend class="sr-only">License Type</legend>
					<div class="volume-toggles">
						<label><input type="radio" name="product" value="basic" v-model="license" checked> Basic</label>
						<label><input type="radio" name="product" value="enterprise" v-model="license"> Enterprise</label>
					</div>
				</fieldset>
			</header>
			<div class="columns rounded overflow-hidden" style="--columns-md: 3; --columns: 3; --gap: var(--spacing-3)">
				<?php foreach ($discounts as $volume => $discount) : ?>
					<div class="block p-12 bg-light rounded text-center" >
						<article>
							<h3 class="mb text-sm">
								<?= $volume ?>+ licenses
							</h3>
							<div class="mb-6">
								<p class="h2">
									Save <?= $discount ?>%
								</p>
								<?php if ($sale->isActive()): ?>
									<p class="sale text-sm">on top!</p>
								<?php endif ?>
							</div>

							<button class="btn btn--filled mb-3" @click.prevent="openCheckout(license, <?= $volume ?>)" name="volume" value="<?= $volume ?>">
								<?= icon('cart') ?> Buy now
							</button>
						</article>
					</div>
				<?php endforeach ?>
			</div>
		</form>
	</section>

	<section class="mb-42 columns columns--reverse" style="--columns: 2; --columns-md: 1; --gap: var(--spacing-36)">
		<div>

			<h2 class="h2 mb-6">For a good cause? <mark class="px-1 rounded">It’s free.</mark></h2>
			<div class="prose mb-6">
				<p>We care about a better society and the future of our planet. We offer free&nbsp;licenses for <strong>students, selected educational projects, social and environmental organizations, charities and non-profits</strong> with insufficient funding.</p>
			</div>

			<a class="btn btn--filled mb-12" href="mailto:support@getkirby.com">
				<?= icon('heart') ?>
				Contact us
			</a>

			<ul class="columns causes" style="--columns: 2; --gap: var(--spacing-12);">
				<?php foreach (collection('causes')->shuffle()->limit(2) as $case) : ?>
					<li>
						<a href="<?= $case->link()->toUrl() ?>">
							<figure>
								<span class="block shadow-2xl mb-3" style="--aspect-ratio: 3/4">
									<?= img($image = $case->image(), [
										'alt' => 'Screenshot of the ' . $image->alt() . ' website',
										'src' => [
											'width' => 300
										],
										'srcset' => [
											'1x' => 400,
											'2x' => 800,
										]
									]) ?>
								</span>
								<figcaption class="text-sm">
									<?= $case->title() ?>
								</figcaption>
							</figure>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>

		<div>
			<h2 class="h2 mb-6">Frequently asked questions</h2>
			<?php snippet('faq') ?>
		</div>
	</section>

	<footer class="h2">
		Manage your existing licenses in our <a href="https://hub.getkirby.com"><span class="link">license&nbsp;hub</span> &rarr;</a>
	</footer>

	<?php snippet('templates/buy/checkout') ?>

</article>

<script type="module">
import {
	createApp,
	reactive
} from '<?= url('assets/js/libraries/petite-vue.js') ?>';


// close price details on clicks outside the details
document.addEventListener("click", (event) => {
	for (const details of [...document.querySelectorAll("details")]) {
		if (details.contains(event.target) === false) {
			details.removeAttribute("open");
		}
	}
});

const checkout = document.querySelector(".checkout");

class PriceInfo extends HTMLElement {
	constructor() {
		super();

		this.key = this.getAttribute("key");
		this.value = 0; // window.priceInfo[this.key] ?? 0;

		// format price values
		if (Number.isFinite(this.value) === true) {
			const formatter = new Intl.NumberFormat("en");
			this.value = formatter.format(this.value);
		}
	}

	connectedCallback() {
		this.textContent = this.value;
		this.classList.add("loaded");
	}
}

const countries = {
  "Aland Islands": "AX",
  "Albania": "AL",
  "Algeria": "DZ",
  "American Samoa": "AS",
  "Andorra": "AD",
  "Angola": "AO",
  "Anguilla": "AI",
  "Antigua and Barbuda": "AG",
  "Argentina": "AR",
  "Armenia": "AM",
  "Aruba": "AW",
  "Australia": "AU",
  "Austria": "AT",
  "Azerbaijan": "AZ",
  "Bahamas": "BS",
  "Bahrain": "BH",
  "Bangladesh": "BD",
  "Barbados": "BB",
  "Belgium": "BE",
  "Belize": "BZ",
  "Benin": "BJ",
  "Bermuda": "BM",
  "Bhutan": "BT",
  "Bolivia": "BO",
  "Bonaire, Sint Eustatius and Saba": "BQ",
  "Bosnia and Herzegovina": "BA",
  "Botswana": "BW",
  "Bouvet Island": "BV",
  "Brazil": "BR",
  "Brit. Indian Ocean": "IO",
  "British Virgin Islands": "VG",
  "Brunei": "BN",
  "Bulgaria": "BG",
  "Burkina Faso": "BF",
  "Burundi": "BI",
  "Cambodia": "KH",
  "Cameroon": "CM",
  "Canada": "CA",
  "Cape Verde": "CV",
  "Cayman Islands": "KY",
  "Chad": "TD",
  "Chile": "CL",
  "China": "CN",
  "Christmas Island": "CX",
  "Cocos Islands": "CC",
  "Colombia": "CO",
  "Comoros": "KM",
  "Cook Islands": "CK",
  "Costa Rica": "CR",
  "Cote D’Ivoire": "CI",
  "Croatia": "HR",
  "Curaçao": "CW",
  "Cyprus": "CY",
  "Czech Republic": "CZ",
  "Denmark": "DK",
  "Djibouti": "DJ",
  "Dominica": "DM",
  "Dominican Republic": "DO",
  "Ecuador": "EC",
  "Egypt": "EG",
  "El Salvador": "SV",
  "Equatorial Guinea": "GQ",
  "Eritrea": "ER",
  "Estonia": "EE",
  "Ethiopia": "ET",
  "Falkland Islands": "FK",
  "Faroe Islands": "FO",
  "Fiji": "FJ",
  "Finland": "FI",
  "France": "FR",
  "French Guiana": "GF",
  "French Polynesia": "PF",
  "French Southern Terr.": "TF",
  "Gabon": "GA",
  "Gambia": "GM",
  "Georgia": "GE",
  "Germany": "DE",
  "Ghana": "GH",
  "Gibraltar": "GI",
  "Greece": "GR",
  "Greenland": "GL",
  "Grenada": "GD",
  "Guadeloupe": "GP",
  "Guam": "GU",
  "Guatemala": "GT",
  "Guernsey": "GG",
  "Guinea": "GN",
  "Guinea-Bissau": "GW",
  "Guyana": "GY",
  "Heard/ Mcdonald Islands": "HM",
  "Holy See/ Vatican City": "VA",
  "Honduras": "HN",
  "Hong Kong": "HK",
  "Hungary": "HU",
  "Iceland": "IS",
  "India": "IN",
  "Indonesia": "ID",
  "Iraq": "IQ",
  "Ireland": "IE",
  "Isle of Man": "IM",
  "Israel": "IL",
  "Italy": "IT",
  "Jamaica": "JM",
  "Japan": "JP",
  "Jersey": "JE",
  "Jordan": "JO",
  "Kazakhstan": "KZ",
  "Kenya": "KE",
  "Kiribati": "KI",
  "Kosovo": "XK",
  "Kuwait": "KW",
  "Kyrgyzstan": "KG",
  "Lao People’s DR": "LA",
  "Latvia": "LV",
  "Lebanon": "LB",
  "Lesotho": "LS",
  "Liberia": "LR",
  "Liechtenstein": "LI",
  "Lithuania": "LT",
  "Luxembourg": "LU",
  "Macao": "MO",
  "Macedonia": "MK",
  "Madagascar": "MG",
  "Malawi": "MW",
  "Malaysia": "MY",
  "Maldives": "MV",
  "Malta": "MT",
  "Marshall Islands": "MH",
  "Martinique": "MQ",
  "Mauritania": "MR",
  "Mauritius": "MU",
  "Mayotte": "YT",
  "Mexico": "MX",
  "Micronesia": "FM",
  "Moldova": "MD",
  "Monaco": "MC",
  "Mongolia": "MN",
  "Montenegro": "ME",
  "Montserrat": "MS",
  "Morocco": "MA",
  "Mozambique": "MZ",
  "Namibia": "NA",
  "Nauru": "NR",
  "Nepal": "NP",
  "Netherlands": "NL",
  "Netherlands Antilles": "AN",
  "New Caledonia": "NC",
  "New Zealand": "NZ",
  "Niger": "NE",
  "Nigeria": "NG",
  "Niue": "NU",
  "Norfolk Island": "NF",
  "Northern Mariana Islands": "MP",
  "Norway": "NO",
  "Oman": "OM",
  "Pakistan": "PK",
  "Palau": "PW",
  "Palestinian Territory": "PS",
  "Panama": "PA",
  "Papua New Guinea": "PG",
  "Paraguay": "PY",
  "Peru": "PE",
  "Philippines": "PH",
  "Pitcairn": "PN",
  "Poland": "PL",
  "Portugal": "PT",
  "Puerto Rico": "PR",
  "Qatar": "QA",
  "Republic of Congo": "CG",
  "Republic of Serbia": "RS",
  "Reunion": "RE",
  "Romania": "RO",
  "Rwanda": "RW",
  "S. Georgia/ Sandwich Islands": "GS",
  "Saint Helena": "SH",
  "Saint Kitts and Nevis": "KN",
  "Saint Lucia": "LC",
  "Saint Martin": "MF",
  "Saint Pierre and Miquelon": "PM",
  "Saint Vincent/ Grenadines": "VC",
  "Samoa": "WS",
  "San Marino": "SM",
  "Sao Tome and Principe": "ST",
  "Saudi Arabia": "SA",
  "Senegal": "SN",
  "Seychelles": "SC",
  "Sierra Leone": "SL",
  "Singapore": "SG",
  "Slovakia": "SK",
  "Slovenia": "SI",
  "Solomon Islands": "SB",
  "South Africa": "ZA",
  "South Korea": "KR",
  "Spain": "ES",
  "Sri Lanka": "LK",
  "Sudan": "SD",
  "Suriname": "SR",
  "Svalbard and Jan Mayen": "SJ",
  "Swaziland": "SZ",
  "Sweden": "SE",
  "Switzerland": "CH",
  "Taiwan": "TW",
  "Tajikistan": "TJ",
  "Tanzania": "TZ",
  "Thailand": "TH",
  "Timor-Leste": "TL",
  "Togo": "TG",
  "Tokelau": "TK",
  "Tonga": "TO",
  "Trinidad and Tobago": "TT",
  "Tunisia": "TN",
  "Turkey": "TR",
  "Turkmenistan": "TM",
  "Turks and Caicos Islands": "TC",
  "Tuvalu": "TV",
  "U.S. Virgin Islands": "VI",
  "Uganda": "UG",
  "Ukraine": "UA",
  "United Arab Emirates": "AE",
  "United Kingdom": "GB",
  "United States": "US",
  "United States (M.O.I.)": "UM",
  "Uruguay": "UY",
  "Uzbekistan": "UZ",
  "Vanuatu": "VU",
  "Vietnam": "VN",
  "Wallis and Futuna": "WF",
  "Western Sahara": "EH",
  "Zambia": "ZM"
};

const zips = [
	"AU",
	"CA",
	"FR",
	"DE",
	"IN",
	"IT",
	"NL",
	"ES",
	"GB",
	"US"
];

createApp({
	amount(amount) {
		// format price values
		if (Number.isFinite(amount) === true) {
			const formatter = new Intl.NumberFormat("en", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
			return this.currencySignTrimmed + formatter.format(amount);
		}
	},
	city: "",
	closeCheckout(event) {
		if (event.target === checkout) {
			checkout.close();
		}
	},
	company: "",
	countries: countries,
	country: "DE",
	currencySign: "€",
	currencySignTrimmed: "€",
	get discountRate() {
		if (this.quantity >= 15) {
			return 15;
		}

		if (this.quantity >= 10) {
			return 10;
		}

		if (this.quantity >= 5) {
			return 5;
		}

		return 0;
	},
	get discountAmount() {
		const factor = (100 - this.discountRate) / 100;
		return (this.netLicenseAmount - (this.netLicenseAmount * factor)) * -1;
	},
	donation: true,
	get donationAmount() {
		return this.donation ? 1 : 0;
	},
	email: "",
	license: "basic",
	async mounted() {

		// fetch with options that allow using the preloaded response
		const response = await fetch("/buy/prices", {
			method: "GET",
			credentials: "include",
			mode: "no-cors",
		});

		const data = await response.json();

		this.currencySign        = data["currency-sign"];
		this.currencySignTrimmed = data["currency-sign-trimmed"];
		this.country             = data["country"];
		this.prices.basic        = data["basic-regular"];
		this.prices.enterprise   = data["enterprise-regular"];
		this.vatRate             = data["vat-rate"];
	},
	get needsZip() {
		return zips.includes(this.country);
	},
	newsletter: false,
	get netLicenseAmount() {
		return this.price * this.quantity;
	},
	get netAmount() {
		return this.netLicenseAmount + this.donationAmount + this.discountAmount;
	},
	openCheckout(license, quantity = 1) {
		this.license = license;
		this.quantity = quantity;
		checkout.showModal();
	},
	get price() {
		return this.prices[this.license];
	},
	prices: {
		basic: 99,
		enterprise: 349
	},
	quantity: 1,
	state: "",
	street: "",
	get totalAmount() {
		return this.netAmount + this.vatAmount;
	},
	get vatAmount() {
		const rate = this.vatIdExists ? 0 : this.vatRate;
		return (this.netAmount / 100) * rate;
	},
	vatRate: 0,
	vatId: "",
	get vatIdExists() {
		return Boolean(this.vatId?.length);
	},
	zip: "",
}).mount();
</script>
