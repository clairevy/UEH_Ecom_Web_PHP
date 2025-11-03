/**
 * Add Product JavaScript - Production Ready
 */

// Class để quản lý Product Form
class ProductFormManager {
  constructor() {
    // DOM elements
    this.form = document.getElementById("addProductForm");
    this.fileInput = document.getElementById("product_images");
    this.imageList = document.getElementById("imageList");
    this.imagePreviewContainer = document.getElementById(
      "imagePreviewContainer"
    );
    // Fix: uploadArea element doesn't exist in HTML, use upload area with correct selector
    this.uploadArea = document.querySelector(".upload-area");

    this.init();
  }

  init() {
    if (this.form) {
      this.form.addEventListener("submit", (e) => this.handleFormSubmit(e));
    }

    if (this.fileInput) {
      this.fileInput.addEventListener("change", (e) => {
        this.handleFileSelect(e.target);
      });
    }

    this.setupSKUGenerator();
    this.setupImagePreview();
    this.setupDragAndDrop();
  }

  /**
   * Setup image preview functionality
   */
  setupImagePreview() {
    console.log("Setting up image preview");

    if (!this.fileInput) {
      console.log("File input not found");
      return;
    }

    console.log("File input found:", this.fileInput);
  }

  /**
   * Setup drag and drop functionality
   */
  setupDragAndDrop() {
    if (!this.uploadArea) {
      console.log("Upload area not found");
      return;
    }

    this.uploadArea.addEventListener("dragover", (e) => this.handleDragOver(e));
    this.uploadArea.addEventListener("dragleave", (e) =>
      this.handleDragLeave(e)
    );
    this.uploadArea.addEventListener("drop", (e) => this.handleDrop(e));
  }

  /**
   * Handle drag over event
   */
  handleDragOver(e) {
    e.preventDefault();
    console.log("Drag over");
    e.currentTarget.style.borderColor = "#007bff";
    e.currentTarget.style.backgroundColor = "#f8f9fa";
  }

  /**
   * Handle drag leave event
   */
  handleDragLeave(e) {
    e.preventDefault();
    console.log("Drag leave");
    e.currentTarget.style.borderColor = "#dee2e6";
    e.currentTarget.style.backgroundColor = "transparent";
  }

  /**
   * Handle drop event
   */
  handleDrop(e) {
    e.preventDefault();
    console.log("Drop event", e.dataTransfer.files.length, "files");

    e.currentTarget.style.borderColor = "#dee2e6";
    e.currentTarget.style.backgroundColor = "transparent";

    const files = e.dataTransfer.files;
    if (files.length > 0) {
      if (this.fileInput) {
        // Create new file list using DataTransfer API
        const dt = new DataTransfer();
        for (let file of files) {
          dt.items.add(file);
        }
        this.fileInput.files = dt.files;

        // Trigger change event to display preview
        this.handleFileSelect(this.fileInput);
      }
    }
  }

  /**
   * Handle form submit - Client-side validation
   */
  handleFormSubmit(e) {
    console.log("🔍 Form submit handler called");

    const name = document.getElementById("name").value.trim();
    const basePrice = document.getElementById("base_price").value;
    const sku = document.getElementById("sku").value.trim();

    let errors = [];

    if (name.length < 3) {
      errors.push("Tên sản phẩm phải có ít nhất 3 ký tự");
    }

    if (!basePrice || parseFloat(basePrice) <= 0) {
      errors.push("Giá sản phẩm phải lớn hơn 0");
    }

    if (!sku) {
      errors.push("SKU không được để trống");
    }

    // Validate categories
    const categoryCheckboxes = document.querySelectorAll(
      ".category-checkbox:checked"
    );
    if (categoryCheckboxes.length === 0) {
      errors.push("Phải chọn ít nhất 1 danh mục");
    }

    // Validate images - BẮT BUỘC phải có ảnh
    const fileInput = document.getElementById("product_images");
    if (!fileInput.files || fileInput.files.length === 0) {
      errors.push("Phải upload ít nhất 1 ảnh sản phẩm");
    } else {
      console.log(`✅ Found ${fileInput.files.length} file(s) to upload`);
    }

    // Validate variants
    const variantItems = document.querySelectorAll(".variant-item");
    if (variantItems.length === 0) {
      errors.push("Phải có ít nhất 1 biến thể sản phẩm");
    } else {
      let hasValidVariant = false;
      variantItems.forEach((item, index) => {
        const size = item.querySelector(".variant-size").value;
        const color = item.querySelector(".variant-color").value;
        const price = item.querySelector(".variant-price").value;
        const stock = item.querySelector(".variant-stock").value;

        if (size && color && price && price > 0 && stock >= 0) {
          hasValidVariant = true;
        } else if (size || color || price || stock) {
          // Only error if partially filled
          errors.push(
            `Biến thể #${index + 1}: Vui lòng điền đầy đủ thông tin hợp lệ`
          );
        }
      });

      if (!hasValidVariant) {
        errors.push("Phải có ít nhất 1 biến thể hợp lệ");
      }
    }

    // If validation fails, prevent submit
    if (errors.length > 0) {
      e.preventDefault();
      console.error("❌ CLIENT VALIDATION FAILED:", errors);
      alert("Lỗi:\n" + errors.join("\n"));
      return false;
    }

    console.log("✅ CLIENT VALIDATION PASSED");
    console.log("📤 Submitting form to:", this.form.action);

    // Disable submit button to prevent double submission
    const submitBtn = this.form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
    }

    // Log FormData for debugging
    const formData = new FormData(this.form);
    console.log("📋 Form data being sent:");
    for (let pair of formData.entries()) {
      if (pair[1] instanceof File) {
        console.log(`  ${pair[0]}:`, pair[1].name, `(${pair[1].size} bytes)`);
      } else {
        console.log(`  ${pair[0]}:`, pair[1]);
      }
    }

    // Allow form to submit normally - DO NOT PREVENT DEFAULT HERE
    console.log("🚀 Form submitting...");
    return true;
  }

  /**
   * Setup auto SKU generator
   */
  setupSKUGenerator() {
    const nameInput = document.getElementById("name");
    const skuInput = document.getElementById("sku");

    if (nameInput && skuInput) {
      nameInput.addEventListener("input", function () {
        if (!skuInput.value) {
          const name = this.value.trim();
          if (name) {
            const sku = generateSKU(name);
            skuInput.value = sku;
          }
        }
      });
    }
  }

  /**
   * Handle file selection
   */
  handleFileSelect(input) {
    console.log("handleFileSelect called with input:", input);

    const files = input.files;
    const imageList = this.imageList;
    const previewContainer = this.imagePreviewContainer;

    console.log("Files:", files ? files.length : 0);
    console.log("ImageList element:", imageList);
    console.log("PreviewContainer element:", previewContainer);

    if (!files || files.length === 0) {
      if (previewContainer) previewContainer.style.display = "none";
      return;
    }

    if (imageList) imageList.innerHTML = "";
    if (previewContainer) previewContainer.style.display = "block";

    Array.from(files).forEach((file, index) => {
      console.log("Processing file:", file.name, file.type, file.size);

      if (file.type.startsWith("image/")) {
        if (file.size > 5242880) {
          alert(`File ${file.name} quá lớn. Kích thước tối đa: 5MB`);
          return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
          console.log("File read successfully:", file.name);

          const imageItem = this.createImagePreviewItem(
            e.target.result,
            file.name,
            index
          );
          if (imageList) {
            imageList.appendChild(imageItem);
          }
        };
        reader.onerror = (e) => {
          console.error("FileReader error:", e);
        };
        reader.readAsDataURL(file);
      } else {
        console.log("File is not an image:", file.type);
      }
    });
  }

  /**
   * Create image preview item
   */
  createImagePreviewItem(imageSrc, fileName, index) {
    const div = document.createElement("div");
    div.className = "col-md-3 mb-3";
    div.setAttribute("data-index", index);

    div.innerHTML = `
      <div class="card">
        <img src="${imageSrc}" 
             class="card-img-top" 
             style="height: 150px; object-fit: cover;" 
             alt="${fileName}">
        <div class="card-body p-2">
          <p class="card-text small text-muted mb-0">${fileName}</p>
          <p class="card-text small text-muted">${this.formatFileSize(
            this.getFileSizeFromInput(index)
          )}</p>
        </div>
      </div>
    `;

    return div;
  }

  /**
   * Format file size
   */
  formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";

    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
  }

  /**
   * Get file size from input by index
   */
  getFileSizeFromInput(index) {
    if (this.fileInput && this.fileInput.files && this.fileInput.files[index]) {
      return this.fileInput.files[index].size;
    }
    return 0;
  }
}

// =================== HELPER FUNCTIONS ===================

/**
 * Remove image preview
 */
function removeImagePreview(button) {
  const imageItem = button.closest("[data-index]");
  if (imageItem) {
    imageItem.remove();

    const container = document.getElementById("imagePreviewContainer");
    const imageList = document.getElementById("imageList");
    if (container && imageList && imageList.children.length === 0) {
      container.style.display = "none";
    }
  }
}

/**
 * Generate SKU from product name
 */
function generateSKU(name) {
  return name
    .toUpperCase()
    .replace(/[^A-Z0-9\s]/g, "")
    .replace(/\s+/g, "-")
    .substring(0, 20);
}

/**
 * Truncate file name for display
 */
function truncateFileName(fileName, maxLength) {
  if (fileName.length <= maxLength) {
    return fileName;
  }

  const ext = fileName.split(".").pop();
  const nameWithoutExt = fileName.substring(
    0,
    fileName.length - ext.length - 1
  );
  const truncated =
    nameWithoutExt.substring(0, maxLength - ext.length - 4) + "...";

  return truncated + "." + ext;
}

// =================== DRAG & DROP FUNCTIONS ===================

/**
 * Handle drag over event
 */
function handleDragOver(event) {
  event.preventDefault();
  event.stopPropagation();

  const uploadArea = event.currentTarget;
  uploadArea.style.borderColor = "#007bff";
  uploadArea.style.backgroundColor = "#f8f9ff";
}

/**
 * Handle drag leave event
 */
function handleDragLeave(event) {
  event.preventDefault();
  event.stopPropagation();

  const uploadArea = event.currentTarget;
  uploadArea.style.borderColor = "var(--border-color)";
  uploadArea.style.backgroundColor = "transparent";
}

/**
 * Handle drop event
 */
function handleDrop(event) {
  event.preventDefault();
  event.stopPropagation();

  const uploadArea = event.currentTarget;
  uploadArea.style.borderColor = "var(--border-color)";
  uploadArea.style.backgroundColor = "transparent";

  const files = event.dataTransfer.files;
  const fileInput = document.getElementById("product_images");

  if (files.length > 0) {
    console.log("📁 Files dropped:", files.length);

    // Create new DataTransfer object to assign files to input
    const dataTransfer = new DataTransfer();
    Array.from(files).forEach((file) => {
      if (file.type.startsWith("image/")) {
        dataTransfer.items.add(file);
      }
    });

    // Assign files to input
    fileInput.files = dataTransfer.files;

    // Trigger change event to update preview
    const changeEvent = new Event("change", { bubbles: true });
    fileInput.dispatchEvent(changeEvent);
  }
}

// =================== INITIALIZE ===================

document.addEventListener("DOMContentLoaded", function () {
  console.log("🚀 Add Product JS loaded!");
  console.log("File input element:", document.getElementById("product_images"));
  console.log(
    "Image preview container:",
    document.getElementById("imagePreviewContainer")
  );

  const productFormManager = new ProductFormManager();
  console.log("✅ ProductFormManager initialized");
});
