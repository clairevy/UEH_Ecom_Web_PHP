// Định nghĩa hàm changeImage ngay lập tức
function changeImage(element, imageUrl) {
  const mainImage = document.getElementById("mainImage");
  if (mainImage) {
    mainImage.src = imageUrl;
  }

  // Bỏ active ở tất cả thumbnail
  document.querySelectorAll(".thumbnail-item").forEach((item) => {
    item.classList.remove("active");
  });
  // Active thumbnail được chọn
  element.classList.add("active");
}

// Chọn option (màu, size)
function selectOption(element) {
  let parent = element.parentNode;
  // Bỏ active ở nhóm hiện tại
  parent.querySelectorAll(".option-btn").forEach((btn) => {
    btn.classList.remove("active");
  });
  // Active option được chọn
  element.classList.add("active");
}

// Chuyển tab (mô tả / bổ sung / đánh giá)
function switchTab(element, tabId) {
  // Reset button active
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.classList.remove("active");
  });
  element.classList.add("active");

  // Reset content active
  document.querySelectorAll(".tab-content").forEach((content) => {
    content.classList.remove("active");
  });
  document.getElementById(tabId).classList.add("active");
}

document.addEventListener("DOMContentLoaded", function () {
  // Search functionality
  const searchBtn = document.querySelector(".search-btn");
  if (searchBtn) {
    searchBtn.addEventListener("click", function () {
      const searchTerm = document.querySelector(".search-input").value;
      if (searchTerm.trim()) {
        window.location.href = `/Ecom_website/customer/products?search=${encodeURIComponent(
          searchTerm.trim()
        )}`;
      }
    });
  }

  // Handle Enter key in search
  const searchInput = document.querySelector(".search-input");
  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        const searchBtn = document.querySelector(".search-btn");
        if (searchBtn) searchBtn.click();
      }
    });
  }
});
