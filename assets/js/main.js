(function () {
  "use strict";

  /**
   * Animation on scroll
   */
  window.addEventListener("load", () => {
    AOS.init({
      duration: 1200,
      easing: "ease-in-out",
      once: true,
      mirror: false,
    });
  });

  /**
   * Preloader
   */
  window.addEventListener("load", function () {
    var preloader = document.getElementById("preloader");
    if (preloader) {
      preloader.style.display = "none";
    }
  });

  /**
   * Translation
   */
  // Language and translation logic
  function detectBrowserLang() {
    const lang = navigator.language || navigator.userLanguage || "en";
    return lang.toLowerCase().startsWith("ar") ? "ar" : "en";
  }

  function getSavedLang() {
    return localStorage.getItem("siteLang") || detectBrowserLang();
  }

  function getSavedDir() {
    return (
      localStorage.getItem("siteDir") ||
      (getSavedLang() === "ar" ? "rtl" : "ltr")
    );
  }

  function setLanguage(lang) {
    fetch(`core/lang/${lang}.json`)
      .then((res) => {
        if (!res.ok) throw new Error("Translation file not found");
        return res.json();
      })
      .then((data) => {
        applyTranslations(data);
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
        localStorage.setItem("siteLang", lang);
        localStorage.setItem("siteDir", lang === "ar" ? "rtl" : "ltr");
      })
      .catch((err) => {
        console.warn("Translation error:", err);
      });
  }

  function applyTranslations(data) {
    document.querySelectorAll("[data-i18n]").forEach((el) => {
      const key = el.getAttribute("data-i18n");
      const value = getNested(data, key);
      if (value !== undefined && value !== null) {
        el.textContent = value;
      }
    });
  }

  function getNested(obj, path) {
    return path
      .split(".")
      .reduce((o, i) => (o && o[i] !== undefined ? o[i] : undefined), obj);
  }

  // Restore dir and lang from localStorage on page load
  var html = document.documentElement;
  html.lang = getSavedLang();
  html.dir = getSavedDir();
  setLanguage(html.lang);

  var globeBtn = document.getElementById("globeBtn");
  if (globeBtn) {
    globeBtn.addEventListener("click", function () {
      var newLang = html.lang === "ar" ? "en" : "ar";
      var newDir = newLang === "ar" ? "rtl" : "ltr";
      html.lang = newLang;
      html.dir = newDir;
      localStorage.setItem("siteLang", newLang);
      localStorage.setItem("siteDir", newDir);
      setLanguage(newLang);
    });
  }

  /**
   * Password toggle and lightweight client-side validation
   * - Handles any number of toggle buttons (class .toggle-password-btn)
   * - Uses data-target on button to find the input, or falls back to the input-group sibling
   * - Debounced validation disables submit when the form is invalid
   */

  // small debounce helper
  function debounce(fn, delay) {
    let t;
    return function (...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // Password toggle for any toggle button
  document.querySelectorAll(".toggle-password-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const targetSelector = btn.getAttribute("data-target");
      let input = null;
      if (targetSelector) input = document.querySelector(targetSelector);
      if (!input) {
        const group = btn.closest(".input-group");
        if (group) input = group.querySelector("input");
      }
      if (!input) return;

      const icon = btn.querySelector("i");
      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";
      if (icon) {
        icon.classList.toggle("bi-eye");
        icon.classList.toggle("bi-eye-slash");
        // subtle opacity animation
        icon.style.transition = "opacity 0.15s ease";
        icon.style.opacity = "0.6";
        setTimeout(() => (icon.style.opacity = "1"), 150);
      }
      input.focus();
    });
  });

  // Lightweight client-side validation for forms within auth pages
  document.querySelectorAll("form.login-form").forEach((form) => {
    const submit = form.querySelector('button[type="submit"], .btn-success');
    if (!submit) return;

    function validateOnce() {
      // collect inputs we care about
      const inputs = Array.from(
        form.querySelectorAll(
          'input[required], input[type="email"], input[type="password"], input[type="text"]'
        )
      );
      let valid = true;
      inputs.forEach((inp) => {
        // trim non-password inputs to avoid accidental whitespace
        if (inp.type !== "password" && typeof inp.value === "string") {
          inp.value = inp.value.trimStart(); // don't aggressively trim end while typing
        }

        // basic required check
        if (inp.hasAttribute("required") && !inp.value) valid = false;

        // email pattern
        if (inp.type === "email" && inp.value) {
          const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!re.test(inp.value)) valid = false;
        }

        // minlength check
        const min = inp.getAttribute("minlength");
        if (min && inp.value && inp.value.length < parseInt(min, 10))
          valid = false;
      });

      submit.disabled = !valid;
    }

    const debouncedValidate = debounce(validateOnce, 180);
    form.addEventListener("input", debouncedValidate);
    // initial run
    validateOnce();
  });
})();
