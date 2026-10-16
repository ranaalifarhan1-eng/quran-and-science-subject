<?php
/**
 * Quran & Science — Dynamic Homepage Entry Point
 * 
 * Serves the approved homepage with server-rendered active teacher profiles
 * from MySQL database when available, while preserving exact frontend markup.
 */

$htmlFile = __DIR__ . '/index.html';
if (!file_exists($htmlFile)) {
    die('index.html not found.');
}

$html = file_get_contents($htmlFile);
$configFile = __DIR__ . '/config.php';

if (file_exists($configFile)) {
    require_once $configFile;

    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            SELECT id, name, role, category, short_description, specialties, image, display_order 
            FROM teachers 
            WHERE status = 'active' 
            ORDER BY display_order ASC, id ASC
        ");
        $stmt->execute();
        $teachers = $stmt->fetchAll();

        if (!empty($teachers)) {
            // Build card HTML
            $cardsHtml = '';
            foreach ($teachers as $idx => $t) {
                $category = !empty($t['category']) ? $t['category'] : 'Quran Learning';
                $name     = $t['name'];
                $role     = $t['role'];
                $desc     = $t['short_description'];
                $image    = $t['image'];
                $order    = $t['display_order'];

                // Category styling
                $catColor = 'text-brand-700 cat-quran';
                $avatarBg = 'from-brand-50 to-brand-100/50 avatar-quran';
                $iconBg   = 'bg-brand-900 text-gold-400 icon-quran';
                
                if (stripos($category, 'Sister') !== false || stripos($category, 'Kid') !== false) {
                    $catColor = 'text-amber-700 cat-sister';
                    $avatarBg = 'from-amber-50 to-amber-100/40 avatar-sister';
                    $iconBg   = 'bg-gold-400 text-brand-950 icon-sister';
                } elseif (stripos($category, 'Academic') !== false || stripos($category, 'Science') !== false || stripos($category, 'Math') !== false) {
                    $catColor = 'text-sky-800 cat-academic';
                    $avatarBg = 'from-sky-50 to-blue-100/40 avatar-academic';
                    $iconBg   = 'bg-slate-900 text-sky-400 icon-academic';
                }

                $avatarHtml = '';
                if (!empty($image) && file_exists(__DIR__ . '/' . ltrim($image, '/'))) {
                    $avatarHtml = '<div class="teacher-card-media"><img src="' . e($image) . '" alt="' . e($name) . '" class="w-full h-44 object-cover teacher-card-img" loading="lazy"></div>';
                } else {
                    $avatarHtml = '
                    <div class="teacher-card-media teacher-card-avatar h-44 bg-gradient-to-b ' . $avatarBg . ' flex flex-col items-center justify-center relative p-4">
                      <div class="teacher-card-avatar-icon w-20 h-20 rounded-full ' . $iconBg . ' flex items-center justify-center shadow-md">
                        <svg viewBox="0 0 24 24" class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5">
                          <circle cx="12" cy="8" r="4"/>
                          <path d="M4 21c.8-5 3.5-7 8-7s7.2 2 8 7"/>
                        </svg>
                      </div>
                    </div>';
                }

                // Specialties chips
                $specs = array_map('trim', explode(',', $t['specialties'] ?? ''));
                $specsHtml = '';
                foreach ($specs as $s) {
                    if (!$s) continue;
                    $specsHtml .= '<span class="teacher-card-tag inline-block px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-emerald-50 text-brand-900 border border-emerald-100">' . e($s) . '</span>';
                }

                $cardsHtml .= '
                <div class="teacher-slide">
                  <div class="teacher-card rounded-2xl border border-slate-200 overflow-hidden shadow-card bg-white flex flex-col transition-all hover:shadow-lg hover:-translate-y-1 h-full">
                    ' . $avatarHtml . '
                    <div class="teacher-card-body p-5 flex-1 flex flex-col">
                      <div class="teacher-card-category text-[11px] font-extrabold uppercase tracking-wider ' . $catColor . '">' . e($category) . '</div>
                      <h3 class="teacher-card-name mt-1 text-base font-extrabold text-slate-950">' . e($name) . '</h3>
                      <p class="teacher-card-desc mt-2 text-xs text-slate-600 leading-5">' . e($desc) . '</p>
                      <div class="teacher-card-tags mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-1.5 mt-auto">
                        ' . $specsHtml . '
                      </div>
                    </div>
                  </div>
                </div>';
            }

            // Replace track content in slider
            $pattern = '/(<div class="teachers-slider-track"[^>]*>)(.*?)(<\/div>\s*<\/div>\s*<\/div>\s*<div class="mt-12 text-center">)/s';
            $replacement = '$1' . $cardsHtml . '$3';
            $html = preg_replace($pattern, $replacement, $html);
        }

        // Query active testimonials
        try {
            $stmtTesti = $pdo->prepare("
                SELECT id, reviewer_name, review_text, location, category, image, display_order 
                FROM testimonials 
                WHERE status = 'active' 
                ORDER BY display_order ASC, id ASC
            ");
            $stmtTesti->execute();
            $testimonials = $stmtTesti->fetchAll();

            if (!empty($testimonials)) {
                $testiCardsHtml = '';
                foreach ($testimonials as $t) {
                    $name     = $t['reviewer_name'];
                    $text     = $t['review_text'];
                    $loc      = $t['location'] ?? '';
                    $cat      = $t['category'] ?? '';
                    $image    = $t['image'] ?? '';

                    $initial  = mb_strtoupper(mb_substr(trim($name), 0, 1, 'UTF-8'), 'UTF-8');
                    if (!$initial) $initial = 'Q';

                    $avatarHtml = '';
                    if (!empty($image) && file_exists(__DIR__ . '/' . ltrim($image, '/'))) {
                        $avatarHtml = '<img src="' . e($image) . '" alt="' . e($name) . '" class="testimonial-avatar" loading="lazy">';
                    } else {
                        $avatarHtml = '<div class="testimonial-avatar-initial">' . e($initial) . '</div>';
                    }

                    $catPillHtml = '';
                    if (!empty($cat)) {
                        $catPillHtml = '<span class="testimonial-category-pill">' . e($cat) . '</span>';
                    }

                    $locHtml = '';
                    if (!empty($loc)) {
                        $locHtml = '<div class="testimonial-author-location">' . e($loc) . '</div>';
                    }

                    $testiCardsHtml .= '
                    <div class="testimonial-slide">
                      <div class="testimonial-card">
                        <svg class="testimonial-quote-icon" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                        <p class="testimonial-text">“' . e($text) . '”</p>
                        <div class="testimonial-footer">
                          <div class="testimonial-author">
                            ' . $avatarHtml . '
                            <div class="min-w-0">
                              <h4 class="testimonial-author-name">' . e($name) . '</h4>
                              ' . $locHtml . '
                            </div>
                          </div>
                          ' . $catPillHtml . '
                        </div>
                      </div>
                    </div>';
                }

                // Replace track content in testimonials slider
                $testiPattern = '/(<div class="testimonials-slider-track"[^>]*>)(.*?)(<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/section>)/s';
                $testiReplacement = '$1' . $testiCardsHtml . '$3';
                $html = preg_replace($testiPattern, $testiReplacement, $html);

                // Reveal testimonials section by removing style="display:none"
                $html = str_replace(
                    '<section id="testimonials" class="py-16 md:py-20 bg-[#F8FAF8] border-b border-slate-100" style="display:none">',
                    '<section id="testimonials" class="py-16 md:py-20 bg-[#F8FAF8] border-b border-slate-100">',
                    $html
                );
            }
        } catch (Throwable $te) {
            // Testimonials table might not exist yet before migration
        }
    } catch (Throwable $e) {
        // Fallback gracefully to index.html static output
    }
}

// Output HTML
echo $html;
