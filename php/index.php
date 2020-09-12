<?php
require 'vendor/autoload.php';
// dotenv is used to read from the '.env' file created for credentials
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <meta http-equiv="Content-Language" content="en-us">
  <meta name="GENERATOR" content="Microsoft FrontPage 6.0">
  <meta name="ProgId" content="FrontPage.Editor.Document">
  <title>Square Payment</title>

  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.4.2/libphonenumber-js.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!--  <script src="https://libphonenumber.googlecode.com/svn/trunk/javascript/i18n/phonenumbers/phonenumberutil.js"></script> -->

  <!-- link to the SqPaymentForm library -->
  <script type="text/javascript" src=
    <?php
        echo "\"";
        echo ($_ENV["USE_PROD"] == 'true')  ?  "https://js.squareup.com/v2/paymentform"
                                            :  "https://js.squareupsandbox.com/v2/paymentform";
        echo "\"";
    ?>
  ></script>
  <script type="text/javascript">
    window.applicationId =
      <?php
        echo "\"";
        echo ($_ENV["USE_PROD"] == 'true')  ?  $_ENV["PROD_APP_ID"]
                                            :  $_ENV["SANDBOX_APP_ID"];
        echo "\"";
      ?>;
    window.locationId =
    <?php
      echo "\"";
      echo ($_ENV["USE_PROD"] == 'true')  ?  $_ENV["PROD_LOCATION_ID"]
                                          :  $_ENV["SANDBOX_LOCATION_ID"];
      echo "\"";
    ?>;


  </script>

  <!-- link to the local SqPaymentForm initialization -->
  <script type="text/javascript" src="js/sq-payment-form.js"></script>
  <!-- link to the custom styles for SqPaymentForm -->
  <link rel="stylesheet" type="text/css" href="css/sq-payment-form.css">
</head>

<body bgcolor="#86AEAE" background="../images/BG013.JPG">

<p align="center"><img border="0" src="../images/logo-color.GIF" width="150" height="117"><img border="0" src="../images/T-Logo-Name.gif" width="70%" height="15%"></p>

  <!-- Begin Payment Form -->
  <div class="col-md-4 col-sm-6 offset-md-4 offset-sm-3">
    <div id="sq-ccbox card card-outline-secondary">
      <div class="card-body">
      <!--
        You should replace the action attribute of the form with the path of
        the URL you want to POST the nonce to (for example, "/process-card").

        You need to then make a "Charge" request to Square's Payments API with
        this nonce to securely charge the customer.

        Learn more about how to setup the server component of the payment form here:
        https://developer.squareup.com/docs/payments-api/overview
      -->
        <h3 class="text-center">Credit Card Payment</h3>
        <hr>

        <form id="nonce-form" novalidate action="process-card.php" method="post">

          <div class="sq-field">
              <label for="cc_name">Card Holder's Name</label>
              <input type="text" class="form-control" name="cc_name" id="cc_name" pattern="\w+ \w+.*" title="First and last name" placeholder="First and last name" style="height: 50px;" required>
          </div>

          <div class="sq-field">
              <label for="cc_email">Email address</label>
              <input type="email" class="email form-control" name="cc_email" id="cc_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Email address" placeholder="Enter email" style="height: 50px;" required>
              <div class="valid-feedback feedback-pos">
                Looks good!
              </div>
              <div class="invalid-feedback feedback-pos">
                Please input valid email ID
              </div>
          </div>

          <div class="sq-field">
              <label for="cc_phone">Phone Number</label>
              <input type="tel" class="form-control phone-format" name="cc_phone" id="cc_phone" title="Phone Number" placeholder="*XXX-XXX-XXXX" required style="height: 50px;">
          </div>

          <div class="sq-field">
              <label for="cc_note">Note</label>
              <input type="text" class="form-control" name="cc_note" id="cc_note" title="Note" placeholder="Enter note" required style="height: 50px;">
          </div>

          <div class="row">
              <label class="sq-label col-md-12">Amount</label>
          </div>
          <div class="form-inline">
              <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                  <input type="text" class="form-control text-right" name="cc_amount" id="cc_amount" placeholder="0.00">
                  <!--<div class="input-group-append"><span class="input-group-text">.00</span></div>-->
              </div>
          </div>
          <hr>

          <div class="sq-field">
            <label class="sq-label">Card Number</label>
            <div id="sq-card-number"></div>
          </div>
          <div class="sq-field-wrapper">
            <div class="sq-field sq-field--in-wrapper">
              <label class="sq-label">CVV</label>
              <div id="sq-cvv"></div>
            </div>
            <div class="sq-field sq-field--in-wrapper">
              <label class="sq-label">Expiration</label>
              <div id="sq-expiration-date"></div>
            </div>
            <div class="sq-field sq-field--in-wrapper">
              <label class="sq-label">Postal</label>
              <div id="sq-postal-code"></div>
            </div>
          </div>

          <div class="sq-field">
            <button id="sq-creditcard" class="btn btn-success btn-lg btn-block" type="submit"  onclick="onGetCardNonce(event)">
              Pay Now
            </button>
          </div>

          <!--
            After a nonce is generated it will be assigned to this hidden input field.
          -->
          <div id="error"></div>
          <input type="hidden" id="card-nonce" name="nonce">
        </form>
      </div>
    </div>
  </div>
  <!-- End Payment Form -->

  <p align="center">&nbsp;</p>


  <div align="center">
    <center>
      <table border="0" width="98%">
        <tr>
          <td>
            <p align="center">
              <font size="1"><a href="../privacy_statement.html" target="_blank">Privacy Statement</a> &lt;&gt; <a href="../DCS_terms.html" target="_blank">Terms &amp; Conditions<br>
              </a></font>
            </p>
            <div align="center">
              <dt style="line-height: 100%"><font size="1">Copyright © 1999 Dragon
              Computer Services. All rights reserved.&nbsp; Revised: 
              <!--webbot
              bot="TimeStamp" S-Type="EDITED" S-Format="%B %d, %Y" startspan -->March 29, 2016<!--webbot bot="TimeStamp" i-checksum="17907" endspan -->
              </font></dt>
            </div>
            <div align="center">
              <dt style="line-height: 100%"><font size="1">Established April, 1999</font></dt>
            </div>
            <div align="center">
              <dt style="line-height: 100%">&nbsp;</dt>
            </div>
          </td>
        </tr>
      </table>
    </center>
  </div>

  <script type="text/javascript">
    $(".phone-format").keyup(function () {
        var val_old = $(this).val();
        var newString = new libphonenumber.AsYouType('US').input(val_old);
        $(this).focus().val('').val(newString);
    });
  </script>

</body>
</html>
