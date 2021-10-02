
## Package Installation 

```bash
composer require one/system --dev
```
## Options.php
### Intelligent options system
#### Example Usage
```php
<?php
require_once __DIR__ ."/vendor/autoload.php";

use System\Essence\Options;

$defaults = [
    'value-1' => 1,
    'value-2' => [
        'sub-value-1' => 3,
        'sub-value-2' => [
            'sub-sub-value-1' => 33,
            'sub-sub-value-2' => 2
        ],
        'sub-value-3' => [
                'val-1' => 'super',
                'val-2' =>'duper',
                'val-3' => 'puper'
        ],
        'sub-value-4' => 1
    ],
];

$options = [
    'value-1' => 1,
    'value-2' => [
        'sub-value-1' => 3,
        'sub-value-2' => ['sub-sub-value-1'=> 33],
        'sub-value-3' => [
            'val-1' => 'super',
        ],
    ]
];

$opts = Options::init($defaults, $options, ['value-2.sub-value-2', 'value-2.sub-value-3']);
```