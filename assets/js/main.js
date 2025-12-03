(function () {
  "use strict";

  /**
   * BookNest Main Application Logic
   * Handles UI interactions, animations, and client-side validation.
   */

  const App = {
    init() {
      this.initAOS();
      this.initPreloader();
      this.initTranslation();
      this.initPasswordToggle();
      this.initFormValidation();
      this.initFavorites();
      this.initLoginToggle();
      this.initBookUpload();
      this.initBooksManagement();
    },

    /**
     * Initialize Animation on Scroll
     */
    initAOS() {
      window.addEventListener("load", () => {
        if (typeof AOS !== "undefined") {
          AOS.init({
            duration: 1200,
            easing: "ease-in-out",
            once: true,
            mirror: false,
          });
        }
      });
    },

    /**
     * Initialize Preloader
     */
    initPreloader() {
      window.addEventListener("load", () => {
        const preloader = document.getElementById("preloader");
        if (preloader) {
          preloader.style.opacity = "0";
          setTimeout(() => {
            preloader.style.display = "none";
          }, 500); // Wait for fade out
        }
      });
    },

    /**
     * Initialize Translation Logic
     */
    initTranslation() {
      const detectBrowserLang = () => {
        const lang = navigator.language || navigator.userLanguage || "en";
        return lang.toLowerCase().startsWith("ar") ? "ar" : "en";
      };

      const getSavedLang = () => {
        return localStorage.getItem("siteLang") || detectBrowserLang();
      };

      const getSavedDir = () => {
        return (
          localStorage.getItem("siteDir") ||
          (getSavedLang() === "ar" ? "rtl" : "ltr")
        );
      };

      const setLanguage = (lang) => {
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
      };

      const applyTranslations = (data) => {
        document.querySelectorAll("[data-i18n]").forEach((el) => {
          const key = el.getAttribute("data-i18n");
          const value = key
            .split(".")
            .reduce(
              (o, i) => (o && o[i] !== undefined ? o[i] : undefined),
              data
            );
          if (value !== undefined && value !== null) {
            el.textContent = value;
          }
        });
      };

      // Restore dir and lang from localStorage on page load
      const html = document.documentElement;
      html.lang = getSavedLang();
      html.dir = getSavedDir();
      setLanguage(html.lang);

      const globeBtn = document.getElementById("globeBtn");
      if (globeBtn) {
        globeBtn.addEventListener("click", () => {
          const newLang = html.lang === "ar" ? "en" : "ar";
          const newDir = newLang === "ar" ? "rtl" : "ltr";
          setLanguage(newLang);
        });
      }
    },

    /**
     * Initialize Password Visibility Toggle
     */
    initPasswordToggle() {
      document.querySelectorAll(".toggle-password-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          e.preventDefault();
          const targetSelector = btn.getAttribute("data-target");
          let input = null;

          if (targetSelector) {
            input = document.querySelector(targetSelector);
          } else {
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
            icon.style.transition = "opacity 0.15s ease";
            icon.style.opacity = "0.6";
            setTimeout(() => (icon.style.opacity = "1"), 150);
          }
          input.focus();
        });
      });
    },

    /**
     * Initialize Form Validation (Debounced)
     */
    initFormValidation() {
      const debounce = (fn, delay) => {
        let t;
        return function (...args) {
          clearTimeout(t);
          t = setTimeout(() => fn.apply(this, args), delay);
        };
      };

      document
        .querySelectorAll("form.login-form, form#registerForm")
        .forEach((form) => {
          const validateOnce = () => {
            const inputs = Array.from(
              form.querySelectorAll(
                'input[required], input[type="email"], input[type="password"], input[type="text"]'
              )
            );

            // Only validate visible inputs
            const visibleInputs = inputs.filter(
              (inp) => inp.offsetParent !== null
            );

            visibleInputs.forEach((inp) => {
              if (inp.type !== "password" && typeof inp.value === "string") {
                // Don't trim while typing in the middle, only start
                // inp.value = inp.value.trimStart();
              }

              let isValid = true;

              // HTML5 validation check
              if (!inp.checkValidity()) {
                isValid = false;
              }

              // Custom email check
              if (inp.type === "email" && inp.value) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(inp.value)) isValid = false;
              }

              // Visual feedback (Bootstrap classes)
              if (inp.value.length > 0) {
                if (isValid) {
                  inp.classList.remove("is-invalid");
                  inp.classList.add("is-valid");
                } else {
                  inp.classList.remove("is-valid");
                  inp.classList.add("is-invalid");
                }
              } else {
                inp.classList.remove("is-valid");
                inp.classList.remove("is-invalid");
              }
            });
          };

          const debouncedValidate = debounce(validateOnce, 300); // Increased debounce to 300ms
          form.addEventListener("input", debouncedValidate);
        });
    },

    /**
     * Initialize Favorites Button
     */
    initFavorites() {
      document.querySelectorAll(".btn-favorite").forEach((btn) => {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          this.classList.toggle("btn-danger");
          const isPressed = this.classList.contains("btn-danger");
          this.setAttribute("aria-pressed", isPressed);

          const icon = this.querySelector("i");
          if (icon) {
            icon.classList.toggle("bi-heart");
            icon.classList.toggle("bi-heart-fill");
          }

          // Persist to localStorage
          try {
            const bookId = this.dataset.bookId;
            if (bookId) {
              const key = "favorites_v1";
              const stored = JSON.parse(localStorage.getItem(key) || "{}");
              if (isPressed) stored[bookId] = true;
              else delete stored[bookId];
              localStorage.setItem(key, JSON.stringify(stored));
            }
          } catch (err) {
            console.error("Storage error", err);
          }
        });
      });
    },

    /**
     * Initialize Login Page Toggle (Parent vs Child)
     */
    initLoginToggle() {
      const parentContainer = document.getElementById("parent");
      const childContainer = document.getElementById("child");
      const parentRadio = document.getElementById("check-parent");
      const childRadio = document.getElementById("check-child");
      const form = document.getElementById("loginForm");

      if (!parentContainer || !childContainer || !parentRadio || !childRadio) {
        return;
      }

      const parentInputs = Array.from(
        parentContainer.querySelectorAll("input")
      );
      const childInputs = Array.from(childContainer.querySelectorAll("input"));

      const setRequired = (type) => {
        if (type === "parent") {
          parentInputs.forEach((i) => {
            i.required = true;
            i.disabled = false;
          });
          childInputs.forEach((i) => {
            i.required = false;
            i.disabled = true; // Disable to prevent submission
            i.value = ""; // Clear value
            i.classList.remove("is-invalid", "is-valid");
          });
        } else {
          parentInputs.forEach((i) => {
            i.required = false;
            i.disabled = true;
            i.value = "";
            i.classList.remove("is-invalid", "is-valid");
          });
          childInputs.forEach((i) => {
            i.required = true;
            i.disabled = false;
          });
        }
      };

      const updateView = () => {
        const isChild = childRadio.checked;
        if (isChild) {
          parentContainer.classList.add("d-none");
          childContainer.classList.remove("d-none");
          setRequired("child");
        } else {
          childContainer.classList.add("d-none");
          parentContainer.classList.remove("d-none");
          setRequired("parent");
        }
      };

      parentRadio.addEventListener("change", updateView);
      childRadio.addEventListener("change", updateView);

      // Initial state
      updateView();
    },

    /**
     * Initialize Book Upload Form
     */
    initBookUpload() {
      const form = document.getElementById("uploadBookForm");
      if (!form) return; // Only run on upload page

      const coverDropZone = document.getElementById("coverDropZone");
      const coverInput = document.getElementById("cover_image");
      const coverPreview = document.getElementById("coverPreview");
      const previewText = document.getElementById("previewText");
      const uploadBookBtn = document.getElementById("uploadBookBtn");
      const bookInput = document.getElementById("book_file");
      const bookFileName = document.getElementById("bookFileName");

      // Cover Image Upload
      if (coverDropZone && coverInput) {
        coverDropZone.addEventListener("click", () => coverInput.click());

        coverInput.addEventListener("change", function () {
          if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
              if (coverPreview && previewText) {
                coverPreview.src = e.target.result;
                coverPreview.classList.remove("d-none");
                previewText.classList.add("d-none");
              }
            };
            reader.readAsDataURL(this.files[0]);
          }
        });
      }

      // Book File Upload
      if (uploadBookBtn && bookInput && bookFileName) {
        uploadBookBtn.addEventListener("click", () => bookInput.click());

        bookInput.addEventListener("change", function () {
          if (this.files && this.files[0]) {
            bookFileName.textContent = this.files[0].name;
          } else {
            bookFileName.textContent = "Upload a book from your device";
          }
        });
      }

      // Form Submission
      form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;

        submitBtn.disabled = true;
        submitBtn.textContent = "Uploading...";

        try {
          const response = await fetch("core/api/books/insert.php", {
            method: "POST",
            body: formData,
          });

          // Always read the response as text first to handle potential non-JSON prefixes
          const text = await response.text();
          let result;

          try {
            // Remove "Done" prefix if present, which caused "Unexpected token 'D'" error in other parts of the app
            const jsonString = text.startsWith("Done")
              ? text.substring(4).trim()
              : text.trim();
            result = JSON.parse(jsonString);
          } catch (jsonParseError) {
            console.error(
              "Failed to parse JSON from server response:",
              jsonParseError,
              "Original text:",
              text
            );
            // If JSON parsing fails, treat it as an error
            throw new Error("Invalid JSON response from server.");
          }

          if (result.success) {
            alert("Book uploaded successfully!");
            form.reset();
            if (coverPreview && previewText && bookFileName) {
              coverPreview.classList.add("d-none");
              previewText.classList.remove("d-none");
              bookFileName.textContent = "Upload a book from your device";
            }

            // Redirect to books page
            setTimeout(() => {
              window.location.href = "?page=books";
            }, 1000);
          } else {
            alert("Error: " + (result.message || "Unknown error occurred"));
          }
        } catch (error) {
          console.error("Error:", error);
          alert("An error occurred while uploading the book: " + error.message);
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
        }
      });
    },

    /**
     * Initialize Books Management (Edit & Delete)
     */
    initBooksManagement() {
      // Only run on books management page
      if (!document.getElementById("editBookModal")) return;

      let editModal;

      // Initialize Bootstrap modal
      const editModalElement = document.getElementById("editBookModal");
      if (editModalElement) {
        editModal = new bootstrap.Modal(editModalElement);
      }
      // Make editBook function globally accessible
      window.editBook = function (id) {
        fetch(`core/api/books/get.php?id=${id}`)
          .then((response) => {
            // Always read the response as text first to handle potential non-JSON prefixes
            return response.text().then((text) => {
              if (!response.ok) {
                // If response is not OK, try to parse error JSON or use text
                try {
                  const errorData = JSON.parse(text);
                  throw new Error(
                    errorData.message ||
                      `HTTP error! Status: ${response.status}`
                  );
                } catch (e) {
                  // If not JSON, use the raw text as error message
                  throw new Error(
                    text || `HTTP error! Status: ${response.status}`
                  );
                }
              }

              // For successful responses, attempt to clean and parse JSON
              // Remove "Done" prefix if present, which caused the "Unexpected token 'D'" error
              const jsonString = text.startsWith("Done")
                ? text.substring(4).trim()
                : text.trim();
              try {
                return JSON.parse(jsonString);
              } catch (e) {
                console.error(
                  "Failed to parse JSON from server response:",
                  e,
                  "Original text:",
                  text
                );
                throw new Error("Invalid JSON response from server.");
              }
            });
          })
          .then((data) => {
            console.log("Book data:", data);
            if (data.success && data.book) {
              const book = data.book;
              document.getElementById("edit_book_id").value = book.ID;
              document.getElementById("edit_title").value = book.TITLE;
              document.getElementById("edit_author").value = book.AUTHOR;
              document.getElementById("edit_language").value = book.LANGUAGE;
              document.getElementById("edit_age_group").value = book.AGE_GROUP;
              document.getElementById("edit_description").value =
                book.DESCRIPTION;
              document.getElementById("edit_is_active").checked =
                book.IS_ACTIVE === "Y";

              if (editModal) {
                editModal.show();
              }
            } else {
              // Handle API-specific errors (e.g., success: false)
              alert(
                "Error loading book data: " + (data.message || "Unknown error")
              );
            }
          })
          .catch((error) => {
            console.error("Error fetching or parsing book data:", error);
            alert("Failed to load book data: " + error.message);
          });
      };

      // Handle edit form submission
      const editForm = document.getElementById("editBookForm");
      if (editForm) {
        editForm.addEventListener("submit", async function (e) {
          e.preventDefault();

          const formData = new FormData(this);
          const data = Object.fromEntries(formData.entries());
          data.isActive = document.getElementById("edit_is_active").checked
            ? "Y"
            : "N";

          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;
          submitBtn.disabled = true;
          submitBtn.textContent = "Saving...";

          try {
            const response = await fetch("core/api/books/update.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(data),
            });

            // Handle response similar to editBook function
            const text = await response.text();
            const jsonString = text.startsWith("Done")
              ? text.substring(4).trim()
              : text.trim();

            let result;
            try {
              result = JSON.parse(jsonString);
            } catch (e) {
              console.error(
                "Failed to parse update response:",
                e,
                "Text:",
                text
              );
              throw new Error("Invalid JSON response from server.");
            }

            if (result.success) {
              alert("Book updated successfully");
              if (editModal) {
                editModal.hide();
              }
              location.reload();
            } else {
              alert("Error: " + (result.message || "Failed to update book"));
            }
          } catch (error) {
            console.error("Error updating book:", error);
            alert(
              "An error occurred while updating the book: " + error.message
            );
          } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        });
      }

      // Make deleteBook function globally accessible
      window.deleteBook = function (id, title) {
        if (
          confirm(
            `Are you sure you want to delete "${title}"? This action cannot be undone.`
          )
        ) {
          fetch("core/api/books/delete.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ id: id }),
          })
            .then((response) => {
              return response.text().then((text) => {
                if (!response.ok) {
                  try {
                    const errorData = JSON.parse(text);
                    throw new Error(
                      errorData.message ||
                        `HTTP error! Status: ${response.status}`
                    );
                  } catch (e) {
                    throw new Error(
                      text || `HTTP error! Status: ${response.status}`
                    );
                  }
                }
                const jsonString = text.startsWith("Done")
                  ? text.substring(4).trim()
                  : text.trim();
                try {
                  return JSON.parse(jsonString);
                } catch (e) {
                  console.error(
                    "Failed to parse JSON from server response:",
                    e,
                    "Original text:",
                    text
                  );
                  throw new Error("Invalid JSON response from server.");
                }
              });
            })
            .then((data) => {
              if (data.success) {
                alert("Book deleted successfully");
                location.reload();
              } else {
                alert("Error: " + (data.message || "Failed to delete book"));
              }
            })
            .catch((error) => {
              console.error("Error deleting book:", error);
              alert(
                "An error occurred while deleting the book: " + error.message
              );
            });
        }
      };
    },
  };

  // Run App
  App.init();
})();
