<head>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php

# Note this line needs to change if you don't use Composer:
# require('connect-php-sdk/autoload.php');
require 'vendor/autoload.php';

# dotenv is used to read from the '.env' file created for credentials
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

# Replace these values. You probably want to start with your Sandbox credentials
# to start: https://developer.squareup.com/docs/testing/sandbox

# The access token to use in all Connect API requests. Use your *sandbox* access
# token if you're just testing things out.
$access_token = ($_ENV["USE_PROD"] == 'true')  ?  $_ENV["PROD_ACCESS_TOKEN"]
                                               :  $_ENV["SANDBOX_ACCESS_TOKEN"];

# Set 'Host' url to switch between sandbox env and production env
# sandbox: https://connect.squareupsandbox.com
# production: https://connect.squareup.com
$host_url = ($_ENV["USE_PROD"] == 'true')  ?  "https://connect.squareup.com"
                                           :  "https://connect.squareupsandbox.com";

$api_config = new \SquareConnect\Configuration();
$api_config->setHost($host_url);
# Initialize the authorization for Square
$api_config->setAccessToken($access_token);
$api_client = new \SquareConnect\ApiClient($api_config);

# Helps ensure this code has been reached via form submission
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  error_log("Received a non-POST request");
  echo "Request not allowed";
  http_response_code(405);
  return;
}

# Fail if the card form didn't send a value for `nonce` to the server
$nonce = $_POST['nonce'];
if (is_null($nonce)) {
  echo "Invalid card data";
  http_response_code(422);
  return;
}


/////////////// Customer //////////////////////
$customer_info = array(
  'given_name'  => $_POST['cc_name'],
  'email_address' => $_POST['cc_email'],
  'phone_number'  => $_POST['cc_phone'],
  'note'          => $_POST['cc_note'],
);

$customer_body = (object)$customer_info;

$api_instance = new SquareConnect\Api\CustomersApi($api_client);
$customer_body = new \SquareConnect\Model\CreateCustomerRequest(); // \SquareConnect\Model\CreateCustomerRequest | An object containing the fields to POST for the request.  See the corresponding object definition for field details.
$customer_id = '';

try {
    $result = $api_instance->createCustomer($customer_info);

    $customer_id = $result->getCustomer()->getId();
//    echo "<pre>";
//    print_r($result);
//    print_r($customer_id);
//    echo "</pre>";
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->createCustomer: ', $e->getMessage(), PHP_EOL;
}



$payments_api = new \SquareConnect\Api\PaymentsApi($api_client);

# To learn more about splitting payments with additional recipients,
# see the Payments API documentation on our [developer site]
# (https://developer.squareup.com/docs/payments-api/overview).
$request_body = array (
  "source_id" => $nonce,
  # Monetary amounts are specified in the smallest unit of the applicable currency.
  # This amount is in cents. It's also hard-coded for $1.00, which isn't very useful.
  "amount_money" => array (
    "amount" => $_POST['cc_amount'] * 100,
    "currency" => "USD"
  ),
  # Every payment you process with the SDK must have a unique idempotency key.
  # If you're unsure whether a particular payment succeeded, you can reattempt
  # it with the same idempotency key without worrying about double charging
  # the buyer.
  "idempotency_key" => uniqid(),

  "customer_id" => $customer_id,
  'note' => $_POST['cc_note']

);


# The SDK throws an exception if a Connect endpoint responds with anything besides
# a 200-level HTTP code. This block catches any exceptions that occur from the request.
try {
  $result = $payments_api->createPayment($request_body);
?>
<div class="jumbotron text-center">
  <h1 class="display-3">Thank You!</h1>
  <p class="lead"><strong>Please check your email</strong> for further instructions on how to complete your pay.</p>
  <hr>
  <p class="lead">
    <a class="btn btn-primary btn-sm" href="https://dragoncomputerservices.com/" role="button">Continue to homepage</a>
  </p>
</div>
<?php
//  echo "<pre>";
//  print_r($result);
//  echo "</pre>";
} catch (\SquareConnect\ApiException $e) {
?>
<div class="jumbotron text-center">
  <h1 class="display-3">Sorry!</h1>
  <p class="lead"><strong>Please check your information</strong> for further instructions on how to complete your pay.</p>
  <hr>
  <p class="lead">
    <a class="btn btn-primary btn-sm" href="https://dragoncomputerservices.com/" role="button">Continue to homepage</a>
  </p>
</div>
<?php
//  echo "Caught exception!<br/>";
//  echo "<pre>"; var_dump($e); echo "</pre>";
//  print_r("<strong>Response body:</strong><br/>");
//  echo "<pre>"; var_dump($e->getResponseBody()); echo "</pre>";
//  echo "<br/><strong>Response headers:</strong><br/>";
//  echo "<pre>"; var_dump($e->getResponseHeaders()); echo "</pre>";
}

?>
</body>