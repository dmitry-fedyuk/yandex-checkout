The module integrates Magento 1 with the **[Yandex.Checkout](https://checkout.yandex.com)** payment service.

## How to install
### Step 1
Remove the current `composer.json` file as it is just a garbage:
```
rm -rf composer.json
```

### Step 2
```
composer require yandex-money/yandex-checkout-sdk-php:* 
```

### Step 3
```
rm -rf app/code/local/LesMills/YandexCheckout ;
rm -rf app/design/frontend/base/default/template/yandex_checkout ;
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
rm -rf var/cache
```

## How to upgrade
Execute the Step 3 from the installation part.