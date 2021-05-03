## Main Functionalities

The Dathard_LogCleaner module allows you to optimize the Magento platform by optimizing the logs. Logs can sometimes take up a lot of memory, so working with them is extremely resource intensive. Such work creates a fairly large load on the server and as a result, the site starts to work slower and sometimes stops working altogether.

The Dathard_LogCleaner module allows you to minimize these problems by reducing the size of the log data, and, accordingly, the resources that are needed to work with them.

In Magento, logs are usually placed in the `var / log` directory. The module allows you to archive all log files in a given directory, thus replacing the old logs with new files that take up less resources.Also in the module, you can configure the frequency of log archiving and the number of archives that should be kept, thus deleting old logs that are no longer needed.

Besides these logs Magento also contains other logs in the database, namely in the tables `report_event`,` report_viewed_product_index`, `report_compared_product_index` and` customer_visitor`. Sometimes these logs are also very large and it would be good to clean them periodically, the module makes it possible to implement this process.


## Installation from zip file

 - Unzip the zip file in `app/code/Dathard`
 - Enable the module by running `php bin/magento module:enable Dathard_LogCleaner`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`


## How to configure

From Magento Admin, select `Store > Settings > Configuration > Dathard > Log Cleaner`.

### Сleaning log files

From the Admin Panel, go to `Store > Settings > Configuration > Dathard > Log Cleaner`, choose `Cleaning log files` section.

![Сleaning log files section](https://github.com/Dathard/images-in-readme/blob/main/Magento2/LogCleaner/cleaning-log-files-section.png?raw=true)

* In the `Enable logs cleaning` field: Select `Yes` to enable optimization of this type of logs.

* The `Rotation period` field allows you to configure the frequency of archiving log files. Archiving can be enabled every day, once a week and once a month. The default value is `Once a week`. 
  If the standard options are not enough, then you can set a custom period, for this you should select the `Custom period` value. When this value is selected, a field will appear in which you need to enter the number of days that will be used as a period for archiving.   

* The Number of saved files field allows you to specify the number of recent archives with logs to be saved. The last 7 archives with log files are saved by default

### Сleaning database logs

From the Admin Panel, go to `Store > Settings > Configuration > Dathard > Log Cleaner`, choose `Сleaning database logs` section.

![Сleaning database logs section](https://github.com/Dathard/images-in-readme/blob/main/Magento2/LogCleaner/cleaning-database-logs-section.png?raw=true)

* In the `Enable logs cleaning` field: Select `Yes` to enable optimization of this type of logs.

* The `Rotation period` field allows you to configure the frequency of archiving log files. Archiving can be enabled every day, once a week and once a month. The default value is `Once a week`. 
