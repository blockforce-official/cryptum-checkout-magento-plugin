Cryptum Checkout plugin for Magento 2
---------------
This is [Cryptum Checkout Extension for Magento 2](https://github.com/blockforce-official/cryptum-checkout-magento-plugin). This extenstion allows to easily accept cryptocurrencies such as ETHEREUM and CELO at your Magento 2 website.

To succesfully use this plugin, you have to have a Cryptum Checkout Bitcoin wallet. You can get it [here](https://blockforce.in/en). Also you have to create a Store project to get storeId and apiKey, to do so create a new store.

**INSTALLATION**

This plugin can be installed different ways:

* Copy files and directories to your server
* /cryptum/ folder from repository should be moved to /app/code/Cryptum/Cryptum folder in magento.

### Copying files and directories to your server manually

Connect to your server by ssh: 

`$ ssh user:password@server`

Go to the magento web-root: 

`cd /var/www/html`

Create directories for the extension: 

`mkdir -p app/code/Cryptum/Cryptum`
 
Clone plugin:

`git clone https://github.com/blockforce-official/cryptum-checkout-magento-plugin.git ./app/code/Cryptum/Cryptum`


Run magento:

`composer require nategood/httpful` (this dependency has to be installed manually)

`bin/magento module:enable Cryptum_Cryptum --clear-static-content`

`bin/magento setup:upgrade`

`bin/magento setup:di:compile`


**CONFIGURATION**

1. Generate ApiKey and StoreId

2. Login in admin panel of your Magento 2.3^ web-shop

3. Navigate to "Stores -> Configuration -> Sales -> Payment methods"

4. Click on "Cryptum Checkout by Blockforce" and fill the form enter your StoreId and ApiKey key.

