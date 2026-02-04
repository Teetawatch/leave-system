<?php
/* ============================================================
 * NASS – Premium Hero Slider (Enhanced Ken Burns & Content Animations)
 * ============================================================ */

add_shortcode('nass_hero_slider', function ($atts) {
    $a = shortcode_atts([
        'category' => '',
        'count' => 6,
        'height' => 600,
        'mobile_height' => 450,
        'tablet_height' => 520,
        'autoplay' => 'true',
        'delay' => 7000,
        'kenburns' => 'true',
        'show_excerpt' => 'true',
        'orderby' => 'date',
        'order' => 'DESC',
        'fullwidth' => 'true',
        'image_fit' => 'cover',
    ], $atts, 'nass_hero_slider');

    $args = [
        'post_type' => 'post',
        'posts_per_page' => max(1, intval($a['count'])),
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'orderby' => sanitize_key($a['orderby']),
        'order' => (strtoupper($a['order']) === 'ASC' ? 'ASC' : 'DESC'),
        'meta_query' => [['key' => '_thumbnail_id', 'compare' => 'EXISTS']]
    ];
    if (!empty($a['category'])) {
        $args['category_name'] = sanitize_title($a['category']);
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '<div style="text-align:center;padding:40px;background:#f0f2f5;color:#666;">ไม่พบรายการสำหรับสไลด์</div>';
    }

    static $ns_printed_assets = false;
    static $instance_id = 0;
    $instance_id++;
    $slider_id = 'ns-slider-' . $instance_id;
    $first = true;

    $wrap_class = 'ns-wrap';
    if ($a['fullwidth'] === 'true')
        $wrap_class .= ' ns-full';
    $wrap_class .= ' ns-fit-' . strtolower($a['image_fit']);

    $inline_vars = "--ns-height:{$a['height']}px;";
    $inline_vars .= "--ns-height-mobile:{$a['mobile_height']}px;";
    $inline_vars .= "--ns-height-tablet:{$a['tablet_height']}px;";
    $inline_vars .= "--ns-delay:{$a['delay']}ms;";

    ob_start(); ?>

    <section class="<?php echo esc_attr($wrap_class); ?>" id="<?php echo esc_attr($slider_id); ?>"
        style="<?php echo esc_attr($inline_vars); ?>">
        <div class="ns-slider <?php echo ($a['kenburns'] === 'true') ? 'ns-kenburns' : ''; ?>"
            data-autoplay="<?php echo esc_attr($a['autoplay']); ?>" data-delay="<?php echo esc_attr($a['delay']); ?>">

            <div class="ns-track">
                <?php while ($q->have_posts()):
                    $q->the_post();
                    $pid = get_the_ID();
                    $title = get_the_title();
                    $permalink = get_permalink();
                    $thumb_id = get_post_thumbnail_id($pid);
                    $cats = get_the_category();
                    $catname = $cats ? $cats[0]->name : '';
                    $excerpt = wp_trim_words(get_the_excerpt($pid), 100, '...'); // เพิ่มเป็น 100 คำ
                    ?>
                    <article class="ns-slide <?php echo $first ? 'is-active' : ''; ?>">
                        <div class="ns-img-wrap">
                            <?php echo wp_get_attachment_image($thumb_id, 'full', false, [
                                'class' => 'ns-img',
                                'alt' => $title,
                                'loading' => $first ? 'eager' : 'lazy',
                            ]); ?>
                        </div>
                        <div class="ns-overlay"></div>
                        <div class="ns-content-container">
                            <div class="ns-content">
                                <?php if ($catname): ?>
                                    <span class="ns-badge"><?php echo esc_html($catname); ?></span>
                                <?php endif; ?>
                                <h2 class="ns-title">
                                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                                </h2>
                                <?php if ($a['show_excerpt'] === 'true' && $excerpt): ?>
                                    <p class="ns-excerpt"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>
                                <div class="ns-actions">
                                    <a href="<?php echo esc_url($permalink); ?>" class="ns-btn-primary">
                                        <span>อ่านรายละเอียด</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php $first = false; endwhile;
                wp_reset_postdata(); ?>
            </div>

            <!-- Navigation -->
            <div class="ns-controls">
                <button class="ns-arrow ns-prev" aria-label="Previous"><svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6" />
                    </svg></button>
                <div class="ns-dots"></div>
                <button class="ns-arrow ns-next" aria-label="Next"><svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6" />
                    </svg></button>
            </div>

            <!-- Progress Bar -->
            <div class="ns-progress-bar">
                <div class="ns-progress-fill"></div>
            </div>
        </div>
    </section>

    <?php if (!$ns_printed_assets):
        $ns_printed_assets = true; ?>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

            .ns-wrap {
                --ns-primary: #0f2557;
                --ns-accent: #f59e0b;
                --ns-white: #ffffff;
                --ns-font-main: 'Prompt', sans-serif;
                width: 100%;
                position: relative;
                font-family: var(--ns-font-main);
                overflow: hidden;
            }

            .ns-wrap.ns-full {
                width: 100vw;
                margin-left: calc(50% - 50vw);
                margin-right: calc(50% - 50vw);
            }

            .ns-slider {
                position: relative;
                height: var(--ns-height);
                background: #000;
                overflow: hidden;
            }

            @media (max-width: 1024px) {
                .ns-slider {
                    height: var(--ns-height-tablet);
                }
            }

            @media (max-width: 640px) {
                .ns-slider {
                    height: var(--ns-height-mobile);
                }
            }

            .ns-track {
                display: flex;
                height: 100%;
                transition: transform 0.8s cubic-bezier(0.645, 0.045, 0.355, 1);
                will-change: transform;
            }

            .ns-slide {
                min-width: 100%;
                height: 100%;
                position: relative;
                overflow: hidden;
            }

            .ns-img-wrap {
                position: absolute;
                inset: 0;
                z-index: 1;
            }

            .ns-img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center;
                min-width: 100%;
                min-height: 100%;
                display: block;
                transform: scale(1.05);
                transition: transform 12s linear;
            }

            /* Ken Burns Effect */
            .ns-kenburns .ns-slide.is-active .ns-img {
                transform: scale(1.2) translate(1%, 1%);
            }

            .ns-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(to top,
                        rgba(15, 23, 42, 0.95) 0%,
                        rgba(15, 23, 42, 0.6) 40%,
                        rgba(0, 0, 0, 0.3) 100%);
                z-index: 2;
            }

            /* Content Animations */
            .ns-content-container {
                position: absolute;
                inset: 0;
                z-index: 3;
                display: flex;
                align-items: flex-end;
                padding: 60px 10%;
                text-align: left;
            }

            .ns-content {
                max-width: 850px;
                color: var(--ns-white);
            }

            .ns-badge {
                display: inline-block;
                background: var(--ns-accent);
                color: #000;
                padding: 5px 15px;
                border-radius: 5px;
                font-weight: 600;
                font-size: 0.85rem;
                margin-bottom: 20px;
                transform: translateY(20px);
                opacity: 0;
                transition: all 0.6s ease;
            }

            .ns-title {
                font-size: clamp(1.2rem, 2.5vw, 1.8rem); /* ปรับให้เล็กลงอีก */
                font-weight: 700;
                line-height: 1.4;
                margin: 0 0 12px;
                transform: translateY(30px);
                opacity: 0;
                transition: all 0.6s ease 0.1s;
                color: #ffffff;
                text-shadow: none;
            }

            .ns-title a {
                color: inherit;
                text-decoration: none;
            }

            .ns-excerpt {
                font-size: clamp(0.9rem, 1.1vw, 1.05rem);
                line-height: 1.6;
                margin-bottom: 30px;
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.6s ease 0.2s;
                /* เอา line-clamp ออกเพื่อให้แสดงข้อความได้จำนวมาก */
                display: block; 
                overflow: hidden;
                color: rgba(255, 255, 255, 0.95);
                max-width: 90%; /* ขยายความกว้างให้อ่านง่ายขึ้น */
            }

            .ns-actions {
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.6s ease 0.3s;
            }

            .ns-slide.is-active .ns-badge,
            .ns-slide.is-active .ns-title,
            .ns-slide.is-active .ns-excerpt,
            .ns-slide.is-active .ns-actions {
                transform: translateY(0);
                opacity: 1;
            }

            /* Buttons */
            .ns-btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                background: white;
                color: var(--ns-primary);
                padding: 14px 32px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .ns-btn-primary:hover {
                background: var(--ns-accent);
                color: black;
                transform: translateX(5px);
            }

            .ns-btn-primary svg {
                width: 20px;
                height: 20px;
                transition: transform 0.3s;
            }

            .ns-btn-primary:hover svg {
                transform: translateX(5px);
            }

            /* Controls */
            .ns-controls {
                position: absolute;
                bottom: 30px;
                right: 5%;
                z-index: 10;
                display: flex;
                align-items: center;
                gap: 20px;
            }

            .ns-arrow {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
                backdrop-filter: blur(5px);
            }

            .ns-arrow:hover {
                background: var(--ns-accent);
                border-color: var(--ns-accent);
                color: black;
            }

            .ns-arrow svg {
                width: 24px;
                height: 24px;
            }

            .ns-dots {
                display: flex;
                gap: 10px;
            }

            .ns-dot {
                width: 12px;
                height: 12px;
                border: 2px solid white;
                background: transparent;
                border-radius: 50%;
                padding: 0;
                cursor: pointer;
                transition: all 0.3s;
            }

            .ns-dot.is-active {
                background: var(--ns-accent);
                border-color: var(--ns-accent);
                width: 30px;
                border-radius: 10px;
            }

            /* Progress Bar */
            .ns-progress-bar {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: rgba(255, 255, 255, 0.1);
                z-index: 10;
            }

            .ns-progress-fill {
                height: 100%;
                background: var(--ns-accent);
                width: 0;
                transition: width linear;
            }

            @media (max-width: 768px) {
                .ns-content-container {
                    padding: 40px 5%;
                    align-items: center;
                    text-align: center;
                }

                .ns-controls {
                    right: 50%;
                    transform: translateX(50%);
                    bottom: 20px;
                    gap: 15px;
                }

                .ns-arrow {
                    display: none;
                }
            }
        </style>

        <script>
            (function () {
                const initSlider = (slider) => {
                    const track = slider.querySelector('.ns-track');
                    const slides = Array.from(track.children);
                    const dotsContainer = slider.querySelector('.ns-dots');
                    const progressFill = slider.querySelector('.ns-progress-fill');
                    const delay = parseInt(slider.dataset.delay) || 7000;
                    const autoplay = slider.dataset.autoplay === 'true';

                    let currentIndex = 0;
                    let interval;
                    let startTime;

                    // Generate Dots
                    slides.forEach((_, i) => {
                        const dot = document.createElement('button');
                        dot.className = `ns-dot ${i === 0 ? 'is-active' : ''}`;
                        dot.onclick = () => goTo(i);
                        dotsContainer.appendChild(dot);
                    });

                    const dots = Array.from(dotsContainer.children);

                    const update = () => {
                        track.style.transform = `translateX(-${currentIndex * 100}%)`;
                        slides.forEach((s, i) => s.classList.toggle('is-active', i === currentIndex));
                        dots.forEach((d, i) => d.classList.toggle('is-active', i === currentIndex));

                        if (autoplay) {
                            resetProgress();
                        }
                    };

                    const resetProgress = () => {
                        progressFill.style.transition = 'none';
                        progressFill.style.width = '0';
                        setTimeout(() => {
                            progressFill.style.transition = `width ${delay}ms linear`;
                            progressFill.style.width = '100%';
                        }, 50);
                    };

                    const goTo = (index) => {
                        currentIndex = (index + slides.length) % slides.length;
                        update();
                        startAutoplay();
                    };

                    const startAutoplay = () => {
                        if (!autoplay) return;
                        clearInterval(interval);
                        interval = setInterval(() => goTo(currentIndex + 1), delay);
                    };

                    slider.querySelector('.ns-prev').onclick = () => goTo(currentIndex - 1);
                    slider.querySelector('.ns-next').onclick = () => goTo(currentIndex + 1);

                    // Handle touch
                    let startX = 0;
                    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; clearInterval(interval); });
                    track.addEventListener('touchend', e => {
                        const diff = startX - e.changedTouches[0].clientX;
                        if (Math.abs(diff) > 50) goTo(currentIndex + (diff > 0 ? 1 : -1));
                        else startAutoplay();
                    });

                    update();
                    startAutoplay();
                };

                document.querySelectorAll('.ns-slider').forEach(initSlider);
            })();
        </script>
    <?php endif; ?>

    <?php
    return ob_get_clean();
});