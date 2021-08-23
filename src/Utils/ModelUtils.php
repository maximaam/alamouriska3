<?php
declare(strict_types=1);

namespace App\Utils;

use App\Entity\Post;
use function ucfirst;

/**
 * Class ModelUtils
 * @package App\Utils
 */
final class ModelUtils
{
    const ENTITY_DOMAIN = [
        ['name' => 'Mots', 'route' => 'mots-algeriens'],
        ['name' => 'Expressions', 'route' => 'expressions-algeriennes'],
        ['name' => 'Proverbes', 'route' => 'proverbes-algeriens'],
        ['name' => 'Blagues', 'route' => 'blagues-algeriennes'],
        ['name' => 'Blogs', 'route' => 'blogs'],
    ];


    /**
     * @param string $domain
     * @return string
     */
    public static function getEntityByDomain(string $domain): string
    {
        return ucfirst(self::ENTITY_DOMAIN[$domain]);
    }

    /**
     * @param string $entity
     * @return string
     */
    public static function getDomainByEntity(string $entity): string
    {
        $domains = \array_flip(self::ENTITY_DOMAIN);

        return $domains[$entity];
    }

    /**
     * @param $post
     * @return string
     * @throws \ReflectionException
     */
    public static function getDomainByPost($post): string
    {
        $domains = \array_flip(self::ENTITY_DOMAIN);
        $entity = PhpUtils::getClassName($post);

        return $domains[strtolower($entity)];
    }

}
