<dialog id="checkout" class="dialog checkout" :data-loading="isFetchingPrices" @click="closeCheckout">
	<form class="dialog-form" action="<?= url('buy') ?>" method="POST" target="_blank" @submit="cachePersonalInfo">
		<div class="checkout-preview">
			<div>
				<h2 class="label">Your order</h2>
				<table>
					<tr>
						<th>
							<div class="inputs">
								<input type="number" name="quantity" value="1" required min="<?= option('buy.quantities.min') ?>" max="<?= option('buy.quantities.max') ?>" step="1" v-model="quantity" @input="restrictQuantity">
								<select required name="product" v-model="product" value="<?= $basic->value() ?>">
									<option value="<?= $basic->value() ?>" selected>Kirby <?= $basic->label() ?></option>
									<option value="<?= $enterprise->value() ?>">Kirby <?= $enterprise->label() ?></option>
								</select>
							</div>
						</th>
						<td>{{ amount(netLicenseAmount) }}</td>
					</tr>
					<tr v-if="discountRate">
						<th>
							<p>Volume Discount (-{{ discountRate }}%)</p>
							<p v-if="quantity >= 50" class="text-xs color-gray-700">Please <a class="underline" href="mailto:support@getkirby.com">contact us</a> for <span class="whitespace-nowrap">high-volume</span> discounts</p>
						</th>
						<td>{{ amount(discountAmount) }}</td>
					</tr>
					<tr v-if="personalInfo.donate">
						<th>
							Your donation
						</th>
						<td>{{ amount(donationAmount) }}</td>
					</tr>
					<tr class="subtotal" v-if="locale.vatRate > 0">
						<th>
							Subtotal
						</th>
						<td>{{ amount(subtotal) }}</td>
					</tr>
					<tr v-if="locale.vatRate > 0">
						<th>
							VAT ({{ vatIdExists ? 0 : locale.vatRate * 100 }}%)
						</th>
						<td>{{ amount(vatAmount) }}</td>
					</tr>
					<tr class="total">
						<th>
							Total
						</th>
						<td>{{ amount(totalAmount) }}</td>
					</tr>
				</table>
			</div>

			<?php if ($donation['customerAmount'] > 0): ?>
			<div>
				<h2 class="font-bold">Support a good cause</h2>
				<p class="mb-3 help">
					For every purchased license we donate <span class="whitespace-nowrap">€<?= $donation['teamAmount'] ?></span><span class="whitespace-nowrap" v-if="locale.currency !== '€'" v-text="' (~ ' + locale.currency + locale.prices.donation.team + ')'"></span> to
					<a class="underline" rel="noopener noreferrer" target="_blank" href="<?= $donation['link'] ?>"><?= $donation['charity'] ?></a> <?= $donation['purpose'] ?>.
				</p>
				<label class="checkbox">
					<input id="donate" type="checkbox" name="donate" v-model="personalInfo.donate">
					<?php if ($donation['customerAmount'] === $donation['teamAmount']): ?>
					Match our donation 💛
					<?php else: ?>
					<span v-text="donationText">Donate an additional €<?= $donation['customerAmount'] ?> per license 💛</span>
					<?php endif ?>
				</label>
			</div>
			<?php endif ?>
		</div>
		<div class="checkout-form">
			<div>
				<div class="field">
					<label class="label" for="email">Email <abbr title="Required">*</abbr></label>
					<input id="email" name="email" class="input" type="email" required v-model="personalInfo.email" placeholder="mail@example.com">
				</div>
				<div class="checkout-country">
					<div class="field flex-grow">
						<label class="label" for="country">Country <abbr title="Required">*</abbr></label>
						<select id="country" name="country" required autocomplete="country" class="input" v-model="personalInfo.country" @change="changeCountry">
							<?php foreach ($countries as $countryCode => $countryName): ?>
							<option value="<?= $countryCode ?>"><?= $countryName ?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div v-if="needsPostalCode" class="field" style="flex-basis: 7rem">
						<label class="label" for="postalCode">Postal Code <abbr title="Required">*</abbr></label>
						<input id="postalCode" name="postalCode" class="input" autocomplete="postal-code" :required="needsPostalCode" v-model="personalInfo.postalCode" type="text">
					</div>
				</div>

				<fieldset class="checkout-company" v-if="locale.vatRate > 0">
					<legend>Company</legend>
					<div class="fields">
						<div class="field">
							<label class="label" for="vatId">VAT ID</label>
							<input id="vatId" name="vatId" class="input" type="text" v-model="personalInfo.vatId">
						</div>

						<div class="field" v-if="vatIdExists">
							<label class="label" for="company">Company <abbr title="Required">*</abbr></label>
							<input id="company" name="company" placeholder="Company name …" autocomplete="organization" class="input" type="text" v-model="personalInfo.company" :required="vatIdExists">
						</div>

						<div class="field" v-if="vatIdExists">
							<label class="label" for="street">Street <abbr title="Required">*</abbr></label>
							<input id="street" name="street" class="input" type="text" v-model="personalInfo.street" :required="vatIdExists">
						</div>

						<div class="field" v-if="vatIdExists">
							<label class="label" for="city">Town/City <abbr title="Required">*</abbr></label>
							<input id="city" name="city" class="input" type="text" v-model="personalInfo.city" :required="vatIdExists">
						</div>

						<div class="field" v-if="vatIdExists">
							<label class="label" for="state">State/County <abbr title="Required">*</abbr></label>
							<input id="state" name="state" class="input" type="text" v-model="personalInfo.state" :required="vatIdExists">
						</div>
					</div>
					<p v-if="vatIdExists" class="help">Your VAT ID and company details will be validated on&nbsp;checkout.</p>
				</fieldset>

				<div class="field">
					<label class="font-bold flex items-center" style="gap: var(--spacing-2)">
						<input id="newsletter" type="checkbox" name="newsletter" v-model="personalInfo.newsletter">
						Subscribe to our Kosmos newsletter
					</label>
					<p class="help">We won't ever spam you! You can unsubscribe at any time. <a class="underline" target="_blank" href="<?= url('kosmos') ?>">Learn more about Kosmos…</a></p>
				</div>

				<div v-if="product === '<?= $basic->value() ?>'" class="field">
					<label class="font-bold flex items-center" style="gap: var(--spacing-2)">
						<input id="limit" type="checkbox" name="limit" required>
						<span>Confirm the revenue limit <abbr title="Required">*</abbr></span>
					</label>
					<p class="help">
						End customers must not exceed an annual revenue/funding of
						<strong><?= $revenueLimitVerbose ?><span v-if="locale.revenueLimit.length" v-text="locale.revenueLimit"></span></strong>
						to be eligible for this license.
						<button type="button" class="underline" @click="product = '<?= $enterprise->value() ?>'">Switch to Enterprise</button> to remove the revenue limit.
					</p>
				</div>
			</div>
			<div class="buttons">
				<button type="submit" class="btn btn--filled"><?= icon('cart') ?> Checkout</button>
			</div>
		</div>

		<p class="checkout-loader" v-if="isFetchingPrices">
			<?= icon('loader') ?>
		</p>

	</form>
</dialog>

<style>
@import url("/assets/css/site/dialog.css");

@media screen and (min-width: 40rem) {
	.checkout form {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-template-areas: "preview form";
	}
}

.dialog[data-loading="true"] {
	pointer-events: none;
}

.checkout-loader {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(255,255,255,.7);
}
.checkout-loader svg {
	animation: Spin 1.5s linear infinite;
	position: absolute;
	top: 50%;
	left: 50%;
}

@keyframes Spin {
	100% {
		transform: rotate(360deg);
	}
}

.checkout-preview {
	grid-area: preview;
	background: var(--color-white);
}

.checkout-form {
	grid-area: form;
	padding: var(--spacing-8);
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.checkout .help {
	font-size: var(--text-xs);
	padding-top: var(--spacing-1);
	color: var(--color-gray-700);
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
	gap: var(--spacing-6);
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
.checkout-preview :where(tr.total, tr.subtotal) > * {
	border-top-width: 2px;
}
.checkout-preview tr.total > * {
	font-weight: var(--font-bold);
}

.checkout-country {
	display: flex;
	align-items: center;
	margin-top: var(--spacing-6);
	margin-bottom: var(--spacing-6);
	gap: var(--spacing-2);
}
.checkout-country .field {
	margin-top: 0 !important;
}

.checkout .btn.btn--filled {
	background: var(--color-purple-400) !important;
	border-color: var(--color-purple-400);
	color: var(--color-purple-900) !important;
	box-shadow: var(--shadow);
}
.checkout .btn.btn--filled svg {
	color: var(--color-purple-800) !important;
}

.checkout-company {
	margin-top: var(--spacing-6);
	margin-bottom: var(--spacing-6);
}
.checkout-company legend {
	font-weight: var(--font-bold);
	margin-bottom: var(--spacing-2);
}
.checkout-company .fields {
	border: 1px solid var(--color-border);
	border-radius: var(--rounded);
	overflow: clip;
}
.checkout-company .field {
	display: grid;
	grid-template-columns: 6.75rem 1fr;
	background: var(--color-white);
	align-items: center;
}
.checkout-company .field + .field {
	margin-top: 0;
	border-top: 1px solid var(--color-border);
}
.checkout-company .label {
	display: flex;
	align-items: center;
	height: 2.25rem;
	font-weight: var(--font-normal);
	margin-bottom: 0;
	white-space: nowrap;
	background: var(--color-gray-100);
	padding: var(--spacing-2);
	border-right: 1px solid var(--color-border);
	color: var(--color-gray-800);
}
.checkout-company .field .input {
	box-shadow: none;
	outline-offset: -2px;
}

</style>
