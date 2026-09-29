document.addEventListener('DOMContentLoaded', function () {
  const baseUrl = (window.CINE_DIARY_BASE_URL || '').replace(/\/$/, '');
  function siteUrl(path) {
    return baseUrl + path;
  }

  // simple lazy background loading for posters
  document.querySelectorAll('.poster').forEach(function (el) {
    const bg = el.style.backgroundImage;
    if (!bg) return;
    // ensure images render smoothly
    const url = bg.replace(/url\(['"]?|['"]?\)/g, '');
    const img = new Image();
    img.src = url;
    img.onload = function () { el.style.backgroundImage = 'url("' + url + '")'; };
  });

  // render stars based on data-rating attribute
  function renderStars(container, rating) {
    container.innerHTML = '';
    const full = Math.floor(rating);
    const half = (rating - full) >= 0.5;
    for (let i = 1; i <= 5; i++) {
      const span = document.createElement('span');
      if (i <= full) {
        span.className = 'star filled';
      } else if (i === full + 1 && half) {
        span.className = 'star half';
      } else {
        span.className = 'star';
      }
      span.textContent = '★';
      container.appendChild(span);
    }
  }

  document.querySelectorAll('.stars').forEach(function (el) {
    const r = parseFloat(el.dataset.rating) || 0;
    renderStars(el, r);
  });

  // Modal interactions
  const modal = document.getElementById('movieDetailModal');
  const modalPoster = modal && modal.querySelector('.movie-modal-poster');
  const modalTitle = modal && modal.querySelector('.movie-modal-title');
  const modalMeta = modal && modal.querySelector('.movie-modal-meta');
  const modalDirectorName = modal && modal.querySelector('.movie-modal-director-name');
  const modalTicketPrice = modal && modal.querySelector('.movie-modal-ticket-price');
  const modalSynopsis = modal && modal.querySelector('.movie-modal-synopsis');
  const modalRatingNum = modal && modal.querySelector('.movie-modal-rating-num');
  const modalStars = modal && modal.querySelector('.movie-modal-rating .stars');
  const modalDetailLink = modal && modal.querySelector('.movie-detail-link');
  const modalTrailerLink = modal && modal.querySelector('.movie-modal-trailer-link');
  const modalCommunityList = modal && modal.querySelector('.movie-modal-community-list');
  const modalCommunityCount = modal && modal.querySelector('.movie-modal-community-count');

  function escapeText(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function formatRelativeTime(value) {
    if (!value) return '';
    return String(value);
  }

  function renderReviewStars(rating) {
    const stars = document.createElement('div');
    stars.className = 'stars';
    renderStars(stars, parseFloat(rating) || 0);
    return stars;
  }

  function normalizeAvatar(url) {
    if (!url) return '';
    if (/^https?:\/\//i.test(url)) return url;
    // Ensure avatar paths include the app base path so they resolve correctly
    if (url.startsWith('/public/')) return baseUrl + url;
    if (url.startsWith('/uploads/')) return baseUrl + '/public' + url;
    if (url.startsWith('/')) return baseUrl + '/public' + url;
    // fallback: treat as filename inside uploads/avatars
    return baseUrl + '/public/uploads/avatars/' + url.replace(/^\/+/, '');
  }

  function normalizeUrl(url) {
    if (!url) return '';
    if (/^https?:\/\//i.test(url)) return url;
    if (url.startsWith('/')) return baseUrl + url;
    return url;
  }

  function formatVnd(value) {
    const amount = Number(value) || 0;
    return amount.toLocaleString('vi-VN') + ' đ';
  }

  function avatarInitial(name) {
    const value = String(name || '').trim();
    return value ? value.charAt(0).toUpperCase() : '?';
  }

  function renderCommunityReviews(reviews) {
    if (!modalCommunityList) return;
    modalCommunityList.innerHTML = '';
    const items = Array.isArray(reviews) ? reviews : [];
    if (modalCommunityCount) {
      modalCommunityCount.textContent = items.length ? items.length + ' bài đánh giá' : 'Chưa có đánh giá';
    }

    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'movie-modal-review';
      empty.innerHTML = '<div class="movie-modal-review-main"><div class="movie-modal-review-title">Chưa có đánh giá</div><div class="movie-modal-review-excerpt">Hãy là người đầu tiên chia sẻ cảm nhận của bạn.</div></div>';
      modalCommunityList.appendChild(empty);
      return;
    }

    items.forEach(function (review) {
      const card = document.createElement('div');
      card.className = 'movie-modal-review';
      card.setAttribute('role', 'button');
      card.setAttribute('tabindex', '0');
      card.addEventListener('click', function () {
        window.location.href = siteUrl('/reviews/' + (review.id || ''));
      });
      card.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          window.location.href = siteUrl('/reviews/' + (review.id || ''));
        }
      });

      const avatarUrl = normalizeAvatar(review.avatar_url || '');
      const avatar = avatarUrl
        ? '<img class="movie-modal-review-avatar" src="' + avatarUrl + '" alt="Avatar">'
        : '<div class="movie-modal-review-avatar movie-modal-review-avatar--empty">' + escapeText(avatarInitial(review.display_name)) + '</div>';

      const title = review.title ? escapeText(review.title) : 'Không có tiêu đề';
      const body = escapeText(review.body || review.excerpt || '');
      const excerpt = body.length > 140 ? body.slice(0, 140).trim() + '...' : body;
      const userUrl = siteUrl('/profile/' + (review.user_id || ''));
      const ratingValue = parseFloat(review.rating) || 0;
      const ratingStarsId = 'review-stars-' + String(review.id || Math.random()).replace(/[^a-zA-Z0-9_-]/g, '');

      card.innerHTML = [
        avatar,
        '<div class="movie-modal-review-main">',
          '<div class="movie-modal-review-top">',
            '<div>',
              '<a class="movie-modal-review-user" href="' + userUrl + '" data-user-link="true">' + escapeText(review.display_name || 'User') + '</a>',
              '<div class="movie-modal-review-title">' + title + '</div>',
            '</div>',
            '<div class="movie-modal-review-rating">',
              '<div class="stars movie-modal-review-stars" id="' + ratingStarsId + '" data-rating="' + escapeText(ratingValue.toFixed(1)) + '"></div>',
              '<div class="movie-modal-review-rating-num">' + escapeText(ratingValue.toFixed(1)) + '/5</div>',
            '</div>',
          '</div>',
          '<div class="movie-modal-review-excerpt">' + excerpt + '</div>',
          '<div class="movie-modal-review-meta">' + formatRelativeTime(review.created_at) + '</div>',
        '</div>'
      ].join('');

      card.querySelector('[data-user-link="true"]').addEventListener('click', function (event) {
        event.stopPropagation();
      });

      modalCommunityList.appendChild(card);
      const starsEl = card.querySelector('#' + ratingStarsId);
      if (starsEl) {
        renderStars(starsEl, ratingValue);
      }
    });
  }

  document.querySelectorAll('.imdb-card-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const data = btn.dataset;
      if (!modal) return;
      modalPoster.style.backgroundImage = 'url("' + data.poster + '")';
      modalTitle.textContent = data.title || '';
      modalMeta.textContent = [data.year, data.genre, data.duration ? data.duration + ' min' : ''].filter(Boolean).join(' · ');
      if (modalDirectorName) modalDirectorName.textContent = data.director || '';
      if (modalTicketPrice) modalTicketPrice.textContent = formatVnd(data.ticketPrice || data.ticket_price || 0);
      modalSynopsis.textContent = data.synopsis || '';
      modalRatingNum.textContent = (data.rating || '0') + '/5 · ' + (data.reviews || '0') + ' đánh giá';
      if (modalDetailLink) modalDetailLink.href = siteUrl('/movies/' + (data.id || ''));
      if (modalTrailerLink) {
        const rawTrailer = data.trailer || data.trailer_url || '';
        const trailerUrl = normalizeUrl(rawTrailer);
        if (trailerUrl) {
          modalTrailerLink.href = trailerUrl;
          modalTrailerLink.style.display = '';
        } else {
          modalTrailerLink.href = '#';
          modalTrailerLink.style.display = 'none';
        }
      }
      if (modalStars) renderStars(modalStars, parseFloat(data.rating) || 0);
      try {
        renderCommunityReviews(JSON.parse(data.reviewItems || '[]'));
      } catch (error) {
        renderCommunityReviews([]);
      }
      modal.style.display = 'flex';
    });
  });

  const closeButtons = modal ? modal.querySelectorAll('.movie-modal-close, .movie-modal-backdrop') : [];
  closeButtons.forEach(function (el) {
    el.addEventListener('click', function () {
      if (modal) modal.style.display = 'none';
    });
  });
});
