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

    // Wrap all .mobile-dropdown-menu in .mobile-dropdown-collapse for smooth height animation
    const currentPath = (window.location.pathname.split('/').pop() || 'index.html').toLowerCase();
    body.querySelectorAll('.mobile-dropdown').forEach(dd => {
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
    });

    // Highlight active Quran subpage link if on a Quran learning page
    const quranDD = body.querySelector('#quranMobileNav');
    if (quranDD) {
      const ddMenu = quranDD.querySelector('.mobile-dropdown-menu');
      const ddBtn = quranDD.querySelector('.mobile-dropdown-btn');
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

    // Highlight active Academic Tutoring subpage if on academic page
    const academicDD = body.querySelector('#academicMobileNav');
    if (academicDD) {
      const ddMenu = academicDD.querySelector('.mobile-dropdown-menu');
      const ddBtn = academicDD.querySelector('.mobile-dropdown-btn');
      if (currentPath === 'academic-tutoring.html') {
        if (ddBtn) ddBtn.classList.add('active');
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

// =========================================================================
// Floating WhatsApp Contact Button Site-Wide
// =========================================================================
function initFloatingWhatsApp(settings) {
  var waBtn = document.getElementById('floatingWhatsApp');

  if (settings && settings.whatsapp) {
    if (!settings.whatsapp.visible) {
      if (waBtn) waBtn.style.display = 'none';
      return;
    }
  }

  var defaultDigits = '923445455154';
  var defaultMsg = 'Hello, I would like to know more about Quran & Science online learning programs.';
  var digits = (settings && settings.whatsapp && settings.whatsapp.digits) ? settings.whatsapp.digits : defaultDigits;
  var msg = (settings && settings.whatsapp && settings.whatsapp.message) ? settings.whatsapp.message : defaultMsg;
  var waHref = 'https://wa.me/' + digits + '?text=' + encodeURIComponent(msg);

  if (waBtn) {
    waBtn.style.display = 'inline-flex';
    waBtn.href = waHref;
    return;
  }

  waBtn = document.createElement('a');
  waBtn.id = 'floatingWhatsApp';
  waBtn.className = 'floating-whatsapp';
  waBtn.href = waHref;
  waBtn.target = '_blank';
  waBtn.rel = 'noopener noreferrer';
  waBtn.setAttribute('aria-label', 'Chat with Quran & Science on WhatsApp');
  waBtn.innerHTML =
    '<span class="floating-whatsapp-tooltip">Chat on WhatsApp</span>' +
    '<div class="floating-whatsapp-icon">' +
      '<svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">' +
        '<path d="M17.507 14.307c-.283-.141-1.674-.827-1.933-.923-.26-.094-.448-.141-.637.142-.189.283-.732.923-.897 1.112-.165.189-.33.212-.613.07-.283-.141-1.196-.441-2.279-1.406-.843-.751-1.411-1.679-1.576-1.962-.165-.283-.018-.436.124-.577.128-.127.283-.33.424-.495.142-.165.189-.283.283-.472.094-.189.047-.354-.024-.495-.07-.141-.637-1.533-.872-2.1-.23-.554-.463-.479-.637-.488-.165-.008-.354-.01-.542-.01-.189 0-.495.07-.755.354-.26.283-.99.967-.99 2.358 0 1.391 1.013 2.735 1.155 2.924.141.189 1.993 3.044 4.829 4.266.675.291 1.202.465 1.613.596.678.216 1.295.185 1.783.112.545-.081 1.674-.684 1.91-1.344.236-.66.236-1.226.165-1.344-.07-.118-.26-.189-.543-.33z"/>' +
        '<path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.892.525 3.662 1.438 5.176L2.05 21.65a.75.75 0 0 0 .918.918l4.474-1.388A9.957 9.957 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm-8.5 10c0-4.694 3.806-8.5 8.5-8.5s8.5 3.806 8.5 8.5-3.806 8.5-8.5 8.5a8.46 8.46 0 0 1-4.303-1.171.75.75 0 0 0-.583-.075l-3.32.99.99-3.32a.75.75 0 0 0-.075-.583A8.46 8.46 0 0 1 3.5 12z"/>' +
      '</svg>' +
    '</div>';

  document.body.appendChild(waBtn);
}

// =========================================================================
// Curricula Auto-loop Carousel / Slider
// =========================================================================
function initCurriculaSlider() {
  var viewport = document.getElementById('curriculaSliderViewport');
  var track = document.getElementById('curriculaSliderTrack');
  var prevBtn = document.getElementById('curriculaPrevBtn');
  var nextBtn = document.getElementById('curriculaNextBtn');
  if (!viewport || !track) return;

  var originalSlides = Array.from(track.children);
  if (originalSlides.length <= 1) return;

  // Duplicate slides once to enable smooth seamless loop
  originalSlides.forEach(function(slide) {
    var clone = slide.cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    track.appendChild(clone);
  });

  var allSlides = Array.from(track.children);
  var currentIndex = 0;
  var totalOriginal = originalSlides.length;
  var autoplayTimer = null;
  var isTransitioning = false;
  var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function getSlideDistance() {
    if (!allSlides[0]) return 0;
    var rect0 = allSlides[0].getBoundingClientRect();
    var rect1 = allSlides[1] ? allSlides[1].getBoundingClientRect() : null;
    if (rect1) {
      return rect1.left - rect0.left;
    }
    return rect0.width + 16;
  }

  function updateSlide(animate) {
    if (!animate) {
      track.style.transition = 'none';
    } else {
      track.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
    }
    var dist = getSlideDistance();
    track.style.transform = 'translateX(' + (-currentIndex * dist) + 'px)';
  }

  function nextSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    currentIndex++;
    updateSlide(true);
  }

  function prevSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    if (currentIndex <= 0) {
      currentIndex = totalOriginal;
      updateSlide(false);
      track.offsetHeight; // force reflow
    }
    currentIndex--;
    updateSlide(true);
  }

  track.addEventListener('transitionend', function() {
    isTransitioning = false;
    if (currentIndex >= totalOriginal) {
      currentIndex = 0;
      updateSlide(false);
    }
  });

  function startAutoplay() {
    if (prefersReduced) return;
    stopAutoplay();
    autoplayTimer = setInterval(nextSlide, 3500);
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  viewport.addEventListener('mouseenter', stopAutoplay);
  viewport.addEventListener('mouseleave', startAutoplay);
  viewport.addEventListener('focusin', stopAutoplay);
  viewport.addEventListener('focusout', startAutoplay);

  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      stopAutoplay();
      prevSlide();
      startAutoplay();
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      stopAutoplay();
      nextSlide();
      startAutoplay();
    });
  }

  // Touch Swipe Handling
  var touchStartX = 0;
  var touchStartY = 0;
  var touchDeltaX = 0;

  viewport.addEventListener('touchstart', function(e) {
    stopAutoplay();
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
    touchDeltaX = 0;
  }, { passive: true });

  viewport.addEventListener('touchmove', function(e) {
    touchDeltaX = e.touches[0].clientX - touchStartX;
  }, { passive: true });

  viewport.addEventListener('touchend', function() {
    if (Math.abs(touchDeltaX) > 40) {
      if (touchDeltaX < 0) {
        nextSlide();
      } else {
        prevSlide();
      }
    }
    startAutoplay();
  }, { passive: true });

  window.addEventListener('resize', function() {
    updateSlide(false);
  });

  startAutoplay();
}

// =========================================================================
// Global Site Settings (Phone Numbers, Visibility & WhatsApp Sync)
// =========================================================================
function initSiteSettings() {
  fetch('api/site-settings.php')
    .then(function(res) {
      return res.ok ? res.json() : null;
    })
    .then(function(data) {
      if (!data || !data.success) return;
      applySiteSettings(data);
    })
    .catch(function() {
      // Graceful fallback: initial HTML defaults remain active
    });
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

var _topbarTickerState = {
  data: null,
  pauseTimer: null,
  resizeTimer: null,
  touchBound: false
};

function getTickerTargetSpeed() {
  var w = window.innerWidth || document.documentElement.clientWidth || 1280;
  if (w <= 680) {
    return 24; // Mobile: 24 px/sec (target: 22–26 px/sec)
  }
  if (w <= 1024) {
    return 28; // Tablet: 28 px/sec (target: 26–30 px/sec)
  }
  return 30; // Desktop: 30 px/sec (target: 28–32 px/sec)
}

function updateTopbarTickerSpeed() {
  var topbar = document.querySelector('.topbar');
  if (!topbar) return;
  var track = topbar.querySelector('.topbar-ticker-track');
  if (!track) return;
  var group1 = track.querySelector('.topbar-ticker-group');
  if (!group1) return;

  var groupWidth = group1.getBoundingClientRect().width || group1.offsetWidth;
  if (!groupWidth || groupWidth <= 0) return;

  var speed = getTickerTargetSpeed();
  var duration = Math.max(30, groupWidth / speed).toFixed(2) + 's';
  topbar.style.setProperty('--ticker-duration', duration);
}

function bindTopbarTouchHandlers(topbar) {
  if (_topbarTickerState.touchBound || !topbar) return;
  _topbarTickerState.touchBound = true;

  topbar.addEventListener('touchstart', function() {
    clearTimeout(_topbarTickerState.pauseTimer);
    topbar.classList.add('is-paused');
  }, { passive: true });

  topbar.addEventListener('touchend', function() {
    clearTimeout(_topbarTickerState.pauseTimer);
    _topbarTickerState.pauseTimer = setTimeout(function() {
      topbar.classList.remove('is-paused');
    }, 1500);
  }, { passive: true });

  topbar.addEventListener('touchcancel', function() {
    clearTimeout(_topbarTickerState.pauseTimer);
    topbar.classList.remove('is-paused');
  }, { passive: true });

  window.addEventListener('resize', function() {
    clearTimeout(_topbarTickerState.resizeTimer);
    _topbarTickerState.resizeTimer = setTimeout(function() {
      if (_topbarTickerState.data) {
        updateTopbarTicker(_topbarTickerState.data);
      } else {
        updateTopbarTickerSpeed();
      }
    }, 200);
  }, { passive: true });
}

function initTopbarTicker() {
  var topbar = document.querySelector('.topbar');
  if (!topbar) return;
  bindTopbarTouchHandlers(topbar);
  updateTopbarTickerSpeed();
}

function updateTopbarTicker(data) {
  var topbar = document.querySelector('.topbar');
  if (!topbar) return;

  var track = topbar.querySelector('.topbar-ticker-track');
  if (!track) return;

  _topbarTickerState.data = data;

  var phoneUs = data.phone_us;
  var phonePk = data.phone_pk;
  var email = data.email;

  var showUs = phoneUs && phoneUs.visible && phoneUs.value;
  var showPk = phonePk && phonePk.visible && phonePk.value;
  var showEmail = email && email.visible && email.value;

  // If no contact items are visible, hide topbar
  if (!showUs && !showPk && !showEmail) {
    topbar.style.display = 'none';
    return;
  }
  topbar.style.display = '';

  var items = [];
  var sep = '<span class="ticker-sep" aria-hidden="true">&bull;</span>';

  if (showUs) {
    items.push(
      '<span class="ticker-item">' +
        '<span class="ticker-ico">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
            '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>' +
          '</svg>' +
        '</span>' +
        '<span class="ticker-lbl">USA:</span> ' +
        '<a href="tel:' + escapeHtml(phoneUs.tel) + '" class="ticker-link" data-contact-phone-us>' + escapeHtml(phoneUs.value) + '</a>' +
      '</span>'
    );
  }

  if (showPk) {
    items.push(
      '<span class="ticker-item">' +
        '<span class="ticker-flag">🇵🇰</span>' +
        '<span class="ticker-lbl">Pakistan:</span> ' +
        '<a href="tel:' + escapeHtml(phonePk.tel) + '" class="ticker-link" data-contact-phone-pk>' + escapeHtml(phonePk.value) + '</a>' +
      '</span>'
    );
  }

  if (showEmail) {
    items.push(
      '<span class="ticker-item">' +
        '<span class="ticker-ico">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' +
            '<rect x="2" y="4" width="20" height="16" rx="2"/>' +
            '<path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>' +
          '</svg>' +
        '</span>' +
        '<span class="ticker-lbl">Email:</span> ' +
        '<a href="mailto:' + escapeHtml(email.value) + '" class="ticker-link" data-contact-email>' + escapeHtml(email.value) + '</a>' +
      '</span>'
    );
  }

  var unitHtml = items.join(sep);

  // Measure 1 unit width to determine optimal repetitions
  track.innerHTML =
    '<div class="topbar-ticker-group">' + unitHtml + sep + '</div>' +
    '<div class="topbar-ticker-group" aria-hidden="true">' + unitHtml + sep + '</div>';

  var testGroup = track.querySelector('.topbar-ticker-group');
  var unitWidth = testGroup ? (testGroup.getBoundingClientRect().width || testGroup.offsetWidth) : 740;
  if (!unitWidth || unitWidth <= 0) unitWidth = 740;

  // Viewport width to cover without gaps
  var viewW = Math.max(window.innerWidth || 0, document.documentElement.clientWidth || 0, 360);
  // Calculate repetitions needed so Group 1 width >= viewport width (supports ultrawide 2560px & 3840px)
  var repsNeeded = Math.ceil(viewW / unitWidth);
  // Minimum 2 repetitions for smooth loop on mobile, up to whatever is required for 4K
  var reps = Math.max(2, repsNeeded);

  var groupContent = '';
  for (var i = 0; i < reps; i++) {
    groupContent += unitHtml + sep;
  }

  track.innerHTML =
    '<div class="topbar-ticker-group">' + groupContent + '</div>' +
    '<div class="topbar-ticker-group" aria-hidden="true">' + groupContent + '</div>';

  // Calculate and apply constant pixel speed duration
  updateTopbarTickerSpeed();

  // Attach touch & pause handlers once
  bindTopbarTouchHandlers(topbar);
}

function applySiteSettings(data) {
  if (!data) return;

  var phoneUs = data.phone_us;
  var phonePk = data.phone_pk;
  var email = data.email;
  var wa = data.whatsapp;

  // 1. USA Phone Number & Visibility
  if (phoneUs) {
    document.querySelectorAll('[data-contact-phone-us]').forEach(function(el) {
      el.textContent = phoneUs.value;
      if (el.tagName === 'A' && el.getAttribute('href') && el.getAttribute('href').startsWith('tel:')) {
        el.href = 'tel:' + phoneUs.tel;
      }
    });
    document.querySelectorAll('[data-contact-phone-us-wrap]').forEach(function(el) {
      el.style.display = phoneUs.visible ? '' : 'none';
    });
    document.querySelectorAll('[data-contact-phone-us-card]').forEach(function(el) {
      el.style.display = phoneUs.visible ? '' : 'none';
      if (el.tagName === 'A') {
        el.href = 'tel:' + phoneUs.tel;
      }
    });
  }

  // 2. Pakistan Phone Number & Visibility
  if (phonePk) {
    document.querySelectorAll('[data-contact-phone-pk]').forEach(function(el) {
      el.textContent = phonePk.value;
      if (el.tagName === 'A' && el.getAttribute('href') && el.getAttribute('href').startsWith('tel:')) {
        el.href = 'tel:' + phonePk.tel;
      }
    });
    document.querySelectorAll('[data-contact-phone-pk-wrap]').forEach(function(el) {
      el.style.display = phonePk.visible ? '' : 'none';
    });
    document.querySelectorAll('[data-contact-phone-pk-card]').forEach(function(el) {
      el.style.display = phonePk.visible ? '' : 'none';
      if (el.tagName === 'A' && !el.hasAttribute('data-whatsapp-link')) {
        el.href = 'tel:' + phonePk.tel;
      }
    });
  }

  // 3. Email Address & Visibility
  if (email) {
    document.querySelectorAll('[data-contact-email]').forEach(function(el) {
      el.textContent = email.value;
      if (el.tagName === 'A') {
        el.href = 'mailto:' + email.value;
      }
    });
    document.querySelectorAll('[data-contact-email-wrap]').forEach(function(el) {
      el.style.display = email.visible ? '' : 'none';
    });
    document.querySelectorAll('[data-contact-email-card]').forEach(function(el) {
      el.style.display = email.visible ? '' : 'none';
      if (el.tagName === 'A') {
        el.href = 'mailto:' + email.value;
      }
    });
  }

  // 4. Contact Containers & Footer Visibility
  var showUs = phoneUs ? phoneUs.visible : true;
  var showPk = phonePk ? phonePk.visible : true;
  var showEmail = email ? email.visible : true;

  document.querySelectorAll('[data-footer-contacts]').forEach(function(el) {
    el.style.display = (showUs || showPk || showEmail) ? '' : 'none';
  });

  document.querySelectorAll('[data-contact-methods-card]').forEach(function(el) {
    el.style.display = (showUs || showPk || showEmail) ? '' : 'none';
  });

  // 5. Update Moving Contact Topbar Ticker
  updateTopbarTicker(data);

  // 6. WhatsApp Configuration & Floating Widget
  if (wa) {
    initFloatingWhatsApp(data);

    var waUrl = 'https://wa.me/' + wa.digits + '?text=' + encodeURIComponent(wa.message);
    document.querySelectorAll('[data-whatsapp-link]').forEach(function(el) {
      var currentHref = el.getAttribute('href') || '';
      if (currentHref.indexOf('text=') !== -1) {
        var textParam = currentHref.substring(currentHref.indexOf('text='));
        el.href = 'https://wa.me/' + wa.digits + '?' + textParam;
      } else {
        el.href = waUrl;
      }
    });

    document.querySelectorAll('[data-whatsapp-number]').forEach(function(el) {
      el.textContent = wa.number;
    });
  }
}

// =========================================================================
// Dynamic Teachers Carousel Component (Shared across Home & About)
// =========================================================================
var _cachedTeachersPromise = null;

function fetchActiveTeachersOnce() {
  if (!_cachedTeachersPromise) {
    _cachedTeachersPromise = fetch('api/teachers.php')
      .then(function(res) {
        return res.ok ? res.json() : null;
      })
      .catch(function() {
        return null;
      });
  }
  return _cachedTeachersPromise;
}

function escapeTeacherHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function renderTeacherSlideHtml(t) {
  var category = t.category || 'Quran Learning';
  var name = t.name || '';
  var role = t.role || '';
  var desc = t.short_description || '';
  var image = t.image;

  var catColor = 'text-brand-700 cat-quran';
  var avatarBg = 'from-brand-50 to-brand-100/50 avatar-quran';
  var iconBg = 'bg-brand-900 text-gold-400 icon-quran';

  var catLower = category.toLowerCase();
  if (catLower.indexOf('sister') !== -1 || catLower.indexOf('kid') !== -1) {
    catColor = 'text-amber-700 cat-sister';
    avatarBg = 'from-amber-50 to-amber-100/40 avatar-sister';
    iconBg = 'bg-gold-400 text-brand-950 icon-sister';
  } else if (catLower.indexOf('academic') !== -1 || catLower.indexOf('science') !== -1 || catLower.indexOf('math') !== -1) {
    catColor = 'text-sky-800 cat-academic';
    avatarBg = 'from-sky-50 to-blue-100/40 avatar-academic';
    iconBg = 'bg-slate-900 text-sky-400 icon-academic';
  }

  var avatarHtml = '';
  if (image) {
    avatarHtml = '<div class="teacher-card-media"><img src="' + escapeTeacherHtml(image) + '" alt="' + escapeTeacherHtml(name) + '" class="w-full h-44 object-cover teacher-card-img" loading="lazy"></div>';
  } else {
    avatarHtml =
      '<div class="teacher-card-media teacher-card-avatar h-44 bg-gradient-to-b ' + avatarBg + ' flex flex-col items-center justify-center relative p-4">' +
        '<div class="teacher-card-avatar-icon w-20 h-20 rounded-full ' + iconBg + ' flex items-center justify-center shadow-md">' +
          '<svg viewBox="0 0 24 24" class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5">' +
            '<circle cx="12" cy="8" r="4"/>' +
            '<path d="M4 21c.8-5 3.5-7 8-7s7.2 2 8 7"/>' +
          '</svg>' +
        '</div>' +
      '</div>';
  }

  var specs = (t.specialties || '').split(',');
  var specsHtml = '';
  specs.forEach(function(s) {
    var trimmed = s.trim();
    if (trimmed) {
      specsHtml += '<span class="teacher-card-tag inline-block px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-emerald-50 text-brand-900 border border-emerald-100">' + escapeTeacherHtml(trimmed) + '</span>';
    }
  });

  return '<div class="teacher-slide">' +
    '<div class="teacher-card rounded-2xl border border-slate-200 overflow-hidden shadow-card bg-white flex flex-col transition-all hover:shadow-lg hover:-translate-y-1 h-full">' +
      avatarHtml +
      '<div class="teacher-card-body p-5 flex-1 flex flex-col">' +
        '<div class="teacher-card-category text-[11px] font-extrabold uppercase tracking-wider ' + catColor + '">' + escapeTeacherHtml(category) + '</div>' +
        '<h3 class="teacher-card-name mt-1 text-base font-extrabold text-slate-950">' + escapeTeacherHtml(name) + '</h3>' +
        '<p class="teacher-card-desc mt-2 text-xs text-slate-600 leading-5">' + escapeTeacherHtml(desc) + '</p>' +
        '<div class="teacher-card-tags mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-1.5 mt-auto">' +
          specsHtml +
        '</div>' +
      '</div>' +
    '</div>' +
  '</div>';
}

function renderTeachersEmptyStateHtml() {
  return '<div class="teachers-empty-state max-w-2xl mx-auto text-center p-8 sm:p-10 rounded-2xl border border-slate-200 bg-slate-50/70 shadow-sm w-full">' +
    '<div class="teachers-empty-icon w-14 h-14 mx-auto rounded-full bg-brand-100 text-brand-800 flex items-center justify-center mb-4">' +
      '<svg viewBox="0 0 24 24" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8">' +
        '<path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>' +
      '</svg>' +
    '</div>' +
    '<h3 class="teachers-empty-title text-lg font-extrabold text-slate-900">Tutor profiles will be available soon.</h3>' +
    '<p class="teachers-empty-desc mt-2 text-xs sm:text-sm text-slate-600 leading-6">We match students with dedicated instructors based on learning goals, age, subject, and time zone. Contact our coordinator to arrange your personalized tutor matching and free trial classes.</p>' +
    '<div class="teachers-empty-actions mt-5 flex flex-wrap justify-center gap-3">' +
      '<a href="contact.html#trial" class="teachers-empty-btn-primary inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-900 hover:bg-brand-950 text-white text-xs font-extrabold transition-all">Start Free Trial Consultation</a>' +
      '<a href="https://wa.me/923445455154?text=Hello%2C%20I%20would%20like%20to%20know%20about%20available%20tutors." target="_blank" rel="noopener noreferrer" class="teachers-empty-btn-whatsapp inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-brand-900 border border-emerald-200 text-xs font-bold transition-all" data-whatsapp-link>WhatsApp Coordinator</a>' +
    '</div>' +
  '</div>';
}

function setupTeacherSlider(mount) {
  var viewport = mount.querySelector('.teachers-slider-viewport');
  var track = mount.querySelector('.teachers-slider-track');
  var controls = mount.querySelector('.teachers-slider-controls');
  var prevBtn = mount.querySelector('.teachers-slider-btn.prev');
  var nextBtn = mount.querySelector('.teachers-slider-btn.next');
  if (!viewport || !track) return;

  var originalSlides = Array.from(track.querySelectorAll('.teacher-slide'));
  var totalOriginal = originalSlides.length;

  // If 0 or 1 slide, no slider controls or loops
  if (totalOriginal <= 1) {
    if (controls) {
      controls.style.display = 'none';
      controls.classList.add('hidden');
    }
    if (totalOriginal === 1) {
      track.style.justifyContent = 'center';
    }
    return;
  }

  // Duplicate slides once to enable smooth seamless loop when enough cards exist
  originalSlides.forEach(function(slide) {
    var clone = slide.cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    track.appendChild(clone);
  });

  var allSlides = Array.from(track.children);
  var currentIndex = 0;
  var autoplayTimer = null;
  var isTransitioning = false;
  var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function getVisibleCount() {
    var w = window.innerWidth;
    if (w > 1200) return 4;
    if (w > 860) return 3;
    if (w > 580) return 2;
    return 1;
  }

  function getSlideDistance() {
    if (!allSlides[0]) return 0;
    var rect0 = allSlides[0].getBoundingClientRect();
    var rect1 = allSlides[1] ? allSlides[1].getBoundingClientRect() : null;
    if (rect1) {
      return rect1.left - rect0.left;
    }
    return rect0.width + 20;
  }

  function updateSlide(animate) {
    if (!animate) {
      track.style.transition = 'none';
    } else {
      track.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
    }
    var dist = getSlideDistance();
    track.style.transform = 'translateX(' + (-currentIndex * dist) + 'px)';
  }

  function nextSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    currentIndex++;
    updateSlide(true);
  }

  function prevSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    if (currentIndex <= 0) {
      currentIndex = totalOriginal;
      updateSlide(false);
      track.offsetHeight; // force reflow
    }
    currentIndex--;
    updateSlide(true);
  }

  track.addEventListener('transitionend', function() {
    isTransitioning = false;
    if (currentIndex >= totalOriginal) {
      currentIndex = 0;
      updateSlide(false);
    }
  });

  function startAutoplay() {
    if (prefersReduced || totalOriginal <= getVisibleCount()) return;
    stopAutoplay();
    autoplayTimer = setInterval(nextSlide, 4500); // 4.5s
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function checkSliderState() {
    var visible = getVisibleCount();
    var canSlide = totalOriginal > visible;
    if (controls) {
      if (canSlide) {
        controls.style.display = 'flex';
        controls.classList.remove('hidden');
      } else {
        controls.style.display = 'none';
        controls.classList.add('hidden');
      }
    }
    if (canSlide) {
      startAutoplay();
      updateSlide(false);
    } else {
      stopAutoplay();
      currentIndex = 0;
      updateSlide(false);
    }
  }

  viewport.addEventListener('mouseenter', stopAutoplay);
  viewport.addEventListener('mouseleave', startAutoplay);
  viewport.addEventListener('focusin', stopAutoplay);
  viewport.addEventListener('focusout', startAutoplay);

  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      stopAutoplay();
      prevSlide();
      startAutoplay();
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      stopAutoplay();
      nextSlide();
      startAutoplay();
    });
  }

  // Touch Swipe Handling
  var touchStartX = 0;
  var touchStartY = 0;
  var touchDeltaX = 0;

  viewport.addEventListener('touchstart', function(e) {
    stopAutoplay();
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
    touchDeltaX = 0;
  }, { passive: true });

  viewport.addEventListener('touchmove', function(e) {
    touchDeltaX = e.touches[0].clientX - touchStartX;
  }, { passive: true });

  viewport.addEventListener('touchend', function() {
    if (Math.abs(touchDeltaX) > 40) {
      if (touchDeltaX < 0) {
        nextSlide();
      } else {
        prevSlide();
      }
    }
    startAutoplay();
  }, { passive: true });

  window.addEventListener('resize', checkSliderState);

  checkSliderState();
}

function initTeacherCarousels() {
  var mounts = document.querySelectorAll('[data-teacher-carousel]');
  if (!mounts.length) return;

  mounts.forEach(function(mount) {
    if (mount.getAttribute('data-carousel-ready') === 'true') return;
    var track = mount.querySelector('.teachers-slider-track');
    if (!track) return;

    var existingSlides = track.querySelectorAll('.teacher-slide');
    // If slides are already server-rendered (e.g. on index.php), use them immediately
    if (existingSlides.length > 0) {
      mount.setAttribute('data-carousel-ready', 'true');
      setupTeacherSlider(mount);
      return;
    }

    // Otherwise, fetch active profiles via API
    fetchActiveTeachersOnce().then(function(data) {
      if (!data || !data.success || !Array.isArray(data.teachers)) {
        return;
      }

      if (data.teachers.length === 0) {
        track.innerHTML = renderTeachersEmptyStateHtml();
        var controls = mount.querySelector('.teachers-slider-controls');
        if (controls) {
          controls.style.display = 'none';
          controls.classList.add('hidden');
        }
        return;
      }

      var cardsHtml = '';
      data.teachers.forEach(function(t) {
        cardsHtml += renderTeacherSlideHtml(t);
      });

      track.innerHTML = cardsHtml;
      mount.setAttribute('data-carousel-ready', 'true');
      setupTeacherSlider(mount);
    });
  });
}

// =========================================================================
// Dynamic Testimonials Carousel Component
// =========================================================================
var _cachedTestimonialsPromise = null;

function fetchActiveTestimonialsOnce() {
  if (!_cachedTestimonialsPromise) {
    _cachedTestimonialsPromise = fetch('api/testimonials.php')
      .then(function(res) {
        return res.ok ? res.json() : null;
      })
      .catch(function() {
        return null;
      });
  }
  return _cachedTestimonialsPromise;
}

function escapeTestimonialHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function renderTestimonialSlideHtml(t) {
  var name = t.reviewer_name || '';
  var text = t.review_text || '';
  var loc = t.location || '';
  var cat = t.category || '';
  var image = t.image;

  var initial = name.trim().charAt(0).toUpperCase() || 'Q';

  var avatarHtml = '';
  if (image) {
    avatarHtml = '<img src="' + escapeTestimonialHtml(image) + '" alt="' + escapeTestimonialHtml(name) + '" class="testimonial-avatar" loading="lazy">';
  } else {
    avatarHtml = '<div class="testimonial-avatar-initial">' + escapeTestimonialHtml(initial) + '</div>';
  }

  var catPillHtml = '';
  if (cat) {
    catPillHtml = '<span class="testimonial-category-pill">' + escapeTestimonialHtml(cat) + '</span>';
  }

  var locHtml = '';
  if (loc) {
    locHtml = '<div class="testimonial-author-location">' + escapeTestimonialHtml(loc) + '</div>';
  }

  return '<div class="testimonial-slide">' +
    '<div class="testimonial-card">' +
      '<svg class="testimonial-quote-icon" viewBox="0 0 24 24" fill="currentColor">' +
        '<path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>' +
      '</svg>' +
      '<p class="testimonial-text">“' + escapeTestimonialHtml(text) + '”</p>' +
      '<div class="testimonial-footer">' +
        '<div class="testimonial-author">' +
          avatarHtml +
          '<div class="min-w-0">' +
            '<h4 class="testimonial-author-name">' + escapeTestimonialHtml(name) + '</h4>' +
            locHtml +
          '</div>' +
        '</div>' +
        catPillHtml +
      '</div>' +
    '</div>' +
  '</div>';
}

function setupTestimonialSlider(mount) {
  var viewport = mount.querySelector('.testimonials-slider-viewport');
  var track = mount.querySelector('.testimonials-slider-track');
  var controls = mount.querySelector('.testimonials-slider-controls');
  var prevBtn = mount.querySelector('.testimonials-slider-btn.prev');
  var nextBtn = mount.querySelector('.testimonials-slider-btn.next');
  if (!viewport || !track) return;

  var originalSlides = Array.from(track.querySelectorAll('.testimonial-slide'));
  var totalOriginal = originalSlides.length;

  if (totalOriginal === 0) {
    if (controls) controls.style.display = 'none';
    var sec = mount.closest('section');
    if (sec) sec.style.display = 'none';
    return;
  }

  // If 1 slide, center and no controls
  if (totalOriginal === 1) {
    if (controls) {
      controls.style.display = 'none';
      controls.classList.add('hidden');
    }
    track.style.justifyContent = 'center';
    return;
  }

  function getVisibleCount() {
    var w = window.innerWidth;
    if (w > 960) return 3;
    if (w > 600) return 2;
    return 1;
  }

  // Duplicate slides once to enable smooth seamless loop if we have enough items
  if (totalOriginal >= 2) {
    originalSlides.forEach(function(slide) {
      var clone = slide.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    });
  }

  var allSlides = Array.from(track.children);
  var currentIndex = 0;
  var autoplayTimer = null;
  var isTransitioning = false;
  var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function getSlideDistance() {
    if (!allSlides[0]) return 0;
    var rect0 = allSlides[0].getBoundingClientRect();
    var rect1 = allSlides[1] ? allSlides[1].getBoundingClientRect() : null;
    if (rect1) {
      return rect1.left - rect0.left;
    }
    return rect0.width + 20;
  }

  function updateSlide(animate) {
    if (!animate) {
      track.style.transition = 'none';
    } else {
      track.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
    }
    var dist = getSlideDistance();
    track.style.transform = 'translateX(' + (-currentIndex * dist) + 'px)';
  }

  function nextSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    currentIndex++;
    updateSlide(true);
  }

  function prevSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    if (currentIndex <= 0) {
      currentIndex = totalOriginal;
      updateSlide(false);
      track.offsetHeight; // force reflow
    }
    currentIndex--;
    updateSlide(true);
  }

  track.addEventListener('transitionend', function() {
    isTransitioning = false;
    if (currentIndex >= totalOriginal) {
      currentIndex = 0;
      updateSlide(false);
    }
  });

  function startAutoplay() {
    if (prefersReduced || totalOriginal <= getVisibleCount()) return;
    stopAutoplay();
    autoplayTimer = setInterval(nextSlide, 5500); // 5.5s
  }

  function stopAutoplay() {
    if (autoplayTimer) {
      clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function checkSliderState() {
    var visible = getVisibleCount();
    var canSlide = totalOriginal > visible;
    if (controls) {
      if (canSlide) {
        controls.style.display = 'flex';
        controls.classList.remove('hidden');
      } else {
        controls.style.display = 'none';
        controls.classList.add('hidden');
      }
    }
    if (canSlide) {
      startAutoplay();
      updateSlide(false);
    } else {
      stopAutoplay();
      currentIndex = 0;
      updateSlide(false);
    }
  }

  viewport.addEventListener('mouseenter', stopAutoplay);
  viewport.addEventListener('mouseleave', startAutoplay);
  viewport.addEventListener('focusin', stopAutoplay);
  viewport.addEventListener('focusout', startAutoplay);

  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      stopAutoplay();
      prevSlide();
      startAutoplay();
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      stopAutoplay();
      nextSlide();
      startAutoplay();
    });
  }

  // Touch Swipe Handling
  var touchStartX = 0;
  var touchStartY = 0;
  var touchDeltaX = 0;

  viewport.addEventListener('touchstart', function(e) {
    stopAutoplay();
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
    touchDeltaX = 0;
  }, { passive: true });

  viewport.addEventListener('touchmove', function(e) {
    touchDeltaX = e.touches[0].clientX - touchStartX;
  }, { passive: true });

  viewport.addEventListener('touchend', function() {
    if (Math.abs(touchDeltaX) > 40) {
      if (touchDeltaX < 0) {
        nextSlide();
      } else {
        prevSlide();
      }
    }
    startAutoplay();
  }, { passive: true });

  window.addEventListener('resize', checkSliderState);

  checkSliderState();
}

function initTestimonialCarousels() {
  var mounts = document.querySelectorAll('[data-testimonial-carousel]');
  if (!mounts.length) return;

  mounts.forEach(function(mount) {
    if (mount.getAttribute('data-carousel-ready') === 'true') return;
    var track = mount.querySelector('.testimonials-slider-track');
    var sec = mount.closest('section');
    if (!track) return;

    var existingSlides = track.querySelectorAll('.testimonial-slide');
    // If slides are already server-rendered (e.g. on index.php), use them immediately
    if (existingSlides.length > 0) {
      mount.setAttribute('data-carousel-ready', 'true');
      if (sec) sec.style.display = '';
      setupTestimonialSlider(mount);
      return;
    }

    // Otherwise, fetch active profiles via API
    fetchActiveTestimonialsOnce().then(function(data) {
      if (!data || !data.success || !Array.isArray(data.testimonials) || data.testimonials.length === 0) {
        if (sec) sec.style.display = 'none';
        return;
      }

      var cardsHtml = '';
      data.testimonials.forEach(function(t) {
        cardsHtml += renderTestimonialSlideHtml(t);
      });

      track.innerHTML = cardsHtml;
      mount.setAttribute('data-carousel-ready', 'true');
      if (sec) sec.style.display = '';
      setupTestimonialSlider(mount);
    });
  });
}

// Auto-run on DOM ready
function initAllModules() {
  try { initTopbarTicker(); } catch (e) { console.error('Topbar ticker init error:', e); }
  try { initFloatingWhatsApp(); } catch (e) { console.error('WhatsApp init error:', e); }
  try { initSiteSettings(); } catch (e) { console.error('Site settings init error:', e); }
  try { initCurriculaSlider(); } catch (e) { console.error('Curricula slider init error:', e); }
  try { initTeacherCarousels(); } catch (e) { console.error('Teachers carousel init error:', e); }
  try { initTestimonialCarousels(); } catch (e) { console.error('Testimonials carousel init error:', e); }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAllModules);
} else {
  initAllModules();
}




