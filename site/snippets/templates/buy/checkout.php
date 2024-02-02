<dialog class="checkout" @click="closeCheckout">
	<form action="<?= url('buy') ?>" method="POST" target="_blank">
		<div class="checkout-preview">
			<div>
				<label class="label">Your order</label>
				<table>
					<tr>
						<th>
							<div class="inputs">
								<input type="number" name="quantity" value="1" required min="1" max="100" step="1" v-model="quantity">
								<select required name="license" v-model="license">
									<option value="basic" selected>Kirby Basic</option>
									<option value="enterprise">Kirby Enterprise</option>
								</select>
							</div>
						</th>
						<td>{{ amount(netLicenseAmount) }}</td>
					</tr>
					<tr v-if="discountRate">
						<th>
							Volume Discount (-{{ discountRate }}%)
						</th>
						<td>{{ amount(discountAmount) }}</td>
					</tr>
					<tr v-if="donation">
						<th>
							Your donation
						</th>
						<td>€1.00</td>
					</tr>
					<tr>
						<th>
							VAT ({{ vatRate }}%)
						</th>
						<td>{{ amount(vatAmount) }}</td>
					</tr>
					<tr>
						<th>
							Total
						</th>
						<td>{{ amount(totalAmount) }}</td>
					</tr>
				</table>
			</div>

			<div class="field">
				<label for="donate" class="label">Support a good cause</label>
				<p class="mb-3">
					For every license purchase we donate €<?= $donation['teamAmount'] ?> to
					<a class="link" rel="noopener noreferrer" target="_blank" href="<?= $donation['link'] ?>"><?= $donation['charity'] ?></a> <?= $donation['purpose'] ?>.
				</p>
				<label class="checkbox">
					<input id="donate" type="checkbox" name="donate" v-model="donation">
					Donate an additional €<?= $donation['customerAmount'] ?> 💛
				</label>
			</div>
		</div>
		<div class="checkout-form">
			<div class="field">
				<label class="label" for="email">Email <abbr title="Required">*</abbr></label>
				<input id="email" name="email" class="input" type="email" required v-model="email" placeholder="mail@example.com">
			</div>
			<div class="field">
				<label class="label" for="country">Country <abbr title="Required">*</abbr></label>
				<select id="country" name="country" required autocomplete="country" class="input" v-model="country">
					<?php foreach ($countries as $countryCode => $countryName): ?>
					<option value="<?= $countryCode ?>"><?= $countryName ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div v-if="needsZip" class="field">
				<label class="label" for="zip">Postal Code <abbr title="Required">*</abbr></label>
				<input id="zip" name="postalCode" class="input" autocomplete="postal-code" :required="needsZip" v-model="zip" type="text">
			</div>
			<div class="field">
				<label class="label" for="vatId">VAT ID</label>
				<input id="vatId" name="vatId" class="input" type="text" v-model="vatId">
				<p v-if="vatIdExists" class="color-gray-700 text-xs pt-1">Your VAT ID will be validated on checkout</p>
			</div>

			<fieldset v-if="vatIdExists">

				<div class="field">
					<label class="label" for="company">Company Name <abbr title="Required">*</abbr></label>
					<input id="company" name="company" autocomplete="organization" class="input" type="text" v-model="company" :required="vatIdExists">
				</div>

				<div class="field">
					<label class="label" for="street">Street <abbr title="Required">*</abbr></label>
					<input id="street" name="street" class="input" type="text" v-model="street" :required="vatIdExists">
				</div>

				<div class="field">
					<label class="label" for="city">Town/City <abbr title="Required">*</abbr></label>
					<input id="city" name="city" class="input" type="text" v-model="city" :required="vatIdExists">
				</div>

				<div class="field">
					<label class="label" for="state">State/County <abbr title="Required">*</abbr></label>
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
				<button type="submit" class="btn btn--filled"><?= icon('cart') ?> Checkout</button>
			</div>
		</div>
	</form>
</dialog>

<style>

.checkout[open] {
	font-size: var(--text-sm);
	margin: auto;
	background: var(--color-light);
	width: 50rem;
	box-shadow: var(--shadow-2xl);
	border-radius: var(--rounded);
}
.checkout::backdrop {
	background: rgba(0,0,0, .7);
}

body:has(.checkout[open]) {
	overflow: hidden;
}

@media screen and (min-width: 40rem) {
	.checkout form {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-template-areas: "preview form";
	}
}

.checkout-preview {
	grid-area: preview;
	background: var(--color-white);
}
.checkout-form {
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
.checkout .label abbr {
	text-decoration: none;
	color: var(--color-red-500);
	margin-left: .125rem;
	display: none;
}

.field:has(*:invalid) .label abbr {
	display: inline;
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
	cursor: pointer;
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
