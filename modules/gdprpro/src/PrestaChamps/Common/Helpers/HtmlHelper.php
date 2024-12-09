<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

namespace PrestaChamps\Common\Helpers;

class HtmlHelper
{
    /**
     * @var string Regular expression used for attribute name validation.
     * @since 2.0.12
     */
    public static $attributeRegex = '/(^|.*\])([\w\.\+]+)(\[.*|$)/u';

    /**
     * @var array list of void elements (element name => 1)
     * @see http://www.w3.org/TR/html-markup/syntax.html#void-element
     */
    public static $voidElements = array(
        'area'    => 1,
        'base'    => 1,
        'br'      => 1,
        'col'     => 1,
        'command' => 1,
        'embed'   => 1,
        'hr'      => 1,
        'img'     => 1,
        'input'   => 1,
        'keygen'  => 1,
        'link'    => 1,
        'meta'    => 1,
        'param'   => 1,
        'source'  => 1,
        'track'   => 1,
        'wbr'     => 1,
    );

    /**
     * @var array the preferred order of attributes in a tag. This mainly affects the order of the attributes
     * that are rendered by [[renderTagAttributes()]].
     */
    public static $attributeOrder = array(
        'type',
        'id',
        'class',
        'name',
        'value',

        'href',
        'src',
        'srcset',
        'form',
        'action',
        'method',

        'selected',
        'checked',
        'readonly',
        'disabled',
        'multiple',

        'size',
        'maxlength',
        'width',
        'height',
        'rows',
        'cols',

        'alt',
        'title',
        'rel',
        'media',
    );
    /**
     * @var array list of tag attributes that should be specially handled when their values are of array type.
     * In particular, if the value of the `data` attribute is `['name' => 'xyz', 'age' => 13]`, two attributes
     * will be generated instead of one: `data-name="xyz" data-age="13"`.
     * @since 2.0.3
     */
    public static $dataAttributes = array('data', 'data-ng', 'ng');


    /**
     * Encodes special characters into HTML entities.
     * The [[\yii\base\Application::charset|application charset]] will be used for encoding.
     *
     * @param string $content      the content to be encoded
     * @param bool   $doubleEncode whether to encode HTML entities in `$content`. If false,
     *                             HTML entities in `$content` will not be further encoded.
     *
     * @return string the encoded content
     * @see decode()
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode($content)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Decodes special HTML entities back to the corresponding characters.
     * This is the opposite of [[encode()]].
     *
     * @param string $content the content to be decoded
     *
     * @return string the decoded content
     * @see encode()
     * @see http://www.php.net/manual/en/function.htmlspecialchars-decode.php
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }

    /**
     * Generates a complete HTML tag.
     *
     * @param string|bool|null $name    the tag name. If $name is `null` or `false`, the corresponding content will be
     *                                  rendered without any tag.
     * @param string           $content the content to be enclosed between the start and end tags. It will not be
     *                                  HTML-encoded. If this is coming from end users, you should consider
     *                                  [[encode()]] it to prevent XSS attacks.
     * @param array            $options the HTML tag attributes (HTML options) in terms of name-value pairs.
     *                                  These will be rendered as the attributes of the resulting tag. The values will
     *                                  be HTML-encoded using [[encode()]]. If a value is null, the corresponding
     *                                  attribute will not be rendered.
     *
     * For example when using `['class' => 'my-class', 'target' => '_blank', 'value' => null]` it will result in the
     * html attributes rendered like this: `class="my-class" target="_blank"`.
     *
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated HTML tag
     * @see beginTag()
     * @see endTag()
     * @throws \Exception
     */
    public static function tag($name, $content = '', $options = array())
    {
        if ($name === null || $name === false) {
            return $content;
        }
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[\Tools::strtolower($name)]) ? $html : "$html$content</$name>";
    }

    /**
     * Generates a start tag.
     *
     * @param string|bool|null $name    the tag name. If $name is `null` or `false`, the corresponding content will be
     *                                  rendered without any tag.
     * @param array            $options the tag options in terms of name-value pairs. These will be rendered as
     *                                  the attributes of the resulting tag. The values will be HTML-encoded using
     *                                  [[encode()]]. If a value is null, the corresponding attribute will not be
     *                                  rendered. See [[renderTagAttributes()]] for details on how attributes are being
     *                                  rendered.
     *
     * @return string the generated start tag
     * @see endTag()
     * @see tag()
     * @throws \Exception
     */
    public static function beginTag($name, $options = array())
    {
        if ($name === null || $name === false) {
            return '';
        }

        return "<$name" . static::renderTagAttributes($options) . '>';
    }

    /**
     * Generates an end tag.
     *
     * @param string|bool|null $name the tag name. If $name is `null` or `false`, the corresponding content will be
     *                               rendered without any tag.
     *
     * @return string the generated end tag
     * @see beginTag()
     * @see tag()
     */
    public static function endTag($name)
    {
        if ($name === null || $name === false) {
            return '';
        }

        return "</$name>";
    }

    /**
     * Generates a style tag.
     *
     * @param string $content the style content
     * @param array  $options the tag options in terms of name-value pairs. These will be rendered as
     *                        the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     *                        If a value is null, the corresponding attribute will not be rendered.
     *                        See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated style tag
     * @throws \Exception
     */
    public static function style($content, $options = array())
    {
        return static::tag('style', $content, $options);
    }

    /**
     * Generates a script tag.
     *
     * @param string $content the script content
     * @param array  $options the tag options in terms of name-value pairs. These will be rendered as
     *                        the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     *                        If a value is null, the corresponding attribute will not be rendered.
     *                        See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated script tag
     * @throws \Exception
     */
    public static function script($content, $options = array())
    {
        return static::tag('script', $content, $options);
    }

    /**
     * Generates a hyperlink tag.
     *
     * @param string            $text    link body. It will NOT be HTML-encoded. Therefore you can pass in HTML code
     *                                   such as an image tag. If this is coming from end users, you should consider
     *                                   [[encode()]] it to prevent XSS attacks.
     * @param array|string|null $url     the URL for the hyperlink tag. If this
     *                                   parameter is null, the "href" attribute will not be generated.
     *
     * @param array             $options the tag options in terms of name-value pairs. These will be rendered as
     *                                   the attributes of the resulting tag. The values will be HTML-encoded using
     *                                   [[encode()]]. If a value is null, the corresponding attribute will not be
     *                                   rendered. See [[renderTagAttributes()]] for details on how attributes are
     *                                   being rendered.
     *
     * @return string the generated hyperlink
     * @throws \Exception
     */
    public static function a($text, $url = null, $options = array())
    {
        if ($url !== null) {
            $options['href'] = $url;
        }

        return static::tag('a', $text, $options);
    }

    /**
     * Generates a mailto hyperlink.
     *
     * @param string $text    link body. It will NOT be HTML-encoded. Therefore you can pass in HTML code
     *                        such as an image tag. If this is coming from end users, you should consider [[encode()]]
     *                        it to prevent XSS attacks.
     * @param string $email   email address. If this is null, the first parameter (link body) will be treated
     *                        as the email address and used.
     * @param array  $options the tag options in terms of name-value pairs. These will be rendered as
     *                        the attributes of the resulting tag. The values will be HTML-encoded using [[encode()]].
     *                        If a value is null, the corresponding attribute will not be rendered.
     *                        See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated mailto link
     * @throws \Exception
     */
    public static function mailto($text, $email = null, $options = array())
    {
        $options['href'] = 'mailto:' . ($email === null ? $text : $email);
        return static::tag('a', $text, $options);
    }

    /**
     * Generates an image tag.
     *
     * @param array|string $src     the image URL. This parameter will be processed by [[Url::to()]].
     * @param array        $options the tag options in terms of name-value pairs. These will be rendered as
     *                              the attributes of the resulting tag. The values will be HTML-encoded using
     *                              [[encode()]]. If a value is null, the corresponding attribute will not be rendered.
     *                              See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * Since version 2.0.12 It is possible to pass the `srcset` option as an array which keys are
     * descriptors and values are URLs. All URLs will be processed by [[Url::to()]].
     *
     * @return string the generated image tag.
     * @throws \Exception
     */
    public static function img($src, $options = array())
    {
        $options['src'] = $src;

        if (isset($options['srcset']) && is_array($options['srcset'])) {
            $srcset = array();
            foreach ($options['srcset'] as $descriptor => $url) {
                unset($url);
                $srcset[] = $src . ' ' . $descriptor;
            }
            $options['srcset'] = implode(',', $srcset);
        }

        if (!isset($options['alt'])) {
            $options['alt'] = '';
        }

        return static::tag('img', '', $options);
    }

    /**
     * Generates an unordered list.
     *
     * @param array|\Traversable $items   the items for generating the list. Each item generates a single list item.
     *                                    Note that items will be automatically HTML encoded if `$options['encode']` is
     *                                    not set or true.
     * @param array              $options options (name => config) for the radio button list. The following options are
     *                                    supported:
     *
     * - encode: boolean, whether to HTML-encode the items. Defaults to true.
     *   This option is ignored if the `item` option is specified.
     * - separator: string, the HTML code that separates items. Defaults to a simple newline (`"\n"`).
     *   This option is available since version 2.0.7.
     * - itemOptions: array, the HTML attributes for the `li` tags. This option is ignored if the `item` option is
     * specified.
     * - item: callable, a callback that is used to generate each individual list item.
     *   The signature of this callback must be:
     *
     *   ```php
     *   function ($item, $index)
     *   ```
     *
     *   where $index is the array key corresponding to `$item` in `$items`. The callback should return
     *   the whole list item tag.
     *
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated unordered list. An empty list tag will be returned if `$items` is empty.
     * @throws \Exception
     */
    public static function ul($items, $options = array())
    {
        $tag = ArrayHelper::remove($options, 'tag', 'ul');
        $encode = ArrayHelper::remove($options, 'encode', true);
        $formatter = ArrayHelper::remove($options, 'item');
        $separator = ArrayHelper::remove($options, 'separator', "\n");
        $itemOptions = ArrayHelper::remove($options, 'itemOptions', array());

        if (empty($items)) {
            return static::tag($tag, '', $options);
        }

        $results = array();
        foreach ($items as $index => $item) {
            if ($formatter !== null) {
                $results[] = call_user_func($formatter, $item, $index);
            } else {
                $results[] = static::tag('li', $encode ? static::encode($item) : $item, $itemOptions);
            }
        }

        return static::tag(
            $tag,
            $separator . implode($separator, $results) . $separator,
            $options
        );
    }

    /**
     * Generates an ordered list.
     *
     * @param array|\Traversable $items   the items for generating the list. Each item generates a single list item.
     *                                    Note that items will be automatically HTML encoded if `$options['encode']` is
     *                                    not set or true.
     * @param array              $options options (name => config) for the radio button list. The following options are
     *                                    supported:
     *
     * - encode: boolean, whether to HTML-encode the items. Defaults to true.
     *   This option is ignored if the `item` option is specified.
     * - itemOptions: array, the HTML attributes for the `li` tags. This option is ignored if the `item` option is
     * specified.
     * - item: callable, a callback that is used to generate each individual list item.
     *   The signature of this callback must be:
     *
     *   ```php
     *   function ($item, $index)
     *   ```
     *
     *   where $index is the array key corresponding to `$item` in `$items`. The callback should return
     *   the whole list item tag.
     *
     * See [[renderTagAttributes()]] for details on how attributes are being rendered.
     *
     * @return string the generated ordered list. An empty string is returned if `$items` is empty.
     * @throws \Exception
     */
    public static function ol($items, $options = array())
    {
        $options['tag'] = 'ol';
        return static::ul($items, $options);
    }

    /**
     * Renders the HTML tag attributes.
     *
     * Attributes whose values are of boolean type will be treated as
     * [boolean attributes](http://www.w3.org/TR/html5/infrastructure.html#boolean-attributes).
     *
     * Attributes whose values are null will not be rendered.
     *
     * The values of attributes will be HTML-encoded using [[encode()]].
     *
     * The "data" attribute is specially handled when it is receiving an array value. In this case,
     * the array will be "expanded" and a list data attributes will be rendered. For example,
     * if `'data' => ['id' => 1, 'name' => 'yii']`, then this will be rendered:
     * `data-id="1" data-name="yii"`.
     * Additionally `'data' => ['params' => ['id' => 1, 'name' => 'yii'], 'status' => 'ok']` will be rendered as:
     * `data-params='{"id":1,"name":"yii"}' data-status="ok"`.
     *
     * @param array $attributes attributes to be rendered. The attribute values will be HTML-encoded using [[encode()]].
     *
     * @return string the rendering result. If the attributes are not empty, they will be rendered
     * into a string with a leading white space (so that it can be directly appended to the tag name
     * in a tag. If there is no attribute, an empty string will be returned.
     * @see addCssClass()
     * @throws \Exception
     */
    public static function renderTagAttributes($attributes)
    {
        if (count($attributes) > 1) {
            $sorted = array();
            foreach (static::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if (in_array($name, static::$dataAttributes, true)) {
                    foreach ($value as $n => $v) {
                        if (is_array($v)) {
                            $html .= " $name-$n='" . JsonHelper::htmlEncode($v) . "'";
                        } else {
                            $html .= " $name-$n=\"" . static::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(static::cssStyleFromArray($value)) . '"';
                } else {
                    $html .= " $name='" . JsonHelper::htmlEncode($value) . "'";
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }


    /**
     * Converts a CSS style array into a string representation.
     *
     * For example,
     *
     * ```php
     * print_r(Html::cssStyleFromArray(['width' => '100px', 'height' => '200px']));
     * // will display: 'width: 100px; height: 200px;'
     * ```
     *
     * @param array $style the CSS style array. The array keys are the CSS property names,
     *                     and the array values are the corresponding CSS property values.
     *
     * @return string the CSS style string. If the CSS style is empty, a null will be returned.
     */
    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        // return null if empty to avoid rendering the "style" attribute
        return $result === '' ? null : rtrim($result);
    }
}
