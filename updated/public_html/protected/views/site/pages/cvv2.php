<?php
$this->pageTitle=Yii::app()->params['site']['nameFull'] . ' - SSL';
$this->breadcrumbs=array(
	'ssl',
);
?>
 
<div style="padding: 20px 40px">
	<h1>What is the Card Verification Code?</h1>
    
	<p>Also known as CVV2 or CVC2, it is a three-digit number imprinted on the signature panel of Visa, Mastercard, and Discover cards to help card-not-present merchants verify that the customer has a legitimate card in hand at the time of the order. The merchant asks the customer for the CVV2 code and then sends it to the card Issuer as part of the authorization request. The card Issuer checks the CVV2 code to determine its validity, then sends a CVV2 result back to the merchant along with the authorization. CVV2 is required on all Visa, Mastercard, and Discover cards.</p>
	
	<h3>Where is the code on my card?</h3>
	
	<p>Visa and MasterCard credit/debit cards have the three-digit value printed on the signature panel on the back of the card immediately following the card account number. The back panel of most Visa/MasterCard cards contain the full 16-digit account number, followed by the CVV/CVC code. Some banks, though, only show the last four digits of the account number followed by the code.</p>
	
	<p>American Express cards have a four-digit non-embossed code located on the front of the card.</p>
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="50%" align="center" style="padding-right:8px">
			<p><b>For Visa/MasterCard and Discover Holders</b></p>
			
			<p>The verification number is a 3-digit number printed on the back of your card. It appears after and to the right of your card number.</p>
			<img src="/images/help/visa_master_card.gif" border="0">
			</td>
			<td align="center" style="padding-left:8px">
			<p><b>On American Express Cards</b></p>
			
			<p>The American Express verification number is a small 4-digit number printed on the front of your card on the right hand side.</p>
			<img src="/images/help/american_expr_card.gif" border="0">
			</td>
		</tr>
	</table>   

</div>
