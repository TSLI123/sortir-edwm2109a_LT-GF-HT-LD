<?php

namespace App\Classes;

use Symfony\Component\Validator\Constraints as Assert;

class FiltresSorties
{
    private $campus;

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
     * @Assert\Type("datetime")
     */
    private $from;

    /**
     * @Assert\Type("datetime")
     * @Assert\GreaterThanOrEqual(propertyPath="from")
     */
    private $to;

    /*
     * @Assert\Choice(choices={"organisateur", "inscrit", "/inscrit", "past"})
     */
    private $choice;

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

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

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from): void
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to): void
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * @param mixed $choice
     */
    public function setChoice($choice): void
    {
        $this->choice = $choice;
    }
}