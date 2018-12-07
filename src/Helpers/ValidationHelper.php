<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.12.18
 * Time: 12:12
 */

namespace App\Helpers;


use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationHelper
{
    /**
     * <code>
     *     [
     *         'property1.1' => array('message1.1', 'message1.2'),
     *         'property1.2' => array('message2')
     *         ........
     *     ]
     * </code>
     *
     * @param ConstraintViolationListInterface $violationsList
     * @param string|null $propertyPath
     * @return array
     */
    public static function violationsToArray(ConstraintViolationListInterface $violationsList, string $propertyPath = null): array
    {
        $output = [];
        foreach ($violationsList as $violation) {
            $output[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        if (null !== $propertyPath) {
            if (array_key_exists($propertyPath, $output)) {
                $output = [$propertyPath => $output[$propertyPath]];
            } else {
                return [];
            }
        }
        return $output;
    }
}