<?php
/**
 * TeleRx Home Service module bootstrap
 * Folder location: /HomeService/
 * This file safely includes the main TeleRx header/footer from the website root.
 */

function hs_e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function hs_current_file() {
    return basename($_SERVER['SCRIPT_NAME'] ?? '');
}

function hs_contact_url() {
    return '../contact.php';
}

function hs_home_url() {
    return 'home-service.php';
}

function hs_service_url($slug) {
    return 'service-details.php?service=' . rawurlencode((string)$slug);
}

function hs_image($path) {
    $path = ltrim((string)$path, '/');
    if ($path !== '' && file_exists(__DIR__ . '/' . $path)) {
        return $path;
    }
    return 'assets/services/placeholder.svg';
}

function hs_rewrite_root_relative_urls($html) {
    // When root header.php/footer.php are included from /HomeService/, links like
    // assets/css/custom.css or about-us.php would otherwise resolve as
    // /HomeService/assets/... or /HomeService/about-us.php. This rewrites only
    // site header/footer HTML output to point one level up.
    return preg_replace_callback(
        '/\b(href|src|action)=(["\'])(?!https?:|mailto:|tel:|javascript:|#|\/\/|\/|data:)([^"\']*)\2/i',
        function ($match) {
            $attr = $match[1];
            $quote = $match[2];
            $url = $match[3];

            if ($url === '' || strpos($url, '../') === 0 || strpos($url, './') === 0) {
                return $match[0];
            }

            return $attr . '=' . $quote . '../' . $url . $quote;
        },
        $html
    );
}

function hs_include_root_file($file, $title = '') {
    $root = dirname(__DIR__);
    $target = $root . '/' . $file;

    if (!file_exists($target)) {
        return false;
    }

    if ($title !== '') {
        $GLOBALS['page_title'] = $title;
    }

    $previous_cwd = getcwd();
    ob_start();
    chdir($root);
    include $target;
    chdir($previous_cwd);
    $html = ob_get_clean();

    echo hs_rewrite_root_relative_urls($html);
    return true;
}

function hs_include_header($title = 'TeleRx Home Service') {
    if (hs_include_root_file('header.php', $title)) {
        return;
    }

    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . hs_e($title) . '</title></head><body>';
}

function hs_include_footer() {
    if (hs_include_root_file('footer.php')) {
        return;
    }

    echo '</body></html>';
}
