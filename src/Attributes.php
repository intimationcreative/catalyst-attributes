<?php

namespace Intimation\Catalyst;

/**
 * $attributes = [
 *     'class' => [
 *          'class-one',
 *          'class-two class-three' => is_this_class_required(),
 *      ],
 *     'data-attribute' => 'some value',
 *     'data-attribute-2' => true,
 *     'data-attribute-3' => null,
 *     'style' => [
 *         'color' => 'red',
 *         'background-color' => null,
 *      ],
 * ];
 *
 * echo \Intimation\Catalyst\Attributes::make( $attributes );
 */
class Attributes
{
    /**
     * Generate an HTML attribute string from an array.
     *
     * @param  array  $attributes  An array of attributes and their values.
     *
     * @return string The generated HTML attribute string.
     */
    public static function make(array $attributes = []): string
    {
        $class = self::generateClassAttribute($attributes['class'] ?? []);
        $style = self::generateStyleAttribute($attributes['style'] ?? []);

        unset($attributes['class'], $attributes['style']);

        $otherAttributes = implode(' ', array_map(function ($name) use ($attributes) {
            return self::generateOtherAttribute($name, $attributes[$name] ?? null);
        }, array_keys($attributes)));

        return trim(sprintf('%s%s %s', $class, $style, $otherAttributes));
    }

    /**
     * Generate a class attribute string from an array of class names.
     *
     * @param  array|string  $classes  The classes to add to the attribute.
     *
     * @return string The generated class attribute string.
     */
    public static function class(array|string $classes): string
    {
        return trim(self::generateClassAttribute($classes));
    }

    /**
     * Generates a string for the HTML class attribute.
     *
     * @param  array|string  $classes  The classes to add to the attribute.
     *
     * @return string The string to use for the class attribute.
     */
    private static function generateClassAttribute(array|string $classes): string
    {
        $classArray  = is_array($classes) ? $classes : [$classes];
        $classString = '';

        foreach ($classArray as $class => $callback) {
            if (is_int($class)) {
                $class = $callback;
                $callback = null;
            }

            if (is_callable($callback) && !call_user_func($callback)) {
                continue;
            }

            $classString .= sprintf('%s ', htmlentities($class));
        }

        return $classString ? sprintf('class="%s" ', trim($classString)) : '';
    }

    /**
     * Generate an attribute other than class or style.
     *
     * @param  string  $name  The name of the attribute.
     * @param  bool|string|null  $value  The value of the attribute.
     *
     * @return string The generated attribute string.
     */
    private static function generateOtherAttribute(string $name, bool|string|null $value): string
    {
        return is_bool($value) ? ($value ? sprintf('%s ', htmlentities($name)) : '') :
            (! is_null($value) ? sprintf('%s="%s" ', htmlentities($name), htmlentities($value)) : '');
    }

    /**
     * Generates a style attribute string based on an array or string of CSS styles.
     *
     * @param  array|string|null  $styles  A string or associative array of styles to apply to an HTML element.
     *
     * @return string The generated style attribute string.
     */
    private static function generateStyleAttribute(array|string|null $styles): string
    {
        if (is_string($styles)) {
            return sprintf('style="%s" ', htmlentities($styles));
        }

        if ( ! is_array($styles)) {
            return '';
        }

        $styleString = '';
        foreach ($styles as $property => $value) {
            if (is_null($value)) {
                continue;
            }
            $styleString .= sprintf('%s:%s;', htmlentities($property), htmlentities($value));
        }

        return $styleString ? sprintf('style="%s" ', htmlentities($styleString)) : '';
    }
}
