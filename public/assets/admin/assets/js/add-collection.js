// JS extracted from add-collection.html
window.pageConfig = {
    sidebar: {
        brandName: 'JEWELLERY',
        activePage: 'collections',
        links: {
            dashboard: '../index.html',
            products: 'products.html',
            categories: 'categories.html',
            collections: 'collections.html',
            orders: 'orders.html',
            customers: 'customers.html'
        }
    }
};

if (window.ComponentManager) {
    window.ComponentManager.init().then(() => {
        console.log('Add collection page initialized');
        initializeAddCollectionPage();
    });
}

function initializeAddCollectionPage() {
    const collectionNameInput = document.getElementById('collectionName');
    const collectionSlugInput = document.getElementById('collectionSlug');

    if (collectionNameInput) {
        collectionNameInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            collectionSlugInput.value = slug;
        });
    }

    const coverImageInput = document.getElementById('coverImageInput');
    if (coverImageInput) {
        coverImageInput.addEventListener('change', function(e) {
            handleImageUpload(e, 'cover', 5);
        });
    }

    const thumbnailImageInput = document.getElementById('thumbnailImageInput');
    if (thumbnailImageInput) {
        thumbnailImageInput.addEventListener('change', function(e) {
            handleImageUpload(e, 'thumbnail', 2);
        });
    }

    const bannerImageInput = document.getElementById('bannerImageInput');
    if (bannerImageInput) {
        bannerImageInput.addEventListener('change', function(e) {
            handleImageUpload(e, 'banner', 5);
        });
    }

    const form = document.getElementById('addCollectionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitCollection();
        });
    }
}

function handleImageUpload(event, type, maxSizeMB) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > maxSizeMB * 1024 * 1024) {
        alert(`Ảnh ${type} vượt quá ${maxSizeMB}MB!`);
        event.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById(`${type}PreviewImg`);
        if (img) img.src = e.target.result;
        const placeholder = document.getElementById(`${type}UploadPlaceholder`);
        if (placeholder) placeholder.style.display = 'none';
        const preview = document.getElementById(`${type}Preview`);
        if (preview) preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removeCoverImage() {
    const input = document.getElementById('coverImageInput');
    if (input) input.value = '';
    const placeholder = document.getElementById('coverUploadPlaceholder');
    if (placeholder) placeholder.style.display = 'block';
    const preview = document.getElementById('coverPreview');
    if (preview) preview.style.display = 'none';
}

function removeThumbnailImage() {
    const input = document.getElementById('thumbnailImageInput');
    if (input) input.value = '';
    const placeholder = document.getElementById('thumbnailUploadPlaceholder');
    if (placeholder) placeholder.style.display = 'block';
    const preview = document.getElementById('thumbnailPreview');
    if (preview) preview.style.display = 'none';
}

function removeBannerImage() {
    const input = document.getElementById('bannerImageInput');
    if (input) input.value = '';
    const placeholder = document.getElementById('bannerUploadPlaceholder');
    if (placeholder) placeholder.style.display = 'block';
    const preview = document.getElementById('bannerPreview');
    if (preview) preview.style.display = 'none';
}

function saveDraft() {
    const collectionName = (document.getElementById('collectionName') || {}).value || '';
    if (!collectionName.trim()) {
        alert('Vui lòng nhập tên collection!');
        return;
    }
    console.log('Saving draft...');
    alert(`Đã lưu nháp collection "${collectionName}" thành công!`);
}

function submitCollection() {
    const collectionName = (document.getElementById('collectionName') || {}).value || '';
    const collectionSlug = (document.getElementById('collectionSlug') || {}).value || '';
    const collectionDescription = (document.getElementById('collectionDescription') || {}).value || '';
    const collectionContent = (document.getElementById('collectionContent') || {}).value || '';
    const collectionType = (document.getElementById('collectionType') || {}).value || '';
    const collectionStatus = (document.getElementById('collectionStatus') || {}).value || '';
    const coverImage = (document.getElementById('coverImageInput') || {}).files ? document.getElementById('coverImageInput').files[0] : null;

    if (!collectionName.trim()) {
        alert('Vui lòng nhập tên collection!');
        return;
    }

    if (!coverImage) {
        alert('Vui lòng upload ảnh bìa!');
        return;
    }

    const collectionData = {
        name: collectionName,
        slug: collectionSlug,
        description: collectionDescription,
        content: collectionContent,
        type: collectionType,
        status: collectionStatus,
        startDate: (document.getElementById('startDate') || {}).value || '',
        endDate: (document.getElementById('endDate') || {}).value || '',
        tags: (document.getElementById('collectionTags') || {}).value || '',
        coverImage: coverImage.name
    };

    console.log('Creating collection:', collectionData);
    alert(`Đã tạo collection "${collectionName}" thành công!`);
    setTimeout(() => {
        window.location.href = 'collections.html';
    }, 1000);
}
