<?php
/**
 * Plugin Name: DaqiToken Accessibility
 * Description: Fixes the empty footer theme-author anchor and adds accessible names to comment form fields.
 * Version: 1.0.0
 * Author: DaqiToken
 */

if (!defined('ABSPATH')) {
    exit;
}

final class DaqiToken_A11y
{
    public static function boot(): void
    {
        add_filter('blocksy:footer:copyright:value', [self::class, 'copyright'], 20);
        add_filter('comment_form_default_fields', [self::class, 'commentFields'], 20);
    }

    /** Replace the default theme attribution (which renders an empty <a>) with branded copy. */
    public static function copyright($text)
    {
        return 'Copyright &copy; {current_year} DaqiToken - All rights reserved.';
    }

    /** Give comment form inputs an explicit accessible name (placeholders are not labels). */
    public static function commentFields($fields)
    {
        $labels = ['author' => 'Name', 'email' => 'Email', 'url' => 'Website'];
        foreach ($fields as $key => $html) {
            if (!isset($labels[$key]) || !is_string($html) || $html === '') {
                continue;
            }
            if (stripos($html, 'aria-label=') !== false) {
                continue;
            }
            $fields[$key] = preg_replace(
                '/<input\b/i',
                '<input aria-label="' . $labels[$key] . '"',
                $html,
                1
            );
        }
        return $fields;
    }
}

DaqiToken_A11y::boot();
