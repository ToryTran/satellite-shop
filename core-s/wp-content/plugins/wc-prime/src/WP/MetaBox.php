<?php

namespace WcPrime\WP;

class MetaBox
{
    use \WcPrime\SingletonTrait;

    protected $boxes = [];

    private $nonceField = 'wc_prime_cpt_nonce';

    protected function __construct()
    {
    }

    public function add($box = [])
    {
        $this->boxes[] = $box;

        return $this;
    }

    public function print($post, $box)
    {
        wp_nonce_field(plugin_basename(__FILE__), $this->nonceField);
        do_action('wc-prime/metabox/before_box_' . $box['id'], $box, $post);
        foreach ($box['args']['fields'] as $field) {
            do_action('wc-prime/metabox/before_field_' . $field['id'], $field, $post);
            switch ($field['type']) {
                case 'text':
                    $this->textField($field, $post);
                    break;
                case 'checkbox':
                    $this->checkboxField($field, $post);
                    break;
                default:
                    do_action('wc-prime/metabox/print_field', $field, $post);
                    break;
            }
            do_action('wc-prime/metabox/after_field_' . $box['id'], $box, $post);
        }
        do_action('wc-prime/metabox/after_box_' . $box['id'], $box, $post);
    }

    public static function register()
    {
        $instance = self::getInstance();
        foreach ($instance->boxes as $box) {
            add_meta_box(
                $box['id'],
                $box['title'],
                [$instance, 'print'],
                $box['post_type'],
                isset($box['context']) ? $box['context'] : 'normal',
                isset($box['priority']) ? $box['priority'] : 'default',
                $box['args']
            );
        }
    }

    public static function registerSaving($postId)
    {
        $instance = self::getInstance();
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!wp_verify_nonce($_POST[$instance->nonceField], plugin_basename(__FILE__))) {
            return;
        }
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $postId))
                return;
        } else {
            if (!current_user_can('edit_post', $postId))
                return;
        }
        foreach ($instance->boxes as $box) {
            foreach ($box['args']['fields'] as $field) {
                $value = $_POST[$field['id']];
                update_post_meta($postId, $field['id'], $value);
            }
        }
    }

    protected function id($name)
    {
        return 'field-' . $name;
    }

    protected function textField($field, $post)
    {
        $value = get_post_meta($post->ID, $field['id'], true);
        ?>
            <div class="wcp-field">
                <label for="<?php echo $this->id($field['id']) ?>"><?php echo $field['title'] ?></label>
                <input type="text" id="<?php echo $this->id($field['id']) ?>" name="<?php echo $field['id'] ?>" placeholder="<?php echo $field['placeholder'] ?>" value="<?php echo esc_attr($value); ?>" />
            </div>
        <?php
    }

    protected function checkboxField($field, $post)
    {
        $value = get_post_meta($post->ID, $field['id'], true);
        ?>
            <div class="wcp-field">
                <label for="<?php echo $this->id($field['id']) ?>"><?php echo $field['title'] ?></label>
                <input type="checkbox" id="<?php echo $this->id($field['id']) ?>" name="<?php echo $field['id'] ?>" placeholder="<?php echo $field['placeholder'] ?>" value="1" <?php checked(1, $value, true) ?> />
            </div>
        <?php
    }
}
