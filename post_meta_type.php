<?php
class Post_meta_type
{
    public static function text($post, $meta)
    {
        echo self::wrap($meta, '<input type="text" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function bool($post, $meta)
    {
        echo self::wrap($meta, '<label><input type="radio" class="regular-text ltr" id="' . $meta['slug'] . '_0" name="' . $meta['slug'] . '" value="0"> oui</label> <label><input type="radio" class="regular-text ltr" id="' . $meta['slug'] . '_1" name="' . $meta['slug'] . '" value="1"> non</label>');
    }

    public static function number($post, $meta)
    {
        echo self::wrap($meta, '<input type="number" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function email($post, $meta)
    {
        echo self::wrap($meta, '<input type="email" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function url($post, $meta)
    {
        echo self::wrap($meta, '<input type="url" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function color($post, $meta)
    {
        echo self::wrap($meta, '<input type="color" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function date($post, $meta)
    {
        echo self::wrap($meta, '<input type="date" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function datetime_local($post, $meta)
    {
        echo self::wrap($meta, '<input type="datetime-local" class="regular-text ltr" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" value="' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '">');
    }

    public static function textarea($post, $meta)
    {
        echo self::wrap($meta, '<textarea rows="10" class="components-textarea-control__input" id="' . $meta['slug'] . '" name="' . $meta['slug'] . '" style="width:100%">' . esc_attr(get_post_meta($post->ID, $meta['slug'], true)) . '</textarea>');
    }

    public static function editor($post, $meta)
    {
        ob_start();
        wp_editor(get_post_meta($post->ID, $meta['slug'], true), $meta['slug'], $settings = array('textarea_name' => $meta['slug']));
        echo self::wrap($meta, ob_get_clean());
    }


    private static function wrap($meta, $content)
    {
        if ($meta['context'] == 'normal' || $meta['context'] == 'advanced') {
            return '<table class="form-table">
        <tr>
            <th>
                <label for="' . $meta['slug'] . '">' . $meta['name'] . '</label>
            </th>
            <td>
                ' . $content . '
                <p class="description">
                ' . $meta['description'] . '
                </p>
            </td>
        </tr>
    </table>';
        } elseif ($meta['context'] == 'side') {
            return '<label for="' . $meta['slug'] . '">' . $meta['name'] . '</label>
            <p class="description">
                ' . $meta['description'] . ':
                </p>
                <div>' . $content . '</div>';
        }
    }
}
