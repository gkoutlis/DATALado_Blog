document.addEventListener("DOMContentLoaded", () => {
  // Dark mode
  const themeToggle = document.getElementById("blThemeToggle");
  const savedTheme = localStorage.getItem("blTheme");
  if (savedTheme === "dark") document.body.classList.add("dark-mode");

  themeToggle?.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("blTheme", document.body.classList.contains("dark-mode") ? "dark" : "light");
  });

  // Global loading spinner
  const overlay = document.getElementById("blSpinnerOverlay");
  const showSpinner = () => overlay?.classList.add("show");
  const hideSpinner = () => overlay?.classList.remove("show");

  // Show spinner on non-GET form submits
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", () => {
      // Avoid spinner for very small actions? Keep it simple: always show.
      showSpinner();
    });
  });

  // Optional: show spinner on links that do navigation (opt-in)
  document.querySelectorAll("a[data-spinner='1']").forEach((a) => {
    a.addEventListener("click", () => showSpinner());
  });

  // Toast flash (Bootstrap 5)
  const toastEl = document.getElementById("blFlashToast");
  if (toastEl && window.bootstrap) {
    const toast = new bootstrap.Toast(toastEl, { delay: 3800 });
    toast.show();
  }

  // Auto-hide spinner after page load
  window.addEventListener("load", () => {
    // small delay to avoid flash
    setTimeout(hideSpinner, 120);
  });

  // Tiny reveal animation (IntersectionObserver)
  const revealTargets = document.querySelectorAll("[data-reveal='1']");
  if ("IntersectionObserver" in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("bl-revealed");
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    revealTargets.forEach((el) => {
      el.style.opacity = "0";
      el.style.transform = "translateY(10px)";
      el.style.transition = "220ms ease";
      io.observe(el);
    });
    document.addEventListener("transitionend", (ev) => {
      if (ev.target.classList?.contains("bl-revealed")) {
        ev.target.style.transform = "";
      }
    });
  }

  // Apply reveal class immediately if JS runs without observer
  document.querySelectorAll(".bl-revealed").forEach((el) => {
    el.style.opacity = "1";
    el.style.transform = "translateY(0)";
  });

  // When element gets revealed
  const style = document.createElement("style");
  style.textContent = `.bl-revealed{opacity:1 !important; transform:translateY(0) !important;}`;
  document.head.appendChild(style);
});
