The module integrates Magento 1 with the **[Yandex.Checkout](https://checkout.yandex.com)** payment service.

## How to install
### Step 1
Remove the current `composer.json` file as it is just a garbage:
```
rm -rf composer.json
```

### Step 2
```
composer require monolog/monolog:*
composer require yandex-money/yandex-checkout-sdk-php:* 
composer require zendframework/zend-filter:*
```

### Step 3
```
sed -i 's/^"Your order # is: %s.","номер вашего заказа: "$/"Your order # is: %s.","Номер Вашего заказа: %s."/' app/design/frontend/iframe/iframe_responsive/locale/ru_RU/translate.csv
```

### Step 4
It adds the [PHP namespaces](https://www.php.net/manual/en/language.namespaces.php) support to Magento 1:
```
sed -i $'s|$classFile = str_replace(\' \', DIRECTORY_SEPARATOR, ucwords(str_replace(\'_\', \' \', $class)));|$classFile = str_replace(\'\\\\\\\\\', \'\/\', str_replace(\' \', DIRECTORY_SEPARATOR, ucwords(str_replace(\'_\', \' \', $class))));|g' app/code/local/Varien/Autoload.php

sed -i $'s|$classFile = uc_words($class, DIRECTORY_SEPARATOR).\'.php\';|$classFile = str_replace(\'\\\\\\\\\', \'\/\', uc_words($class, DIRECTORY_SEPARATOR).\'.php\');|g' app/code/core/Mage/Core/functions.php
```

### Step 5
```
rm -f app/etc/modules/LesMills_YandexCheckout.xml ;
rm -rf app/code/community/Df ;
rm -rf app/code/local/LesMills/YandexCheckout ;
rm -rf app/design/frontend/base/default/template/df ;
rm -rf app/design/frontend/base/default/template/yandex_checkout ;
rm -rf skin/frontend/base/default/df ;
rm -rf skin/frontend/base/default/yandex_checkout ;
ORG=lesmills-com ;
REPO=yandex-checkout ;
FILE=$REPO.tar.gz ;
VERSION=$(curl -s https://api.github.com/repos/$ORG/$REPO/releases | grep tag_name | head -n 1 | cut -d '"' -f 4) ;
curl -L -o $FILE https://github.com/$ORG/$REPO/archive/$VERSION.tar.gz ;
tar xzvf $FILE ;
rm -f $FILE ;
cp -r $REPO-$VERSION/* . ;
rm -rf $REPO-$VERSION 
rm -rf var/cache var/full_page_cache
```

## How to upgrade
Execute the Step 5 from the installation part.