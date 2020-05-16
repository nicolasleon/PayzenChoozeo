# Payzen Choozeo

This module offers allows your customers to use Choozeo tthe Payzen payment in several times, operated by the Lyra Networks compagny.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is PayzenMulti.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/payzen-choozeo-module:~1.0
```

## Usage

To use the PayzenChoozeo module, you must first install the basic Payzen module: `https://github.com/Thelia-modules/Payzen` and configure it.
Then all configurations for PayzenChoozeo are in the Payzen basic setup in the "Multiple times payment with Choozeo" section.

You can set a minimum and maximum amount to reach for the customer to choose this payment method, if they are at 0 there'll no minimum or maximum.
You can also choose the value (in percent) of the first payment, the number payments to be made by the customer and the interval between this payment.
