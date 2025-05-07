//js code to select Unsplash images
function selectImages(){
    const accessKey = "53M4w0Qe-lhJwfLm2Wmc4D0j0sTSjGu0vGjrwDGp7zQ";
    document.getElementById('imageSearch').addEventListener('input', async (e) => {     
        const query = e.target.value.trim();
        const container = document.getElementById('imageResults');
        if (!query) return container.innerHTML = "";
        
        container.innerHTML = "<p>Searching...</p>";

        const res = await fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=10&client_id=${accessKey}`);
        const data = await res.json();
        container.innerHTML = "";
        data.results.forEach(photo => {
            const img = document.createElement('img');
            img.src = photo.urls.thumb;
            img.alt = photo.alt_description;
            img.style.cursor = 'pointer';
            img.style.border = '2px solid transparent';
            img.style.borderRadius = '8px';
            img.onclick = async () => {
            
                // Highlight selected image
                document.querySelectorAll('#imageResults img').forEach(i => i.style.border = '2px solid transparent');
                img.style.border = '2px solid #2196f3';

                // Set the image URL in the hidden form field
                document.getElementById('selectedImage').value = photo.urls.regular;

                // Track the download (Unsplash requirement)
                const downloadURL = photo.links.download_location;
                const fullURL = downloadURL.includes('?') ? `${downloadURL}&client_id=${accessKey}`: `${downloadURL}?client_id=${accessKey}`;

                try {
                    await fetch(fullURL); // async tracking event
                    console.log("Download tracked successfully.");
                } 
                catch (err) { 
                    console.error("Error tracking download:", err);
                }
            };

            container.appendChild(img);

        });
    });
}

// --- Form Validation for template selection ---
function validateClassTemp() {
    const form = document.querySelector('form[name="createCourse"]');
    const templateSelect = document.getElementById('template');
    const submitButton = form.querySelector('button[type="submit"]');
    const errorMessage = document.createElement('div');

    if (!form || !templateSelect || !submitButton) return;

    // Disable submit button initially
    submitButton.disabled = true;

    templateSelect.addEventListener('change', function () {
        if (templateSelect.value === "") {
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
            errorMessage.style.display = 'none';
        }
    });

    form.addEventListener('submit', function (event) {
        if (templateSelect.value === "") {
            event.preventDefault();
            errorMessage.style.display = 'block';
            templateSelect.focus();
        } else {
            errorMessage.style.display = 'none';
        }
    });
}

// Running js functions ---
document.addEventListener('DOMContentLoaded', function () {
    selectImages();
    validateClassTemp();

    document.getElementById('selectedImage').value = "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3MzY1NzV8MHwxfHNlYXJjaHwxMHx8c3RyZW5ndGh8ZW58MHx8fHwxNzQ1NjgyMDg3fDA&ixlib=rb-4.0.3&q=80&w=1080";

    if (noTemplates) {
        setTimeout(() => {
            showNoTemplatesModal();
        }, 500); 
    }
});

// --- Show custom popup if no templates exist ---
function showNoTemplatesModal() {
    const modal = document.getElementById('noTemplatesModal');
    const overlay = document.getElementById('modalOverlay');
    const createBtn = document.getElementById('createTemplateBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    if (!modal || !overlay) return;

    modal.style.display = 'block';
    overlay.style.display = 'block';  

    createBtn.onclick = function() {
        window.location.href = 'createTemp.php';
    };

    cancelBtn.onclick = function() {
        modal.style.display = 'none';
        overlay.style.display = 'none'; 
    };

    closeBtn.onclick = function() {
        modal.style.display = 'none';
        overlay.style.display = 'none'; 
    };
}



