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
      this.initUsersManagement();
      this.initChildrenManagement();
      this.initAccountTabs();
      this.initProfileManagement();
      this.initBookUpload();
      this.initBooksManagement();
      this.initBookReader();
      this.initQuizManagement();
      this.initQuizTaking();
      this.initQuizProgress();
      this.initEduDashboard();
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
        // Handle data-i18n attributes
        document.querySelectorAll("[data-i18n]").forEach((el) => {
          const key = el.getAttribute("data-i18n");
          const value = key
            .split(".")
            .reduce(
              (o, i) => (o && o[i] !== undefined ? o[i] : undefined),
              data
            );
          if (value !== undefined && value !== null) {
            el.innerHTML = value;
          }
        });

        // Handle data-i18n-placeholder attributes
        document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
          const key = el.getAttribute("data-i18n-placeholder");
          const value = key
            .split(".")
            .reduce(
              (o, i) => (o && o[i] !== undefined ? o[i] : undefined),
              data
            );
          if (value !== undefined && value !== null) {
            el.placeholder = value;
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
      const favButtons = document.querySelectorAll(".btn-favorite");
      if (favButtons.length === 0) return;

      // Load favorites from backend on page load
      fetch("core/api/favorites/status.php")
        .then((res) => res.json())
        .then((data) => {
          if (data.success && data.favorites) {
            // Apply favorited state to buttons
            favButtons.forEach((btn) => {
              const bookId = parseInt(btn.dataset.bookId);
              if (data.favorites.includes(bookId)) {
                this.setFavoriteState(btn, true);
              }
            });
          }
        })
        .catch((err) => {
          console.error("Failed to load favorites:", err);
        });

      // Add click handlers
      favButtons.forEach((btn) => {
        btn.addEventListener("click", (e) => {
          e.preventDefault();
          this.toggleFavorite(btn);
        });
      });
    },

    /**
     * Set favorite button state
     */
    setFavoriteState(btn, isFavorited) {
      const isTextButton = btn.classList.contains("text");

      // Update button color and aria
      if (isFavorited) {
        btn.classList.add("btn-danger");
        btn.setAttribute("aria-pressed", "true");
      } else {
        btn.classList.remove("btn-danger");
        btn.setAttribute("aria-pressed", "false");
      }

      // Handle icon (for both icon-only and text buttons)
      const icon = btn.querySelector("i");
      if (icon) {
        if (isFavorited) {
          icon.classList.remove("bi-heart");
          icon.classList.add("bi-heart-fill");
        } else {
          icon.classList.remove("bi-heart-fill");
          icon.classList.add("bi-heart");
        }
      }

      // Handle text button (toggle text content)
      if (isTextButton) {
        const textSpan = btn.querySelector(".btn-text");
        if (textSpan) {
          textSpan.textContent = isFavorited
            ? "Remove from Favorites"
            : "Add to Favorites";
        }
        // Update aria-label
        const bookTitle = btn.dataset.bookTitle || "this book";
        btn.setAttribute(
          "aria-label",
          isFavorited
            ? `Remove ${bookTitle} from favorites`
            : `Add ${bookTitle} to favorites`
        );
      }
    },

    /**
     * Toggle favorite status
     */
    toggleFavorite(btn) {
      const bookId = parseInt(btn.dataset.bookId);
      const isCurrentlyFavorited = btn.classList.contains("btn-danger");
      const action = isCurrentlyFavorited ? "unlike" : "like";

      // Optimistically update UI
      this.setFavoriteState(btn, !isCurrentlyFavorited);

      // Send to backend
      fetch("core/api/favorites/toggle.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          book_id: bookId,
          action: action,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            // Update localStorage for offline support
            try {
              const key = "favorites_v1";
              const stored = JSON.parse(localStorage.getItem(key) || "{}");
              if (data.is_favorited) {
                stored[bookId] = true;
              } else {
                delete stored[bookId];
              }
              localStorage.setItem(key, JSON.stringify(stored));
            } catch (err) {
              console.error("Storage error", err);
            }
          } else {
            // Revert UI on error
            this.setFavoriteState(btn, isCurrentlyFavorited);
            console.error("Failed to toggle favorite:", data.message);
          }
        })
        .catch((err) => {
          // Revert UI on error
          this.setFavoriteState(btn, isCurrentlyFavorited);
          console.error("API error:", err);
        });
    },

    /**
     * Initialize Login Page Toggle (Parent/Educator vs Child)
     */
    initLoginToggle() {
      const parentContainer = document.getElementById("parent");
      const childContainer = document.getElementById("child");
      const parentRadio = document.getElementById("check-parent");
      const eduRadio = document.getElementById("check-edu");
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
        if (type === "parent" || type === "edu") {
          // Parent and EDU use the same email/password form
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
          // Child uses code/passkey form
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
          // Both Parent and EDU show the parent form
          childContainer.classList.add("d-none");
          parentContainer.classList.remove("d-none");
          setRequired(eduRadio?.checked ? "edu" : "parent");
        }
      };

      parentRadio.addEventListener("change", updateView);
      if (eduRadio) {
        eduRadio.addEventListener("change", updateView);
      }
      childRadio.addEventListener("change", updateView);

      // Initial state
      updateView();
    },

    /**
     * Initialize Users Management (Edit & Delete)
     */
    initUsersManagement() {
      // Only run on users management page
      if (!document.getElementById("editUserModal")) return;

      // Make openEditModal globally accessible
      window.openEditModal = function (userId) {
        fetch(`core/api/users/get.php?id=${userId}`)
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
            if (data.success) {
              const user = data.user;
              document.getElementById("editUserId").value = user.ID;
              document.getElementById("editFirstName").value =
                user.FIRST_NAME || "";
              document.getElementById("editLastName").value =
                user.LAST_NAME || "";
              document.getElementById("editUsername").value =
                user.USERNAME || "";
              document.getElementById("editPhone").value = user.PHONE || "";
              document.getElementById("editRole").value = user.ROLE_ID || "";
              document.getElementById("editSubscribed").checked =
                user.IS_SUBSCRIBED === "Y";

              // Show modal
              const modal = new bootstrap.Modal(
                document.getElementById("editUserModal")
              );
              modal.show();
            } else {
              alert("Error loading user data: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Failed to load user data");
          });
      };

      // Make saveUser globally accessible
      window.saveUser = function () {
        const form = document.getElementById("editUserForm");
        const formData = new FormData(form);

        // Convert FormData to JSON object
        const data = {};
        formData.forEach((value, key) => {
          data[key] = value;
        });

        // Handle checkbox
        data.is_subscribed = document.getElementById("editSubscribed").checked
          ? "Y"
          : "N";

        const saveBtn = document.querySelector("#editUserModal .btn-primary");
        const originalText = saveBtn.textContent;
        saveBtn.disabled = true;
        saveBtn.textContent = "Saving...";

        fetch("core/api/users/update.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
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

              // Remove "Done" prefix if present
              const jsonString = text.startsWith("Done")
                ? text.substring(4).trim()
                : text.trim();
              try {
                return JSON.parse(jsonString);
              } catch (e) {
                console.error(
                  "Failed to parse JSON:",
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
              alert("User updated successfully");
              bootstrap.Modal.getInstance(
                document.getElementById("editUserModal")
              ).hide();
              location.reload();
            } else {
              alert("Error: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Failed to save changes: " + error.message);
          })
          .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
          });
      };

      // Make deleteUser globally accessible
      window.deleteUser = function (userId) {
        if (
          confirm(
            "Are you sure you want to delete this user? This action cannot be undone."
          )
        ) {
          fetch("core/api/users/delete.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ id: userId }),
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

                // Remove "Done" prefix if present
                const jsonString = text.startsWith("Done")
                  ? text.substring(4).trim()
                  : text.trim();
                try {
                  return JSON.parse(jsonString);
                } catch (e) {
                  console.error(
                    "Failed to parse JSON:",
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
                alert("User deleted successfully");
                document.querySelector(`tr[data-user-id="${userId}"]`).remove();
                // Update count
                const caption = document.querySelector("table caption");
                const currentCount = parseInt(
                  caption.textContent.match(/\d+/)[0]
                );
                caption.textContent = `Users - Total: ${
                  currentCount - 1
                } records`;
              } else {
                alert("Error: " + data.message);
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              alert("Failed to delete user: " + error.message);
            });
        }
      };
    },

    /**
     * Initialize Children Management
     */
    initChildrenManagement() {
      // Only run on account page with children management
      if (!document.getElementById("childrenListContainer")) return;

      // Helper function to parse API response
      const parseApiResponse = (response) => {
        return response.text().then((text) => {
          if (!response.ok) {
            try {
              const errorData = JSON.parse(text);
              throw new Error(
                errorData.message || `HTTP error! Status: ${response.status}`
              );
            } catch (e) {
              throw new Error(text || `HTTP error! Status: ${response.status}`);
            }
          }
          const jsonString = text.startsWith("Done")
            ? text.substring(4).trim()
            : text.trim();
          try {
            return JSON.parse(jsonString);
          } catch (e) {
            console.error("Failed to parse JSON:", e, "Original text:", text);
            throw new Error("Invalid JSON response from server.");
          }
        });
      };

      // Escape HTML helper
      const escapeHtml = (text) => {
        if (!text) return "";
        return text
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
      };

      // Fetch children
      const fetchChildren = () => {
        fetch("core/api/children/list.php")
          .then((response) => parseApiResponse(response))
          .then((data) => {
            if (data.success) {
              renderChildren(data.children);
              updateParentPasskey(data.parent_passkey);
            } else {
              console.error("Failed to fetch children:", data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      };

      // Update parent passkey display
      const updateParentPasskey = (passkey) => {
        const display = document.getElementById("parentPasskeyDisplay");
        if (display) {
          display.textContent = passkey ? `#${passkey}` : "Not set";
        }
      };

      // Render children
      const renderChildren = (children) => {
        const container = document.getElementById("childrenListContainer");
        const summaryContainer = document.getElementById("childSummaryCards");

        if (!container) return;

        if (children.length === 0) {
          container.innerHTML =
            '<div class="col-12 text-center text-muted py-4">No children added yet. Click "Add Child" to get started.</div>';
          if (summaryContainer) summaryContainer.innerHTML = "";
          return;
        }

        // Render List in Tab
        container.innerHTML = children
          .map(
            (child) => `
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-emoji-smile fs-3"></i>
                  </div>
                  <div>
                    <h5 class="card-title mb-0">${escapeHtml(child.NAME)}</h5>
                    <small class="text-muted">Age: ${child.AGE} years</small>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="small text-muted d-block">Login Code</label>
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control bg-light" value="${
                      child.CODE
                    }" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('${
                      child.CODE
                    }')">
                      <i class="bi bi-clipboard"></i>
                    </button>
                  </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                  <button class="btn btn-sm btn-outline-primary" onclick="openEditChildModal(${
                    child.ID
                  }, '${escapeHtml(child.NAME)}', '${child.DOB}')">
                    <i class="bi bi-pencil"></i> Edit
                  </button>
                  <button class="btn btn-sm btn-outline-danger" onclick="deleteChild(${
                    child.ID
                  }, '${escapeHtml(child.NAME)}')">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        `
          )
          .join("");

        // Render Summary Cards (Top)
        if (summaryContainer) {
          summaryContainer.innerHTML = children
            .map(
              (child) => `
            <div class="col-12 col-md-4 col-lg-3">
              <div class="rounded-4 p-4 shadow-sm h-100" style="background-color:#ffedd5;">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-3">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-white"
                      style="background-color:#fb923c; width: 50px; height: 50px;">
                      <i class="bi bi-person fs-4"></i>
                    </span>
                    <div>
                      <h5 class="mb-0 fw-bold">${escapeHtml(child.NAME)}</h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <h6 class="rounded-pill text-bg-secondary px-3 py-2">Child</h6>
                  </div>
                </div>
                <p class="mb-0 mt-3 small text-truncate">Code: ${child.CODE}</p>
              </div>
            </div>
          `
            )
            .join("");
        }
      };

      // Make functions globally accessible
      window.submitAddChild = function () {
        const form = document.getElementById("addChildForm");
        const errorDiv = document.getElementById("addChildError");
        const btn = document.querySelector("#addChildModal .btn-primary");

        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        btn.disabled = true;
        btn.textContent = "Adding...";
        errorDiv.classList.add("d-none");

        fetch("core/api/children/add.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        })
          .then((response) => parseApiResponse(response))
          .then((result) => {
            if (result.success) {
              const modal = bootstrap.Modal.getInstance(
                document.getElementById("addChildModal")
              );
              modal.hide();
              form.reset();
              fetchChildren();
              alert("Child added successfully!");
            } else {
              errorDiv.textContent = result.message || "Failed to add child";
              errorDiv.classList.remove("d-none");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            errorDiv.textContent = "An error occurred. Please try again.";
            errorDiv.classList.remove("d-none");
          })
          .finally(() => {
            btn.disabled = false;
            btn.textContent = "Add Child";
          });
      };

      window.openEditChildModal = function (id, name, dob) {
        document.getElementById("editChildId").value = id;
        document.getElementById("editChildName").value = name;
        document.getElementById("editChildDob").value = dob;

        const modal = new bootstrap.Modal(
          document.getElementById("editChildModal")
        );
        modal.show();
      };

      window.submitEditChild = function () {
        const form = document.getElementById("editChildForm");
        const errorDiv = document.getElementById("editChildError");
        const btn = document.querySelector("#editChildModal .btn-primary");

        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        btn.disabled = true;
        btn.textContent = "Saving...";
        errorDiv.classList.add("d-none");

        fetch("core/api/children/update.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        })
          .then((response) => parseApiResponse(response))
          .then((result) => {
            if (result.success) {
              const modal = bootstrap.Modal.getInstance(
                document.getElementById("editChildModal")
              );
              modal.hide();
              fetchChildren();
              alert("Child profile updated successfully!");
            } else {
              errorDiv.textContent = result.message || "Failed to update child";
              errorDiv.classList.remove("d-none");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            errorDiv.textContent = "An error occurred. Please try again.";
            errorDiv.classList.remove("d-none");
          })
          .finally(() => {
            btn.disabled = false;
            btn.textContent = "Save Changes";
          });
      };

      window.deleteChild = function (id, name) {
        if (
          !confirm(
            `Are you sure you want to delete ${name}? This action cannot be undone.`
          )
        ) {
          return;
        }

        fetch("core/api/children/delete.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: id }),
        })
          .then((response) => parseApiResponse(response))
          .then((result) => {
            if (result.success) {
              fetchChildren();
            } else {
              alert("Failed to delete child: " + result.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while deleting.");
          });
      };

      window.copyToClipboard = function (text) {
        navigator.clipboard
          .writeText(text)
          .then(() => {
            alert("Code copied to clipboard!");
          })
          .catch((err) => {
            console.error("Failed to copy: ", err);
          });
      };

      // Initial fetch
      fetchChildren();
    },

    /**
     * Initialize Account Page Tab Persistence
     */
    initAccountTabs() {
      // Only run on account page
      const tabContainer = document.querySelector("#underlineTabs");
      if (!tabContainer) return;

      const tabStorageKey = "activeAccountTab";
      const tabTriggerList = document.querySelectorAll(
        '#underlineTabs button[data-bs-toggle="tab"]'
      );

      // Restore last active tab
      const savedTab = localStorage.getItem(tabStorageKey);
      if (savedTab) {
        const savedTabElement = document.querySelector(
          `[data-bs-target="${savedTab}"]`
        );
        if (savedTabElement) {
          const tab = new bootstrap.Tab(savedTabElement);
          tab.show();
        }
      } else {
        // Default to first tab if none saved
        const firstTab = document.querySelector(
          '#underlineTabs button[data-bs-toggle="tab"]'
        );
        if (firstTab) {
          const tab = new bootstrap.Tab(firstTab);
          tab.show();
        }
      }

      // Save tab state when changed
      tabTriggerList.forEach((tabTrigger) => {
        tabTrigger.addEventListener("shown.bs.tab", function (event) {
          const targetSelector = event.target.getAttribute("data-bs-target");
          localStorage.setItem(tabStorageKey, targetSelector);
        });
      });
    },

    /**
     * Initialize Profile Management (Personal Info & Password)
     */
    initProfileManagement() {
      // Personal Info Form
      const personalInfoForm = document.getElementById("personalInfoForm");
      if (personalInfoForm) {
        personalInfoForm.addEventListener("submit", async function (e) {
          e.preventDefault();

          const errorDiv = document.getElementById("personalInfoError");
          const successDiv = document.getElementById("personalInfoSuccess");
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;

          errorDiv.classList.add("d-none");
          successDiv.classList.add("d-none");

          const formData = new FormData(this);
          const data = Object.fromEntries(formData.entries());

          submitBtn.disabled = true;
          submitBtn.textContent = "Saving...";

          try {
            const response = await fetch("core/api/users/update-profile.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(data),
            });

            const text = await response.text();
            const jsonString = text.startsWith("Done")
              ? text.substring(4).trim()
              : text.trim();

            let result;
            try {
              result = JSON.parse(jsonString);
            } catch (e) {
              throw new Error("Invalid JSON response");
            }

            if (result.success) {
              successDiv.textContent = result.message;
              successDiv.classList.remove("d-none");
              // Update displayed name if needed
              setTimeout(() => location.reload(), 1500);
            } else {
              errorDiv.textContent = result.message;
              errorDiv.classList.remove("d-none");
            }
          } catch (error) {
            console.error("Error:", error);
            errorDiv.textContent = "An error occurred. Please try again.";
            errorDiv.classList.remove("d-none");
          } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        });
      }

      // Password Form
      const passwordForm = document.getElementById("passwordForm");
      if (passwordForm) {
        passwordForm.addEventListener("submit", async function (e) {
          e.preventDefault();

          const errorDiv = document.getElementById("passwordError");
          const successDiv = document.getElementById("passwordSuccess");
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;

          errorDiv.classList.add("d-none");
          successDiv.classList.add("d-none");

          const formData = new FormData(this);
          const data = Object.fromEntries(formData.entries());

          // Basic client-side validation
          if (data.new_password !== data.confirm_password) {
            errorDiv.textContent = "New password and confirmation do not match";
            errorDiv.classList.remove("d-none");
            return;
          }

          submitBtn.disabled = true;
          submitBtn.textContent = "Updating...";

          try {
            const response = await fetch("core/api/users/update-password.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(data),
            });

            const text = await response.text();
            const jsonString = text.startsWith("Done")
              ? text.substring(4).trim()
              : text.trim();

            let result;
            try {
              result = JSON.parse(jsonString);
            } catch (e) {
              throw new Error("Invalid JSON response");
            }

            if (result.success) {
              successDiv.textContent = result.message;
              successDiv.classList.remove("d-none");
              this.reset();
            } else {
              errorDiv.textContent = result.message;
              errorDiv.classList.remove("d-none");
            }
          } catch (error) {
            console.error("Error:", error);
            errorDiv.textContent = "An error occurred. Please try again.";
            errorDiv.classList.remove("d-none");
          } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        });
      }
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
          const response = await fetch("core/api/books/add.php", {
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

    /**
     * Initialize PDF Book Reader with Text-to-Speech
     */
    initBookReader() {
      // Only run if we are on the reader page and config is present
      if (!window.bookNestConfig) return;

      // Check if PDF.js is available
      if (typeof pdfjsLib === "undefined") {
        console.error("PDF.js library not loaded");
        return;
      }

      // Set worker
      pdfjsLib.GlobalWorkerOptions.workerSrc = "assets/js/pdf.worker.min.js";

      // Book Reader State
      const BookReader = {
        pdfDoc: null,
        currentPage: 1,
        totalPages: 0,
        scale: 1.5,
        isReadingAloud: false,
        pdfPath: window.bookNestConfig.pdfPath,
        currentPageInstance: null, // Store current page for text extraction
        speechSynthesis: window.speechSynthesis,
        currentUtterance: null,

        // DOM Elements
        elements: {
          canvas: document.getElementById("pdfCanvas"),
          pageCounter: document.getElementById("pageCounter"),
          prevBtn: document.getElementById("prevBtn"),
          nextBtn: document.getElementById("nextBtn"),
          zoomInBtn: document.getElementById("zoomInBtn"),
          zoomOutBtn: document.getElementById("zoomOutBtn"),
          fullscreenBtn: document.getElementById("fullscreenBtn"),
          readAloudBtn: document.getElementById("readAloudBtn"),
          readAloudText: document.getElementById("readAloudText"),
          loadingSpinner: document.getElementById("loadingSpinner"),
          pageThumbnails: document.getElementById("pageThumbnails"),
          bookStage: document.getElementById("bookStage"),
        },

        init() {
          if (!this.pdfPath) {
            this.showError("No PDF file available for this book.");
            return;
          }
          this.bindEvents();
          this.loadPDF();
        },

        bindEvents() {
          // Navigation
          this.elements.prevBtn?.addEventListener("click", () =>
            this.previousPage()
          );
          this.elements.nextBtn?.addEventListener("click", () =>
            this.nextPage()
          );

          // Zoom controls
          this.elements.zoomInBtn?.addEventListener("click", () =>
            this.zoomIn()
          );
          this.elements.zoomOutBtn?.addEventListener("click", () =>
            this.zoomOut()
          );

          // Fullscreen
          this.elements.fullscreenBtn?.addEventListener("click", () =>
            this.toggleFullscreen()
          );

          // Read Aloud
          this.elements.readAloudBtn?.addEventListener("click", () =>
            this.toggleReadAloud()
          );

          // Keyboard navigation
          document.addEventListener("keydown", (e) => {
            // Don't trigger if user is typing in an input
            if (e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA")
              return;

            if (e.key === "ArrowLeft") this.previousPage();
            if (e.key === "ArrowRight") this.nextPage();
            if (e.key === "+" || e.key === "=") this.zoomIn();
            if (e.key === "-") this.zoomOut();
            if (e.key === " " || e.key === "Spacebar") {
              e.preventDefault();
              this.toggleReadAloud();
            }
          });
        },

        async loadPDF() {
          try {
            this.showLoading(true);
            const loadingTask = pdfjsLib.getDocument(this.pdfPath);
            this.pdfDoc = await loadingTask.promise;
            this.totalPages = this.pdfDoc.numPages;
            this.updatePageCounter();
            this.generateThumbnails();
            await this.renderPage(this.currentPage);
            this.showLoading(false);
          } catch (error) {
            console.error("Error loading PDF:", error);
            this.showError("Failed to load PDF. Please try again.");
            this.showLoading(false);
          }
        },

        async renderPage(pageNum) {
          if (!this.pdfDoc) return;

          try {
            const page = await this.pdfDoc.getPage(pageNum);
            // Store current page instance for text extraction
            this.currentPageInstance = page;

            const viewport = page.getViewport({
              scale: this.scale,
            });
            const canvas = this.elements.canvas;
            const ctx = canvas.getContext("2d");

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            const renderContext = {
              canvasContext: ctx,
              viewport: viewport,
            };

            await page.render(renderContext).promise;
            this.updateUI();
          } catch (error) {
            console.error("Error rendering page:", error);
          }
        },

        /**
         * Extract text content from the current PDF page
         * @returns {Promise<string>} The extracted text
         */
        async extractTextFromPage() {
          if (!this.currentPageInstance) {
            console.error("No page instance available for text extraction");
            return "";
          }

          try {
            const textContent = await this.currentPageInstance.getTextContent();
            const textItems = textContent.items;

            if (!textItems || textItems.length === 0) {
              return "";
            }

            // Concatenate all text items, adding spaces between them
            let fullText = "";
            let lastY = null;

            textItems.forEach((item) => {
              // Add line break if Y position changes significantly (new line)
              if (lastY !== null && Math.abs(item.transform[5] - lastY) > 5) {
                fullText += " ";
              }
              fullText += item.str;
              lastY = item.transform[5];
            });

            return fullText.trim();
          } catch (error) {
            console.error("Error extracting text:", error);
            return "";
          }
        },

        /**
         * Get a preferred English voice for speech synthesis
         * @returns {SpeechSynthesisVoice|null}
         */
        getPreferredVoice() {
          const voices = this.speechSynthesis.getVoices();

          // Preferred voice names (in order of preference)
          const preferredNames = [
            "Google US English",
            "Google UK English Female",
            "Google UK English Male",
            "Microsoft Zira",
            "Microsoft David",
            "Samantha",
            "Alex",
            "Victoria",
          ];

          // Try to find a preferred voice
          for (const name of preferredNames) {
            const voice = voices.find((v) => v.name.includes(name));
            if (voice) return voice;
          }

          // Fall back to any English voice
          const englishVoice = voices.find((v) => v.lang.startsWith("en"));
          if (englishVoice) return englishVoice;

          // Fall back to default
          return voices[0] || null;
        },

        /**
         * Stop any ongoing speech synthesis
         */
        stopReading() {
          if (this.speechSynthesis.speaking || this.speechSynthesis.pending) {
            this.speechSynthesis.cancel();
          }
          this.isReadingAloud = false;
          this.currentUtterance = null;
          this.updateReadAloudUI(false);
        },

        /**
         * Update the Read Aloud button UI
         * @param {boolean} isReading - Whether reading is active
         */
        updateReadAloudUI(isReading) {
          const btn = this.elements.readAloudBtn;
          const icon = btn?.querySelector("i");
          const text = this.elements.readAloudText;

          if (isReading) {
            btn?.classList.add("playing", "reading-active");
            icon?.classList.remove("bi-volume-up-fill");
            icon?.classList.add("bi-stop-fill");
            if (text) text.textContent = "Stop Reading";
          } else {
            btn?.classList.remove("playing", "reading-active");
            icon?.classList.remove("bi-stop-fill");
            icon?.classList.add("bi-volume-up-fill");
            if (text) text.textContent = "Read Aloud";
          }
        },

        /**
         * Toggle Read Aloud functionality using Web Speech API
         */
        async toggleReadAloud() {
          // If currently reading, stop
          if (this.isReadingAloud) {
            this.stopReading();
            return;
          }

          // Check if Speech Synthesis is supported
          if (!("speechSynthesis" in window)) {
            alert("Sorry, your browser doesn't support text-to-speech.");
            return;
          }

          try {
            // Show loading state
            const btn = this.elements.readAloudBtn;
            const text = this.elements.readAloudText;
            if (text) text.textContent = "Loading...";
            btn?.classList.add("loading");

            // Extract text from current page
            const pageText = await this.extractTextFromPage();

            if (!pageText || pageText.length === 0) {
              alert("No text found on this page.");
              if (text) text.textContent = "Read Aloud";
              btn?.classList.remove("loading");
              return;
            }

            // Create speech utterance
            this.currentUtterance = new SpeechSynthesisUtterance(pageText);

            // Wait for voices to load (needed for some browsers)
            if (this.speechSynthesis.getVoices().length === 0) {
              await new Promise((resolve) => {
                this.speechSynthesis.addEventListener(
                  "voiceschanged",
                  resolve,
                  { once: true }
                );
                // Timeout fallback
                setTimeout(resolve, 1000);
              });
            }

            // Set voice
            const voice = this.getPreferredVoice();
            if (voice) {
              this.currentUtterance.voice = voice;
            }

            // Configure speech parameters
            this.currentUtterance.rate = 0.9; // Slightly slower for children
            this.currentUtterance.pitch = 1.0;
            this.currentUtterance.volume = 1.0;

            // Event handlers
            this.currentUtterance.onstart = () => {
              this.isReadingAloud = true;
              btn?.classList.remove("loading");
              this.updateReadAloudUI(true);
              console.log(`Reading page ${this.currentPage} aloud...`);
            };

            this.currentUtterance.onend = () => {
              this.isReadingAloud = false;
              this.currentUtterance = null;
              this.updateReadAloudUI(false);
              console.log("Finished reading page.");
            };

            this.currentUtterance.onerror = (event) => {
              console.error("Speech synthesis error:", event.error);
              this.isReadingAloud = false;
              this.currentUtterance = null;
              this.updateReadAloudUI(false);
              btn?.classList.remove("loading");

              if (event.error !== "canceled") {
                alert("An error occurred while reading. Please try again.");
              }
            };

            // Start speaking
            this.speechSynthesis.speak(this.currentUtterance);
          } catch (error) {
            console.error("Error in toggleReadAloud:", error);
            alert("An error occurred. Please try again.");
            this.updateReadAloudUI(false);
          }
        },

        async generateThumbnails() {
          const container = this.elements.pageThumbnails;
          if (!container) return;

          container.innerHTML = "";

          for (let i = 1; i <= this.totalPages; i++) {
            const thumbnail = document.createElement("div");
            thumbnail.className = `page-thumbnail ${
              i === this.currentPage ? "active" : ""
            } m-1`;
            thumbnail.dataset.page = i;
            thumbnail.innerHTML = `
                        <div class="thumbnail-preview">
                            <canvas id="thumb-${i}"></canvas>
                        </div>
                        <span class="thumbnail-label">Page ${i}</span>
                    `;
            thumbnail.addEventListener("click", () => this.goToPage(i));
            container.appendChild(thumbnail);

            // Render thumbnail (small scale)
            this.renderThumbnail(i);
          }
        },

        async renderThumbnail(pageNum) {
          try {
            const page = await this.pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({
              scale: 0.15,
            });
            const canvas = document.getElementById(`thumb-${pageNum}`);
            if (!canvas) return;

            const ctx = canvas.getContext("2d");
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            await page.render({
              canvasContext: ctx,
              viewport: viewport,
            }).promise;
          } catch (error) {
            console.error("Error rendering thumbnail:", error);
          }
        },

        goToPage(pageNum) {
          if (pageNum < 1 || pageNum > this.totalPages) return;
          // Stop reading when changing pages
          if (this.isReadingAloud) {
            this.stopReading();
          }
          this.currentPage = pageNum;
          this.renderPage(pageNum);
        },

        previousPage() {
          if (this.currentPage > 1) {
            // Stop reading when changing pages
            if (this.isReadingAloud) {
              this.stopReading();
            }
            this.currentPage--;
            this.renderPage(this.currentPage);
          }
        },

        nextPage() {
          if (this.currentPage < this.totalPages) {
            // Stop reading when changing pages
            if (this.isReadingAloud) {
              this.stopReading();
            }
            this.currentPage++;
            this.renderPage(this.currentPage);

            // Check if reached last page - trigger book completion
            if (this.currentPage === this.totalPages) {
              this.onBookComplete();
            }
          }
        },

        /**
         * Called when user reaches the last page of the book
         */
        onBookComplete() {
          // Show the book completion modal
          const bookCompleteModal =
            document.getElementById("bookCompleteModal");
          if (bookCompleteModal && typeof bootstrap !== "undefined") {
            const modal = new bootstrap.Modal(bookCompleteModal);
            modal.show();
          }
        },

        zoomIn() {
          if (this.scale < 3) {
            this.scale += 0.25;
            this.renderPage(this.currentPage);
          }
        },

        zoomOut() {
          if (this.scale > 0.5) {
            this.scale -= 0.25;
            this.renderPage(this.currentPage);
          }
        },

        toggleFullscreen() {
          const stage = this.elements.bookStage;
          if (!document.fullscreenElement) {
            stage?.requestFullscreen();
            this.elements.fullscreenBtn
              .querySelector("i")
              ?.classList.replace("bi-fullscreen", "bi-fullscreen-exit");
          } else {
            document.exitFullscreen();
            this.elements.fullscreenBtn
              .querySelector("i")
              ?.classList.replace("bi-fullscreen-exit", "bi-fullscreen");
          }
        },

        updateUI() {
          this.updatePageCounter();
          this.updateNavButtons();
          this.updateThumbnails();
        },

        updatePageCounter() {
          if (this.elements.pageCounter) {
            this.elements.pageCounter.textContent = `Page ${
              this.currentPage
            } of ${this.totalPages || 1}`;
          }
        },

        updateNavButtons() {
          if (this.elements.prevBtn) {
            this.elements.prevBtn.disabled = this.currentPage <= 1;
          }
          if (this.elements.nextBtn) {
            this.elements.nextBtn.disabled =
              this.currentPage >= this.totalPages;
          }
        },

        updateThumbnails() {
          const thumbnails = document.querySelectorAll(".page-thumbnail");
          thumbnails.forEach((thumb) => {
            const pageNum = parseInt(thumb.dataset.page);
            if (pageNum === this.currentPage) {
              thumb.classList.add("active");
              thumb.scrollIntoView({
                behavior: "smooth",
                block: "nearest",
              });
            } else {
              thumb.classList.remove("active");
            }
          });
        },

        showLoading(show) {
          if (this.elements.loadingSpinner) {
            this.elements.loadingSpinner.style.display = show
              ? "block"
              : "none";
          }
          if (this.elements.canvas) {
            this.elements.canvas.style.display = show ? "none" : "block";
          }
        },

        showError(message) {
          if (this.elements.canvas) {
            this.elements.canvas.style.display = "none";
          }
          const wrapper = document.querySelector(".page-content-wrapper");
          if (wrapper) {
            wrapper.innerHTML = `
                        <div class="text-center text-white py-5">
                            <i class="bi bi-exclamation-triangle fs-1 mb-3"></i>
                            <p>${message}</p>
                        </div>
                    `;
          }
        },
      };

      if (
        !window.bookNestConfig.bookNotFound &&
        window.bookNestConfig.pdfPath
      ) {
        BookReader.init();
      }
    },

    /**
     * Initialize Quiz Management
     * Handles CRUD operations for quizzes
     */
    initQuizManagement() {
      // Check if we're on a quiz page
      if (!window.quizPageConfig && !window.quizFormConfig) return;

      const QuizManager = {
        questions: [],
        editingQuestionIndex: null,

        init() {
          if (window.quizFormConfig) {
            this.initQuizForm();
          }
          if (window.quizPageConfig) {
            this.initQuizzesList();
          }
        },

        // ===== QUIZ FORM (Create/Edit) =====
        initQuizForm() {
          this.bindFormEvents();

          // If edit mode, load quiz data
          if (
            window.quizFormConfig.isEditMode &&
            window.quizFormConfig.quizId
          ) {
            this.loadQuizForEdit(window.quizFormConfig.quizId);
          }

          this.updateQuestionsPreview();
        },

        bindFormEvents() {
          const addQuestionBtn = document.getElementById("addQuestionBtn");
          const clearQuestionBtn = document.getElementById("clearQuestionBtn");
          const quizForm = document.getElementById("quizForm");

          if (addQuestionBtn) {
            addQuestionBtn.addEventListener("click", () => this.addQuestion());
          }

          if (clearQuestionBtn) {
            clearQuestionBtn.addEventListener("click", () =>
              this.clearQuestionForm()
            );
          }

          if (quizForm) {
            quizForm.addEventListener("submit", (e) =>
              this.handleFormSubmit(e)
            );
          }
        },

        addQuestion() {
          const questionText = document.getElementById("questionText");
          const optionInputs = document.querySelectorAll(".option-input");
          const correctAnswerRadio = document.querySelector(
            'input[name="correctAnswer"]:checked'
          );

          // Validate question text
          if (!questionText || !questionText.value.trim()) {
            this.showAlert("error", "Please enter a question");
            return;
          }

          // Get options
          const options = [];
          let filledOptionsCount = 0;

          optionInputs.forEach((input, index) => {
            const text = input.value.trim();
            if (text) {
              filledOptionsCount++;
              options.push({
                text: text,
                is_correct:
                  correctAnswerRadio &&
                  parseInt(correctAnswerRadio.value) === index,
              });
            }
          });

          // Validate at least 2 options
          if (filledOptionsCount < 2) {
            this.showAlert("error", "Please provide at least 2 options");
            return;
          }

          // Validate correct answer selected
          if (!correctAnswerRadio) {
            this.showAlert("error", "Please select the correct answer");
            return;
          }

          // Check if the selected correct answer has text
          const correctIndex = parseInt(correctAnswerRadio.value);
          const correctOption = optionInputs[correctIndex];
          if (!correctOption || !correctOption.value.trim()) {
            this.showAlert("error", "The correct answer option must have text");
            return;
          }

          // Create question object
          const question = {
            question_text: questionText.value.trim(),
            type: "multiple_choice",
            options: options,
          };

          // Add or update question
          if (this.editingQuestionIndex !== null) {
            this.questions[this.editingQuestionIndex] = question;
            this.editingQuestionIndex = null;
            document.getElementById("addQuestionBtn").innerHTML =
              '<i class="bi bi-plus-lg me-2"></i><span>Add Question</span>';
          } else {
            this.questions.push(question);
          }

          this.clearQuestionForm();
          this.updateQuestionsPreview();
          this.showAlert("success", "Question added successfully");
        },

        clearQuestionForm() {
          const questionText = document.getElementById("questionText");
          const optionInputs = document.querySelectorAll(".option-input");
          const correctAnswerRadios = document.querySelectorAll(
            'input[name="correctAnswer"]'
          );

          if (questionText) questionText.value = "";
          optionInputs.forEach((input) => (input.value = ""));
          correctAnswerRadios.forEach((radio) => (radio.checked = false));

          this.editingQuestionIndex = null;
          const addBtn = document.getElementById("addQuestionBtn");
          if (addBtn) {
            addBtn.innerHTML =
              '<i class="bi bi-plus-lg me-2"></i><span>Add Question</span>';
          }
        },

        editQuestion(index) {
          const question = this.questions[index];
          if (!question) return;

          document.getElementById("questionText").value =
            question.question_text;

          const optionInputs = document.querySelectorAll(".option-input");
          const correctAnswerRadios = document.querySelectorAll(
            'input[name="correctAnswer"]'
          );

          // Reset all inputs
          optionInputs.forEach((input) => (input.value = ""));
          correctAnswerRadios.forEach((radio) => (radio.checked = false));

          // Fill in options
          question.options.forEach((option, i) => {
            if (optionInputs[i]) {
              optionInputs[i].value = option.text;
              if (option.is_correct && correctAnswerRadios[i]) {
                correctAnswerRadios[i].checked = true;
              }
            }
          });

          this.editingQuestionIndex = index;
          document.getElementById("addQuestionBtn").innerHTML =
            '<i class="bi bi-check-lg me-2"></i><span>Update Question</span>';

          // Scroll to form
          document
            .getElementById("questionBuilder")
            .scrollIntoView({ behavior: "smooth" });
        },

        deleteQuestion(index) {
          this.questions.splice(index, 1);
          this.updateQuestionsPreview();
          this.showAlert("info", "Question removed");
        },

        updateQuestionsPreview() {
          const container = document.getElementById("questionsPreview");
          const noQuestionsMsg = document.getElementById("noQuestionsMessage");
          const questionCount = document.getElementById("questionCount");

          if (!container) return;

          // Update count
          if (questionCount) {
            questionCount.textContent = this.questions.length;
          }

          // Show/hide empty message
          if (noQuestionsMsg) {
            noQuestionsMsg.classList.toggle(
              "d-none",
              this.questions.length > 0
            );
          }

          // Remove existing question items (keep no questions message)
          container
            .querySelectorAll(".question-item")
            .forEach((el) => el.remove());

          // Add question items
          this.questions.forEach((question, index) => {
            const correctOption = question.options.find((o) => o.is_correct);
            const correctText = correctOption ? correctOption.text : "Not set";

            const item = document.createElement("div");
            item.className =
              "list-group-item question-item d-flex justify-content-between align-items-start border-0 px-0 py-2";
            item.innerHTML = `
              <div class="flex-grow-1">
                <div class="fw-medium">${index + 1}. ${this.escapeHtml(
              question.question_text
            )}</div>
                <small class="text-success">
                  <i class="bi bi-check-circle-fill me-1"></i>
                  Correct: ${this.escapeHtml(correctText)}
                </small>
              </div>
              <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-outline-primary edit-question-btn" data-index="${index}">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn" data-index="${index}">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            `;
            container.appendChild(item);
          });

          // Bind edit/delete buttons
          container.querySelectorAll(".edit-question-btn").forEach((btn) => {
            btn.addEventListener("click", () =>
              this.editQuestion(parseInt(btn.dataset.index))
            );
          });

          container.querySelectorAll(".delete-question-btn").forEach((btn) => {
            btn.addEventListener("click", () =>
              this.deleteQuestion(parseInt(btn.dataset.index))
            );
          });
        },

        async handleFormSubmit(e) {
          e.preventDefault();

          const title = document.getElementById("quizTitle")?.value.trim();
          const description = document
            .getElementById("quizDescription")
            ?.value.trim();
          const bookId = document.getElementById("quizBook")?.value;
          const quizId = document.getElementById("quizId")?.value;

          // Validate title
          if (!title) {
            this.showAlert("error", "Quiz title is required");
            document.getElementById("quizTitle")?.classList.add("is-invalid");
            return;
          }

          // Validate questions
          if (this.questions.length === 0) {
            this.showAlert("error", "Please add at least one question");
            return;
          }

          const saveBtn = document.getElementById("saveQuizBtn");
          const originalText = saveBtn?.innerHTML;
          if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML =
              '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
          }

          try {
            const endpoint = quizId
              ? "core/api/quizzies/update.php"
              : "core/api/quizzies/add.php";
            const payload = {
              title: title,
              description: description,
              book_id: bookId || null,
              questions: this.questions,
            };

            if (quizId) {
              payload.id = parseInt(quizId);
            }

            const response = await fetch(endpoint, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (result.success) {
              this.showAlert(
                "success",
                result.message || "Quiz saved successfully"
              );
              setTimeout(() => {
                window.location.href = "?page=quizzes";
              }, 1500);
            } else {
              throw new Error(result.message || "Failed to save quiz");
            }
          } catch (error) {
            console.error("Quiz save error:", error);
            this.showAlert("error", error.message || "An error occurred");
          } finally {
            if (saveBtn) {
              saveBtn.disabled = false;
              saveBtn.innerHTML = originalText;
            }
          }
        },

        async loadQuizForEdit(quizId) {
          try {
            const response = await fetch(
              `core/api/quizzies/get.php?id=${quizId}`
            );
            const result = await response.json();

            if (!result.success || !result.quiz) {
              throw new Error(result.message || "Quiz not found");
            }

            const quiz = result.quiz;

            // Fill form fields
            document.getElementById("quizTitle").value = quiz.TITLE || "";
            document.getElementById("quizDescription").value =
              quiz.DESCRIPTION || "";

            if (quiz.BOOK_ID) {
              document.getElementById("quizBook").value = quiz.BOOK_ID;
            }

            // Load questions
            if (quiz.questions && quiz.questions.length > 0) {
              this.questions = quiz.questions.map((q) => ({
                question_text: q.QUESTION,
                type: q.TYPE || "multiple_choice",
                options: q.options.map((o) => ({
                  text: o.OPTION,
                  is_correct: o.IS_CORRECT === "Y",
                })),
              }));
              this.updateQuestionsPreview();
            }
          } catch (error) {
            console.error("Load quiz error:", error);
            this.showAlert("error", error.message || "Failed to load quiz");
          }
        },

        // ===== QUIZZES LIST =====
        initQuizzesList() {
          this.loadQuizzesList();
          this.bindDeleteModal();
        },

        async loadQuizzesList() {
          const loadingEl =
            document.getElementById("quizzesLoading") ||
            document.getElementById("adminQuizzesLoading");
          const gridEl = document.getElementById("quizzesGrid");
          const tableContainer = document.getElementById(
            "adminQuizzesTableContainer"
          );
          const emptyEl =
            document.getElementById("quizzesEmpty") ||
            document.getElementById("adminQuizzesEmpty");

          try {
            let url = "core/api/quizzies/get.php?all=true";

            // For EDU users (not admin page), filter by their user ID
            if (
              !window.quizPageConfig.isAdmin &&
              !window.quizPageConfig.isAdminPage
            ) {
              url += `&user_id=${window.quizPageConfig.currentUserId}`;
            }

            const response = await fetch(url);
            const result = await response.json();

            if (loadingEl) loadingEl.classList.add("d-none");

            if (!result.success) {
              throw new Error(result.message || "Failed to load quizzes");
            }

            const quizzes = result.quizzes || [];

            if (quizzes.length === 0) {
              if (emptyEl) emptyEl.classList.remove("d-none");
              return;
            }

            // Admin table view
            if (tableContainer) {
              tableContainer.classList.remove("d-none");
              this.renderQuizzesTable(quizzes);
              this.updateAdminStats(quizzes);
            }

            // Educator grid view
            if (gridEl) {
              this.renderQuizzesGrid(quizzes);
            }
          } catch (error) {
            console.error("Load quizzes error:", error);
            if (loadingEl) loadingEl.classList.add("d-none");
            this.showAlert("error", error.message || "Failed to load quizzes");
          }
        },

        renderQuizzesGrid(quizzes) {
          const container = document.getElementById("quizzesGrid");
          if (!container) return;

          container.innerHTML = quizzes
            .map((quiz) => {
              const canEdit =
                window.quizPageConfig.isAdmin ||
                quiz.USER_ID == window.quizPageConfig.currentUserId;

              return `
              <div class="col-lg-4 col-md-6">
                <div class="card border rounded-3 h-100">
                  <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-truncate" title="${this.escapeHtml(
                      quiz.TITLE
                    )}">
                      ${this.escapeHtml(quiz.TITLE)}
                    </h5>
                    ${
                      canEdit
                        ? `
                      <div class="d-flex gap-2">
                        <a href="?page=create-quiz&id=${quiz.ID}" class="btn btn-sm btn-secondary-light d-flex align-items-center gap-1">
                          <i class="bi bi-pencil-square"></i>
                          <span>Edit</span>
                        </a>
                        <button class="btn btn-sm btn-danger d-flex align-items-center gap-1 delete-quiz-btn" data-id="${quiz.ID}">
                          <i class="bi bi-trash"></i>
                          <span>Delete</span>
                        </button>
                      </div>
                    `
                        : ""
                    }
                  </div>
                  <div class="card-body">
                    <p class="mb-2 text-muted small">
                      ${
                        quiz.DESCRIPTION
                          ? this.escapeHtml(
                              quiz.DESCRIPTION.substring(0, 100)
                            ) + (quiz.DESCRIPTION.length > 100 ? "..." : "")
                          : "No description"
                      }
                    </p>
                    <div class="d-flex gap-3 text-muted small">
                      <span><i class="bi bi-question-circle me-1"></i>${
                        quiz.QUESTION_COUNT || 0
                      } questions</span>
                      ${
                        quiz.BOOK_TITLE
                          ? `<span><i class="bi bi-book me-1"></i>${this.escapeHtml(
                              quiz.BOOK_TITLE
                            )}</span>`
                          : ""
                      }
                    </div>
                  </div>
                  <div class="card-footer bg-white border-top">
                    <small class="text-muted">
                      Created: ${new Date(
                        quiz.CREATED_DATE
                      ).toLocaleDateString()}
                    </small>
                  </div>
                </div>
              </div>
            `;
            })
            .join("");

          // Bind delete buttons
          container.querySelectorAll(".delete-quiz-btn").forEach((btn) => {
            btn.addEventListener("click", () =>
              this.showDeleteModal(btn.dataset.id)
            );
          });
        },

        renderQuizzesTable(quizzes) {
          const tbody = document.getElementById("adminQuizzesBody");
          if (!tbody) return;

          tbody.innerHTML = quizzes
            .map(
              (quiz, index) => `
            <tr>
              <td>${index + 1}</td>
              <td>
                <strong>${this.escapeHtml(quiz.TITLE)}</strong>
              </td>
              <td>
                ${
                  quiz.FIRST_NAME
                    ? `${quiz.FIRST_NAME} ${quiz.LAST_NAME || ""}`
                    : quiz.USERNAME || "Unknown"
                }
              </td>
              <td>${quiz.BOOK_TITLE || "-"}</td>
              <td><span class="badge bg-primary">${
                quiz.QUESTION_COUNT || 0
              }</span></td>
              <td>${new Date(quiz.CREATED_DATE).toLocaleDateString()}</td>
              <td>
                <div class="d-flex gap-1">
                  <a href="?page=create-quiz&id=${
                    quiz.ID
                  }" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <button class="btn btn-sm btn-outline-danger delete-quiz-btn" data-id="${
                    quiz.ID
                  }">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `
            )
            .join("");

          // Bind delete buttons
          tbody.querySelectorAll(".delete-quiz-btn").forEach((btn) => {
            btn.addEventListener("click", () =>
              this.showDeleteModal(btn.dataset.id)
            );
          });
        },

        updateAdminStats(quizzes) {
          const totalQuizzes = document.getElementById("totalQuizzes");
          const totalQuestions = document.getElementById("totalQuestions");
          const totalCreators = document.getElementById("totalCreators");

          if (totalQuizzes) totalQuizzes.textContent = quizzes.length;

          if (totalQuestions) {
            const questionSum = quizzes.reduce(
              (sum, q) => sum + (parseInt(q.QUESTION_COUNT) || 0),
              0
            );
            totalQuestions.textContent = questionSum;
          }

          if (totalCreators) {
            const uniqueCreators = new Set(quizzes.map((q) => q.USER_ID)).size;
            totalCreators.textContent = uniqueCreators;
          }
        },

        bindDeleteModal() {
          const confirmBtn = document.getElementById("confirmDeleteQuizBtn");
          if (confirmBtn) {
            confirmBtn.addEventListener("click", () => this.deleteQuiz());
          }
        },

        showDeleteModal(quizId) {
          document.getElementById("deleteQuizId").value = quizId;
          const modal = new bootstrap.Modal(
            document.getElementById("deleteQuizModal")
          );
          modal.show();
        },

        async deleteQuiz() {
          const quizId = document.getElementById("deleteQuizId").value;
          const confirmBtn = document.getElementById("confirmDeleteQuizBtn");

          if (!quizId) return;

          const originalText = confirmBtn?.innerHTML;
          if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML =
              '<span class="spinner-border spinner-border-sm"></span>';
          }

          try {
            const response = await fetch("core/api/quizzies/delete.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ id: parseInt(quizId) }),
            });

            const result = await response.json();

            if (result.success) {
              bootstrap.Modal.getInstance(
                document.getElementById("deleteQuizModal")
              )?.hide();
              this.showAlert("success", "Quiz deleted successfully");
              this.loadQuizzesList();
            } else {
              throw new Error(result.message || "Failed to delete quiz");
            }
          } catch (error) {
            console.error("Delete quiz error:", error);
            this.showAlert("error", error.message || "An error occurred");
          } finally {
            if (confirmBtn) {
              confirmBtn.disabled = false;
              confirmBtn.innerHTML = originalText;
            }
          }
        },

        // ===== UTILITIES =====
        escapeHtml(text) {
          if (!text) return "";
          const div = document.createElement("div");
          div.textContent = text;
          return div.innerHTML;
        },

        showAlert(type, message) {
          if (typeof Swal !== "undefined") {
            Swal.fire({
              icon:
                type === "error"
                  ? "error"
                  : type === "success"
                  ? "success"
                  : "info",
              title:
                type === "error"
                  ? "Error"
                  : type === "success"
                  ? "Success"
                  : "Info",
              text: message,
              timer: type === "success" ? 2000 : undefined,
              showConfirmButton: type !== "success",
            });
          } else {
            alert(message);
          }
        },
      };

      QuizManager.init();
    },

    /**
     * Initialize Quiz Taking Experience
     * Handles the actual quiz-taking interface
     */
    initQuizTaking() {
      // Check if we're on the quiz taking page
      if (!window.quizConfig) return;

      const QuizTaker = {
        config: window.quizConfig,
        currentQuestion: 0,
        answers: {},
        startTime: Date.now(),
        timerInterval: null,

        elements: {
          questionContainer: document.getElementById("questionContainer"),
          prevBtn: document.getElementById("prevQuestionBtn"),
          nextBtn: document.getElementById("nextQuestionBtn"),
          submitBtn: document.getElementById("submitQuizBtn"),
          progressBar: document.getElementById("quizProgress"),
          currentQuestionNum: document.getElementById("currentQuestionNum"),
          timer: document.getElementById("quizTimer"),
        },

        init() {
          if (!this.config.questions || this.config.questions.length === 0) {
            return;
          }

          this.bindEvents();
          this.startTimer();
          this.renderQuestion(0);
          this.updateNavigation();
        },

        bindEvents() {
          this.elements.prevBtn?.addEventListener("click", () =>
            this.previousQuestion()
          );
          this.elements.nextBtn?.addEventListener("click", () =>
            this.nextQuestion()
          );
          this.elements.submitBtn?.addEventListener("click", () =>
            this.submitQuiz()
          );
        },

        startTimer() {
          this.timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
            const minutes = Math.floor(elapsed / 60)
              .toString()
              .padStart(2, "0");
            const seconds = (elapsed % 60).toString().padStart(2, "0");
            if (this.elements.timer) {
              this.elements.timer.textContent = `${minutes}:${seconds}`;
            }
          }, 1000);
        },

        renderQuestion(index) {
          const question = this.config.questions[index];
          if (!question) return;

          const selectedAnswer = this.answers[question.ID];
          const optionLetters = ["A", "B", "C", "D", "E", "F"];

          const optionsHtml = question.options
            .map(
              (option, i) => `
              <div class="col-12 col-md-6">
                <button type="button" 
                        class="option-btn d-flex align-items-center w-100 ${
                          selectedAnswer == option.ID ? "selected" : ""
                        }" 
                        data-question-id="${question.ID}"
                        data-option-id="${option.ID}"
                        aria-label="Option ${
                          optionLetters[i]
                        }: ${this.escapeHtml(option.OPTION)}">
                  <span class="option-letter">${optionLetters[i]}</span>
                  <span class="option-text">${this.escapeHtml(
                    option.OPTION
                  )}</span>
                </button>
              </div>
            `
            )
            .join("");

          this.elements.questionContainer.innerHTML = `
            <h4 class="mb-4 fw-semibold">${this.escapeHtml(
              question.QUESTION
            )}</h4>
            <div class="row g-3">${optionsHtml}</div>
          `;

          // Bind option click events
          this.elements.questionContainer
            .querySelectorAll(".option-btn")
            .forEach((btn) => {
              btn.addEventListener("click", (e) => {
                const questionId = btn.dataset.questionId;
                const optionId = btn.dataset.optionId;
                this.selectAnswer(questionId, optionId);
              });
            });

          // Update question counter
          if (this.elements.currentQuestionNum) {
            this.elements.currentQuestionNum.textContent = index + 1;
          }

          // Update progress bar
          const progressPercent =
            ((index + 1) / this.config.totalQuestions) * 100;
          if (this.elements.progressBar) {
            this.elements.progressBar.style.width = `${progressPercent}%`;
          }
        },

        selectAnswer(questionId, optionId) {
          this.answers[questionId] = optionId;

          // Update UI to show selected
          this.elements.questionContainer
            .querySelectorAll(".option-btn")
            .forEach((btn) => {
              btn.classList.remove("selected");
              if (btn.dataset.optionId === optionId) {
                btn.classList.add("selected");
              }
            });
        },

        previousQuestion() {
          if (this.currentQuestion > 0) {
            this.currentQuestion--;
            this.renderQuestion(this.currentQuestion);
            this.updateNavigation();
          }
        },

        nextQuestion() {
          if (this.currentQuestion < this.config.totalQuestions - 1) {
            this.currentQuestion++;
            this.renderQuestion(this.currentQuestion);
            this.updateNavigation();
          }
        },

        updateNavigation() {
          // Previous button
          if (this.elements.prevBtn) {
            this.elements.prevBtn.disabled = this.currentQuestion === 0;
          }

          // Next / Submit visibility
          const isLastQuestion =
            this.currentQuestion === this.config.totalQuestions - 1;

          if (this.elements.nextBtn) {
            this.elements.nextBtn.classList.toggle("d-none", isLastQuestion);
          }
          if (this.elements.submitBtn) {
            this.elements.submitBtn.classList.toggle("d-none", !isLastQuestion);
          }
        },

        async submitQuiz() {
          // Stop timer
          if (this.timerInterval) {
            clearInterval(this.timerInterval);
          }

          // Disable submit button
          if (this.elements.submitBtn) {
            this.elements.submitBtn.disabled = true;
            this.elements.submitBtn.innerHTML =
              '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
          }

          try {
            const response = await fetch("core/api/quizzies/submit.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                quiz_id: this.config.quizId,
                answers: this.answers,
              }),
            });

            const result = await response.json();

            if (result.success) {
              this.showResults(result.score);
            } else {
              throw new Error(result.message || "Failed to submit quiz");
            }
          } catch (error) {
            console.error("Quiz submit error:", error);
            alert("Failed to submit quiz: " + error.message);

            // Re-enable submit button
            if (this.elements.submitBtn) {
              this.elements.submitBtn.disabled = false;
              this.elements.submitBtn.innerHTML =
                '<i class="bi bi-check-lg me-2"></i>Submit Quiz';
            }
          }
        },

        showResults(score) {
          const modal = document.getElementById("quizResultsModal");
          if (!modal) return;

          const resultsHeader = document.getElementById("resultsHeader");
          const resultsIcon = document.getElementById("resultsIcon");
          const resultsTitle = document.getElementById("resultsTitle");
          const resultsSubtitle = document.getElementById("resultsSubtitle");
          const scorePercentage = document.getElementById("scorePercentage");
          const scoreDetails = document.getElementById("scoreDetails");

          // Determine result type
          let headerClass, iconHtml, title, subtitle;

          if (score.percentage >= 80) {
            headerClass = "bg-success text-white";
            iconHtml =
              '<i class="bi bi-trophy-fill" style="font-size: 4rem;"></i>';
            title = "🎉 Excellent! 🎉";
            subtitle = "You're a reading superstar!";
          } else if (score.percentage >= 60) {
            headerClass = "bg-primary text-white";
            iconHtml =
              '<i class="bi bi-hand-thumbs-up-fill" style="font-size: 4rem;"></i>';
            title = "👏 Great Job! 👏";
            subtitle = "Keep up the good work!";
          } else if (score.percentage >= 40) {
            headerClass = "bg-warning text-dark";
            iconHtml =
              '<i class="bi bi-emoji-smile" style="font-size: 4rem;"></i>';
            title = "Good Effort!";
            subtitle = "Try reading the book again for better results.";
          } else {
            headerClass = "bg-secondary text-white";
            iconHtml = '<i class="bi bi-book" style="font-size: 4rem;"></i>';
            title = "Keep Trying!";
            subtitle = "Read the book more carefully and try again.";
          }

          resultsHeader.className = `text-center py-4 ${headerClass}`;
          resultsIcon.innerHTML = iconHtml;
          resultsTitle.textContent = title;
          resultsSubtitle.textContent = subtitle;
          scorePercentage.textContent = `${Math.round(score.percentage)}%`;
          scoreDetails.textContent = `${score.correct} of ${score.total} correct`;

          // Show modal
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
        },

        escapeHtml(text) {
          if (!text) return "";
          const div = document.createElement("div");
          div.textContent = text;
          return div.innerHTML;
        },
      };

      QuizTaker.init();
    },

    /**
     * Initialize Quiz Progress (Account Page)
     */
    initQuizProgress() {
      const container = document.getElementById("quizProgressContainer");
      if (!container) return;

      const loadProgress = async () => {
        try {
          const response = await fetch(
            "core/api/quizzies/scores.php?view=summary"
          );
          const result = await response.json();

          if (result.success) {
            renderProgress(result.data);
          } else {
            container.innerHTML = `<div class="alert alert-warning">${
              result.message || "Failed to load progress"
            }</div>`;
          }
        } catch (error) {
          console.error("Error loading progress:", error);
          container.innerHTML =
            '<div class="alert alert-danger">Error loading progress data</div>';
        }
      };

      const renderProgress = (data) => {
        if (!data || data.length === 0) {
          container.innerHTML =
            '<p class="text-center text-muted py-4">No children profiles found.</p>';
          return;
        }

        const escapeHtml = (text) => {
          if (!text) return "";
          const div = document.createElement("div");
          div.textContent = text;
          return div.innerHTML;
        };

        container.innerHTML = data
          .map((child) => {
            const recentScoresHtml =
              child.recent_scores.length > 0
                ? child.recent_scores
                    .map(
                      (score) => `
                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                        <div>
                            <div class="fw-bold text-dark">${escapeHtml(
                              score.quiz_title
                            )}</div>
                            <small class="text-muted">
                                <i class="bi bi-book me-1"></i>${escapeHtml(
                                  score.book_title
                                )}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="badge ${
                              score.SCORE_PERCENTAGE >= 80
                                ? "bg-success"
                                : score.SCORE_PERCENTAGE >= 60
                                ? "bg-primary"
                                : "bg-warning text-dark"
                            }">
                                ${Math.round(score.SCORE_PERCENTAGE)}%
                            </div>
                            <div class="small text-muted mt-1">
                                ${new Date(
                                  score.DATE_COMPLETED
                                ).toLocaleDateString()}
                            </div>
                        </div>
                    </div>
                `
                    )
                    .join("")
                : '<p class="text-muted small fst-italic mb-0">No quizzes taken yet.</p>';

            return `
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-person-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">${escapeHtml(
                                  child.child_name
                                )}</h5>
                                <div class="d-flex gap-3 text-muted small mt-1">
                                    <span><i class="bi bi-check-circle me-1"></i>${
                                      child.total_quizzes
                                    } Quizzes</span>
                                    <span><i class="bi bi-graph-up me-1"></i>${
                                      child.average_score
                                    }% Avg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Recent Activity</h6>
                        <div class="recent-scores-list">
                            ${recentScoresHtml}
                        </div>
                    </div>
                </div>
            `;
          })
          .join("");
      };

      // Load data immediately
      loadProgress();

      // Reload when tab is shown
      const tabBtn = document.getElementById("quiz-progress-tab");
      if (tabBtn) {
        tabBtn.addEventListener("shown.bs.tab", loadProgress);
      }
    },

    /**
     * Initialize Educator Dashboard
     */
    initEduDashboard() {
      const container = document.getElementById("eduStatsContainer");
      if (!container) return;

      const elements = {
        tableBody: document.getElementById("eduScoresBody"),
        statTotal: document.getElementById("statTotalStudents"),
        statAvg: document.getElementById("statClassAverage"),
        statDiff: document.getElementById("statDifficultQuiz"),
        showingCount: document.getElementById("showingCount"),
      };

      let allData = [];

      const loadDashboardData = async () => {
        try {
          // Fetch ALL data without any filters
          const url = "core/api/quizzies/scores.php?view=detailed";

          const response = await fetch(url);
          const result = await response.json();

          if (result.success) {
            allData = result.data;
            updateDashboard(result.data, result.summary);
          } else {
            showError(result.message);
          }
        } catch (error) {
          console.error("Error loading dashboard:", error);
          showError("Failed to load dashboard data");
        }
      };

      const updateDashboard = (data, summary) => {
        // Update Stats
        if (summary) {
          elements.statAvg.textContent = `${summary.class_average}%`;
          elements.statDiff.textContent = summary.most_difficult_quiz || "N/A";
          // Total students is tricky from just scores, but we can count unique IDs in data
          const uniqueStudents = new Set(data.map((d) => d.child_id)).size;
          elements.statTotal.textContent = uniqueStudents;
        }

        // Update Table
        if (data.length === 0) {
          elements.tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    No quiz records found.
                </td>
            </tr>
          `;
          elements.showingCount.textContent = "Showing 0 results";
          return;
        }

        const escapeHtml = (text) => {
          if (!text) return "";
          const div = document.createElement("div");
          div.textContent = text;
          return div.innerHTML;
        };

        elements.tableBody.innerHTML = data
          .map(
            (row) => `
            <tr>
                <td class="px-4">
                    <div class="fw-bold">${escapeHtml(row.child_name)}</div>
                </td>
                <td class="px-4">${escapeHtml(row.quiz_title)}</td>
                <td class="px-4 text-muted small">${escapeHtml(
                  row.book_title
                )}</td>
                <td class="px-4 text-center">
                    <span class="badge ${
                      row.SCORE_PERCENTAGE >= 80
                        ? "bg-success"
                        : row.SCORE_PERCENTAGE >= 60
                        ? "bg-primary"
                        : "bg-warning text-dark"
                    }">${Math.round(row.SCORE_PERCENTAGE)}%</span>
                </td>
                <td class="px-4 text-center small text-muted">
                    ${new Date(row.DATE_COMPLETED).toLocaleDateString()}
                </td>
                <td class="px-4 text-end">
                    <span class="badge bg-light text-muted">${
                      row.CORRECT_ANSWERS
                    }/${row.TOTAL_QUESTIONS}</span>
                </td>
            </tr>
        `
          )
          .join("");

        elements.showingCount.textContent = `Showing ${data.length} results`;
      };

      const showError = (msg) => {
        elements.tableBody.innerHTML = `
            <tr><td colspan="6" class="text-center text-danger py-4">${msg}</td></tr>
        `;
      };

      // Initial Load
      loadDashboardData();
    },
  };

  // Run App
  // Children Management Functions (Global scope for inline onclick handlers)
  window.submitAddChild = function () {
    const form = document.getElementById("addChildForm");
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => (data[key] = value));

    fetch("core/api/children/add.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((r) => r.json())
      .then((result) => {
        if (result.success) {
          alert("Child added successfully!");
          location.reload();
        } else {
          alert("Error: " + result.message);
        }
      })
      .catch((err) => alert("Failed: " + err.message));
  };

  window.openEditChildModal = function (childId) {
    fetch(`core/api/children/get.php?id=${childId}`)
      .then((r) => r.json())
      .then((result) => {
        if (result.success) {
          const child = result.child;
          document.getElementById("editChildId").value = child.ID;
          document.getElementById("editChildName").value = child.NAME;
          document.getElementById("editChildDob").value = child.DOB;
          new bootstrap.Modal(document.getElementById("editChildModal")).show();
        } else {
          alert("Error: " + result.message);
        }
      });
  };

  window.submitEditChild = function () {
    const data = {
      id: document.getElementById("editChildId").value,
      name: document.getElementById("editChildName").value,
      dob: document.getElementById("editChildDob").value,
    };

    fetch("core/api/children/update.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((r) => r.json())
      .then((result) => {
        if (result.success) {
          alert("Child updated successfully!");
          location.reload();
        } else {
          alert("Error: " + result.message);
        }
      })
      .catch((err) => alert("Failed: " + err.message));
  };

  window.showDeleteChildModal = function (childId) {
    fetch("core/api/children/delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: childId }),
    })
      .then((r) => r.json())
      .then((result) => {
        if (result.success) {
          alert("Child deleted successfully!");
          location.reload();
        } else {
          alert("Error: " + result.message);
        }
      })
      .catch((err) => alert("Failed: " + err.message));
  };

  window.copyToClipboard = function (text) {
    navigator.clipboard.writeText(text).then(() => {
      alert("Copied to clipboard: " + text);
    });
  };

  window.clearAllFavorites = function () {
    if (
      !confirm(
        "Are you sure you want to remove all favorites? This cannot be undone."
      )
    ) {
      return;
    }

    fetch("core/api/favorites/clear.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
    })
      .then((r) => r.json())
      .then((result) => {
        if (result.success) {
          alert("All favorites cleared successfully!");
          location.reload();
        } else {
          alert("Error: " + result.message);
        }
      })
      .catch((err) => alert("Failed: " + err.message));
  };

  App.init();
})();
