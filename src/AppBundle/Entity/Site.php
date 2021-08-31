<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entities that have a somewhat fixed, physical extension.
 *
 * @see http://schema.org/Place Documentation on Schema.org
 *
 * @ORM\Entity
 * @ORM\Table(name="site")
 */
class Site
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string The latitude of the site.
     *
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    public $latitude;

    /**
     * @var string The longitude of the site.
     *
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    public $longitude;

    /**
     * @var string The (optional) of the item.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    public $marker;

    /**
     * @var string The title of the item.
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    public $title;

    /**
     * @var string The street-address of the item.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    public $street;

    /**
     * @var string Override (german) street.
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    public $streetOverride;

    /**
     * @var string The description of the item.
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    public $description;

    /**
     * @var string URL of the item.
     *
     * @Assert\Url
     * @ORM\Column(nullable=true)
     */
    public $url;

    /**
     * @var string Additional info about the item.
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    public $additional;

    /**
     * Sets id.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets marker.
     *
     * @param string $marker
     *
     * @return $this
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;

        return $this;
    }

    /**
     * Gets marker.
     *
     * @return string
     */
    public function getMarker()
    {
        if (!empty($this->marker)) {
            return $this->marker;
        }
        return $this->id;
    }

    /**
     * Gets urls.
     *
     * @return array
     */
    public function getUrlSeparated()
    {
        $urls = [];

        if (!empty($this->url)) {
            return $urls;
        }

        return preg_splace('/\s*;\s*/', $this->url);
    }

    /**
     * Gets urls.
     *
     * @return array
     */
    public function getJuedischesHamburgSeparated()
    {
        $urls = [];

        if (!array_key_exists('dasjuedischehamburg', $this->additional)) {
            return $urls;
        }

        $content = $this->additional['dasjuedischehamburg'];
        if (empty($content)) {
            return $urls;
        }

        return preg_split('/\s*;\s*/', $content);
    }

    /**
     * Gets localized name.
     *
     * @return string
     */
    public function getStreetLocalized($locale = 'de')
    {
        if (!is_null($this->streetOverride)
            && is_array($this->streetOverride)
            && !empty($this->streetOverride[$locale]))
        {
            return $this->streetOverride[$locale];
        }

        return $this->street;
    }
}
