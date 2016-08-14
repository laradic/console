<?php
/**
 * Copyright (c) 2016 Robin Radic.
 *
 * License can be found inside the package and is available at radic.mit-license.org.
 *
 * @author             Robin Radic
 * @copyright         Copyright (c) 2015, Robin Radic. All rights reserved
 * @license          https://radic.mit-license.org The MIT License (MIT)
 */
namespace Laradic\Console\Descriptor;

use Symfony\Component\Console\Descriptor\JsonDescriptor;
use Symfony\Component\Console\Descriptor\MarkdownDescriptor;
use Symfony\Component\Console\Descriptor\XmlDescriptor;

class DescriptorHelper extends \Symfony\Component\Console\Helper\DescriptorHelper
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this
            ->register('txt', new TextDescriptor())
            ->register('xml', new XmlDescriptor())
            ->register('json', new JsonDescriptor())
            ->register('md', new MarkdownDescriptor())
        ;
    }


}
