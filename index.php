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
                $catColor = 'text-brand-700';
                $avatarBg = 'from-brand-50 to-brand-100/50';
                $iconBg   = 'bg-brand-900 text-gold-400';
                
                if (stripos($category, 'Sister') !== false || stripos($category, 'Kid') !== false) {
                    $catColor = 'text-amber-700';
                    $avatarBg = 'from-amber-50 to-amber-100/40';
                    $iconBg   = 'bg-gold-400 text-brand-950';
                } elseif (stripos($category, 'Academic') !== false || stripos($category, 'Science') !== false || stripos($category, 'Math') !== false) {
                    $catColor = 'text-sky-800';
                    $avatarBg = 'from-sky-50 to-blue-100/40';
                    $iconBg   = 'bg-slate-900 text-sky-400';
                }

                $avatarHtml = '';
                if (!empty($image) && file_exists(__DIR__ . '/' . ltrim($image, '/'))) {
                    $avatarHtml = '<img src="' . e($image) . '" alt="' . e($name) . '" class="w-full h-44 object-cover" loading="lazy">';
                } else {
                    $avatarHtml = '
                    <div class="h-44 bg-gradient-to-b ' . $avatarBg . ' flex flex-col items-center justify-center relative p-4">
                      <div class="w-20 h-20 rounded-full ' . $iconBg . ' flex items-center justify-center shadow-md">
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
                    $specsHtml .= '<span class="inline-block px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-emerald-50 text-brand-900 border border-emerald-100">' . e($s) . '</span>';
                }

                $cardsHtml .= '
                <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-card bg-white flex flex-col transition-all hover:shadow-lg hover:-translate-y-1">
                  ' . $avatarHtml . '
                  <div class="p-5 flex-1 flex flex-col">
                    <div class="text-[11px] font-extrabold uppercase tracking-wider ' . $catColor . '">' . e($category) . '</div>
                    <h3 class="mt-1 text-base font-extrabold text-slate-950">' . e($name) . '</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-5">' . e($desc) . '</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-1.5 mt-auto">
                      ' . $specsHtml . '
                    </div>
                  </div>
                </div>';
            }

            // Replace grid content
            $pattern = '/(<div id="teachers-grid"[^>]*>)(.*?)(<\/div>\s*<div class="mt-12 text-center">)/s';
            $replacement = '$1' . $cardsHtml . '$3';
            $html = preg_replace($pattern, $replacement, $html);
        }
    } catch (Throwable $e) {
        // Fallback gracefully to index.html static output
    }
}

// Output HTML
echo $html;
