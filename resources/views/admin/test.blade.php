<h1>DATE : {{Carbon\Carbon::now()->toAtomString()}}</h1>


<form target="_new" method="post" action="{{action('Api\IpnController@postNotify')}}">
<input name="mc_gross" type="hidden" value="5000.00" />
<input name="outstanding_balance" type="hidden" value="0.00" />
<input name="period_type" type="hidden" value="Regular" />
<input name="next_payment_date" type="hidden" value="03:00:00 Apr 23, 2020 PDT" />
<input name="protection_eligibility" type="hidden" value="Ineligible" />
<input name="payment_cycle" type="hidden" value="Daily" />
<input name="tax" type="hidden" value="0.00" />
<input name="payer_id" type="hidden" value="RPUPAYBF4J3Z4" />
<input name="payment_date" type="hidden" value="08:58:12 Apr 22, 2020 PDT" />
<input name="payment_status" type="hidden" value="Completed" />
<input name="product_name" type="hidden" value="1 Month Corporate Plan subscription on Qcommerce." />
<input name="charset" type="hidden" value="windows-1252" />
<input name="recurring_payment_id" type="hidden" value="I-DHYSUB8UANGL" />
<input name="first_name" type="hidden" value="Bryan" />
<input name="mc_fee" type="hidden" value="160.00" />
<input name="notify_version" type="hidden" value="3.9" />
<input name="amount_per_cycle" type="hidden" value="5000.00" />
<input name="payer_status" type="hidden" value="verified" />
<input name="currency_code" type="hidden" value="PHP" />
<input name="business" type="hidden" value="xenzen09-facilitator@gmail.com" />
<input name="verify_sign" type="hidden" value="AW5YcxHWmMakqNHfkLRiZzGKj.E6AkHgoxdgAgFM0LirDfjUDzLrjx2k" />
<input name="payer_email" type="hidden" value="xenzen09-buyer@gmail.com" />
<input name="initial_payment_amount" type="hidden" value="0.00" />
<input name="profile_status" type="hidden" value="Active" />
<input name="amount" type="hidden" value="5000.00" />
<input name="txn_id" type="hidden" value="3HV51929774831440" />
<input name="payment_type" type="hidden" value="instant" />
<input name="last_name" type="hidden" value="Buyer" />
<input name="receiver_email" type="hidden" value="xenzen09-facilitator@gmail.com" />
<input name="receiver_id" type="hidden" value="TNC3XEGRGTB7J" />
<input name="txn_type" type="hidden" value="recurring_payment" />
<input name="mc_currency" type="hidden" value="PHP" />
<input name="residence_country" type="hidden" value="US" />
<input name="test_ipn" type="hidden" value="1" />
<input name="transaction_subject" type="hidden" value="1 Month Corporate Plan subscription on Qcommerce." />
<input name="shipping" type="hidden" value="0.00" />
<input name="product_type" type="hidden" value="1" />
<input name="time_created" type="hidden" value="02:29:34 Apr 21, 2020 PDT" />
<input name="ipn_track_id" type="hidden" value="ee6049d51b230" />
  <input type="submit"/>
</form>





	{{-- <input type="hidden" name="payment_type" value="instant" />
	<input type="hidden" name="payment_date" value="14:18:27 Apr 21, 2020 PST" />
	<input type="hidden" name="payment_status" value="Completed" />
	<input type="hidden" name="address_status" value="confirmed" />
	<input type="hidden" name="payer_status" value="verified" />
	<input type="hidden" name="first_name" value="John" />
	<input type="hidden" name="last_name" value="Smith" />
	<input type="hidden" name="payer_email" value="buyer@paypalsandbox.com" />
	<input type="hidden" name="payer_id" value="TESTBUYERID01" />
	<input type="hidden" name="address_name" value="John Smith" />
	<input type="hidden" name="address_country" value="United States" />
	<input type="hidden" name="address_country_code" value="US" />
	<input type="hidden" name="address_zip" value="95131" />
	<input type="hidden" name="address_state" value="CA" />
	<input type="hidden" name="address_city" value="San Jose" />
	<input type="hidden" name="address_street" value="123 any street" />
	<input type="hidden" name="business" value="seller@paypalsandbox.com" />
	<input type="hidden" name="receiver_email" value="seller@paypalsandbox.com" />
	<input type="hidden" name="receiver_id" value="seller@paypalsandbox.com" />
	<input type="hidden" name="residence_country" value="US" />
	<input type="hidden" name="item_name1" value="something" />
	<input type="hidden" name="item_number1" value="AK-1234" />
	<input type="hidden" name="quantity" value="1" />
	<input type="hidden" name="tax" value="0" />
	<input type="hidden" name="mc_currency" value="USD" />
	<input type="hidden" name="mc_fee" value="0.44" />
	<input type="hidden" name="mc_gross_1" value="12.34" />
	<input type="hidden" name="mc_handling" value="2.06" />
	<input type="hidden" name="mc_handling1" value="1.67" />
	<input type="hidden" name="mc_shipping" value="3.02" />
	<input type="hidden" name="mc_shipping1" value="1.02" />
	<input type="hidden" name="txn_type" value="cart" />
	<input type="hidden" name="txn_id" value="508716559" />
	<input type="hidden" name="notify_version" value="2.1" />
	<input type="hidden" name="receipt_ID" value="" />
	<input type="hidden" name="invoice" value="" /> --}}