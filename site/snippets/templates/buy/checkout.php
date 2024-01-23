<dialog v-scope class="checkout">
	<form action="<?= url('buy/basic') ?>" method="POST">
		<div class="field">
			<label class="label" for="email">Email</label>
			<input id="email" name="email" class="input" type="email" required v-model="email" placeholder="mail@example.com">
		</div>
		<div class="field">
			<label class="label" for="country">Country</label>
			<select id="country" name="country" class="input" v-model="country">
				<option v-for="(code, name) in countries" :value="code">{{ name }}</option>
			</select>
		</div>
		<div v-if="needsZip" class="field">
			<label class="label" for="zip">Postal Code</label>
			<input id="zip" class="input" :required="needsZip" v-model="zip" type="text">
		</div>
		<div class="field">
			<label class="label" for="vatid">VAT ID</label>
			<input id="vatid" name="vatId" class="input" type="text" v-model="vatId">
			<p v-if="vatIdExists" class="color-gray-700 text-xs pt-1">Your VAT ID will be validated on checkout</p>
		</div>

		<fieldset v-if="vatIdExists">

			<div class="field">
				<label class="label" for="company">Company Name</label>
				<input id="company" name="company" class="input" type="text" v-model="company" :required="vatIdExists">
			</div>

			<div class="field">
				<label class="label" for="street">Street</label>
				<input id="street" name="street" class="input" type="text" v-model="street" :required="vatIdExists">
			</div>

			<div class="field">
				<label class="label" for="city">Town/City</label>
				<input id="city" name="city" class="input" type="text" v-model="city" :required="vatIdExists">
			</div>

			<div class="field">
				<label class="label" for="state">State/County</label>
				<input id="state" name="state" class="input" type="text" v-model="state" :required="vatIdExists">
			</div>
		</fieldset>

		<div class="field">
			<label class="label" for="newsletter">Newsletter</label>
			<label class="checkbox">
				<input id="newsletter" type="checkbox" name="newsletter" v-model="newsletter">
				Subscribe to our newsletter
			</label>
		</div>

		<div class="buttons">
			<button formmethod="dialog" formnovalidate type="submit" class="btn btn--filled"><?= icon('cancel') ?> Cancel</button>
			<button class="btn btn--filled"><?= icon('cart') ?> Checkout</button>
		</div>
	</form>
	<div class="checkout-preview">
		<div>
			<label class="label">Your order</label>
			<table>
				<tr>
					<th>
						<div class="inputs">
							<input type="number" name="quantity" value="1" required min="1" max="100" step="1" v-model="quantity">
							<select required v-model="license">
								<option value="basic">Kirby Basic</option>
								<option value="enterprise">Kirby Enterprise</option>
							</select>
						</div>
					</th>
					<td>€{{ netPrice.toFixed(2) }}</td>
				</tr>
				<tr v-if="discount">
					<th>
						Volume Discount (-{{ discount }}%)
					</th>
					<td>€{{ discounted.toFixed(2) }}</td>
				</tr>
				<tr>
					<th>
						VAT ({{ vatFactor }}%)
					</th>
					<td>€{{ vat.toFixed(2) }}</td>
				</tr>
				<tr v-if="donation">
					<th>
						Your donation
					</th>
					<td>€1.00</td>
				</tr>
				<tr>
					<th>
						Total
					</th>
					<td>€{{ total.toFixed(2) }}</td>
				</tr>
			</table>
		</div>

		<div class="field">
			<label for="donate-checkbox" class="label">Support a good cause</label>
			<p class="mb-3">
				For every license purchase we donate €<?= $donation['teamAmount'] ?> to
				<a class="link" rel="noopener noreferrer" target="_blank" href="<?= $donation['link'] ?>"><?= $donation['charity'] ?></a> <?= $donation['purpose'] ?>.
			</p>
			<label class="checkbox">
				<input id="donate-checkbox" type="checkbox" name="donate" v-model="donation">
				Donate an additional €<?= $donation['customerAmount'] ?> 💛
			</label>
		</div>
	</div>
</dialog>

<script type="module">
// document.querySelector(".checkout").showModal();

const prices = {
	basic: 99,
	enterprise: 349
};

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

import {
	createApp,
	reactive
} from '<?= url('assets/js/libraries/petite-vue.js') ?>';

createApp({
	city: "",
	company: "",
	countries: countries,
	country: "DE",
	get discount() {
		if (this.quanitity >= 15) {
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
	get discounted() {
		const factor = (100 - this.discount) / 100;
		return (this.netPrice - (this.netPrice * factor)) * -1;
	},
	donation: true,
	email: "",
	license: "basic",
	get needsZip() {
		return zips.includes(this.country);
	},
	newsletter: false,
	get netPrice() {
		return this.price * this.quantity;
	},
	quantity: 1,
	get price() {
		return prices[this.license];
	},
	state: "",
	street: "",
	get total() {
		let total = this.netPrice + this.discounted + this.vat;

		if (this.donation) {
			total += 1;
		}

		return total;
	},
	get vatFactor() {
		return this.vatIdExists ? 0 : 19;
	},
	get vat() {
		if (this.vatIdExists) {
			return 0;
		}

		return ((this.netPrice + this.discounted) / 100) * this.vatFactor;
	},
	vatId: "",
	get vatIdExists() {
		return Boolean(this.vatId?.length);
	},
	zip: "",
}).mount();

</script>

<style>
.checkout[open] {
	font-size: var(--text-sm);
	margin: auto;
	background: var(--color-light);
	width: 50rem;
	box-shadow: var(--shadow-2xl);
	border-radius: var(--rounded);
	display: grid;
	grid-template-columns: 1fr 1fr;
	grid-template-areas: "preview form";
}
.checkout::backdrop {
	background: rgba(0,0,0, .7);
}
.checkout-preview {
	grid-area: preview;
	background: var(--color-white);
}
.checkout form {
	grid-area: form;
	padding: var(--spacing-8);
}
.checkout .field + .field {
	margin-top: var(--spacing-6);
}
.checkout .label {
	display: block;
	font-weight: var(--font-bold);
	margin-bottom: var(--spacing-2);
}
.checkout .input {
	height: 2.25rem;
	background: var(--color-white);
	padding: var(--spacing-2);
	border-radius: var(--rounded-sm);
	box-shadow: 0px 0px 0px 1px var(--color-border);
}
.checkout select.input {
	appearance: none;
}
.checkout .checkbox {
	height: 2.25rem;
	display: flex;
	align-items: center;
	color: var(--color-black);
	padding: var(--spacing-2) var(--spacing-3);
	background: var(--color-white);
	border-radius: var(--rounded-sm);
	box-shadow: 0px 0px 0px 1px var(--color-border);
	gap: var(--spacing-3);
}

.checkout fieldset {
	margin-block: var(--spacing-6);
}
.checkout .buttons {
	margin-top: var(--spacing-8);
	display: flex;
	gap: .75rem;
}
.checkout .buttons .btn {
	flex-basis: 50%;
	flex-grow: 1;
}
.checkout-preview {
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	padding: var(--spacing-8);
}
.checkout-preview :where(th, td) {
	border-top: 1px solid var(--color-border);
	padding-block: var(--spacing-2);
}
.checkout-preview th {
	font-weight: var(--font-normal);
}
.checkout-preview th .inputs {
	display: flex;
	gap: .25rem;
	align-items: center;
}
.checkout-preview th :where(input, select) {
	background: var(--color-light);
	height: 1.25rem;
	line-height: 1.25;
	padding-inline: var(--spacing-1);
	border-radius: var(--rounded);
}
.checkout-preview th input {
	width: 3rem;
}
.checkout-preview td {
	text-align: right;
}
.checkout-preview tr:last-child * {
	border-top-width: 2px;
	font-weight: var(--font-bold);
}
</style>
