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
   * Toggle Password Visibility
   * This script toggles the visibility of the password input field when the eye icon is clicked.
   */
  const togglePassword = document.getElementById("togglePassword");
  if (togglePassword) {
    togglePassword.addEventListener("click", function (e) {
      e.preventDefault();
      const passwordInput = document.getElementById("floatingPassword");
      const icon = this.querySelector("i");

      // Toggle the password visibility
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Toggle the icon with a smooth transition
      icon.style.transition = "opacity 0.2s ease-in-out";
      icon.style.opacity = "0";

      setTimeout(() => {
        icon.classList.toggle("bi-eye");
        icon.classList.toggle("bi-eye-slash");
        icon.style.opacity = "1";
      }, 100);

      // Add focus back to password input
      passwordInput.focus();
    });

    // Add hover effect
    togglePassword.addEventListener("mouseover", function () {
      this.querySelector("i").style.opacity = "0.8";
    });

    togglePassword.addEventListener("mouseout", function () {
      this.querySelector("i").style.opacity = "1";
    });
  }
})();
