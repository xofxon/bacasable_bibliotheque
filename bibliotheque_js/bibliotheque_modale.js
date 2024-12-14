document.addEventListener('DOMContentLoaded', function() {
    var modal = document.querySelector('.modal-content');
    var isDragging = false;
    var offsetX = 0;
    var offsetY = 0;

    modal.addEventListener('mousedown', function(e) {
        isDragging = true;
        offsetX = e.clientX - modal.offsetLeft;
        offsetY = e.clientY - modal.offsetTop;
    });

    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            modal.style.left = (e.clientX - offsetX) + 'px';
            modal.style.top = (e.clientY - offsetY) + 'px';
        }
    });

    document.addEventListener('mouseup', function() {
        isDragging = false;
    });
});
