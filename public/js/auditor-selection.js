// Smart Auditor Selection
let auditors = [];

function loadAuditors(sortBy = 'performance') {
    const auditorList = document.getElementById('auditor-list');
    auditorList.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';

    fetch(`/api/auditors?sort_by=${sortBy}`)
        .then(response => response.json())
        .then(data => {
            auditors = data;
            renderAuditorList();
        })
        .catch(error => {
            console.error('Error loading auditors:', error);
            auditorList.innerHTML = '<div class="alert alert-danger">Failed to load auditors</div>';
        });
}

function renderAuditorList() {
    const auditorList = document.getElementById('auditor-list');
    
    if (auditors.length === 0) {
        auditorList.innerHTML = '<p class="text-muted">No auditors available</p>';
        return;
    }

    let html = '';
    auditors.forEach(auditor => {
        const statusColor = auditor.workload_color;
        const statusText = auditor.workload_status;
        const stars = '⭐'.repeat(Math.round(auditor.performance_score / 20));

        html += `
            <div class="form-check border-bottom pb-3 mb-3">
                <input class="form-check-input" type="checkbox" name="auditor_ids[]" 
                       value="${auditor.id}" id="auditor_${auditor.id}">
                <label class="form-check-label w-100" for="auditor_${auditor.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${auditor.name}</strong>
                            <span class="badge bg-${statusColor} ms-2">${statusText}</span>
                        </div>
                        <div class="text-end">
                            <div class="small">${stars} (${auditor.performance_score}/100)</div>
                        </div>
                    </div>
                    <div class="small text-muted mt-1">
                        <div>Current Load: ${auditor.active_projects_count} active projects</div>
                        ${auditor.specialization ? `<div>Specialization: ${auditor.specialization}</div>` : ''}
                        ${auditor.certification ? `<div>Certification: ${auditor.certification}</div>` : ''}
                    </div>
                </label>
            </div>
        `;
    });

    auditorList.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    loadAuditors();

    document.getElementById('sort_by').addEventListener('change', function() {
        loadAuditors(this.value);
    });
});
