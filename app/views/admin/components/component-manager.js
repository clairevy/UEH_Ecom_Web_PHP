/**
 * Component Manager for Jewelry Admin Dashboard
 * Handles loading and managing reusable components
 */

class ComponentManager {
  constructor() {
    this.components = {};
    this.basePath = this.getBasePath();
  }

  /**
   * Get the base path for components based on current page location
   */
  getBasePath() {
    const currentPath = window.location.pathname;
    if (currentPath.includes("/pages/")) {
      return "../components/";
    }
    return "./components/";
  }

  /**
   * Load a component from file
   */
  async loadComponent(componentName) {
    if (this.components[componentName]) {
      return this.components[componentName];
    }

    try {
      const response = await fetch(`${this.basePath}${componentName}.html`);
      if (!response.ok) {
        throw new Error(`Failed to load component: ${componentName}`);
      }
      const html = await response.text();
      this.components[componentName] = html;
      return html;
    } catch (error) {
      console.error(`Error loading component ${componentName}:`, error);
      return "";
    }
  }

  /**
   * Render sidebar component
   */
  async renderSidebar(config = {}) {
    const html = await this.loadComponent("sidebar");
    const container = document.getElementById("sidebar-container");
    if (container) {
      container.innerHTML = html;

      // Configure sidebar
      this.configureSidebar(config);
    }
  }

  /**
   * Configure sidebar with specific settings
   */
  configureSidebar(config) {
    const sidebar = document.querySelector(".sidebar");
    if (!sidebar) return;

    // Set brand name
    if (config.brandName) {
      const brandElement = sidebar.querySelector("[data-brand]");
      if (brandElement) {
        brandElement.textContent = config.brandName;
      }
    }

    // Set active page
    if (config.activePage) {
      sidebar.querySelectorAll(".sidebar-nav-link").forEach((link) => {
        link.classList.remove("active");
        const linkPage = link.getAttribute("data-page");
        if (linkPage === config.activePage) {
          link.classList.add("active");
        }
        // Handle customer-related pages
        if (
          (config.activePage === "customers" ||
            config.activePage === "customer-roles") &&
          (linkPage === "customers" || linkPage === "customer-roles")
        ) {
          // Keep both customer nav items highlighted when on any customer page
          if (linkPage === config.activePage) {
            link.classList.add("active");
          }
        }
      });
    }

    // Configure categories
    if (config.categories) {
      const categoriesList = sidebar.querySelector("#categories-list");
      const categoriesTitle = sidebar.querySelector("[data-categories-title]");

      if (categoriesTitle && config.categoriesTitle) {
        categoriesTitle.textContent = config.categoriesTitle;
      }

      if (categoriesList && config.categories.length > 0) {
        categoriesList.innerHTML = config.categories
          .map(
            (cat) => `
                    <div class="d-flex justify-content-between text-light">
                        <span>${cat.name}</span>
                        <span class="badge bg-secondary">${cat.count}</span>
                    </div>
                `
          )
          .join("");
      }
    }

    // Set navigation links
    if (config.links) {
      this.updateSidebarLinks(config.links);
    }

    // Configure add category button
    this.configureAddCategoryButton();

    // Configure all navigation
    this.configureAllNavigation();
  }

  /**
   * Update sidebar navigation links
   */
  updateSidebarLinks(links) {
    const navLinks = document.querySelectorAll(".sidebar-nav-link");
    navLinks.forEach((link) => {
      const page = link.getAttribute("data-page");
      if (links[page]) {
        link.href = links[page];
      }
    });
  }

  /**
   * Configure add category button with proper relative path
   */
  configureAddCategoryButton() {
    const addCategoryBtn = document.getElementById("add-category-btn");
    if (addCategoryBtn) {
      addCategoryBtn.addEventListener("click", () => {
        const currentPath = window.location.pathname;
        let targetPath;

        if (currentPath.includes("/pages/")) {
          // Already in pages directory
          targetPath = "add-category.html";
        } else {
          // In root directory
          targetPath = "pages/add-category.html";
        }

        window.location.href = targetPath;
      });
    }
  }

  /**
   * Configure all navigation links with proper relative paths
   */
  configureAllNavigation() {
    const allNavLinks = document.querySelectorAll(
      ".sidebar-nav-link[data-page]"
    );
    allNavLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();

        const page = link.getAttribute("data-page");
        const currentPath = window.location.pathname;
        let targetPath;

        if (page === "dashboard") {
          // Handle dashboard specially
          if (currentPath.includes("/pages/")) {
            targetPath = "dashboard.php";
          } else {
            targetPath = "index.php";
          }
        } else {
          // Handle other pages - redirect through index.php
          if (currentPath.includes("/pages/")) {
            // Already in pages directory - go back to index.php
            targetPath = `../index.php?page=${page}`;
          } else {
            // In root directory - redirect through index.php
            targetPath = `index.php?page=${page}`;
          }
        }

        window.location.href = targetPath;
      });
    });
  }

  /**
   * Render header component
   */
  async renderHeader(config = {}) {
    const html = await this.loadComponent("header");
    const container = document.getElementById("header-container");
    if (container) {
      container.innerHTML = html;
      this.configureHeader(config);
    }
  }

  /**
   * Configure header with specific settings
   */
  configureHeader(config) {
    const header = document.querySelector(".header");
    if (!header) return;

    // Set page title
    if (config.title) {
      const titleElement = header.querySelector("[data-page-title]");
      if (titleElement) {
        titleElement.textContent = config.title;
      }
    }

    // Set breadcrumb
    if (config.breadcrumb) {
      const breadcrumbElement = header.querySelector("[data-breadcrumb]");
      if (breadcrumbElement) {
        breadcrumbElement.textContent = config.breadcrumb;
      }
    }

    // Show/hide date range
    if (config.showDateRange) {
      const dateRangeSection = header.querySelector("#date-range-section");
      if (dateRangeSection) {
        dateRangeSection.style.display = "flex";
        if (config.dateRange) {
          const dateRangeElement = header.querySelector("[data-date-range]");
          if (dateRangeElement) {
            dateRangeElement.textContent = config.dateRange;
          }
        }
      }
    }

    // Add additional actions
    if (config.additionalActions) {
      const actionsContainer = header.querySelector("#additional-actions");
      if (actionsContainer) {
        actionsContainer.innerHTML = config.additionalActions;
      }
    }
  }

  /**
   * Render pagination component
   */
  async renderPagination(container, config = {}) {
    const html = await this.loadComponent("pagination");
    if (container) {
      container.innerHTML = html;
      this.configurePagination(container, config);
    }
  }

  /**
   * Configure pagination
   */
  configurePagination(container, config) {
    const pagination = container.querySelector("[data-pagination-label]");
    if (!pagination) return;

    if (config.ariaLabel) {
      pagination.setAttribute("aria-label", config.ariaLabel);
    }

    // Configure current page and total pages
    if (config.currentPage || config.totalPages) {
      // This would require more complex logic to generate page numbers
      // For now, keeping the static structure
    }
  }

  /**
   * Render stats card
   */
  async renderStatsCard(container, config = {}) {
    const html = await this.loadComponent("stats-card");
    if (container) {
      container.innerHTML = html;
      this.configureStatsCard(container, config);
    }
  }

  /**
   * Configure stats card
   */
  configureStatsCard(container, config) {
    const card = container.querySelector(".stats-card");
    if (!card) return;

    if (config.value) {
      const valueElement = card.querySelector("[data-value]");
      if (valueElement) valueElement.textContent = config.value;
    }

    if (config.label) {
      const labelElement = card.querySelector("[data-label]");
      if (labelElement) labelElement.textContent = config.label;
    }

    if (config.percentage) {
      const percentageElement = card.querySelector("[data-percentage]");
      if (percentageElement) percentageElement.textContent = config.percentage;
    }

    if (config.comparison) {
      const comparisonElement = card.querySelector("[data-comparison]");
      if (comparisonElement) comparisonElement.textContent = config.comparison;
    }

    // Set positive/negative trend
    if (config.trend) {
      const changeElement = card.querySelector(".stats-change");
      if (changeElement) {
        changeElement.className = `stats-change ${config.trend}`;
      }
    }
  }

  /**
   * Render product card
   */
  async renderProductCard(container, config = {}) {
    const html = await this.loadComponent("product-card");
    if (container) {
      const cardElement = document.createElement("div");
      cardElement.innerHTML = html;
      container.appendChild(cardElement.firstElementChild);

      this.configureProductCard(container.lastElementChild, config);
    }
  }

  /**
   * Configure product card
   */
  configureProductCard(container, config) {
    if (!container) return;

    // Set product data
    const fields = [
      { selector: "[data-product-name]", value: config.name },
      { selector: "[data-product-category]", value: config.category },
      { selector: "[data-product-price]", value: config.price },
      { selector: "[data-product-summary]", value: config.summary },
      { selector: "[data-sales-count]", value: config.salesCount },
      { selector: "[data-stock-count]", value: config.stockCount },
    ];

    fields.forEach((field) => {
      if (field.value !== undefined) {
        const element = container.querySelector(field.selector);
        if (element) element.textContent = field.value;
      }
    });

    // Set product image
    if (config.image) {
      const imageElement = container.querySelector("[data-product-image]");
      if (imageElement) {
        imageElement.src = config.image;
        imageElement.alt = config.name || "Product";
      }
    }

    // Set edit link
    if (config.editLink) {
      const editElement = container.querySelector("[data-edit-link]");
      if (editElement) {
        editElement.href = config.editLink;
      }
    }
  }

  /**
   * Render table card
   */
  async renderTableCard(container, config = {}) {
    const html = await this.loadComponent("table-card");
    if (container) {
      container.innerHTML = html;
      this.configureTableCard(container, config);
    }
  }

  /**
   * Configure table card
   */
  configureTableCard(container, config) {
    const card = container.querySelector(".table-card");
    if (!card) return;

    // Set table title
    if (config.title) {
      const titleElement = card.querySelector("[data-table-title]");
      if (titleElement) titleElement.textContent = config.title;
    }

    // Set table headers
    if (config.headers) {
      const headerContainer = card.querySelector("[data-table-header]");
      if (headerContainer) {
        headerContainer.innerHTML = `<tr>${config.headers
          .map((header) => `<th>${header}</th>`)
          .join("")}</tr>`;
      }
    }

    // Set table actions
    if (config.actions) {
      const actionsContainer = card.querySelector("[data-table-actions]");
      if (actionsContainer) {
        actionsContainer.innerHTML = config.actions
          .map(
            (action) =>
              `<li><a class="dropdown-item" href="${action.link || "#"}">${
                action.text
              }</a></li>`
          )
          .join("");
      }
    }
  }

  /**
   * Initialize components for a page
   */
  async initializePage(pageConfig) {
    // Render sidebar
    if (pageConfig.sidebar !== false) {
      await this.renderSidebar(pageConfig.sidebar || {});
    }

    // Render header
    if (pageConfig.header !== false) {
      await this.renderHeader(pageConfig.header || {});
    }

    // Initialize any page-specific components
    if (pageConfig.components) {
      for (const [componentType, configs] of Object.entries(
        pageConfig.components
      )) {
        if (Array.isArray(configs)) {
          configs.forEach((config, index) => {
            const container = document.querySelector(
              config.container || `#${componentType}-${index}`
            );
            if (container) {
              this[
                `render${
                  componentType.charAt(0).toUpperCase() + componentType.slice(1)
                }`
              ](container, config);
            }
          });
        }
      }
    }
  }
}

// Create global instance
window.ComponentManager = new ComponentManager();

// Auto-initialize if page config exists
document.addEventListener("DOMContentLoaded", () => {
  if (window.pageConfig) {
    window.ComponentManager.initializePage(window.pageConfig);
  }
});
