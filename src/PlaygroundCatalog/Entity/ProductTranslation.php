<?php

namespace PlaygroundCatalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog_product_translation", indexes={
 *      @ORM\Index(name="catalog_product_translation", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class ProductTranslation extends AbstractTranslation
{

}