<!--
title: Tree
subtitle: Helpers
-->
# Tree

Inside a Command handler

```php
$array = [
    'root1' => [
        'lonely',
        'parent'  => [
            'child',
            'child' => [
                'grandchild',
            ],
        ],
        'single',
        'parent2' => [
            'child',
            'child' => [
                'grandchild',
            ],
        ],
    ],
    // colors work too
    '<info>root2</info>' => [
        '<comment>lonely</comment>',
        'parent'  => [
            'child',
            'child' => [
                'grandchild',
            ],
        ],
        'single',
        'parent2' => [
            'child',
            'child' => [
                'grandchild',
            ],
        ],
    ],
];
$tree  = $this->tree()->make();
$tree->addArray($array);
$tree->printTree($this->getOutput());
```
