// Smart Auditor Selection
let auditors = [];

function loadAuditors() {
    const auditorsList = document.getElementById('auditorsList') || document.getElementById('auditor-list');
    if (!auditorsList) return;

    const sortByElement = document.getElementById('sortBy') || document.getElementById('sort_by');
    const sortBy = sortByElement ? sortByElement.value : 'performance';

    auditorsList.innerHTML = `
        <div class="text-center">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mb-0 mt-2">Loading auditors...</p>
        </div>
    `;

    fetch(`/api/auditors?sort_by=${sortBy}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                auditorsList.innerHTML = '<p class="text-muted mb-0">No auditors available</p>';
                return;
            }

            let html = '';
            data.forEach(auditor => {
                const score = auditor.workload_score !== undefined ? auditor.workload_score : (auditor.current_load || 0);
                const workloadClass = auditor.workload_color_class || ('workload-' + Math.min(score, 5));
                const label = auditor.workload_status || (score === 0 ? 'Available' : (score <= 2 ? 'Light' : 'Busy'));

                const bgClass = score >= 5 ? 'auditor-busy' : '';

                html += `
                <div class="form-check mb-3 p-3 border rounded ${bgClass}">
                    <input class="form-check-input" type="checkbox" name="auditor_ids[]" 
                           value="${auditor.id}" id="auditor${auditor.id}">
                    <label class="form-check-label w-100" for="auditor${auditor.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${auditor.user.name}</strong>
                                <br>
                                <small class="text-muted">${auditor.specialization || 'General'}</small>
                                ${auditor.certification ? `<br><small class="text-info">${auditor.certification}</small>` : ''}
                            </div>
                            <div class="text-end">
                                <span class="badge ${workloadClass}">${label} (${score})</span>
                                <br>
                                <small class="text-muted">Load: ${score} projects</small>
                            </div>
                        </div>
                    </label>
                </div>
            `;
            });

            auditorsList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading auditors:', error);
            auditorsList.innerHTML = '<p class="text-danger mb-0">Error loading auditors. Please refresh the page.</p>';
        });
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('auditorsList') || document.getElementById('auditor-list')) {
        loadAuditors();

        const sortByElement = document.getElementById('sortBy') || document.getElementById('sort_by');
        if (sortByElement) {
            sortByElement.addEventListener('change', function () {
                loadAuditors();
            });
        }
    }
});
