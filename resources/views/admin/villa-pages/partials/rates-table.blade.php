<div class="mb-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Rates Topic (EN)</label>
            <input type="text" id="rates_topic_en" class="form-control" placeholder="e.g., The Room" value="{{ $villaEn?->rates_topic ?? '' }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Rates Topic (FR)</label>
            <input type="text" id="rates_topic_fr" class="form-control" placeholder="e.g., La Chambre" value="{{ $villaFr?->rates_topic ?? '' }}">
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <label class="form-label fw-bold">Date Range Sentence (EN)</label>
            <input type="text" id="rates_sentence_en" class="form-control" placeholder="e.g., Available from January to December" value="{{ $villaEn?->rates_sentence ?? '' }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Date Range Sentence (FR)</label>
            <input type="text" id="rates_sentence_fr" class="form-control" placeholder="e.g., Disponible de janvier à décembre" value="{{ $villaFr?->rates_sentence ?? '' }}">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Room Image</label>
        <div class="border-2 border-dashed rounded p-4 text-center roomImageDropZone" data-villa-id="{{ $villa->id }}">
            @if ($villaEn && $villaEn->room_image_path)
                <img src="{{ asset('storage/' . $villaEn->room_image_path) }}" alt="Room" class="img-fluid mb-3" style="max-height: 200px;">
            @else
                <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #0d6efd;"></i>
            @endif
            <p class="mt-3 mb-0 text-muted">Drag & drop or click to upload room image</p>
            <input type="file" name="room_image" class="d-none roomImageInput" accept="image/*" data-villa-id="{{ $villa->id }}">
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Room Type</th>
                <th>Season</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ratesTableBody">
            @forelse ($rates as $rate)
                <tr class="rate-row" data-rate-id="{{ $rate->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $rate->room_type }}</td>
                    <td>{{ $rate->season_name }}</td>
                    <td>{{ $rate->season_start?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $rate->season_end?->format('Y-m-d') ?? '-' }}</td>
                    <td>${{ number_format($rate->price, 2) }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning edit-rate" data-rate-id="{{ $rate->id }}" data-bs-toggle="modal" data-bs-target="#rateModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-rate" data-rate-id="{{ $rate->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No rates added yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#rateModal">
    <i class="bi bi-plus-circle"></i> Add New Rate
</button>

<!-- Rate Modal -->
<div class="modal fade" id="rateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rateForm">
                    <input type="hidden" id="rateId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Room Type *</label>
                        <input type="text" id="roomType" class="form-control" placeholder="e.g., Standard Double">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Season Name *</label>
                        <input type="text" id="seasonName" class="form-control" placeholder="e.g., Summer Peak">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Season Start Date</label>
                        <input type="date" id="seasonStart" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Season End Date</label>
                        <input type="date" id="seasonEnd" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Price (USD) *</label>
                        <input type="number" id="price" class="form-control" placeholder="0.00" step="0.01" min="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRateBtn">Save Rate</button>
            </div>
        </div>
    </div>
</div>

<script>
    const villaId = {{ $villa->id }};
    const rateModal = new bootstrap.Modal(document.getElementById('rateModal'));

    // Add new rate
    document.getElementById('saveRateBtn').addEventListener('click', saveRate);

    function saveRate() {
        const rateId = document.getElementById('rateId').value;
        const data = {
            room_type: document.getElementById('roomType').value,
            season_name: document.getElementById('seasonName').value,
            season_start: document.getElementById('seasonStart').value,
            season_end: document.getElementById('seasonEnd').value,
            price: document.getElementById('price').value,
        };

        if (!data.room_type || !data.season_name || !data.price) {
            alert('Please fill in required fields');
            return;
        }

        const method = rateId ? 'PUT' : 'POST';
        const url = rateId 
            ? `/admin/villa-pages/${villaId}/rates/${rateId}` 
            : `/admin/villa-pages/${villaId}/rates`;

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    // Delete rate
    document.querySelectorAll('.delete-rate').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const rateId = btn.dataset.rateId;
            if (confirm('Delete this rate?')) {
                fetch(`/admin/villa-pages/${villaId}/rates/${rateId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });

    // Edit rate
    document.querySelectorAll('.edit-rate').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const rateId = btn.dataset.rateId;
            const row = document.querySelector(`[data-rate-id="${rateId}"]`);
            const cells = row.querySelectorAll('td');

            document.getElementById('rateId').value = rateId;
            document.getElementById('roomType').value = cells[1].textContent;
            document.getElementById('seasonName').value = cells[2].textContent;
            document.getElementById('seasonStart').value = cells[3].textContent === '-' ? '' : cells[3].textContent;
            document.getElementById('seasonEnd').value = cells[4].textContent === '-' ? '' : cells[4].textContent;
            document.getElementById('price').value = cells[5].textContent.replace('$', '').replace(',', '');
        });
    });

    // Room image upload
    document.querySelectorAll('.roomImageDropZone').forEach(zone => {
        const villaId = zone.dataset.villaId;
        const input = zone.querySelector('.roomImageInput');

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('bg-light');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('bg-light');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('bg-light');
            input.files = e.dataTransfer.files;
            uploadRoomImage(input.files[0], villaId);
        });

        zone.addEventListener('click', () => {
            input.click();
        });

        input.addEventListener('change', (e) => {
            uploadRoomImage(e.target.files[0], villaId);
        });
    });

    function uploadRoomImage(file, villaId) {
        const formData = new FormData();
        formData.append('room_image', file);
        formData.append('locale', 'en');

        fetch(`/admin/villa-pages/${villaId}/room-upload`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
</script>
