document.addEventListener('DOMContentLoaded', function() {
    const mediaItems = document.getElementById('media-items');
    const mediaCollection = document.getElementById('media-collection');
    const addMediaButton = document.getElementById('add-media-button');
    const toggleMediaBtn = document.getElementById('toggleMediaBtn');
    const toggleBtnText = document.getElementById('toggleBtnText');
    const mainMediaInput = document.getElementById('main-media-input');
    const mainMediaPreview = document.getElementById('mainMediaPreview');
    const mainMediaPreviewContent = document.getElementById('mainMediaPreviewContent');
    const modalElement = document.getElementById('mediaModal');
    const mediaUrlInput = document.getElementById('mediaUrlInput');
    const modalPreviewContainer = document.getElementById('modalPreviewContainer');
    const modalPreviewContent = document.getElementById('modalPreviewContent');
    const saveMediaButton = document.getElementById('saveMediaButton');
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    const cancelButton = modalElement ? modalElement.querySelector('.btn-secondary') : null;

    let mediaVisible = false;

    function openModal() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            modalElement.classList.add('show');
            modalElement.style.display = 'block';
            document.body.classList.add('modal-open');

            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }

    function closeModal() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
        } else {
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');

            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }

    function renderPreview(url, container) {
        if (!container) return;

        if (!url) {
            container.innerHTML = '';
            return;
        }

        if (url.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
            container.innerHTML = `<img src="${url}" alt="Aperçu" class="img-fluid" style="max-height: 150px;">`;
        } else if (url.includes('youtube') || url.includes('embed')) {
            container.innerHTML = `<div class="ratio ratio-16x9"><iframe src="${url}" allowfullscreen></iframe></div>`;
        } else {
            container.innerHTML = `<div class="alert alert-warning">L'aperçu n'est pas disponible pour cette URL</div>`;
        }
    }

    if (mainMediaInput) {
        mainMediaInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url && mainMediaPreview) {
                mainMediaPreview.classList.remove('d-none');
                renderPreview(url, mainMediaPreviewContent);
            } else if (mainMediaPreview) {
                mainMediaPreview.classList.add('d-none');
            }
        });

        if (mainMediaInput.value.trim() && mainMediaPreview) {
            mainMediaPreview.classList.remove('d-none');
            renderPreview(mainMediaInput.value.trim(), mainMediaPreviewContent);
        }
    }

    document.querySelectorAll('.media-url-input').forEach(function(input) {
        const url = input.value.trim();
        const previewContainer = input.nextElementSibling;

        if (url && previewContainer) {
            renderPreview(url, previewContainer);
        }

        input.addEventListener('input', function() {
            const container = this.nextElementSibling;
            if (container) {
                renderPreview(this.value.trim(), container);
            }
        });
    });

    if (toggleMediaBtn && mediaCollection && toggleBtnText) {
        toggleMediaBtn.addEventListener('click', function() {
            mediaVisible = !mediaVisible;
            if (mediaVisible) {
                mediaCollection.classList.remove('d-none');
                mediaCollection.classList.add('d-block');
                toggleBtnText.textContent = 'Masquer les médias';
            } else {
                mediaCollection.classList.remove('d-block');
                mediaCollection.classList.add('d-none', 'd-md-block');
                toggleBtnText.textContent = 'Afficher les médias';
            }
        });
    }

    if (addMediaButton && modalElement) {
        addMediaButton.addEventListener('click', function() {
            if (mediaUrlInput) {
                mediaUrlInput.value = '';
            }
            if (modalPreviewContainer) {
                modalPreviewContainer.classList.add('d-none');
            }

            const modalTitle = modalElement.querySelector('.modal-title');
            if (modalTitle) {
                modalTitle.textContent = 'Ajouter un média';
            }
            if (saveMediaButton) {
                saveMediaButton.textContent = 'Ajouter';
            }

            openModal();
        });
    }

    if (mediaUrlInput && modalPreviewContainer && modalPreviewContent) {
        mediaUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                modalPreviewContainer.classList.remove('d-none');
                renderPreview(url, modalPreviewContent);
            } else {
                modalPreviewContainer.classList.add('d-none');
            }
        });
    }

    closeButtons.forEach(function(button) {
        button.addEventListener('click', closeModal);
    });

    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modalElement && modalElement.classList.contains('show')) {
            closeModal();
        }
    });

    if (saveMediaButton && mediaCollection && mediaItems) {
        saveMediaButton.addEventListener('click', function() {
            if (!mediaUrlInput) return;

            const url = mediaUrlInput.value.trim();
            if (!url) {
                alert('Veuillez entrer une URL valide');
                return;
            }

            const index = parseInt(mediaCollection.dataset.index || '0');

            const prototype = mediaCollection.dataset.prototype;
            if (!prototype) {
                return;
            }

            const newWidget = prototype.replace(/__name__/g, index);

            const mediaItem = document.createElement('div');
            mediaItem.className = 'col-12 col-md-6 mb-3 media-item';
            mediaItem.innerHTML = `
                <div class="card h-100">
                    <div class="card-header p-2 d-flex justify-content-between align-items-center">
                        <span class="small">Média ${index + 1}</span>
                        <button type="button" class="btn btn-sm btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body p-3">
                        ${newWidget}
                        <div class="media-preview mt-2"></div>
                    </div>
                </div>
            `;

            mediaItems.appendChild(mediaItem);

            const input = mediaItem.querySelector('input');
            if (input) {
                input.value = url;
                input.classList.add('media-url-input');

                input.addEventListener('input', function() {
                    const previewContainer = this.nextElementSibling;
                    if (previewContainer) {
                        renderPreview(this.value.trim(), previewContainer);
                    }
                });
            }

            const previewContainer = mediaItem.querySelector('.media-preview');
            if (previewContainer) {
                renderPreview(url, previewContainer);
            }

            mediaCollection.dataset.index = (index + 1).toString();

            const deleteButton = mediaItem.querySelector('.btn-delete');
            if (deleteButton) {
                deleteButton.addEventListener('click', function() {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                        mediaItem.remove();
                    }
                });
            }

            closeModal();
        });
    }

    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                this.closest('.media-item').remove();
            }
        });
    });

    const mainMediaTabs = document.getElementById('mainMediaTabs');
    if (mainMediaTabs) {
        const urlTab = document.getElementById('url-tab');
        const fileTab = document.getElementById('file-tab');
        const urlPane = document.getElementById('url');
        const filePane = document.getElementById('file');

        if (urlTab && fileTab && urlPane && filePane) {
            urlTab.addEventListener('click', function(e) {
                e.preventDefault();

                urlTab.classList.add('active');
                urlPane.classList.add('show', 'active');

                fileTab.classList.remove('active');
                filePane.classList.remove('show', 'active');
            });

            fileTab.addEventListener('click', function(e) {
                e.preventDefault();

                fileTab.classList.add('active');
                filePane.classList.add('show', 'active');

                urlTab.classList.remove('active');
                urlPane.classList.remove('show', 'active');
            });
        }
    }

    const mainMediaFileUpload = document.querySelector('input[type="file"]:not([multiple])');
    if (mainMediaFileUpload) {
        mainMediaFileUpload.addEventListener('change', function(e) {
            if (this.files && this.files[0] && mainMediaPreview && mainMediaPreviewContent) {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    mainMediaPreview.classList.remove('d-none');
                    mainMediaPreviewContent.innerHTML = `
                        <img src="${e.target.result}" alt="Aperçu" class="img-fluid" style="max-height: 150px;">
                        <div class="mt-1 text-muted small">Fichier: ${file.name} (${(file.size / 1024).toFixed(1)} Ko)</div>
                    `;
                }

                reader.readAsDataURL(file);
            }
        });
    }

    const mediaGalleryFileUpload = document.getElementById('gallery-file-upload');
    if (mediaGalleryFileUpload && mediaItems && mediaCollection) {
        mediaGalleryFileUpload.addEventListener('change', function(e) {
            if (!this.files || this.files.length === 0) return;

            const files = Array.from(this.files);
            let startIndex = parseInt(mediaCollection.dataset.index || '0');

            let processedFiles = 0;

            files.forEach((file, i) => {
                const reader = new FileReader();
                const index = startIndex + i;

                reader.onload = function(e) {
                    const prototype = mediaCollection.dataset.prototype;
                    if (!prototype) return;

                    const newWidget = prototype.replace(/__name__/g, index);

                    const mediaItem = document.createElement('div');
                    mediaItem.className = 'col-12 col-md-6 mb-3 media-item';
                    mediaItem.innerHTML = `
                        <div class="card h-100">
                            <div class="card-header p-2 d-flex justify-content-between align-items-center">
                                <span class="small">Média ${index + 1} (Fichier)</span>
                                <button type="button" class="btn btn-sm btn-danger btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="card-body p-3">
                                ${newWidget}
                                <div class="media-preview mt-2">
                                    <img src="${e.target.result}" alt="Aperçu" class="img-fluid" style="max-height: 150px;">
                                    <div class="mt-1 text-muted small">Fichier: ${file.name} (${(file.size / 1024).toFixed(1)} Ko)</div>
                                </div>
                            </div>
                        </div>
                    `;

                    mediaItems.appendChild(mediaItem);

                    const input = mediaItem.querySelector('input');
                    if (input) {
                        input.value = `[Fichier local: ${file.name}]`;
                        input.classList.add('media-url-input', 'bg-light');
                    }

                    const deleteButton = mediaItem.querySelector('.btn-delete');
                    if (deleteButton) {
                        deleteButton.addEventListener('click', function() {
                            if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                                mediaItem.remove();
                            }
                        });
                    }

                    processedFiles++;

                    if (processedFiles === files.length) {
                        mediaCollection.dataset.index = (startIndex + files.length).toString();
                    }
                };

                reader.readAsDataURL(file);
            });

            this.value = '';
        });
    }
});