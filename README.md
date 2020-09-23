# sk-firmy

Jednoduchý wrapper na získanie údajov Slovenských firiem a SZČO podľa IČO/DIČ z ORSR a Registra účtovných závierok + overenie registrácie na DPH (IČ DPH) z EU VIES.

Zdrojový kód inšpirovaný z repozitára [samuelszabo/SK-doplnenie-udajov-firmy](https://github.com/samuelszabo/SK-doplnenie-udajov-firmy).

### Inštalácia

``
$ composer require krehak/sk-firmy
``

### Načítanie balíčka
V prípade, že v projekte nepoužívate composer. V opačnom prípade sa package naloaduje automaticky pomocou PSR-4.

```php
require_once './vendor/autoload.php';
```

### Použitie
V hlavičke PHP súboru:

```php
use Krehak\SkFirmy\SkFirmy;
```

Vo vašej časti kódu:

```php
...

$skFirmy = new SkFirmy();
$results = $skFirmy->find('[FIELD]', '[ID]')->getResults();

print_r($results);

...
```

Premenná `$results` bude obsahovať pole hodnôt.

#### Možnosti

| Názov | Popis |
| --- | --- |
| `[FIELD]` | Políčko na vyhľadávanie (momentálne dostupné len 'ico' alebo 'dic') |
| `[ID]` | IČO alebo DIČ (podľa nastavenia [FIELD]) |

#### Návratové hodnoty (pre každý záznam)

| Názov | Popis |
| --- | --- |
| `name` | Obchodné meno |
| `street` | Ulica |
| `city` | Mesto |
| `zip` | PSČ |
| `business_id` | IČO |
| `tax_id` | DIČ |
| `vat_id` | IČ DPH |

### Príklad
```php
// index.php

require_once './vendor/autoload.php';

use Krehak\SkFirmy\SkFirmy;

$skFirmy = new SkFirmy();
$results = $skFirmy->find('ico', '31322832')->getResults();

echo '<pre>';
print_r($results);
echo '</pre>';
```
![Ukážka v prehliadači](https://raw.githubusercontent.com/krehak/sk-firmy/master/examples/example.png)
