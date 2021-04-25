# Magento 2 Module Dathard LogCleaner

 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)


### Type 1: Zip file

 - Unzip the zip file in `app/code/Dathard`
 - Enable the module by running `php bin/magento module:enable Dathard_LogCleaner`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

Using the configurations in the admin panel, the module can be flexibly to set for any needs. In order to get the configuration go to the path `Stores-> Settings-> Configuration-> Dathard-> Log Cleaner`.

There is a `Module Enable` field in the module configurations, which is responsible for activating the functionality of the module, the module is disabled by default.

The `Rotation period` field allows you to configure the frequency of archiving log files. Archiving can be enabled every day, once a week and once a month. The default value is `Once a week`.
If the standard options are not enough, then you can set a custom period, for this you should select the `Custom period` value. When this value is selected, a field will appear in which you need to enter the number of days that will be used as a period for archiving.


The `Number of saved files` field allows you to specify the number of recent archives with logs to be saved. The last 7 archives with log files are saved by default

