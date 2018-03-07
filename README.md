# Fiat2LTC-WooCommerce

This is a plugin for WooCommerce/Wordpress to display live Litecoin (and Bitcoin/Ethereum) prices in your shop.
It will load iframes displaying live prices from fiat2ltc.com

## Settings
### Denominations
There are options to display prices in these denominations:

ŁTC (Ł) in łites (ł) (Ł/1,000)	
₿TC (₿) in ƀits (ƀ) (₿/1,000,000)	
ΞTH (Ξ) in milliΞTH (mΞ) (Ξ/1,000)

### Currency Switcher
There is an option to display a currency switcher under each price, or there is a widget to add to the sidebar.

The switcher can also manually be inserted into templates with this code:
```php
<?php flCurrencyMenu(home_url($wp->request),"div","css_class_goes_here","View prices in:","css_style: goes_here;"); ?>
```

## Installation

Upload the plugin to your shop, activate it!