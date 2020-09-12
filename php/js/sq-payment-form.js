/**
 * Define callback function for "sq-button"
 * @param {*} event
 */

function onGetCardNonce(event) {
  var isValid = false;

  isValid = /^[a-zA-Z]+ [a-zA-Z]+$/.test( document.getElementById('cc_name').value );
  if ( !isValid ) {
    alert('Please enter your full name (first & last name).');
    document.getElementById('cc_name').focus();
    event.preventDefault();
    return;
  }

  isValid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test( document.getElementById('cc_email').value );
  if ( !isValid )
  {
    alert('Please enter your email address.');
    document.getElementById('cc_email').focus();
    event.preventDefault();
    return;
  }
/*
  var phoneUtil = i18n.phonenumbers.PhoneNumberUtil.getInstance();
  var regionCode = phoneUtil.getRegionCodeForCountryCode('US');
  var number = phoneUtil.parseAndKeepRawInput(document.getElementById('cc_phone').value, regionCode);

  if (phoneUtil.isValidNumber(number)) {
  }
*/
  isValid = /^(1\s|1|)?((\(\d{3}\))|\d{3})(\-|\s)?(\d{3})(\-|\s)?(\d{4})$/.test( document.getElementById('cc_phone').value );
  if ( !isValid )
  {
    alert('Please enter your phone number.(XXX-XXX-XXXX)');
    document.getElementById('cc_phone').focus();
    event.preventDefault();
    return;
  }

  isValid = document.getElementById('cc_amount').value > 0 ? true : false;
  if ( !isValid )
  {
    alert('Please enter your payment amount.');
    document.getElementById('cc_amount').focus();
    event.preventDefault();
    return;
  }

  // Don't submit the form until SqPaymentForm returns with a nonce
  event.preventDefault();

  // Request a nonce from the SqPaymentForm object
  paymentForm.requestCardNonce();
}

// Initializes the SqPaymentForm object by
// initializing various configuration fields and providing implementation for callback functions.
var paymentForm = new SqPaymentForm({
  // Initialize the payment form elements
  applicationId: applicationId,
  locationId: locationId,
  inputClass: 'sq-input',

  // Customize the CSS for SqPaymentForm iframe elements
  inputStyles: [{
    backgroundColor: 'transparent',
    color: '#333333',
    fontFamily: '"Helvetica Neue", "Helvetica", sans-serif',
    fontSize: '16px',
    fontWeight: '400',
    placeholderColor: '#8594A7',
    placeholderFontWeight: '400',
    padding: '16px',
    _webkitFontSmoothing: 'antialiased',
    _mozOsxFontSmoothing: 'grayscale'
  }],

  // Initialize the credit card placeholders
  cardNumber: {
    elementId: 'sq-card-number',
    placeholder: '•••• •••• •••• ••••'
  },
  cvv: {
    elementId: 'sq-cvv',
    placeholder: 'CVV'
  },
  expirationDate: {
    elementId: 'sq-expiration-date',
    placeholder: 'MM/YY'
  },
  postalCode: {
    elementId: 'sq-postal-code'
  },

  // SqPaymentForm callback functions
  callbacks: {

    /*
     * callback function: methodsSupported
     * Triggered when: the page is loaded.
     */
    methodsSupported: function (methods) {

    },

    /*
     * callback function: createPaymentRequest
     * Triggered when: a digital wallet payment button is clicked.
     */
    createPaymentRequest: function () {

      var paymentRequestJson = {
        requestShippingAddress: false,
        requestBillingInfo: true,
        shippingContact: {
          familyName: "CUSTOMER LAST NAME",
          givenName: "CUSTOMER FIRST NAME",
          email: "mycustomer@example.com",
          country: "USA",
          region: "CA",
          city: "San Francisco",
          addressLines: [
            "1455 Market St #600"
          ],
          postalCode: "94103",
          phone:"14255551212"
        },
        currencyCode: "USD",
        countryCode: "US",
        total: {
          label: "MERCHANT NAME",
          amount: "1.00",
          pending: false
        },
        lineItems: [
          {
            label: "Subtotal",
            amount: "1.00",
            pending: false
          }
        ]
      };

      return paymentRequestJson;
    },

    /*
     * callback function: validateShippingContact
     * Triggered when: a shipping address is selected/changed in a digital
     *                 wallet UI that supports address selection.
     */
    validateShippingContact: function (contact) {

      var validationErrorObj ;
      /* ADD CODE TO SET validationErrorObj IF ERRORS ARE FOUND */
      return validationErrorObj ;
    },

    /*
     * callback function: cardNonceResponseReceived
     * Triggered when: SqPaymentForm completes a card nonce request
     */
    cardNonceResponseReceived: function(errors, nonce, cardData, billingContact, shippingContact) {
      if (errors){
        var error_html = "";
        for (var i =0; i < errors.length; i++){
          error_html += "<li> " + errors[i].message + " </li>";
        }
        document.getElementById("error").innerHTML = error_html;
        document.getElementById('sq-creditcard').disabled = false;

        return;
      }else{
        document.getElementById("error").innerHTML = "";
      }

      // Assign the nonce value to the hidden form field
      document.getElementById('card-nonce').value = nonce;

      // POST the nonce form to the payment processing page
      document.getElementById('nonce-form').submit();

    },

    /*
     * callback function: unsupportedBrowserDetected
     * Triggered when: the page loads and an unsupported browser is detected
     */
    unsupportedBrowserDetected: function() {
      /* PROVIDE FEEDBACK TO SITE VISITORS */
    },

    /*
     * callback function: inputEventReceived
     * Triggered when: visitors interact with SqPaymentForm iframe elements.
     */
    inputEventReceived: function(inputEvent) {
      switch (inputEvent.eventType) {
        case 'focusClassAdded':
          /* HANDLE AS DESIRED */
          break;
        case 'focusClassRemoved':
          /* HANDLE AS DESIRED */
          break;
        case 'errorClassAdded':
          /* HANDLE AS DESIRED */
          break;
        case 'errorClassRemoved':
          /* HANDLE AS DESIRED */
          break;
        case 'cardBrandChanged':
          /* HANDLE AS DESIRED */
          break;
        case 'postalCodeChanged':
          /* HANDLE AS DESIRED */
          break;
      }
    },

    /*
     * callback function: paymentFormLoaded
     * Triggered when: SqPaymentForm is fully loaded
     */
    paymentFormLoaded: function() {
      /* HANDLE AS DESIRED */
    }
  }
});
