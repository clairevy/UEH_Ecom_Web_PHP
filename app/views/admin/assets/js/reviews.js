// JS extracted from reviews.html
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'reviews';

function viewReview(id){ alert('Xem chi tiết đánh giá: '+id); }
function approveReview(id){ if(confirm('Duyệt đánh giá?')){ alert('Đã duyệt!'); location.reload(); } }
function rejectReview(id){ if(confirm('Từ chối đánh giá?')){ alert('Đã từ chối!'); location.reload(); } }
function replyReview(id){ const r = prompt('Nhập phản hồi:'); if(r && r.trim()) alert('Phản hồi đã gửi!'); }
function hideReview(id){ if(confirm('Ẩn đánh giá?')){ alert('Đã ẩn!'); location.reload(); } }
function bulkApprove(){ const checked = document.querySelectorAll('.row-checkbox:checked'); if(checked.length===0){ alert('Vui lòng chọn ít nhất một đánh giá!'); return; } if(confirm(`Duyệt ${checked.length} đánh giá?`)){ alert('Đã duyệt!'); location.reload(); } }
function bulkReject(){ const checked = document.querySelectorAll('.row-checkbox:checked'); if(checked.length===0){ alert('Vui lòng chọn ít nhất một đánh giá!'); return; } if(confirm(`Từ chối ${checked.length} đánh giá?`)){ alert('Đã từ chối!'); location.reload(); } }

document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('reviewSearch');
    if (search) search.addEventListener('input', function(){ const t=this.value.toLowerCase(); document.querySelectorAll('#reviewsTable tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(t)?'':'none'); });

    const filter = document.getElementById('statusFilter');
    if (filter) filter.addEventListener('change', function(){ const v=this.value; document.querySelectorAll('#reviewsTable tbody tr').forEach(row=>{ const status=(row.querySelector('.badge')||{}).textContent||''; if(v==='all') row.style.display=''; else if(v==='pending'&&status.toLowerCase().includes('chờ')) row.style.display=''; else if(v==='approved'&&status.toLowerCase().includes('duyệt')) row.style.display=''; else if(v==='rejected'&&status.toLowerCase().includes('từ')) row.style.display=''; else row.style.display='none'; }); });

    const selectAll = document.getElementById('selectAll');
    if(selectAll) selectAll.addEventListener('change', function(){ document.querySelectorAll('.row-checkbox').forEach(cb=>cb.checked=this.checked); });
});
