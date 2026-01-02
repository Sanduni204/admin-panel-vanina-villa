@props(['name' => 'gallery_images', 'label' => 'Gallery Images'])

<div class="mb-3">
    <label class="form-label">{{ $label }}</label>

    <div class="card">
        <div class="card-body">
            <input
                type="file"
                id="galleryImageInput"
                name="{{ $name }}[]"
                multiple
                accept="image/jpeg,image/png,image/webp"
                class="form-control mb-3"
            >
            <small class="text-muted">You can select multiple images at once. Max 5MB per image.</small>

            <div id="galleryPreview" class="row g-2 mt-3">
                <!-- Images will be added here dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('galleryImageInput');
    const preview = document.getElementById('galleryPreview');

    if (!input || !preview) return;

    input.addEventListener('change', function() {
        preview.innerHTML = '';
        const files = this.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type.match('image.*')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'col-md-3';
                    div.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" alt="Preview" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <input type="text" name="gallery_alt_en[]" class="form-control form-control-sm mb-2" placeholder="Alt text EN">
                                <input type="text" name="gallery_alt_fr[]" class="form-control form-control-sm mb-2" placeholder="Alt text FR">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    preview.appendChild(div);
                };

                reader.readAsDataURL(file);
            }
        }
    });
});
</script>
