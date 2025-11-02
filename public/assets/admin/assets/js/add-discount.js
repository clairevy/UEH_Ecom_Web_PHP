// Form handling
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates
    const now = new Date();
    const startDate = new Date(now.getTime() + 24 * 60 * 60 * 1000); // Tomorrow
    const endDate = new Date(now.getTime() + 30 * 24 * 60 * 60 * 1000); // 30 days later
    
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (startDateInput) startDateInput.value = startDate.toISOString().slice(0, 16);
    if (endDateInput) endDateInput.value = endDate.toISOString().slice(0, 16);

    // Real-time preview updates
    const fieldsToWatch = [
        'discountCode', 'programName', 'description', 'discountType',
        'discountValue', 'maxDiscount', 'minOrderValue', 'usageLimit',
        'startDate', 'endDate', 'status'
    ];
    
    fieldsToWatch.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            const eventType = element.tagName === 'SELECT' ? 'change' : 'input';
            element.addEventListener(eventType, updatePreview);
        }
    });

    // Form submission
    const discountForm = document.getElementById('discountForm');
    if (discountForm) {
        discountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                return;
            }

            // Collect form data
            const formData = collectFormData();
            
            console.log('Discount data:', formData);
            alert('Đã tạo mã giảm giá thành công!');
            
            // Redirect to discounts list
            window.location.href = 'discounts.html';
        });
    }
});

function toggleDiscountFields() {
    const discountType = document.getElementById('discountType').value;
    const valueField = document.getElementById('discountValueField');
    const maxField = document.getElementById('maxDiscountField');
    
    if (discountType === 'percentage') {
        if (valueField) valueField.style.display = 'block';
        if (maxField) maxField.style.display = 'block';
        const discountValue = document.getElementById('discountValue');
        if (discountValue) discountValue.placeholder = '20';
    } else {
        if (valueField) valueField.style.display = 'block';
        if (maxField) maxField.style.display = 'none';
        const discountValue = document.getElementById('discountValue');
        if (discountValue) discountValue.placeholder = '50000';
    }
    updatePreview();
}

function updatePreview() {
    // Update preview based on form values
    console.log('Updating preview...');
}

function validateForm() {
    const code = document.getElementById('discountCode').value;
    const name = document.getElementById('programName').value;
    
    if (!code || !name) {
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        return false;
    }
    return true;
}

function collectFormData() {
    return {
        code: document.getElementById('discountCode').value,
        name: document.getElementById('programName').value,
        description: document.getElementById('description').value,
        type: document.getElementById('discountType').value,
        value: document.getElementById('discountValue').value,
        // Add more fields as needed
    };
}

function saveDraft() {
    console.log('Saving draft...');
    alert('Đã lưu nháp!');
}

function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const codeInput = document.getElementById('discountCode');
    if (codeInput) {
        codeInput.value = code;
        updatePreview();
    }
}
