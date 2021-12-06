<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        'concat_space' => ['spacing' => 'one'],
        'comment_to_phpdoc' => false,
        'ordered_class_elements' => ['sort_algorithm' => 'none']
    ])
    ->setFinder($finder)
;
