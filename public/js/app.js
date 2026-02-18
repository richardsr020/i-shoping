(function () {
    function buildPreviewContainer(input) {
        const container = document.createElement('div');
        container.className = 'js-image-preview';
        container.style.display = 'none';
        container.style.marginTop = '10px';
        container.style.gap = '8px';
        container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(110px, 1fr))';
        input.insertAdjacentElement('afterend', container);
        return container;
    }

    function createCard(file) {
        const card = document.createElement('div');
        card.style.border = '1px solid #ddd';
        card.style.borderRadius = '8px';
        card.style.padding = '6px';
        card.style.background = '#fff';
        card.style.display = 'grid';
        card.style.gap = '6px';

        const img = document.createElement('img');
        img.alt = file.name;
        img.style.width = '100%';
        img.style.height = '90px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '6px';
        img.style.background = '#f3f3f3';

        const name = document.createElement('div');
        name.textContent = file.name;
        name.style.fontSize = '12px';
        name.style.color = '#555';
        name.style.overflow = 'hidden';
        name.style.textOverflow = 'ellipsis';
        name.style.whiteSpace = 'nowrap';

        const reader = new FileReader();
        reader.onload = function (event) {
            img.src = String(event.target && event.target.result ? event.target.result : '');
        };
        reader.readAsDataURL(file);

        card.appendChild(img);
        card.appendChild(name);
        return card;
    }

    function bindImagePreview(input) {
        if (!(input instanceof HTMLInputElement) || input.type !== 'file') {
            return;
        }

        const accept = (input.getAttribute('accept') || '').toLowerCase();
        if (!accept.includes('image')) {
            return;
        }

        const preview = buildPreviewContainer(input);

        input.addEventListener('change', function () {
            const files = Array.from(input.files || []);
            preview.innerHTML = '';

            if (files.length === 0) {
                preview.style.display = 'none';
                return;
            }

            preview.style.display = 'grid';
            files.forEach(function (file) {
                if (file.type && !file.type.startsWith('image/')) {
                    return;
                }
                preview.appendChild(createCard(file));
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        inputs.forEach(function (input) {
            bindImagePreview(input);
        });
    });
})();
