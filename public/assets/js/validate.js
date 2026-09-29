document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.querySelector('[data-validate="register"]');
  if (registerForm) {
    registerForm.addEventListener("submit", (event) => {
      const password = registerForm.querySelector("[name='password']").value;
      const confirm = registerForm.querySelector("[name='password_confirm']").value;
      const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

      if (!pattern.test(password)) {
        event.preventDefault();
        alert("Password must be at least 8 chars with upper, lower, number.");
        return;
      }
      if (password !== confirm) {
        event.preventDefault();
        alert("Passwords do not match.");
      }
    });
  }

  const reviewForm = document.querySelector('[data-validate="review"]');
  if (reviewForm) {
    reviewForm.addEventListener("submit", (event) => {
      const title = reviewForm.querySelector("[name='title']").value.trim();
      let body = reviewForm.querySelector("[name='body']").value || '';
      // Normalize whitespace and NBSPs, then trim
      body = body.replace(/\u00A0/g, ' ').replace(/\s+/g, ' ').trim();

      // rating: ensure a radio (half step) is selected
      const ratingInput = reviewForm.querySelector("input[name='rating']:checked");

      if (title.length === 0) {
        event.preventDefault();
        alert("Please provide a title for your review.");
        return;
      }

      if (body.length < 50) {
        event.preventDefault();
        alert("Review needs at least 50 characters.");
        return;
      }

      if (!ratingInput) {
        event.preventDefault();
        alert("Please provide a rating (supports half-star steps).");
        return;
      }
    });
  }
});
