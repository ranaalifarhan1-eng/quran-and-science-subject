// Quran & Science — Shared Application Script

function openMobileDrawer() {
  const nav = document.getElementById('mobileNav');
  const btn = document.getElementById('menuBtn');
  const backdrop = document.getElementById('mobileDrawerBackdrop');
  if (!nav || !btn) return;

  const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
  if (scrollbarWidth > 0) {
    document.body.style.paddingRight = scrollbarWidth + 'px';
  }
  document.documentElement.classList.add('drawer-scroll-lock');
  document.body.classList.add('drawer-scroll-lock');

  nav.classList.add('open');
  nav.setAttribute('aria-hidden', 'false');
  if (backdrop) backdrop.classList.add('open');

  btn.classList.add('is-open');
  btn.setAttribute('aria-expanded', 'true');

  const closeBtn = document.getElementById('mobileDrawerClose');
  if (closeBtn) {
    setTimeout(() => {
      if (nav.classList.contains('open')) {
        closeBtn.focus({ preventScroll: true });
      }
    }, 60);
  }
}

function closeMobileDrawer(returnFocus) {
  const nav = document.getElementById('mobileNav');
  const btn = document.getElementById('menuBtn');
  const backdrop = document.getElementById('mobileDrawerBackdrop');
  if (!nav) return;

  const wasOpen = nav.classList.contains('open');
  nav.classList.remove('open');
  nav.setAttribute('aria-hidden', 'true');
  if (backdrop) backdrop.classList.remove('open');

  if (btn) {
    btn.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
  }

  document.documentElement.classList.remove('drawer-scroll-lock');
  document.body.classList.remove('drawer-scroll-lock');
  document.body.style.paddingRight = '';

  if (returnFocus && wasOpen && btn) {
    btn.focus({ preventScroll: true });
  }
}

function toggleMenu() {
  const nav = document.getElementById('mobileNav');
  if (!nav) return;
  if (nav.classList.contains('open')) {
    closeMobileDrawer(true);
  } else {
    openMobileDrawer();
  }
}

function toggleMobileDropdown(id) {
  const dd = document.getElementById(id);
  if (!dd) return;
  const isOpen = dd.classList.toggle('open');
  const btn = dd.querySelector('.mobile-dropdown-btn');
  if (btn) btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

(function initMobileSlideDrawer() {
  const nav = document.getElementById('mobileNav');
  const btn = document.getElementById('menuBtn');
  if (!nav || !btn) return;

  // Upgrade hamburger button icon to crisp SVG while preserving aria attributes
  btn.setAttribute('aria-controls', 'mobileNav');
  btn.setAttribute('aria-expanded', 'false');
  btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="17" x2="20" y2="17"></line></svg>';

  // Create backdrop overlay if not already present
  let backdrop = document.getElementById('mobileDrawerBackdrop');
  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.id = 'mobileDrawerBackdrop';
    backdrop.className = 'mobile-drawer-backdrop';
    backdrop.setAttribute('aria-hidden', 'true');
    backdrop.addEventListener('click', () => closeMobileDrawer(false));
  }

  // Upgrade #mobileNav internal structure into Header + Scrollable Body + Footer CTA
  if (!nav.querySelector('.mobile-drawer-head')) {
    const head = document.createElement('div');
    head.className = 'mobile-drawer-head';
    head.innerHTML =
      '<a class="mobile-drawer-brand" href="index.html" aria-label="Quran &amp; Science Online Learning Academy">' +
        '<img class="mobile-drawer-logo" src="assets/images/logo/logo.png" alt="Quran &amp; Science Online Learning Academy" width="166" height="93">' +
      '</a>' +
      '<button type="button" class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Close navigation menu">' +
        '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
      '</button>';

    const body = document.createElement('div');
    body.className = 'mobile-drawer-body';

    const foot = document.createElement('div');
    foot.className = 'mobile-drawer-foot';

    const children = Array.from(nav.children);
    children.forEach(child => {
      if (child.tagName === 'A' && child.classList.contains('btn-gold')) {
        foot.appendChild(child);
      } else {
        body.appendChild(child);
      }
    });

    const footNote = document.createElement('p');
    footNote.className = 'mobile-drawer-foot-note';
    footNote.textContent = '3-Day Free Trial • Flexible global timings';
    foot.appendChild(footNote);

    // Wrap .mobile-dropdown-menu in .mobile-dropdown-collapse for smooth height animation
    const dd = body.querySelector('#quranMobileNav');
    if (dd) {
      const ddMenu = dd.querySelector('.mobile-dropdown-menu');
      const ddBtn = dd.querySelector('.mobile-dropdown-btn');
      dd.classList.remove('open');
      if (ddBtn) ddBtn.setAttribute('aria-expanded', 'false');
      if (ddMenu && !ddMenu.parentElement.classList.contains('mobile-dropdown-collapse')) {
        const collapseWrap = document.createElement('div');
        collapseWrap.className = 'mobile-dropdown-collapse';
        dd.insertBefore(collapseWrap, ddMenu);
        collapseWrap.appendChild(ddMenu);
      }

      // Highlight active Quran subpage link if on a Quran learning page
      const currentPath = (window.location.pathname.split('/').pop() || 'index.html').toLowerCase();
      const quranPagesMap = {
        'learn-quran-online.html': 'learn-quran-online.html',
        'para-1.html': 'learn-quran-online.html',
        'para-2.html': 'learn-quran-online.html',
        'para-3.html': 'learn-quran-online.html',
        'para-4.html': 'learn-quran-online.html',
        'para-5.html': 'learn-quran-online.html',
        'para-6.html': 'learn-quran-online.html',
        'para-7.html': 'learn-quran-online.html',
        'para-8.html': 'learn-quran-online.html',
        'para-9.html': 'learn-quran-online.html',
        'para-10.html': 'learn-quran-online.html',
    'para-11.html': 'learn-quran-online.html',
    'para-12.html': 'learn-quran-online.html',
    'para-13.html': 'learn-quran-online.html',
    'para-14.html': 'learn-quran-online.html',
    'para-15.html': 'learn-quran-online.html',
    'para-16.html': 'learn-quran-online.html',
    'para-17.html': 'learn-quran-online.html',
    'para-18.html': 'learn-quran-online.html',
    'para-19.html': 'learn-quran-online.html',
    'para-20.html': 'learn-quran-online.html',
    'para-21.html': 'learn-quran-online.html',
    'para-22.html': 'learn-quran-online.html',
    'para-23.html': 'learn-quran-online.html',
    'para-24.html': 'learn-quran-online.html',
    'para-25.html': 'learn-quran-online.html',
    'para-26.html': 'learn-quran-online.html',
    'para-27.html': 'learn-quran-online.html',
    'para-28.html': 'learn-quran-online.html',
    'para-29.html': 'learn-quran-online.html',
    'para-30.html': 'learn-quran-online.html',
        'kalimas.html': 'kalimas.html',
        'namaz.html': 'namaz.html',
        'duas.html': 'duas.html',
        'noorani-qaida.html': 'noorani-qaida.html'
      };
      if (quranPagesMap[currentPath] && ddMenu) {
        if (ddBtn) ddBtn.classList.add('active');
        ddMenu.querySelectorAll('a').forEach(link => {
          const href = (link.getAttribute('href') || '').toLowerCase();
          if (href === quranPagesMap[currentPath]) {
            link.classList.add('active');
          }
        });
      }
    }

    nav.innerHTML = '';
    nav.appendChild(head);
    nav.appendChild(body);
    nav.appendChild(foot);
  }

  nav.setAttribute('role', 'dialog');
  nav.setAttribute('aria-modal', 'true');
  nav.setAttribute('aria-label', 'Mobile navigation');
  nav.setAttribute('aria-hidden', 'true');

  // Portal backdrop and drawer directly to document.body to avoid .header backdrop-filter clipping
  document.body.appendChild(backdrop);
  document.body.appendChild(nav);

  const closeBtn = document.getElementById('mobileDrawerClose');
  if (closeBtn) {
    closeBtn.addEventListener('click', () => closeMobileDrawer(true));
  }

  // Close drawer when any navigation link (excluding the Quran accordion toggle button) is clicked
  nav.querySelectorAll('a[href]').forEach(a => {
    a.addEventListener('click', () => closeMobileDrawer(false));
  });

  // Auto-close drawer if window is resized to desktop (> 1024px)
  window.addEventListener('resize', () => {
    if (window.innerWidth > 1024 && nav.classList.contains('open')) {
      closeMobileDrawer(false);
    }
  });
})();

// Desktop Quran dropdown interaction
document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
  const toggle = dropdown.querySelector('.dropdown-toggle');
  if (!toggle) return;

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = dropdown.classList.contains('open');
    document.querySelectorAll('.nav-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) {
      dropdown.classList.add('open');
      toggle.setAttribute('aria-expanded', 'true');
    } else {
      dropdown.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
});

// Close open dropdowns on outside click or Esc, and trap focus in mobile drawer
document.addEventListener('click', (e) => {
  if (!e.target.closest('.nav-dropdown')) {
    document.querySelectorAll('.nav-dropdown.open').forEach(d => {
      d.classList.remove('open');
      const toggle = d.querySelector('.dropdown-toggle');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    });
  }
});

document.addEventListener('keydown', (e) => {
  const nav = document.getElementById('mobileNav');
  const isDrawerOpen = nav && nav.classList.contains('open');

  if (e.key === 'Escape') {
    if (isDrawerOpen) {
      e.preventDefault();
      closeMobileDrawer(true);
      return;
    }
    document.querySelectorAll('.nav-dropdown.open').forEach(d => {
      d.classList.remove('open');
      const toggle = d.querySelector('.dropdown-toggle');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    });
    closeLightbox();
  } else if (e.key === 'Tab' && isDrawerOpen) {
    const focusables = Array.from(
      nav.querySelectorAll('button:not([disabled]), a[href]')
    ).filter(el => el.offsetParent !== null);
    if (focusables.length > 0) {
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }
});

// Trial Form submission handling
document.querySelectorAll('[data-trial-form]').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    const success = form.parentElement.querySelector('.success');
    if (success) success.classList.add('show');
    form.reset();
  });
});

// Noorani Qaida Lightbox Viewer
let currentQaidaPage = 1;
const totalQaidaPages = 33;

const qaidaPagesData = [
  { page: 1, file: '01_lwsoptimized.webp', title: 'Lesson Page 01: Arabic Alphabet (Huruf Mufradat)' },
  { page: 2, file: '02_lwsoptimized.webp', title: 'Lesson Page 02: Compound Letters (Murakkabat)' },
  { page: 3, file: '03_lwsoptimized.webp', title: 'Lesson Page 03: Abbreviated Letters (Muqatta\'at)' },
  { page: 4, file: '04-1_lwsoptimized.webp', title: 'Lesson Page 04: Movements (Harakaat - Zabar, Zer, Pesh)' },
  { page: 5, file: '05-1_lwsoptimized.webp', title: 'Lesson Page 05: Tanween (Do Zabar, Do Zer, Do Pesh)' },
  { page: 6, file: '06_lwsoptimized.webp', title: 'Lesson Page 06: Tanween & Harakaat Exercises' },
  { page: 7, file: '07_lwsoptimized.webp', title: 'Lesson Page 07: Standing Movements (Khari Harakaat)' },
  { page: 8, file: '08_lwsoptimized.webp', title: 'Lesson Page 08: Letters of Maddah & Leen' },
  { page: 9, file: '09_lwsoptimized.webp', title: 'Lesson Page 09: Exercises on Maddah & Leen' },
  { page: 10, file: '10-2_lwsoptimized.webp', title: 'Lesson Page 10: Sukoon / Jazm (Resting Letters)' },
  { page: 11, file: '11-3_lwsoptimized.webp', title: 'Lesson Page 11: Sukoon / Jazm Practice Exercises' },
  { page: 12, file: '12-3_lwsoptimized.webp', title: 'Lesson Page 12: Tashdeed / Shaddah' },
  { page: 13, file: '13-2_lwsoptimized.webp', title: 'Lesson Page 13: Tashdeed Practice Exercises' },
  { page: 14, file: '14-2_lwsoptimized.webp', title: 'Lesson Page 14: Tashdeed Combined with Sukoon' },
  { page: 15, file: '15-2_lwsoptimized.webp', title: 'Lesson Page 15: Tashdeed with Tashdeed' },
  { page: 16, file: '16-2_lwsoptimized.webp', title: 'Lesson Page 16: Tashdeed with Letters of Madd' },
  { page: 17, file: '17-1_lwsoptimized.webp', title: 'Lesson Page 17: Rules of Noon Sakin & Tanween' },
  { page: 18, file: '18-1_lwsoptimized.webp', title: 'Lesson Page 18: Izhar (Clear Pronunciation)' },
  { page: 19, file: '19-1_lwsoptimized.webp', title: 'Lesson Page 19: Idgham (Assimilation / Merging)' },
  { page: 20, file: '20-1_lwsoptimized.webp', title: 'Lesson Page 20: Iqlab (Conversion)' },
  { page: 21, file: '21-1_lwsoptimized.webp', title: 'Lesson Page 21: Ikhfa (Concealment with Ghunnah)' },
  { page: 22, file: '22-1_lwsoptimized.webp', title: 'Lesson Page 22: Rules of Meem Sakin' },
  { page: 23, file: '23-1_lwsoptimized.webp', title: 'Lesson Page 23: Izhar Shafawi (Meem Sakin)' },
  { page: 24, file: '24-1_lwsoptimized.webp', title: 'Lesson Page 24: Idgham Shafawi (Meem Sakin)' },
  { page: 25, file: '25-1_lwsoptimized.webp', title: 'Lesson Page 25: Ikhfa Shafawi (Meem Sakin)' },
  { page: 26, file: '26-2_lwsoptimized.webp', title: 'Lesson Page 26: Rules of Letter Ra (Tafkheem & Tarqeeq)' },
  { page: 27, file: '27-1_lwsoptimized.webp', title: 'Lesson Page 27: Rules of Lam in Lafz-ul-Jalalah' },
  { page: 28, file: '28-1_lwsoptimized.webp', title: 'Lesson Page 28: Letters of Qalqalah (Echoing Sound)' },
  { page: 29, file: '29-1_lwsoptimized.webp', title: 'Lesson Page 29: Waqf Signs & Stopping Rules' },
  { page: 30, file: '30-2_lwsoptimized.webp', title: 'Lesson Page 30: Comprehensive Revision Exercise 1' },
  { page: 31, file: '31-1_lwsoptimized.webp', title: 'Lesson Page 31: Comprehensive Revision Exercise 2' },
  { page: 32, file: '32-1_lwsoptimized.webp', title: 'Lesson Page 32: Quranic Words Reading Practice' },
  { page: 33, file: '33-1_lwsoptimized.webp', title: 'Lesson Page 33: Quranic Verses Reading Readiness' }
];

// Daily Duas Lightbox Data
let currentDuaIndex = 1;
let currentLightboxMode = 'qaida';

const duasData = [
  { id: 1, file: '1-1-1_lwsoptimized.webp', title: 'Daily Supplications (Booklet Cover)', urdu: 'کتابچہ دعائیں' },
  { id: 2, file: '2_lwsoptimized.webp', title: 'Table of Contents & Curriculum', urdu: 'فہرست مضامین' },
  { id: 3, file: '4_lwsoptimized.webp', title: 'Iman-e-Mufasal (Faith Explained)', urdu: 'ایمان مفصل' },
  { id: 4, file: '5_lwsoptimized.webp', title: 'Azan — Arabic Call to Prayer', urdu: 'اذان (عربی کلمات)' },
  { id: 5, file: '6_lwsoptimized.webp', title: 'Azan — Urdu & English Translation', urdu: 'اذان کا ترجمہ' },
  { id: 6, file: '7_lwsoptimized.webp', title: 'Dua After Azan', urdu: 'اذان کے بعد کی دعا' },
  { id: 7, file: '8_lwsoptimized.webp', title: 'Namaz Part 1: Sana, Ta\'awwuz & Surah Fatiha', urdu: 'نماز: ثناء، تعوذ اور سورت فاتحہ' },
  { id: 8, file: '9_lwsoptimized.webp', title: 'Namaz Part 2: Surah Ikhlas, Ruku & Tasbeeh', urdu: 'نماز: سورت اخلاص اور رکوع' },
  { id: 9, file: '10_lwsoptimized.webp', title: 'Namaz Part 3: Qauma, Sajdah & Tashahhud', urdu: 'نماز: قومہ، سجدہ اور تشہد' },
  { id: 10, file: '11_lwsoptimized.webp', title: 'Namaz Part 4: Durood-e-Ibrahim & Dua-e-Masura', urdu: 'نماز: درود ابراہیمی اور دعا' },
  { id: 11, file: '12_lwsoptimized.webp', title: 'Namaz Part 5: Salam (Conclusion)', urdu: 'نماز: سلام' },
  { id: 12, file: '13_lwsoptimized.webp', title: 'Ayat-ul-Kursi — Arabic & Urdu', urdu: 'آیت الکرسی (عربی و اردو)' },
  { id: 13, file: '14_lwsoptimized.webp', title: 'Ayat-ul-Kursi — English Translation', urdu: 'آیت الکرسی (انگریزی ترجمہ)' },
  { id: 14, file: '15_lwsoptimized.webp', title: 'Dua-e-Qanoot (Witr Prayer) — Arabic & Urdu', urdu: 'دعائے قنوت (عربی و اردو)' },
  { id: 15, file: '16_lwsoptimized.webp', title: 'Dua-e-Qanoot — English Translation', urdu: 'دعائے قنوت (انگریزی ترجمہ)' },
  { id: 16, file: '17_lwsoptimized.webp', title: 'When Entering the Mosque', urdu: 'مسجد میں داخل ہونے کی دعا' },
  { id: 17, file: '18_lwsoptimized.webp', title: 'When Leaving the Mosque', urdu: 'مسجد سے نکلنے کی دعا' },
  { id: 18, file: '19_lwsoptimized.webp', title: 'Dua to Start Eating a Meal', urdu: 'کھانا شروع کرنے کی دعا' },
  { id: 19, file: '20_lwsoptimized.webp', title: 'Dua When Forgot in Middle of Meal', urdu: 'کھانے کے درمیان بھول جانے کی دعا' },
  { id: 20, file: '21_lwsoptimized.webp', title: 'Dua After Finishing a Meal', urdu: 'کھانا کھانے کے بعد کی دعا' },
  { id: 21, file: '22_lwsoptimized.webp', title: 'Dua for Drinking Milk', urdu: 'دودھ پینے کی دعا' },
  { id: 22, file: '23_lwsoptimized.webp', title: 'Dua for Going to Sleep', urdu: 'سوتے وقت کی دعا' },
  { id: 23, file: '24_lwsoptimized.webp', title: 'Dua for When Awaking', urdu: 'نیند سے بیدار ہونے کی دعا' },
  { id: 24, file: '25_lwsoptimized.webp', title: 'Dua for Entering the Bathroom', urdu: 'بیت الخلاء میں داخل ہونے کی دعا' },
  { id: 25, file: '26_lwsoptimized.webp', title: 'Dua for Coming Out of Bathroom', urdu: 'بیت الخلاء سے باہر نکلنے کی دعا' },
  { id: 26, file: '27_lwsoptimized.webp', title: 'Dua When Wearing Clothes', urdu: 'لباس پہننے کی دعا' },
  { id: 27, file: '28_lwsoptimized.webp', title: 'Dua for Entering Graveyard', urdu: 'قبرستان میں داخل ہونے کی دعا' },
  { id: 28, file: '29_lwsoptimized.webp', title: 'Dua When Looking in Mirror', urdu: 'آئینہ دیکھنے کی دعا' },
  { id: 29, file: '30_lwsoptimized.webp', title: 'Dua When Seeing New Moon', urdu: 'چاند دیکھنے کی دعا' },
  { id: 30, file: '31_lwsoptimized.webp', title: 'Dua When Leaving the House', urdu: 'گھر سے باہر نکلنے کی دعا' },
  { id: 31, file: '32_lwsoptimized.webp', title: 'Dua for Shab-e-Qadr', urdu: 'شب قدر کی دعا' },
  { id: 32, file: '33_lwsoptimized.webp', title: 'Intention for Fasting (Sahar)', urdu: 'روزہ رکھنے کی نیت' },
  { id: 33, file: '34_lwsoptimized.webp', title: 'Dua for Breaking Fast (Iftar)', urdu: 'روزہ افطار کرنے کی دعا' }
];
const totalDuaPages = duasData.length;

function openQaidaLightbox(pageNum) {
  currentLightboxMode = 'qaida';
  const modal = document.getElementById('qaidaLightbox');
  const img = document.getElementById('lightboxImg');
  const title = document.getElementById('lightboxTitle');
  const counter = document.getElementById('lightboxCounter');
  if (!modal || !img) return;

  currentQaidaPage = Math.max(1, Math.min(totalQaidaPages, pageNum));
  const data = qaidaPagesData[currentQaidaPage - 1];

  img.src = 'assets/images/noorani-qaida/' + data.file;
  img.alt = data.title;
  if (title) title.textContent = data.title;
  if (counter) counter.textContent = `Page ${currentQaidaPage} of ${totalQaidaPages}`;

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function openDuaLightbox(duaNum) {
  currentLightboxMode = 'duas';
  const modal = document.getElementById('duasLightbox') || document.getElementById('qaidaLightbox');
  const img = document.getElementById('lightboxImg');
  const title = document.getElementById('lightboxTitle');
  const counter = document.getElementById('lightboxCounter');
  if (!modal || !img) return;

  currentDuaIndex = Math.max(1, Math.min(totalDuaPages, duaNum));
  const data = duasData[currentDuaIndex - 1];

  img.src = 'assets/images/duas/' + data.file;
  img.alt = data.title;
  if (title) title.textContent = data.title + (data.urdu ? ' — ' + data.urdu : '');
  if (counter) counter.textContent = `Supplication ${currentDuaIndex} of ${totalDuaPages}`;

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

// Namaz Step-by-Step Lightbox Data
let currentNamazStep = 1;

const namazData = [
  { id: 1, file: '3-1_lwsoptimized.webp', title: 'Step 01: Al-Qayyam (Takbeer, Ta\'awwuz & Tasmiyah)', posture: 'Posture 1: Standing (Qiyam)' },
  { id: 2, file: '4-1_lwsoptimized.webp', title: 'Step 02: Qiyam — Recitation of Surah Al-Fatihah', posture: 'Posture 2: Standing Recitation' },
  { id: 3, file: '5-1_lwsoptimized.webp', title: 'Step 03: Qiyam — Recitation of Surah Al-Ikhlas', posture: 'Posture 2: Additional Surah' },
  { id: 4, file: '7-1_lwsoptimized.webp', title: 'Step 04: Ruku — Bowing with Subhana Rabbiyal-Azeem', posture: 'Posture 3: Bowing (Ruku)' },
  { id: 5, file: '8-1_lwsoptimized.webp', title: 'Step 05: Qaumah — Standing Straight after Ruku', posture: 'Posture 4: Rising (Qaumah)' },
  { id: 6, file: '9-1_lwsoptimized.webp', title: 'Step 06: First Sajdah — Subhana Rabbiyal-A\'la', posture: 'Posture 5: First Prostration' },
  { id: 7, file: '10-1_lwsoptimized.webp', title: 'Step 07: Second Sajdah — Repeated Prostration', posture: 'Posture 7: Second Prostration' },
  { id: 8, file: '11-1_lwsoptimized.webp', title: 'Step 08: Quood — Tashahhud (At-Tahiyyat Arabic Text)', posture: 'Posture 8: Sitting (Jalsah / Qa\'dah)' },
  { id: 9, file: '12-1_lwsoptimized.webp', title: 'Step 09: Quood — Tashahhud Translation & Raka\'at Rules', posture: 'Posture 8: Sitting Guidance' },
  { id: 10, file: '13-1_lwsoptimized.webp', title: 'Step 10: Quood — Durood-e-Ibrahim (Part 1)', posture: 'Posture 8: Salawat on Prophet' },
  { id: 11, file: '14-1_lwsoptimized.webp', title: 'Step 11: Quood — Durood-e-Ibrahim (Part 2)', posture: 'Posture 8: Barakah Supplication' },
  { id: 12, file: '15-1_lwsoptimized.webp', title: 'Step 12: Quood — Dua-e-Masura (Quranic Prayer)', posture: 'Posture 8: Supplication before Salam' },
  { id: 13, file: '16-1_lwsoptimized.webp', title: 'Step 13: Salam (Tasleem) — Turning Face Right & Left', posture: 'Posture 9: Concluding Prayer' }
];
const totalNamazSteps = namazData.length;

function closeLightbox() {
  document.querySelectorAll('.qaida-lightbox.open, #qaidaLightbox.open, #duasLightbox.open, #namazLightbox.open, #kalimasLightbox.open').forEach(modal => {
    modal.classList.remove('open');
  });
  document.body.style.overflow = '';
}

document.querySelectorAll('.qaida-lightbox').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeLightbox();
  });
});

function navQaidaLightbox(direction) {
  let next = currentQaidaPage + direction;
  if (next < 1) next = totalQaidaPages;
  if (next > totalQaidaPages) next = 1;
  openQaidaLightbox(next);
}

function navDuaLightbox(direction) {
  let next = currentDuaIndex + direction;
  if (next < 1) next = totalDuaPages;
  if (next > totalDuaPages) next = 1;
  openDuaLightbox(next);
}

function openNamazLightbox(stepNum) {
  currentLightboxMode = 'namaz';
  const modal = document.getElementById('namazLightbox') || document.getElementById('qaidaLightbox');
  const img = document.getElementById('lightboxImg');
  const title = document.getElementById('lightboxTitle');
  const counter = document.getElementById('lightboxCounter');
  if (!modal || !img) return;

  currentNamazStep = Math.max(1, Math.min(totalNamazSteps, stepNum));
  const data = namazData[currentNamazStep - 1];

  img.src = 'assets/images/namaz/' + data.file;
  img.alt = data.title;
  if (title) title.textContent = data.title + (data.posture ? ' (' + data.posture + ')' : '');
  if (counter) counter.textContent = `Step ${currentNamazStep} of ${totalNamazSteps}`;

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function navNamazLightbox(direction) {
  let next = currentNamazStep + direction;
  if (next < 1) next = totalNamazSteps;
  if (next > totalNamazSteps) next = 1;
  openNamazLightbox(next);
}

// Six Kalimas Lightbox Data
let currentKalimaIndex = 1;

const kalimasData = [
  { id: 1, file: 'kalma-1.jpg', title: 'First Kalima — Tayyibah (Word of Purity)', urdu: 'کلمہ طیبہ' },
  { id: 2, file: 'kalma-2.jpg', title: 'Second Kalima — Shahadah (Word of Evidence)', urdu: 'کلمہ شہادت' },
  { id: 3, file: 'kalma-3-1.jpg', title: 'Third Kalima — Tamjeed (Word of Glory)', urdu: 'کلمہ تمجید' },
  { id: 4, file: 'kalma-4.jpg', title: 'Fourth Kalima — Tawheed (Word of Oneness)', urdu: 'کلمہ توحید' },
  { id: 5, file: 'kalma-5.jpg', title: 'Fifth Kalima — Astaghfar (Word of Penitence)', urdu: 'کلمہ استغفار' },
  { id: 6, file: 'kalma-6.jpg', title: 'Sixth Kalima — Radd-e-Kufr (Word of Rejecting Disbelief)', urdu: 'کلمہ رد کفر' }
];
const totalKalimas = kalimasData.length;

function openKalimaLightbox(kalimaNum) {
  currentLightboxMode = 'kalimas';
  const modal = document.getElementById('kalimasLightbox') || document.getElementById('qaidaLightbox');
  const img = document.getElementById('lightboxImg');
  const title = document.getElementById('lightboxTitle');
  const counter = document.getElementById('lightboxCounter');
  if (!modal || !img) return;

  currentKalimaIndex = Math.max(1, Math.min(totalKalimas, kalimaNum));
  const data = kalimasData[currentKalimaIndex - 1];

  img.src = 'assets/images/kalimas/' + data.file;
  img.alt = data.title;
  if (title) title.textContent = data.title + (data.urdu ? ' — ' + data.urdu : '');
  if (counter) counter.textContent = `Kalima ${currentKalimaIndex} of ${totalKalimas}`;

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function navKalimaLightbox(direction) {
  let next = currentKalimaIndex + direction;
  if (next < 1) next = totalKalimas;
  if (next > totalKalimas) next = 1;
  openKalimaLightbox(next);
}

// Lightbox keyboard arrows & Escape
document.addEventListener('keydown', (e) => {
  const openModal = document.querySelector('.qaida-lightbox.open, #qaidaLightbox.open, #duasLightbox.open, #namazLightbox.open, #kalimasLightbox.open');
  if (!openModal) return;
  if (e.key === 'Escape') {
    closeLightbox();
    return;
  }
  if (e.key === 'ArrowLeft') {
    if (openModal.id === 'kalimasLightbox' || currentLightboxMode === 'kalimas') {
      navKalimaLightbox(-1);
    } else if (openModal.id === 'namazLightbox' || currentLightboxMode === 'namaz') {
      navNamazLightbox(-1);
    } else if (openModal.id === 'duasLightbox' || currentLightboxMode === 'duas') {
      navDuaLightbox(-1);
    } else {
      navQaidaLightbox(-1);
    }
  }
  if (e.key === 'ArrowRight') {
    if (openModal.id === 'kalimasLightbox' || currentLightboxMode === 'kalimas') {
      navKalimaLightbox(1);
    } else if (openModal.id === 'namazLightbox' || currentLightboxMode === 'namaz') {
      navNamazLightbox(1);
    } else if (openModal.id === 'duasLightbox' || currentLightboxMode === 'duas') {
      navDuaLightbox(1);
    } else {
      navQaidaLightbox(1);
    }
  }
});

// ============================================================================
// SHARED QURAN PARA FULLSCREEN VIEWER (CLICK TO OPEN + ZOOM + PAN + FIT/RESET)
// ============================================================================
var qvState = {
  slides: [],
  currentIndex: 0,
  zoom: 1,
  minZoom: 0.5,
  maxZoom: 3.0,
  step: 0.25,
  panX: 0,
  panY: 0,
  isDragging: false,
  startX: 0,
  startY: 0,
  pinchStartDist: 0,
  pinchStartZoom: 1
};

function initQuranViewerSlides() {
  if (Array.isArray(window.paraSlides) && window.paraSlides.length > 0) {
    qvState.slides = window.paraSlides;
    return;
  }
  var sheets = document.querySelectorAll('.quran-page-sheet');
  var discovered = [];
  sheets.forEach(function(sheet, idx) {
    var img = sheet.querySelector('.quran-page-img');
    var badge = sheet.querySelector('.quran-page-badge');
    if (img) {
      discovered.push({
        src: img.getAttribute('src'),
        label: badge ? badge.textContent.trim() : ('Page ' + (idx + 1) + ' of ' + sheets.length)
      });
    }
  });
  qvState.slides = discovered;
}

function setReaderView(mode) {
  var container = document.getElementById('quranPagesContainer');
  var btnReading = document.getElementById('btnViewReading');
  var btnGrid = document.getElementById('btnViewGrid');
  if (!container) return;
  if (mode === 'reading') {
    container.classList.remove('view-grid', 'quran-pages-grid');
    container.classList.add('view-reading');
    if (btnReading) btnReading.classList.add('active');
    if (btnGrid) btnGrid.classList.remove('active');
  } else {
    container.classList.remove('view-reading');
    container.classList.add('view-grid', 'quran-pages-grid');
    if (btnGrid) btnGrid.classList.add('active');
    if (btnReading) btnReading.classList.remove('active');
  }
}

function applyQuranViewerTransform() {
  var img = document.getElementById('paraLightboxImg');
  var stage = document.getElementById('qvStage');
  var zoomReadout = document.getElementById('qvZoomPercent');
  var btnZoomOut = document.getElementById('qvZoomOut');
  var btnZoomIn = document.getElementById('qvZoomIn');

  if (qvState.zoom <= 1) {
    qvState.panX = 0;
    qvState.panY = 0;
  }

  if (img) {
    img.style.transform = 'translate3d(' + Math.round(qvState.panX) + 'px, ' + Math.round(qvState.panY) + 'px, 0) scale(' + qvState.zoom + ')';
  }
  if (stage) {
    if (qvState.zoom > 1) {
      stage.classList.add('can-pan');
    } else {
      stage.classList.remove('can-pan', 'is-dragging');
    }
  }
  if (zoomReadout) {
    zoomReadout.textContent = Math.round(qvState.zoom * 100) + '%';
  }
  if (btnZoomOut) {
    btnZoomOut.disabled = qvState.zoom <= qvState.minZoom + 0.001;
  }
  if (btnZoomIn) {
    btnZoomIn.disabled = qvState.zoom >= qvState.maxZoom - 0.001;
  }
}

function quranViewerSetZoom(newZoom) {
  var clamped = Math.max(qvState.minZoom, Math.min(qvState.maxZoom, Math.round(newZoom * 100) / 100));
  qvState.zoom = clamped;
  applyQuranViewerTransform();
}

function quranViewerZoom(delta) {
  quranViewerSetZoom(qvState.zoom + delta);
}

function quranViewerFit() {
  qvState.zoom = 1.0;
  qvState.panX = 0;
  qvState.panY = 0;
  applyQuranViewerTransform();
}

function quranViewerReset() {
  qvState.zoom = 1.0;
  qvState.panX = 0;
  qvState.panY = 0;
  applyQuranViewerTransform();
}

function updateParaLightbox() {
  initQuranViewerSlides();
  if (!qvState.slides.length) return;

  var total = qvState.slides.length;
  qvState.currentIndex = Math.max(0, Math.min(total - 1, qvState.currentIndex));
  var slide = qvState.slides[qvState.currentIndex];

  var img = document.getElementById('paraLightboxImg');
  var counterTop = document.getElementById('paraLightboxCounter');
  var counterBottom = document.getElementById('qvBottomCounter');
  var title = document.getElementById('paraLightboxTitle');
  var prevBtn = document.getElementById('qvPrevBtn');
  var nextBtn = document.getElementById('qvNextBtn');

  if (img && slide) {
    img.src = slide.src;
    img.alt = slide.label || ('Page ' + (qvState.currentIndex + 1) + ' of ' + total);
  }

  var pageText = 'Page ' + (qvState.currentIndex + 1) + ' of ' + total;
  if (counterTop) counterTop.textContent = pageText;
  if (counterBottom) counterBottom.textContent = pageText;
  if (title && slide) title.textContent = slide.label || pageText;

  // Disable Previous at Page 1 and Next at final Page of current Para (never jump to another Para)
  var isFirst = qvState.currentIndex <= 0;
  var isLast = qvState.currentIndex >= total - 1;

  if (prevBtn) {
    prevBtn.disabled = isFirst;
    prevBtn.classList.toggle('is-disabled', isFirst);
  }
  if (nextBtn) {
    nextBtn.disabled = isLast;
    nextBtn.classList.toggle('is-disabled', isLast);
  }

  quranViewerReset();
}

function openParaLightbox(index) {
  initQuranViewerSlides();
  var lb = document.getElementById('paraLightbox');
  if (!lb || !qvState.slides.length) return;

  qvState.currentIndex = typeof index === 'number' ? index : 0;
  updateParaLightbox();

  lb.classList.add('open', 'active');
  document.body.style.overflow = 'hidden';
  document.documentElement.style.overflow = 'hidden';
}

function closeParaLightbox() {
  var lb = document.getElementById('paraLightbox');
  if (lb) {
    lb.classList.remove('open', 'active');
  }
  document.body.style.overflow = '';
  document.documentElement.style.overflow = '';
}

function navParaLightbox(dir) {
  initQuranViewerSlides();
  var total = qvState.slides.length;
  if (!total) return;
  var nextIndex = qvState.currentIndex + dir;
  // Strictly clamp within current Para (do not wrap around or jump to another Para)
  if (nextIndex < 0 || nextIndex >= total) return;
  qvState.currentIndex = nextIndex;
  updateParaLightbox();
}

// Bind delegated click & interaction events once DOM is ready
(function setupQuranViewerInteractions() {
  function bindAll() {
    initQuranViewerSlides();

    // Ensure clicking ANY part of a .quran-page-sheet (image, card, header, badge, or Enlarge button) opens that exact page
    var sheets = document.querySelectorAll('.quran-page-sheet');
    sheets.forEach(function(sheet, idx) {
      sheet.setAttribute('data-page-index', String(idx));
      sheet.addEventListener('click', function(e) {
        e.preventDefault();
        openParaLightbox(idx);
      });
      var zoomBtn = sheet.querySelector('.quran-page-zoom-btn');
      if (zoomBtn) {
        zoomBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          openParaLightbox(idx);
        });
      }
    });

    var stage = document.getElementById('qvStage');
    var lb = document.getElementById('paraLightbox');
    if (!stage || !lb) return;

    // Close when clicking empty dark backdrop inside stage
    stage.addEventListener('click', function(e) {
      if (e.target === stage && !qvState.isDragging && qvState.zoom <= 1) {
        closeParaLightbox();
      }
    });

    // Desktop Mouse Wheel Zoom inside stage
    stage.addEventListener('wheel', function(e) {
      if (!lb.classList.contains('open') && !lb.classList.contains('active')) return;
      e.preventDefault();
      var delta = e.deltaY < 0 ? qvState.step : -qvState.step;
      quranViewerZoom(delta);
    }, { passive: false });

    // Desktop Mouse Drag / Pan when zoomed
    stage.addEventListener('mousedown', function(e) {
      if (qvState.zoom <= 1 || e.button !== 0) return;
      e.preventDefault();
      qvState.isDragging = true;
      qvState.startX = e.clientX - qvState.panX;
      qvState.startY = e.clientY - qvState.panY;
      stage.classList.add('is-dragging');
    });

    window.addEventListener('mousemove', function(e) {
      if (!qvState.isDragging) return;
      e.preventDefault();
      var maxPanX = (window.innerWidth * (qvState.zoom - 0.5)) * 0.65;
      var maxPanY = (window.innerHeight * (qvState.zoom - 0.5)) * 0.65;
      qvState.panX = Math.max(-maxPanX, Math.min(maxPanX, e.clientX - qvState.startX));
      qvState.panY = Math.max(-maxPanY, Math.min(maxPanY, e.clientY - qvState.startY));
      applyQuranViewerTransform();
    });

    window.addEventListener('mouseup', function() {
      if (qvState.isDragging) {
        qvState.isDragging = false;
        stage.classList.remove('is-dragging');
      }
    });

    // Mobile Touch Drag/Pan & Two-Finger Pinch-to-Zoom
    stage.addEventListener('touchstart', function(e) {
      if (e.touches.length === 2) {
        e.preventDefault();
        var dx = e.touches[0].clientX - e.touches[1].clientX;
        var dy = e.touches[0].clientY - e.touches[1].clientY;
        qvState.pinchStartDist = Math.hypot(dx, dy);
        qvState.pinchStartZoom = qvState.zoom;
      } else if (e.touches.length === 1 && qvState.zoom > 1) {
        qvState.isDragging = true;
        qvState.startX = e.touches[0].clientX - qvState.panX;
        qvState.startY = e.touches[0].clientY - qvState.panY;
        stage.classList.add('is-dragging');
      }
    }, { passive: false });

    stage.addEventListener('touchmove', function(e) {
      if (e.touches.length === 2 && qvState.pinchStartDist > 0) {
        e.preventDefault();
        var dx = e.touches[0].clientX - e.touches[1].clientX;
        var dy = e.touches[0].clientY - e.touches[1].clientY;
        var dist = Math.hypot(dx, dy);
        var scaleRatio = dist / qvState.pinchStartDist;
        quranViewerSetZoom(qvState.pinchStartZoom * scaleRatio);
      } else if (e.touches.length === 1 && qvState.isDragging && qvState.zoom > 1) {
        e.preventDefault();
        var maxPanX = (window.innerWidth * (qvState.zoom - 0.5)) * 0.7;
        var maxPanY = (window.innerHeight * (qvState.zoom - 0.5)) * 0.7;
        qvState.panX = Math.max(-maxPanX, Math.min(maxPanX, e.touches[0].clientX - qvState.startX));
        qvState.panY = Math.max(-maxPanY, Math.min(maxPanY, e.touches[0].clientY - qvState.startY));
        applyQuranViewerTransform();
      }
    }, { passive: false });

    stage.addEventListener('touchend', function() {
      qvState.isDragging = false;
      qvState.pinchStartDist = 0;
      stage.classList.remove('is-dragging');
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindAll);
  } else {
    bindAll();
  }
})();

// Keyboard Shortcuts for Fullscreen Quran Page Viewer
document.addEventListener('keydown', function(e) {
  var lb = document.getElementById('paraLightbox');
  if (!lb || (!lb.classList.contains('open') && !lb.classList.contains('active'))) return;

  if (e.key === 'Escape') {
    e.preventDefault();
    closeParaLightbox();
  } else if (e.key === 'ArrowLeft') {
    e.preventDefault();
    navParaLightbox(-1);
  } else if (e.key === 'ArrowRight') {
    e.preventDefault();
    navParaLightbox(1);
  } else if (e.key === '+' || e.key === '=') {
    e.preventDefault();
    quranViewerZoom(qvState.step);
  } else if (e.key === '-' || e.key === '_') {
    e.preventDefault();
    quranViewerZoom(-qvState.step);
  } else if (e.key === '0') {
    e.preventDefault();
    quranViewerReset();
  }
});



