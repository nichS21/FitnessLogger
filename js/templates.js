let selectedExercises = [];
let draggedIndex = null;

function openExerciseModal(eid, name) {
    document.getElementById('modalEid').value = eid;
    document.getElementById('modalExerciseName').innerText = name;
    document.getElementById('modalReps').value = '';
    document.getElementById('modalSets').value = '';
    document.getElementById('modalWeight').value = '';
    document.getElementById('modalTime').value = '';

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('exerciseModal'));
    modal.show();
}

function addExerciseToTemplate() {
    const setsInput = document.getElementById('modalSets');
    const sets = parseInt(setsInput.value.trim(), 10);

    if (isNaN(sets) || sets <= 0 || sets.toString() !== setsInput.value.trim()) {
        showToast('Sets must be a positive whole number.', 'error');
        setsInput.focus();
        return;
    }

    const eid = document.getElementById('modalEid').value;
    const name = document.getElementById('modalExerciseName').innerText;
    const reps = document.getElementById('modalReps').value.trim();
    const weight = document.getElementById('modalWeight').value.trim();
    const time = document.getElementById('modalTime').value.trim();

    selectedExercises.push({
        eid,
        name,
        reps: reps !== '' ? parseInt(reps, 10) || 0 : 0,
        sets,
        weight: weight !== '' ? parseInt(weight, 10) || 0 : 0,
        time: time !== '' ? parseInt(time, 10) || 0 : 0
    });

    renderExerciseTable();
    bootstrap.Modal.getInstance(document.getElementById('exerciseModal')).hide();
}


function renderExerciseTable() {
    const table = document.getElementById('templateTableBody');
    table.innerHTML = '';

    selectedExercises.forEach((ex, index) => {
      const row = document.createElement('tr');
      row.setAttribute('draggable', 'true');
      row.setAttribute('data-index', index);
      row.classList.add('draggable-row');

      row.innerHTML = `
        <td>${ex.name}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'reps', this.innerText)">${ex.reps || '0'}</td>
        <td contenteditable="true" onblur="updateField(${index}, 'sets', this.innerText)">${ex.sets}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'weight', this.innerText)">${ex.weight || '0'}</td>
        <td contenteditable="true" oninput="updateField(${index}, 'time', this.innerText)">${ex.time || '0'}</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeExercise(${index})">Remove</button>
        </td>
        <td class="drag-handle text-center">
            <span style="cursor: move;">⋮⋮⋮</span>
        </td>
      `;

      // Drag event handlers
      row.addEventListener('dragstart', dragStart);
      row.addEventListener('dragover', dragOver);
      row.addEventListener('drop', dropRow);
      row.addEventListener('dragend', dragEnd);

      table.appendChild(row);
    });
}

function updateField(index, field, value) {
    value = value.trim();

    if (['sets', 'reps', 'weight', 'time'].includes(field)) {
        const parsed = parseInt(value, 10);

        if (field === 'sets') {
            // Sets is required and must be positive integer
            if (isNaN(parsed) || parsed <= 0 || parsed.toString() !== value) {
                showToast('Sets must be a positive whole number.', 'error');
                renderExerciseTable(); 
                return;
            }
            selectedExercises[index][field] = parsed;
        } else {
            // Optional fields (reps, weight, time)
            if (value === '') {
                selectedExercises[index][field] = 0; 
            } else {
                if (isNaN(parsed) || parsed <= 0 || parsed.toString() !== value) {
                    showToast(`${capitalize(field)} must be a positive whole number if entered.`, 'error');
                    renderExerciseTable();
                    return;
                }
                selectedExercises[index][field] = parsed;
            }
        }
    } else {
        selectedExercises[index][field] = value;
    }
}

// Helper function to capitalize field names for error message
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function removeExercise(index) {
    selectedExercises.splice(index, 1);
    renderExerciseTable();
}

function dragStart(e) {
    draggedIndex = parseInt(e.target.getAttribute('data-index'));
    e.target.classList.add('dragging');
}

function dragOver(e) {
    e.preventDefault(); // Necessary for drop to work
}

function dropRow(e) {
    const targetRow = e.target.closest('tr');
    if (!targetRow) return;

    const targetIndex = parseInt(targetRow.getAttribute('data-index'));

    if (draggedIndex === null || targetIndex === draggedIndex) return;

    const movedItem = selectedExercises.splice(draggedIndex, 1)[0];
    selectedExercises.splice(targetIndex, 0, movedItem);

    draggedIndex = null;
    renderExerciseTable();  
}

function dragEnd(e) {
    e.target.classList.remove('dragging');
}

document.getElementById('exerciseSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.exercise-btn').forEach(btn => {
      const name = btn.getAttribute('data-name').toLowerCase();
      btn.style.display = name.includes(query) ? 'inline-block' : 'none';
    });
});

document.querySelector('form').addEventListener('submit', function () {
document.getElementById('templateExercisesJSON').value = JSON.stringify(selectedExercises);

});

function showToast(message, type = 'error') {
    let toastEl = document.getElementById('errorToast');
    let toastBody = document.getElementById('errorToastBody');

    // If toast DOM doesn't exist, create it dynamically
    if (!toastEl) {
        const container = document.createElement('div');
        container.className = "position-fixed top-0 end-0 p-3";
        container.style.zIndex = "1055";
        container.innerHTML = `
            <div id="errorToast" class="toast align-items-center text-bg-${type === 'error' ? 'danger' : 'success'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="errorToastBody">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        document.body.appendChild(container);
        toastEl = document.getElementById('errorToast');
        toastBody = document.getElementById('errorToastBody');
    }

    // Update message and styling
    toastBody.innerText = message;
    toastEl.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : 'success'} border-0`;

    // Show the toast using Bootstrap
    const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.show();
}

