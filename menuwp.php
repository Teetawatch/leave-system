<?php
/* ==========================================================================
 * NASS – PROFESSIONAL UNIFIED HEADER (V7.0 Fixed Layout)
 *  - Fixed Full Width Alignment Strategy
 *  - Removed Mobile Toggle appearing on Desktop
 *  - Cleaned up Layout Overflow issues
 * ========================================================================= */

/**
 * 1. Register Menu Location
 */
add_action('init', function () {
    register_nav_menus([
        'nass_mega_menu_location' => __('NASS Mega Menu (หลัก)', 'nass-mega-menu'),
    ]);
});

/**
 * 2. Shortcode
 */
add_shortcode('nass_mega_menu', 'nass_render_mega_menu_system');

/**
 * 3. Custom Walker Class
 */
if (!class_exists('NASS_Mega_Menu_Walker')):

    class NASS_Mega_Menu_Walker extends Walker_Nav_Menu
    {
        public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
        {
            if (!$element)
                return;
            $id = $element->{$this->db_fields['id']};
            $has_children = !empty($children_elements[$id]);

            if ($has_children) {
                $element->classes[] = 'has-children';
                if ($depth === 0) {
                    $is_mega = false;
                    foreach ($children_elements[$id] as $child) {
                        if (!empty($children_elements[$child->ID])) {
                            $is_mega = true;
                            break;
                        }
                    }
                    $element->classes[] = $is_mega ? 'nass-mode-mega' : 'nass-mode-simple';
                }
            }
            parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
        }

        public function start_lvl(&$output, $depth = 0, $args = null)
        {
            $classes = ['sub-menu'];
            if ($depth === 0)
                $classes[] = 'nass-dropdown-panel';
            $output .= "\n<ul class=\"" . esc_attr(implode(' ', $classes)) . "\">\n";
        }

        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
        {
            $classes = empty($item->classes) ? [] : (array) $item->classes;
            $classes[] = 'lvl-' . $depth;

            // Extract Icon classes
            $icon_classes = array_filter($classes, function ($c) {
                return preg_match('/^(fa|fas|far|fab|fal|fad)(-|$)/', $c) || strpos($c, 'fa-') === 0;
            });
            $classes = array_diff($classes, $icon_classes);

            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
            $output .= '<li class="' . esc_attr($class_names) . '">';

            $atts = [
                'title' => !empty($item->attr_title) ? $item->attr_title : '',
                'target' => !empty($item->target) ? $item->target : '',
                'rel' => !empty($item->xfn) ? $item->xfn : '',
                'href' => !empty($item->url) ? $item->url : '#',
            ];
            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value))
                    $attributes .= ' ' . $attr . '="' . ($attr === 'href' ? esc_url($value) : esc_attr($value)) . '"';
            }

            $icon_html = !empty($icon_classes) ? '<i class="' . esc_attr(implode(' ', $icon_classes)) . ' nm-icon"></i>' : '';
            $title = apply_filters('the_title', $item->title, $item->ID);

            $item_output = isset($args->before) ? $args->before : '';
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $icon_html . '<span class="nm-text">' . $title . '</span>';

            if ($depth === 0 && in_array('has-children', (array) $item->classes)) {
                $item_output .= '<i class="fa-solid fa-chevron-down nm-caret"></i>';
            }
            $item_output .= '</a>';

            // Mobile-only toggle button (Only visible on mobile via CSS)
            if (in_array('has-children', (array) $item->classes)) {
                $item_output .= '<button class="nm-mobile-toggle" aria-label="Toggle Submenu"><i class="fa-solid fa-chevron-right"></i></button>';
            }

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }
endif;

/**
 * 4. Render Function (Unified)
 */
function nass_render_mega_menu_system($atts)
{
    static $nass_assets_printed = false;

    $a = shortcode_atts([
        'logo' => 'https://nass.ac.th/wp-content/uploads/2025/10/NSRS.webp',
        'logo_url' => '',
        'sticky' => 'true',
        'phone' => '52382',
        'email' => 'nass.ac.th@navy.mi.th',
    ], $atts);

    $final_logo = !empty($a['logo_url']) ? $a['logo_url'] : $a['logo'];

    ob_start(); ?>

    <?php if (!$nass_assets_printed):
        $nass_assets_printed = true; ?>
        <!-- Assets -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

            /* ===== CSS Variables ===== */
            :root {
                --nm-primary: #223554;
                --nm-accent: #C8A03A;
                --nm-white: #ffffff;
                --nm-text: #1e293b;
                --nm-font: 'Prompt', sans-serif;
                --nm-height: 90px;
                --nm-height-scrolled: 70px;
                --ntb-bg: #223554;
                --ntb-accent: #C8A03A;
                --ntb-white: #ffffff;
            }

            /* ===== Reset & Base ===== */
            .nm-wrapper * {
                box-sizing: border-box;
            }

            /* FORCE HIDE Mobile Elements on Desktop */
            .nm-mobile-toggle,
            .nm-mobile-btn,
            .nm-mobile-drawer {
                display: none !important;
            }

            /* ===== Main Wrapper handling Sticky for EVERYTHING ===== */
            .nm-wrapper {
                background: rgba(255, 255, 255, 0.98);
                font-family: var(--nm-font);

                /* FIX: Use Transform method for perfect centering */
                width: 100vw;
                position: relative;
                left: 50%;
                transform: translateX(-50%);
                margin-left: 0 !important;
                margin-right: 0 !important;

                z-index: 99999;
                border-bottom: 1px solid rgba(0, 0, 0, 0.06);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                box-sizing: border-box;
                transition: all 0.3s ease;
            }

            .nm-wrapper.is-sticky {
                position: fixed !important;
                top: 0 !important;
                /* Keep the centering even when sticky */
                left: 50% !important;
                transform: translateX(-50%) !important;
            }

            .nm-wrapper.scrolled {
                box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            }

            /* =========================================
                       PART 1: TOP BAR STYLES
                       ========================================= */
            .nass-topbar {
                background: var(--ntb-bg);
                color: var(--ntb-white);
                font-size: 14px;
                width: 100%;
                position: relative;
                z-index: 2;
            }

            .nass-topbar::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, var(--ntb-accent), #fbbf24, var(--ntb-accent));
            }

            .ntb-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 8px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .ntb-group {
                display: flex;
                align-items: center;
                gap: 20px;
            }

            .ntb-item {
                display: flex;
                align-items: center;
                gap: 8px;
                color: rgba(255, 255, 255, 0.9);
                text-decoration: none;
                transition: all 0.2s ease;
                cursor: pointer;
                border: none;
                background: transparent;
                padding: 0;
                font-family: inherit;
            }

            .ntb-item:hover {
                color: var(--ntb-white);
                transform: translateY(-1px);
            }

            .ntb-icon {
                width: 28px;
                height: 28px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                color: var(--ntb-white);
            }

            .ntb-access {
                background: rgba(0, 0, 0, 0.2);
                padding: 4px 12px;
                border-radius: 50px;
                display: flex;
                align-items: center;
                gap: 15px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .ntb-fz-ctrl {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .ntb-fz-btn {
                width: 24px;
                height: 24px;
                border-radius: 4px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                background: transparent;
                color: white;
                cursor: pointer;
                font-size: 14px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .ntb-fz-btn:hover {
                background: white;
                color: var(--ntb-bg);
            }

            /* Dropdowns & Extras */
            .ntb-dropdown {
                position: relative;
            }

            .ntb-menu,
            .ntb-search-wrap {
                position: absolute;
                top: calc(100% + 10px);
                right: 0;
                background: white;
                border-radius: 8px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
                transition: all 0.3s ease;
                z-index: 10050;
                padding: 10px;
                min-width: 200px;
                border-top: 3px solid var(--nm-accent);
            }

            .ntb-search-wrap {
                width: 300px;
            }

            .ntb-menu.is-open,
            .ntb-search-wrap.is-open {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .ntb-menu a {
                display: block;
                padding: 8px 12px;
                color: var(--nm-text);
                text-decoration: none;
                border-radius: 6px;
                font-size: 14px;
            }

            .ntb-menu a:hover {
                background: #f1f5f9;
                color: var(--nm-primary);
            }

            .ntb-search-form {
                display: flex;
                gap: 5px;
            }

            .ntb-search-input {
                flex: 1;
                border: 1px solid #ddd;
                padding: 8px 10px;
                border-radius: 4px;
                font-family: inherit;
            }

            .ntb-search-btn {
                background: transparent;
                color: #888;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                cursor: pointer;
            }

            .ntb-search-btn:hover {
                color: var(--nm-primary);
            }


            /* =========================================
                       PART 2: MAIN MENU STYLES
                       ========================================= */
            .nm-container {
                max-width: 1400px;
                width: 100%;
                margin: 0 auto;
                padding: 0 20px;
                height: var(--nm-height);
                display: grid;
                grid-template-columns: auto 1fr auto;
                align-items: center;
                gap: 30px;
                /* Space between logo and menu */
                transition: height 0.3s ease;
            }

            .nm-wrapper.scrolled .nm-container {
                height: var(--nm-height-scrolled);
            }

            /* Logo */
            .nm-brand {
                flex-shrink: 0;
                display: flex;
                align-items: center;
            }

            .nm-brand img {
                max-height: 70px;
                width: auto;
                min-width: 50px;
                object-fit: contain;
                display: block;
                transition: max-height 0.3s ease;
            }

            .nm-wrapper.scrolled .nm-brand img {
                max-height: 55px;
            }

            /* Navigation */
            .nm-nav {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                min-width: 0;
                overflow: visible;
            }

            .nm-root {
                display: flex;
                flex-wrap: nowrap;
                list-style: none;
                margin: 0;
                padding: 0;
                height: 100%;
                gap: 0;
            }

            .nm-root>li.lvl-0 {
                height: 100%;
                display: flex;
                align-items: center;
                position: relative;
            }

            .nm-root>li.lvl-0>a {
                padding: 0 12px;
                color: var(--nm-primary);
                text-decoration: none;
                font-weight: 500;
                font-size: 14px;
                height: 100%;
                display: flex;
                align-items: center;
                gap: 6px;
                transition: color 0.3s;
                white-space: nowrap;
            }

            .nm-root>li.lvl-0:hover>a {
                color: var(--nm-accent);
            }

            .nm-icon {
                color: var(--nm-accent);
                font-size: 16px;
                margin-right: 5px;
            }

            .nm-caret {
                font-size: 10px;
                margin-left: 4px;
                opacity: 0.6;
            }

            /* Dropdown Panels */
            .nass-dropdown-panel {
                position: absolute;
                top: 100%;
                left: 0;
                background: white;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                opacity: 0;
                visibility: hidden;
                transform: translateY(15px);
                transition: all 0.3s ease;
                z-index: 10000;
                border-radius: 0 0 12px 12px;
                border-top: 3px solid var(--nm-accent);
                overflow: hidden;
            }

            .nm-root>li.lvl-0:hover .nass-dropdown-panel {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            /* Mega Menu */
            .nass-mode-mega .nass-dropdown-panel {
                position: fixed;
                left: 5%;
                width: 90%;
                max-width: 1400px;
                right: auto;
                padding: 40px;
                display: flex;
                flex-wrap: wrap;
                gap: 40px;
                justify-content: center;
            }

            .nass-mode-mega .nass-dropdown-panel>li {
                flex: 0 0 220px;
                list-style: none;
            }

            .nass-group-title {
                display: block;
                text-transform: uppercase;
                font-weight: 700;
                color: var(--nm-primary);
                margin-bottom: 15px;
                font-size: 13px;
                letter-spacing: 1px;
                padding-bottom: 8px;
            }

            .nass-mode-mega .nass-dropdown-panel>li>a {
                font-weight: 700;
                color: var(--nm-primary);
                padding-bottom: 10px;
                margin-bottom: 10px;
                display: block;
                text-decoration: none;
            }

            /* Simple Dropdown */
            .nass-mode-simple .nass-dropdown-panel {
                width: 260px;
                padding: 10px 0;
            }

            .nass-mode-simple .sub-menu li a {
                padding: 12px 25px;
                display: block;
                color: var(--nm-text);
                text-decoration: none;
                transition: all 0.2s;
                border-left: 3px solid transparent;
            }

            .nass-mode-simple .sub-menu li a:hover {
                background: #f8fafc;
                color: var(--nm-primary);
                border-left-color: var(--nm-accent);
                padding-left: 30px;
            }

            /* Responsive */
            @media (max-width: 1600px) {
                .nm-root>li.lvl-0>a {
                    padding: 0 10px;
                    font-size: 13px;
                    gap: 5px;
                }

                .nm-brand img {
                    max-height: 65px;
                }
            }

            @media (max-width: 1400px) {
                .nm-root>li.lvl-0>a {
                    padding: 0 8px;
                    font-size: 12px;
                    gap: 4px;
                }

                .nm-brand img {
                    max-height: 60px;
                }
            }

            /* Mobile Responsive */
            @media (max-width: 1100px) {
                .nm-container {
                    display: flex;
                    justify-content: space-between;
                }

                .nm-mobile-btn {
                    display: flex !important;
                }

                .nm-mobile-toggle {
                    display: flex !important;
                }

                .nm-nav {
                    display: flex !important;
                    /* Ensure it overrides any other display rule */
                    position: fixed;
                    top: 0;
                    right: -100%;
                    width: 300px;
                    height: 100vh;
                    background: white;
                    z-index: 10050;
                    transition: right 0.4s ease;
                    flex-direction: column;
                    justify-content: flex-start;
                    padding: 80px 20px;
                    overflow-y: auto;
                    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
                    left: auto;
                }

                .nm-nav.is-active {
                    right: 0;
                }

                .nm-root {
                    flex-direction: column;
                    height: auto;
                    gap: 0;
                    width: 100%;
                }

                .nm-root>li.lvl-0 {
                    width: 100%;
                    height: auto;
                    display: block;
                }

                .nm-root>li.lvl-0>a {
                    padding: 15px 0;
                    font-size: 15px;
                }

                .nm-mobile-toggle {
                    position: absolute;
                    right: 0;
                    top: 10px;
                    width: 35px;
                    height: 35px;
                    background: #f1f5f9;
                    border: none;
                    border-radius: 5px;
                    align-items: center;
                    justify-content: center;
                }

                /* Hide Topbar Text on Mobile */
                .ntb-item .txt {
                    display: none;
                }

                .nass-dropdown-panel {
                    position: static !important;
                    width: 100% !important;
                    display: none;
                    transform: none !important;
                    box-shadow: none !important;
                    border: none !important;
                    padding: 0 0 0 15px !important;
                    opacity: 1;
                    visibility: visible;
                    border-radius: 0 !important;
                    background: #f8fafc;
                }

                .nass-dropdown-panel.is-open {
                    display: block;
                }

                .nass-mode-mega .nass-dropdown-panel {
                    padding: 10px 0 10px 15px !important;
                    gap: 10px;
                }

                .nass-mode-mega .nass-dropdown-panel>li {
                    flex: none;
                    width: 100%;
                }
            }

            /* Mobile Toggle Button */
            .nm-mobile-btn {
                justify-self: end;
                display: none;
                width: 44px;
                height: 44px;
                background: #f1f5f9;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: var(--nm-primary);
            }

            /* High Contrast */
            html.ntb-contrast {
                filter: invert(1) hue-rotate(180deg);
            }

            html.ntb-contrast img,
            html.ntb-contrast video,
            html.ntb-contrast .nass-topbar,
            html.ntb-contrast .nm-wrapper {
                filter: invert(1) hue-rotate(180deg);
            }
        </style>

        <script>
            (function () {
                document.addEventListener('DOMContentLoaded', () => {
                    const html = document.documentElement;
                    const wrapper = document.querySelector('.nm-wrapper');
                    const nav = document.getElementById('nm-nav');
                    const btn = document.getElementById('nm-mobile-btn');

                    /* --- Sticky Logic --- */
                    const calcOffset = () => {
                        const admin = document.getElementById('wpadminbar');
                        const h_admin = admin ? admin.offsetHeight : 0;
                        html.style.setProperty('--ntb-admin-h', h_admin + 'px');

                        if (wrapper && window.scrollY > 0) {
                            wrapper.classList.toggle('scrolled', window.scrollY > 30);
                        }
                    };
                    window.addEventListener('resize', calcOffset);
                    window.addEventListener('scroll', calcOffset, { passive: true });
                    calcOffset();

                    /* --- TopBar Toggles --- */
                    document.addEventListener('click', (e) => {
                        const searchToggle = e.target.closest('[data-ntb="search-toggle"]');
                        const langToggle = e.target.closest('[data-ntb="lang-toggle"]');
                        const contrastBtn = e.target.closest('[data-ntb="contrast"]');
                        const fzPlus = e.target.closest('[data-ntb="fz-plus"]');
                        const fzMinus = e.target.closest('[data-ntb="fz-minus"]');
                        const mobileToggle = e.target.closest('.nm-mobile-toggle');

                        // Close Sticky Dropdowns if clicking outside
                        if (!e.target.closest('.ntb-dropdown') && !e.target.closest('.ntb-search-wrap')) {
                            document.querySelectorAll('.ntb-menu, .ntb-search-wrap').forEach(el => el.classList.remove('is-open'));
                        }

                        if (searchToggle) {
                            e.preventDefault();
                            const wrap = searchToggle.closest('.ntb-dropdown').querySelector('.ntb-search-wrap');
                            wrap.classList.toggle('is-open');
                        } else if (langToggle) {
                            e.preventDefault();
                            const menu = langToggle.closest('.ntb-dropdown').querySelector('.ntb-menu');
                            menu.classList.toggle('is-open');
                        } else if (contrastBtn) {
                            html.classList.toggle('ntb-contrast');
                            localStorage.setItem('ntb_contrast', html.classList.contains('ntb-contrast') ? '1' : '0');
                        } else if (fzPlus || fzMinus) {
                            let cur = parseFloat(getComputedStyle(html).getPropertyValue('--ntb-fz-scale')) || 1;
                            cur += fzPlus ? 0.05 : -0.05;
                            cur = Math.min(1.3, Math.max(0.9, cur));
                            html.style.setProperty('--ntb-fz-scale', cur);
                            html.style.fontSize = (16 * cur) + 'px';
                            localStorage.setItem('ntb_fz', cur);
                        }

                        // Mobile Submenu Toggle
                        if (mobileToggle) {
                            e.preventDefault(); // Stop link click
                            const sub = mobileToggle.closest('li').querySelector('.nass-dropdown-panel');
                            if (sub) {
                                sub.classList.toggle('is-open');
                                mobileToggle.style.transform = sub.classList.contains('is-open') ? 'rotate(90deg)' : 'none';
                            }
                        }
                    });

                    // Init Accessibility
                    if (localStorage.getItem('ntb_contrast') === '1') html.classList.add('ntb-contrast');
                    const fz = localStorage.getItem('ntb_fz');
                    if (fz) {
                        html.style.setProperty('--ntb-fz-scale', fz);
                        html.style.fontSize = (16 * fz) + 'px';
                    }

                    /* --- Mobile Menu --- */
                    if (btn && nav) {
                        btn.addEventListener('click', () => {
                            nav.classList.toggle('is-active');
                            const icon = btn.querySelector('i');
                            if (nav.classList.contains('is-active')) {
                                icon.className = 'fa-solid fa-xmark';
                            } else {
                                icon.className = 'fa-solid fa-bars';
                            }
                        });
                    }
                });
            })();
        </script>
    <?php endif; ?>

    <!-- UNIFIED HEADER WRAPPER -->
    <header class="nm-wrapper <?php echo ($a['sticky'] === 'true') ? 'is-sticky' : ''; ?>">

        <!-- PART 1: TOP BAR -->
        <div class="nass-topbar">
            <div class="ntb-container">
                <div class="ntb-group">
                    <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $a['phone'])); ?>" class="ntb-item">
                        <span class="ntb-icon"><i class="fas fa-phone-alt"></i></span>
                        <span class="txt"><?php echo esc_html($a['phone']); ?></span>
                    </a>
                    <a href="mailto:<?php echo esc_attr($a['email']); ?>" class="ntb-item">
                        <span class="ntb-icon"><i class="fas fa-envelope"></i></span>
                        <span class="txt"><?php echo esc_html($a['email']); ?></span>
                    </a>
                </div>

                <div class="ntb-group">
                    <!-- Search -->
                    <div class="ntb-dropdown">
                        <button class="ntb-item" data-ntb="search-toggle">
                            <span class="ntb-icon"><i class="fas fa-search"></i></span>
                            <span class="txt">ค้นหา</span>
                        </button>
                        <div class="ntb-search-wrap">
                            <form class="ntb-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                <input class="ntb-search-input" type="search" name="s" placeholder="ค้นหา..." />
                                <button class="ntb-search-btn"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>

                    <!-- Language -->
                    <div class="ntb-dropdown">
                        <button class="ntb-item" data-ntb="lang-toggle">
                            <span class="ntb-icon"><i class="fas fa-language"></i></span>
                            <span class="txt">Language</span>
                        </button>
                        <div class="ntb-menu">
                            <?php
                            if (function_exists('pll_the_languages')) {
                                $langs = pll_the_languages(['raw' => 1]);
                                foreach ($langs as $l)
                                    echo '<a href="' . $l['url'] . '">' . $l['name'] . '</a>';
                            } else {
                                echo '<a href="#">Thai (ไทย)</a><a href="#">English</a>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Tools -->
                    <div class="ntb-access">
                        <button class="ntb-item" data-ntb="contrast" title="ความคมชัด"><span class="ntb-icon"><i
                                    class="fas fa-adjust"></i></span></button>
                        <div class="ntb-fz-ctrl">
                            <button class="ntb-fz-btn" data-ntb="fz-minus">-</button>
                            <span style="font-size:10px; color:white; opacity:0.8;">SIZE</span>
                            <button class="ntb-fz-btn" data-ntb="fz-plus">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PART 2: MAIN MENU -->
        <div class="nm-container">
            <div class="nm-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url($final_logo); ?>" alt="Logo">
                </a>
            </div>

            <nav class="nm-nav" id="nm-nav">
                <?php
                wp_nav_menu([
                    'theme_location' => 'nass_mega_menu_location',
                    'container' => false,
                    'menu_class' => 'nm-root',
                    'walker' => new NASS_Mega_Menu_Walker(),
                    'depth' => 3,
                    'fallback_cb' => false,
                ]);
                ?>
            </nav>

            <button class="nm-mobile-btn" id="nm-mobile-btn" aria-label="Toggle Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <?php
    return ob_get_clean();
}
