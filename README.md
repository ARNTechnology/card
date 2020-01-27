# arntech/card

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

An implementation of [ISO/IEC 7812](https://en.wikipedia.org/wiki/ISO/IEC_7812) and [ISO/IEC 7812-1](https://www.iso.org/obp/ui/#iso:std:iso-iec:7812:-1:ed-5:v1:en)
## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require arntech/card
```

## Usage/Examples
```php
use ARNTech\Card\Model\Financial\Card as FinancialCard;
use ARNTech\Card\Model\Financial\Virtual\Card as VirtualCard;
use ARNTech\Card\Model\CardNumber;
use ARNTech\Card\Model\LuhnCardNumber;

$testCardNumber = '44823300xxxx2314'; // this should be a valid card number
$cardExpiration = '0222';
$card = new CardNumber('1111');
echo $card; //prints 1111xxxxxxxx1111
echo $card->getType();//prints 1
echo $card->getTypeTest();//prints Airlines
echo $card->getPlainNumber();//prints 1111 - it's not advised to use it unless explicitly needed

$card = new CardNumber('01111');//throws an exception

$card = new LuhnCardNumber('1111');//throws an exception as the card does not validate Luhn Algorithm
$card = new LuhnCardNumber($testCardNumber);//passes luhn validation
//LuhnCardNumber extends CardNumber

$card = new FinancialCard($testCardNumber, $cardExpiration);//passes luhn validation
echo json_encode($card); //prints {"number":"4482xxxxxxxx2314","expiration":"02-2022"}
$card = new FinancialCard($testCardNumber, '02/21');//passes luhn validation
echo json_encode($card); //prints {"number":"4482xxxxxxxx2314","expiration":"02-2021"}
echo $card->getNumber()->getVendor();//prints 1
echo $card->getNumber()->getVendorName();//prints Visa

$card = new VirtualCard($testCardNumber, $cardExpiration, '123');
//VirtualCard extends FinancialCard
echo $card->isExpired()?'true':'false';//prits false
echo json_encode($card);//prints {"number":"4482xxxxxxxx2314","expiration":"02-2022","cvv2":"123"}
$card=new VirtualCard($testCardNumber, $cardExpiration, '1234');//throws Cvv2Exception
```


[badge-source]: https://img.shields.io/badge/source-ramsey/uuid-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/packagist/v/ramsey/uuid.svg?style=flat-square&label=release
[badge-license]: https://img.shields.io/packagist/l/ramsey/uuid.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/ramsey/uuid.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/ramsey/uuid.svg?style=flat-square&colorB=mediumvioletred
