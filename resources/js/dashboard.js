/**
 * Dashboard Layout Module — Professional Version
 * ─────────────────────────────────────────────────
 * Minimal, high-performance state management for the dashboard.
 * Instead of manipulating individual elements, we toggle state classes
 * on the <body>. The CSS handles all responsive layouts internally.
 */

const BREAKPOINTS = Object.freeze({
  tablet: 768,
  desktop: 1024,
});

/* ────────── Helpers ────────── */
function getViewport() {
  const w = window.innerWidth;
  if (w < BREAKPOINTS.tablet) return 'mobile';
  if (w < BREAKPOINTS.desktop) return 'tablet';
  return 'desktop';
}

/* ────────── Sidebar Toggle ────────── */
function toggleSidebar() {
  const vp = getViewport();
  const b = document.body;

  if (vp === 'mobile') {
    const opening = b.classList.toggle('is-mobile-open');
    b.classList.toggle('overflow-hidden', opening);
  } else if (vp === 'tablet') {
    b.classList.toggle('is-tablet-expanded');
  } else {
    b.classList.toggle('is-sidebar-collapsed');
  }
}

function updateThemeIcon(isDark) {
  const icon = document.getElementById('theme-toggle-icon');
  if (icon) {
    icon.className = isDark ? 'fa-solid fa-sun text-lg' : 'fa-solid fa-moon text-lg';
  }
}

/* ────────── Dark Mode ────────── */
function initDarkMode() {
  const stored = localStorage.getItem('theme');
  const isDark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
  document.documentElement.classList.toggle('dark', isDark);
  updateThemeIcon(isDark);
}

function toggleDarkMode() {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  updateThemeIcon(isDark);
}

/* ────────── Resize handler ────────── */
function onResize() {
  const b = document.body;
  const vp = getViewport();

  // Reset states when resizing up
  if (vp !== 'mobile') {
    if (b.classList.contains('is-mobile-open')) {
      b.classList.remove('is-mobile-open', 'overflow-hidden');
    }
  }
}

/* ────────── Init ────────── */
document.addEventListener('DOMContentLoaded', () => {
  initDarkMode();
  window.addEventListener('resize', onResize);
});

/* ────────── Notifications Toggle ────────── */
function toggleNotifications() {
  const b = document.body;
  const drawer = document.getElementById('notifications-drawer');
  const overlay = document.getElementById('notifications-overlay');

  const isOpen = b.classList.toggle('is-notifications-open');

  if (drawer) {
    // In LTR, use -translate-x-full/0. In RTL, use translate-x-full/0.
    // Tailwind v4 handles logical properties well, but drawer is fixed to inset-inline-end.
    // So translate-x-full will literally push it out of the screen on the end side.
    drawer.classList.toggle('translate-x-full', !isOpen);
    drawer.classList.toggle('translate-x-0', isOpen);
  }

  if (overlay) {
    overlay.classList.toggle('hidden', !isOpen);
  }

  if (getViewport() === 'mobile') {
    b.classList.toggle('overflow-hidden', isOpen);
  }
}

// Global exposure
window.toggleSidebar = toggleSidebar;
window.toggleDarkMode = toggleDarkMode;
window.toggleNotifications = toggleNotifications;
