/* ============================================
   ADMIN CONFIG — Credentials & Settings
   ============================================ */

const ADMIN_CONFIG = {
  /* Default credentials — SHA-256 hashed */
  defaultUsername: 'admin',
  /* SHA-256 of 'admin123' */
  defaultPasswordHash: '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9',

  /* Session */
  sessionKey: 'portfolio_admin_session',
  sessionTimeout: 2 * 60 * 60 * 1000, // 2 hours in ms
  lastActivityKey: 'portfolio_admin_last_activity',
  lastLoginKey: 'portfolio_admin_last_login',

  /* Rate Limiting */
  maxLoginAttempts: 5,
  lockoutDuration: 30 * 1000, // 30 seconds
  loginAttemptsKey: 'portfolio_admin_login_attempts',
  lockoutUntilKey: 'portfolio_admin_lockout_until',

  /* Activity Log */
  activityLogKey: 'portfolio_admin_activity_log',
  maxActivityLogEntries: 50,

  /* Data Storage Keys */
  dataStoreKey: 'portfolio_data_store',
  messageBoardSettingsKey: 'portfolio_mb_settings',
  appearanceKey: 'portfolio_appearance',
  siteSettingsKey: 'portfolio_site_settings',
  adminAccountKey: 'portfolio_admin_account',
};

/* ---- SHA-256 Hash Utility ---- */
async function sha256(message) {
  const msgBuffer = new TextEncoder().encode(message);
  const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

/* Make available globally */
if (typeof window !== 'undefined') {
  window.ADMIN_CONFIG = ADMIN_CONFIG;
  window.sha256 = sha256;
}
