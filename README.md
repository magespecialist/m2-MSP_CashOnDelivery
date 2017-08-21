# Cash On Delivery for Magento 2

This module is a Cash On Delivery implementation for Magento2 allowing you to define an additional fee based on destination country, region and total amount.

<img src="https://github.com/magespecialist/m2-MSP_CashOnDlivery/blob/master/screenshots/screen4.png" />

## Main features:

* Multiple currencies allowed
* Multi store allowed
* Percent or static fee supported
* Fee per country / region / amount
* Default fee fallback
* Multi website support
* Exclude specific regions

## Installing in your Magento

* From your CLI run: ```composer require msp/cashondelivery```
* Log-in your Magento backend
* Go to Stores > Configuration > Sales > Payment Methods > Cash On Delivery
* Configure Cash On Delivery according to your preferences

<img src="https://github.com/magespecialist/m2-MSP_CashOnDlivery/blob/master/screenshots/screen1.png" />

### Configuring fees

* Log-in your Magento backend
* Go to Stores > Configuration > Sales > Payment Methods > Cash On Delivery
* Scroll down untill you see **Export CSV** button
* Click and download **msp_cashondelivery.csv** file
* Change the CSV file and upload using the "browse" button
* Save

<img src="https://github.com/magespecialist/m2-MSP_CashOnDlivery/blob/master/screenshots/screen2.png" />

### CSV syntax

MSP Cash On Delivery CSV file syntax is really simple. You have 5 columns: **country**, **region**, **from_amount**, **fee**, **website**

* **country**: ISO 2 letters country code. Use * as wildcard to indicate all countries
* **region**: Region name. Use * as wildcard to indicate all regions
* **from_amount**: Indicates the minimum amount to apply the additional fee
* **fee**: The fee to apply (in base currency). Adding **%** after the fee indicates a percent value
* **website**: Magento website code (e.g.: *base*). Use * as wildcard to indicate all websites

<img src="https://github.com/magespecialist/m2-MSP_CashOnDlivery/blob/master/screenshots/screen3.png" />
