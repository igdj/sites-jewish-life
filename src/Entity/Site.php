<?php

namespace App\Entity;

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
    public static function setRelated(&$sites)
    {
        $sitesById = [];
        foreach ($sites as $site) {
            $sitesById[$site->getId()] = $site;
        }

        foreach ($sites as $site) {
            foreach ($site->getRelatedIds() as $id) {
                if (array_key_exists($id, $sitesById)) {
                    $site->related[] = $sitesById[$id];
                }
            }
        }
    }

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
     * @var string The (optional) marker of the item.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    public $marker;

    /**
     * @var string The title of the item.
     *
     * @ORM\Column(type="json", nullable=true)
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
     * @ORM\Column(type="json", nullable=true)
     */
    public $streetOverride;

    /**
     * @var string The description of the item.
     *
     * @ORM\Column(type="json", nullable=true)
     */
    public $description;

    /**
     * @var string URL of the item.
     *
     * @Assert\Url
     * @ORM\Column(nullable=true, length=511)
     */
    public $url;

    /**
     * @var string The topic of the item.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    public $topic;

    /**
     * @var array Additional info about the item.
     *
     * @ORM\Column(type="json", nullable=true)
     */
    public $additional;

    /**
     * @var array Related Site.
     *
     */
    public $related = [];

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
     * Gets localized title.
     *
     * @return string
     */
    public function getTitleLocalized($locale = 'de')
    {
        if (is_null($this->title)
            || !is_array($this->title))
        {
            return null;
        }

        if (array_key_exists($locale, $this->title) && !empty($this->title[$locale])) {
            return $this->title[$locale];
        }

        // default locale
        return $this->title['de'];
    }

    /**
     * Gets localized description.
     *
     * @return string
     */
    public function getDescriptionLocalized($locale = 'de')
    {
        if (is_null($this->description)
            || !is_array($this->description))
        {
            return null;
        }

        if (array_key_exists($locale, $this->description) && !empty($this->description[$locale])) {
            return $this->description[$locale];
        }

        // default locale
        return $this->description['de'];
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
        if (empty($this->url)) {
            return [];
        }

        $urls = preg_split('/\s*;\s+/', $this->url);

        $nonempty = array_filter($urls);

        return $nonempty;
    }

    /**
     * Gets article info.
     *
     * @return array
     */
    public function getKeydocumentsLocalized($locale = 'de')
    {
        if (is_null($this->additional) || !array_key_exists('keydocuments', $this->additional)) {
            return [];
        }

        $ret = [];

        foreach ($this->additional['keydocuments'] as $info) {
            if (array_key_exists($locale, $info) && !empty($info[$locale])) {
                $ret[] = $info[$locale];
            }
        }

        return $ret;
    }

    /**
     * Gets slugs.
     *
     * @return array
     */
    public function getJuedischesHamburg()
    {
        if (is_null($this->additional) || !array_key_exists('dasjuedischehamburg', $this->additional)) {
            return [];
        }

        return $this->additional['dasjuedischehamburg'];
    }

    /**
     * Gets ids.
     *
     * @return array
     */
    public function getRelatedIds()
    {
        if (is_null($this->additional) || !array_key_exists('related', $this->additional)) {
            return [];
        }

        // don't return own id amongst related
        return array_diff($this->additional['related'], [ $this->id ]);
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
