<?php
/**
 * Part of the $author$ PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */
namespace Laradic\Console\Helpers;

class ModesHelper extends Helper
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $available;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $modes;

    /**
     * ModesHelper constructor.
     */
    public function __construct()
    {
        $this->modes = collect();
    }

    public static function supported()
    {
        return true;
    }

    public function enable($name)
    {
        if(!$this->modes->contains($name)){
            $this->modes->push($name);
        }
        return $this;
    }

    public function disable($name)
    {
        $this->modes = collect($this->modes->reverse()->except($name)->toArray());
    }

    public function isEnabled($name)
    {
        return $this->modes->contains($name);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getModes()
    {
        return $this->modes;
    }

    /**
     * Set the enabled value
     *
     * @param \Illuminate\Support\Collection $modes
     *
     * @return ModesHelper
     */
    public function setModes($modes)
    {
        $this->modes = $modes;
        return $this;
    }

    public function getName()
    {
        return 'modes';
    }
}
