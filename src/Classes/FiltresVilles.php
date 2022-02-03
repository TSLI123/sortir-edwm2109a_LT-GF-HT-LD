<?php

namespace App\Classes;

use Symfony\Component\Validator\Constraints as Assert;

class FiltresVilles
{
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     * min=1,
     * max=50,
     * minMessage="Minimum 1 characters please!",
     * maxMessage="Maximum 50 characters please!"
     * )
     */
    private $search;

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search): void
    {
        $this->search = $search;
    }

}