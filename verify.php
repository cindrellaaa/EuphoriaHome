<?php

require('config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $dbname);

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
            $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{    
    //$razorpay_order_id = $_SESSION['razorpay_order_id'];
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $contact = $_SESSION['contact'];
    $name = $_SESSION['name'];
    $gender = $_SESSION['gender'];
    $email = $_SESSION['email'];
    $amount = $_SESSION['amount'];
    $sql = "INSERT INTO `donation_details` (`contact`, `name`, `gender`, `email`, `amount`, `vstatus`, `razorpay_payment_id`) VALUES ('$contact', '$name', '$gender', '$email', '$amount', 'success', '$razorpay_payment_id')";
    if(mysqli_query($conn, $sql)){
        echo "Payment Details:";
    }
    $html = "<p>Your payment was successful</p>
             <p>Payment ID: {$_POST['razorpay_payment_id']}</p>";             
}
else
{
    $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
}
echo $html;
?>
<html>
<head>
</head>
<body>
<div id="center_button">
    <button onclick="location.href='about.php'">Back to Home</button>
</div>
</body>
</html>