import './bootstrap';

/**
 * PadiCare Mobile Interactions
 * Desktop untouched — only enhances mobile/tablet UX.
 */
document.addEventListener('DOMContentLoaded', () => {

  /* ───────────────────────────────────────────────────────────
     Sidebar Toggle (Mobile only)
     ─────────────────────────────────────────────────────────── */
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const toggleBtn = document.getElementById('sidebarToggle');

  if (sidebar && backdrop && toggleBtn) {
    const open = () => {
      sidebar.classList.add('show');
      backdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    };
    const close = () => {
      sidebar.classList.remove('show');
      backdrop.classList.remove('active');
      document.body.style.overflow = '';
    };

    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      sidebar.classList.contains('show') ? close() : open();
    });

    backdrop.addEventListener('click', close);

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && sidebar.classList.contains('show')) close();
    });

    // Close on route change (popstate)
    window.addEventListener('popstate', close);
  }

  /* ───────────────────────────────────────────────────────────
     Bottom Nav: highlight active item by current path
     (Backup for server-rendered active class)
     ─────────────────────────────────────────────────────────── */
  const bottomNav = document.getElementById('bottomNav');
  if (bottomNav) {
    const currentPath = window.location.pathname;
    const items = bottomNav.querySelectorAll('.bottom-nav-item');

    items.forEach((item) => {
      const href = item.getAttribute('href');
      if (href && href !== '#' && currentPath.startsWith(href)) {
        items.forEach((i) => i.classList.remove('active'));
        item.classList.add('active');
      }
    });
  }

  /* ───────────────────────────────────────────────────────────
     Smooth scroll behavior for iOS
     ─────────────────────────────────────────────────────────── */
  let touchStartY = 0;
  document.addEventListener('touchstart', (e) => {
    touchStartY = e.touches[0].clientY;
  }, { passive: true });

  /* ───────────────────────────────────────────────────────────
     Prevent ghost taps on iOS (300ms delay is already solved
     by viewport, but this helps with tap highlight)
     ─────────────────────────────────────────────────────────── */
  if ('ontouchstart' in window) {
    document.querySelectorAll('a, button, .btn, .bottom-nav-item')
      .forEach((el) => {
        el.addEventListener('touchstart', function () {
          // minimal touch feedback — noop, browser handles it
        }, { passive: true });
      });
  }

  /* ───────────────────────────────────────────────────────────
     Hide bottom nav on keyboard open (mobile)
     ─────────────────────────────────────────────────────────── */
  if (bottomNav && 'visualViewport' in window) {
    let wasKeyboardOpen = false;
    window.visualViewport.addEventListener('resize', () => {
      const keyboardVisible = window.visualViewport.height < window.innerHeight * 0.8;
      if (keyboardVisible && !wasKeyboardOpen) {
        bottomNav.style.transform = 'translateY(100%)';
        bottomNav.style.transition = 'transform 0.2s';
        wasKeyboardOpen = true;
      } else if (!keyboardVisible && wasKeyboardOpen) {
        bottomNav.style.transform = '';
        wasKeyboardOpen = false;
      }
    });
  }
});
