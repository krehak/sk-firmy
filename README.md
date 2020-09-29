# sk-firmy

Jednoduchý wrapper na získanie údajov Slovenských firiem a SZČO podľa IČO/DIČ z ORSR a Registra účtovných závierok + overenie registrácie na DPH (IČ DPH) z EU VIES.

Zdrojový kód inšpirovaný z repozitára [samuelszabo/SK-doplnenie-udajov-firmy](https://github.com/samuelszabo/SK-doplnenie-udajov-firmy).

## Inštalácia

``
$ composer require krehak/sk-firmy
``

### Načítanie balíčka
V prípade, že v projekte nepoužívate composer. V opačnom prípade sa package naloaduje automaticky pomocou PSR-4.

```php
require_once './vendor/autoload.php';
```

## Použitie
V hlavičke PHP súboru:

```php
use Krehak\SkFirmy\SkFirmy;
use Krehak\SkFirmy\Fields\BusinessId; // Ak budete vyhľadávať podľa IČO
use Krehak\SkFirmy\Fields\TaxId; // Ak budete vyhľadávať podľa DIČ
```

Vo vašej časti kódu:

```php
...

$skFirmy = new SkFirmy();
$results = $skFirmy->find(FieldType('xxx'))->getResults();

print_r($results);

...
```

#### Možnosti FieldType

| Objekt | Popis |
| --- | --- |
| `new BusinessId('xxx')` | Vyhľadať podľa IČO |
| `new TaxId('xxx')` | Vyhľadať podľa DIČ |

##### Premenná `$results` bude obsahovať pole hodnôt:

| Názov | Popis |
| --- | --- |
| `name` | Obchodné meno |
| `street` | Ulica |
| `city` | Mesto |
| `zip` | PSČ |
| `business_id` | IČO |
| `tax_id` | DIČ |
| `vat_id` | IČ DPH (len ak je overená registrácia na DPH) |

## Príklad

```php
// index.php

require_once './vendor/autoload.php';

use Krehak\SkFirmy\SkFirmy;
use Krehak\SkFirmy\Fields\BusinessId;

$skFirmy = new SkFirmy();
$results = $skFirmy->find(new BusinessId('31322832'))->getResults();

echo '<pre>';
print_r($results);
echo '</pre>';
```
![Ukážka v prehliadači](https://raw.githubusercontent.com/krehak/sk-firmy/master/examples/example.png)

### Milestones

- [x] Vyhľadávanie podľa IČO/DIČ
- [x] Overovanie IČ DPH
- [ ] Vyhľadávanie podľa obchodného mena
- [ ] Vyhľadávanie podľa sídla
- [ ] Vyhľadávanie podľa mena a priezviska
- [ ] Vyhľadávanie pomocou full-textu
- [ ] Zoznam konateľov do výpisu (v prípade s.r.o.)
