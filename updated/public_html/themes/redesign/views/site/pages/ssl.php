<?php
$this->pageTitle=Yii::app()->params['site']['nameFull'] . ' - SSL';
$this->breadcrumbs=array(
	'ssl',
);
?>
 
<div style="padding: 20px 40px">
	<h1>What is SSL?</h1>
    
    <p>
		<p>
			SSL (Secure Sockets Layer), also known as TLS (Transport Layer Security), is a protocol that allows two programs to communicate with each other in a secure way. Like TCP/IP, SSL allows programs to create "sockets," endpoints for communication, and make connections between those sockets. But SSL, which is built on top of TCP, adds the additional capability of encryption. The HTTPS protocol spoken by web browsers when communicating with secure sites is simply the usual World Wide Web HTTP protocol, "spoken" over SSL instead of directly over TCP.
		</p>
		<p>
			In addition to providing privacy, SSL encryption also allows us to verify the identity of the party we are talking to. This can be very important if we don't trust the Internet. While it is unlikely in practice that the root DNS servers of the Internet will be subverted, a "man in the middle" attack elsewhere on the network could substitute the address of one Internet site for another. SSL prevents this scenario by providing a mathematically sound way to verify the other program's identity. When you log on to your bank's website, you want to be very, very sure you are talking to your bank!
		</p>
    </p>    

</div>
