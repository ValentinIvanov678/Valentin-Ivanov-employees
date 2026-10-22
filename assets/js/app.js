document.addEventListener('DOMContentLoaded', () => {
    const uidForm = document.getElementById('uid-form');
    const uploadForm = document.getElementById('upload-form');
    const csvFileInput = document.getElementById('csv-file-input');
    const fileLabel = document.getElementById('file-label');
    const uploadBtn = document.getElementById('upload-btn');
    const backBtn = document.getElementById('back-btn');
    const spinner = document.getElementById('loading-spinner');
    const resultsContent = document.getElementById('results-content');

    function showView(viewId) {
        document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
        document.getElementById(viewId).classList.add('active');
    }

    csvFileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            fileLabel.textContent = e.target.files[0].name;
            uploadBtn.removeAttribute('disabled');
        } else {
            fileLabel.textContent = 'Choose CSV file or drag & drop here';
            uploadBtn.setAttribute('disabled', 'true');
        }
    });

    backBtn.addEventListener('click', () => {
        showView('view-selection');
    });

    uidForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const uid = document.getElementById('file-uid-select').value;
        showView('view-results');
        fetchResults(`/api/process?UID=${encodeURIComponent(uid)}`);
    });

    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const file = csvFileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('csv_file', file);

        showView('view-results');
        fetchResults('/api/process', {
            method: 'POST',
            body: formData
        });
    });

    async function fetchResults(url, options = {}) {
        spinner.classList.remove('hidden');
        resultsContent.innerHTML = '';

        try {
            const response = await fetch(url, options);
            const result = await response.json();

            spinner.classList.add('hidden');

            if (result.status === 'success') {
                renderResults(result.data);
            } else {
                resultsContent.innerHTML = `<p style="color: red; text-align: center;">Error: ${result.message}</p>`;
            }
        } catch (err) {
            spinner.classList.add('hidden');
            resultsContent.innerHTML = `<p style="color: red; text-align: center;">Network or Server Error occurred.</p>`;
            console.error(err);
        }
    }

    function renderResults(pairs) {
        if (!pairs || pairs.length === 0) {
            resultsContent.innerHTML = `<p style="text-align: center; color: var(--text-muted);">No overlapping collaboration pairs found.</p>`;
            return;
        }

        let rank1Html = '';
        let rank2And3Html = '';
        let gridHtml = '';

        pairs.forEach((pair, index) => {
            let projectsHtml = '';
            pair.Projects.forEach(proj => {
                projectsHtml += `
                    <div class="project-item">
                        <span>Project #${proj.ProjectID} (${proj.OverlapStart} to ${proj.OverlapEnd})</span>
                        <strong>${proj.OverlapDays} days</strong>
                    </div>
                `;
            });

            const rankClass = index === 0 ? 'rank-1' : (index === 1 ? 'rank-2' : (index === 2 ? 'rank-3' : ''));

            const cardMarkup = `
                <div class="pair-card ${rankClass}">
                    <div class="pair-header">
                        <span class="employees-title">#${index + 1} • Emp #${pair.Employee1} & Emp #${pair.Employee2}</span>
                        <span class="badge">${pair.TotalOverlapDays} Total Days</span>
                    </div>
                    <div class="project-sublist">
                        ${projectsHtml}
                    </div>
                </div>
            `;

            if (index === 0) {
                rank1Html = cardMarkup;
            } else if (index === 1 || index === 2) {
                rank2And3Html += cardMarkup;
            } else {
                gridHtml += cardMarkup;
            }
        });

        resultsContent.innerHTML = `
            <div class="podium-container">
                ${rank1Html}
                ${rank2And3Html ? `<div class="podium-row-2">${rank2And3Html}</div>` : ''}
            </div>
            ${gridHtml ? `<div class="cards-grid">${gridHtml}</div>` : ''}
        `;
    }
});