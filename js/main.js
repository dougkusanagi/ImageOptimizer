const CANVAS = document.createElement('canvas');
const CTX = CANVAS.getContext('2d');

CANVAS.width = document.querySelector('#width').value;
CANVAS.height = document.querySelector('#width').value;

CANVAS.style.maxWidth = '100%';
CANVAS.style.backgroundColor = '#fff';

// Append canvas to body
document.querySelector('#container-canvas').appendChild(CANVAS);

function getParamsForm(name) {
    const form = new FormData();
    form.append('name', name);
    form.append('ext', document.querySelector('#extension').value);
    form.append('dir', document.querySelector('#output-dir').value);
    form.append('width', document.querySelector('#width').value);
    form.append('saveWebpAsJpeg',
        document.querySelector('#saveWebpAsJpeg').checked ? 1 : 0);
    form.append('data', CANVAS.toDataURL(
        document.querySelector('#extension').value,
        document.querySelector('#quality').value));
    
    return form;
}

function saveDataUrl(name) {
    const form = getParamsForm(name);
    // Fetch save.php
    return fetch('save.php', {
        method: 'POST',
        body: form
    });
}

// Load image
function loadImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = url;
    });
}

// Draw image
function drawImage(img, x, y) {
    return new Promise(resolve => {
        CTX.drawImage(img, x, y, CANVAS.width, CANVAS.height);
        resolve();
    });
}

function loadFilesToList(files, list, callback) {
    for (let index = 0; index < files.length; index++) {
        var file = files[index];
        if (file.type.match(/image.*/)) {
            list.push({
                name: file.name,
                url: URL.createObjectURL(file),
            });
        }
    }
    callback();
}

function optimizeAllImages(images, i = 0) {
    loadImage(images[i].url).then(img => {
        return drawImage(img, 0, 0);
    }).then(() => {
        return saveDataUrl(images[i].name);
    }).then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
            return;
        }

        if (i < images.length - 1) {
            optimizeAllImages(images, i + 1);
            return;
        }

        alert('Todas as imagens foram otimizadas!');
    });
}

function startOptimizeAllImages() {
    const imageList = [];

    loadFilesToList(getFileListFromInput(), imageList, () => {
        optimizeAllImages(imageList);
    });
}

function loadFirstImageToCanvas(firstImage) {
    loadImage(firstImage.url).then(img => {
        drawImage(img, 0, 0);
    });
}

function loadImagesToSystem() {
    const imageList = [];

    loadFilesToList(getFileListFromInput(), imageList, () => {
        loadFirstImageToCanvas(imageList[0]);
    });
}

function getFileListFromInput() {
    return document.querySelector('#dir').files;
}

document.querySelector('#start').addEventListener('click', startOptimizeAllImages);
document.querySelector('#dir').addEventListener('change', loadImagesToSystem);

