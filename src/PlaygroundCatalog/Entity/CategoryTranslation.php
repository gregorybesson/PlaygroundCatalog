<?php

namespace PlaygroundCatalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog_category_translation", indexes={
 *      @ORM\Index(name="catalog_category_translation", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class CategoryTranslation extends AbstractTranslation
{

}