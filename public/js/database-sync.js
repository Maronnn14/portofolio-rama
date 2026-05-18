/* ============================================
   DATABASE SYNC - MySQL bridge for Laravel API
   Keeps existing localStorage workflow intact
   ============================================ */

(function () {
  if (typeof window === 'undefined' || typeof localStorage === 'undefined') return;

  const apiBase = window.PORTFOLIO_API_BASE;
  if (!apiBase) return;

  const sync = async (url, payload) => {
    try {
      await fetch(`${apiBase}${url}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
      });
    } catch (e) {
      console.warn('Database sync failed', e);
    }
  };

  window.syncPortfolioDataToDatabase = (data) => sync('/portfolio-data', { data });
  window.syncPortfolioMessagesToDatabase = (messages) => sync('/portfolio-messages', { messages });

  if (!localStorage.getItem('portfolio_messages')
      && Array.isArray(window.PORTFOLIO_DB_MESSAGES)
      && window.PORTFOLIO_DB_MESSAGES.length) {
    localStorage.setItem('portfolio_messages', JSON.stringify(window.PORTFOLIO_DB_MESSAGES));
  }

  const originalSetItem = localStorage.setItem.bind(localStorage);

  localStorage.setItem = function (key, value) {
    originalSetItem(key, value);

    try {
      if (key === 'portfolio_data_store') {
        window.syncPortfolioDataToDatabase(JSON.parse(value));
      }

      if (key === 'portfolio_messages') {
        window.syncPortfolioMessagesToDatabase(JSON.parse(value));
      }
    } catch {
      // Keep the original localStorage behavior even if sync payload is invalid.
    }
  };
})();
