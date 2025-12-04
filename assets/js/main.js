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
