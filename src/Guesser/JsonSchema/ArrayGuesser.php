<?php

namespace Joli\Jane\Guesser\JsonSchema;

use Joli\Jane\Guesser\ChainGuesserAwareInterface;
use Joli\Jane\Guesser\ChainGuesserAwareTrait;
use Joli\Jane\Guesser\Guess\ArrayType;
use Joli\Jane\Guesser\Guess\MultipleType;
use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Guesser\GuesserInterface;
use Joli\Jane\Guesser\TypeGuesserInterface;

use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Registry;
use Joli\Jane\Schema;

class ArrayGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface
{
    use ChainGuesserAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function supportObject($object)
    {
        return (($object instanceof JsonSchema) && $object->getType() === 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($object, $name, Registry $registry, Schema $schema)
    {
        $items = $object->getItems();

        if ($items === null) {
            return new ArrayType($object, new Type($object, 'mixed'));
        }

        if (!is_array($items)) {
            return new ArrayType($object, $this->chainGuesser->guessType($items, $name, $registry, $schema));
        }

        $type = new MultipleType($object);

        foreach ($items as $item) {
            $type->addType(new ArrayType($object, $this->chainGuesser->guessType($item, $name, $registry, $schema)));
        }

        return $type;
    }
}
